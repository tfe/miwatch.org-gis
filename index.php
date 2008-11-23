<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>MISM GIS Project - MIWatch.org</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="title" content="MISM GIS Project - MIWatch.org" />
    <meta name="author" content="MISM GIS Project Team: Todd Eichel, Ryan Keane, Zhizhou Liu, Kevin Purtell" />
    
    <!-- Google Maps JS, with localhost API key -->
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAKt8DF9unss4amjuxq0LAChT2yXp_ZAY8_ufC3CFXhHIE1NvwkxRhR4J4-LIRRFwnvUdI7sxRM958_A"
          type="text/javascript"></script>
    
    <!-- Google Maps JS, with toddeichel.com API key -->
    <!--<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAKt8DF9unss4amjuxq0LAChROd-J2Mwx5suvTDodChn7ion8O_xTpHTKoI5tqPFoASaIpFO9A-VTHxw"
          type="text/javascript"></script> -->
    <script type="text/javascript">

      //<![CDATA[

      function load() {
        if (GBrowserIsCompatible()) {
          var map = new GMap2(document.getElementById("map"));
          map.setCenter(new GLatLng(37.4419, -122.1419), 10);
        }
      }

      //]]>
    </script>
  </head>

  <body onload="load()" onunload="GUnload()">
    <h1>MISM GIS Project - MIWatch.org</h1>
    
    <div id="map" style="width: 800px; height: 500px"></div>
    
    <div id="footer">
      <p>&copy; 2008 MIWatch.org</p>
    </div>
  </body>
</html>