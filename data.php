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

// build appropriate filter string
if (isset($_GET['filter'])) {
  $filter_sql = 'WHERE `category_1` IN ("'.strtotitle(implode('","', array_keys($_GET['filter']))).'")';
} else {
  // the filter GET variables should always be set; if they're not, don't return any results
  $filter_sql = 'WHERE FALSE';
}

// Select all the rows in the markers table, subject to filter string
$query = "SELECT * FROM $dbtable $filter_sql ORDER BY name_1";
$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  
  // concatenate address strings (separate line 1 and 2 for display and full for geocoding)
  $address_1 = (!empty($row['address_2'])) ? $row['address_1'].', '.$row['address_2'] : $row['address_1'];
  $address_2 = $row['city'] . ', ' . $row['state'] . ' ' . $row['zip'];
  $address = implode(', ', array($row['address_1'], $row['city'], $row['state'], $row['zip']));
  
  // concatenate names, if name_2 exists
  $name = (!empty($row['name_2'])) ? $row['name_1'].': '.$row['name_2'] : $row['name_1'];
  
  // if location is missing either latitude or longitude info, geocode it
  // using the Google Maps geocoder (returns CSV), and store in the database for future use
  if ((empty($row['latitude']) || empty($row['longitude'])) ||
      ($row['latitude'] == 0   || $row['longitude'] == 0 )) {

    $url = "http://maps.google.com/maps/geo?output=csv&sensor=false&key=".$api_key."&q=".urlencode($address);
    
    // create HTTP request object
    $req =& new HTTP_Request();
    $req->setURL($url);

    // Initialize delay in geocode speed
    $delay = 0;
    $geocode_pending = true;
    
    // loop until we get a good status from geocoder
    while ($geocode_pending) {
    
      $req->sendRequest();

      // deal with HTTP response (decode response or throw error)
      $response_code = $req->getResponseCode();
      $response_body = $req->getResponseBody();
      
      if ($response_code != '200') die('Error doing HTTP request to geocoder. HTTP response code: '.$response_code);
      
      $geocoded = explode(',', $response_body);
      
      $row['geocoding_status_code'] = $geocoded[0];
      $row['geocoding_accuracy'] = $geocoded[1];
      $row['latitude'] = $geocoded[2];
      $row['longitude']  = $geocoded[3];
      
      // handle different status codes from geocoder
      switch ($row['geocoding_status_code']) {
        case 200: // successful geocode
          $geocode_pending = false;
          break;
        case 620: // sent geocodes too fast
          $delay += 100000;
          break;
        default: // failure to geocode, of some sort. could do additional exception handling here. for now, treat it the same as any other.
          $geocode_pending = false;
          break;
      }
    }
    usleep($delay);
    
    // store the lat/long in db for later use
    $query = <<<SQL
      UPDATE locations 
      SET 
        latitude="{$row['latitude']}", 
        longitude="{$row['longitude']}", 
        geocoding_accuracy="{$row['geocoding_accuracy']}",
        geocoding_status_code="{$row['geocoding_status_code']}"
      WHERE id={$row['id']}
      LIMIT 1
SQL;
    if (!mysql_query($query)) {
      die("Unable to update database for location ID {$row['id']}. (".mysql_error().")");
    }
  }
  
  // ADD TO XML DOCUMENT NODE  
  $node = $dom->createElement("marker");  
  $newnode = $parnode->appendChild($node);   
  $newnode->setAttribute("name", $name);
  $newnode->setAttribute("address", $address);
  $newnode->setAttribute("address_1", $address_1);
  $newnode->setAttribute("address_2", $address_2);
  $newnode->setAttribute("phone", $row['phone']);
  $newnode->setAttribute("website", $row['website']);
  $newnode->setAttribute("lat", $row['latitude']);  
  $newnode->setAttribute("lng", $row['longitude']);  
  $newnode->setAttribute("type", $row['category_1']);
}

echo $dom->saveXML();


?>