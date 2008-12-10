<?php 

/**
 * PHP script to pull location data from MySQL, output XML for Google Maps.
 * Credit: http://code.google.com/apis/maps/articles/phpsqlajax.html
 * 
 */

require_once($_SERVER['DOCUMENT_ROOT'] . "/php/init.php"); // do site init stuff

// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node); 

// Opens a connection to a MySQL server
$connection=mysql_connect ($dbhost, $dbuser, $dbpass);
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

// Set the active MySQL database
$db_selected = mysql_select_db($dbname, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
}

// Select all the rows in the markers table
$query = "SELECT * FROM $dbtable WHERE 1";
$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  
  // concatenate address string
  $address = implode(', ', array($row['address_1'], $row['city'], $row['state'], $row['zip']));
  
  // concatenate names, if name_2 exists
  if (!empty($row['name_2'])) {
    $name = implode(': ', array($row['name_1'], $row['name_2']));
  } else {
    $name = $row['name_1'];
  }
  
  // if location is missing either latitude or longitude info, geocode it
  // using the Google Maps geocoder (returns CSV), and store in the database for future use
  if ((empty($row['latitude']) || empty($row['longitude'])) ||
      ($row['latitude'] == 0   || $row['longitude'] == 0 )) {
    
    $url = "http://maps.google.com/maps/geo?q=".urlencode($address)."&output=csv&sensor=false&key=".$api_key;
    
    // create HTTP request object
    $req =& new HTTP_Request();
    $req->setURL($url);
    $req->sendRequest();

    // deal with HTTP response (decode response or throw error)
    $response_code = $req->getResponseCode();
    $response_body = $req->getResponseBody();
    switch ($response_code) {
      case 200:
        $geocoded = explode(',', $response_body);
        break;
      // other handleable cases should be dealt with here
      default:
        die('Error doing HTTP request to geocoder. HTTP response code: '.$response_code);
        break;
    }
    
    // store the lat/long in db for later use
    $row['geocoding_response_code'] = $geocoded[0];
    $row['geocoding_accuracy'] = $geocoded[1];
    $row['latitude'] = $geocoded[2];
    $row['longitude']  = $geocoded[3];
    
    $query = <<<SQL
      UPDATE locations 
      SET 
        latitude="{$row['latitude']}", 
        longitude="{$row['longitude']}", 
        geocoding_accuracy="{$row['geocoding_accuracy']}",
        geocoding_response_code="{$row['geocoding_response_code']}",
        geocoding_response_body="$response_body"
      WHERE id={$row['id']}
      LIMIT 1
SQL;
    if (!mysql_query($query)) {
      die("Unable to update database for location ID {$row['id']}");
    }
  }
  
  // ADD TO XML DOCUMENT NODE  
  $node = $dom->createElement("marker");  
  $newnode = $parnode->appendChild($node);   
  $newnode->setAttribute("name", $name);
  $newnode->setAttribute("address", $address);  
  $newnode->setAttribute("lat", $row['latitude']);  
  $newnode->setAttribute("lng", $row['longitude']);  
  $newnode->setAttribute("type", $row['category_1']);
}

echo $dom->saveXML();



?>