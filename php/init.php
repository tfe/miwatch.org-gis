<?php
/**
 * Site-wide Initialization Stuff
 * 
 * Contains site-wide initialization procedures for MIWatch.org GIS.
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
  print_r($var);
  echo "</pre>";
}

?>
