<?php
/**
 * Site-wide Initialization Stuff
 * 
 * Contains site-wide initialization procedures and helper functions for MIWatch.org GIS.
 * 
 * Author: Todd Eichel (todd@toddeichel.com), 1 December 2008
 */
 
// pull in database configuration
require_once($_SERVER['DOCUMENT_ROOT'] . "/php/dbconf.inc");

// try to pull in PEAR HTTP, provide useful error if not present
if (!include_once('HTTP/Request.php')) {
  die('PEAR::HTTP/Request module not found. To install, see: http://pear.php.net/manual/en/package.http.http-request.php');
}

// figure out which API key to use
if ($_SERVER['SERVER_NAME'] == 'localhost') {
  $api_key = "ABQIAAAAKt8DF9unss4amjuxq0LAChT2yXp_ZAY8_ufC3CFXhHIE1NvwkxRhR4J4-LIRRFwnvUdI7sxRM958_A";
} else {
  $api_key = "ABQIAAAAKt8DF9unss4amjuxq0LAChROd-J2Mwx5suvTDodChn7ion8O_xTpHTKoI5tqPFoASaIpFO9A-VTHxw";
}

///////////////////////////// HELPER FUNCTIONS /////////////////////////////

// easy way to debug variables
function debug($var) {
  echo "<pre>";
  if (is_array($var)) {
    print_r($var);
  } else {
    var_dump($var);    
  }
  echo "</pre>";
}

// Converts $title to Title Case, and returns the result.
// http://www.sitepoint.com/blogs/2005/03/15/title-case-in-php/
function strtotitle($title) {
  $title = strtolower(str_replace('_', ' ', $title));
  // Our array of 'small words' which shouldn't be capitalised if
  // they aren't the first word. Add your own words to taste.
  $smallwordsarray = array( 'of','a','the','and','an','or','nor','but','is','if','then','else','when', 'at','from','by','on','off','for','in','out','over','to','into','with' );
  // Split the string into separate words
  $words = explode(' ', $title);
  foreach ($words as $key => $word) {
    // If this word is the first, or it's not one of our small words, capitalise it
    // with ucwords().
    if ($key == 0 or !in_array($word, $smallwordsarray)) $words[$key] = ucwords($word);
  } // Join the words back into a string
  $newtitle = implode(' ', $words);
  return $newtitle;
}

// Sanitizes variables for use in SQL queries.
// http://us3.php.net/manual/en/function.mysql-real-escape-string.php#82110
function sanitize($var) {
  if (is_array($var)) {   //run each array item through this function (by reference)        
    foreach ($var as &$val) {
      $val = sanitize($val);
    }
  }
  else if (is_string($var)) { //clean strings
    $var = mysql_real_escape_string($var);
  }
  else if (is_null($var)) {   //convert null variables to SQL NULL
    $var = "NULL";
  }
  else if (is_bool($var)) {   //convert boolean variables to binary boolean
    $var = ($var) ? 1 : 0;
  }
  return $var;
}


?>
