<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/php/init.php"); // do site init stuff ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>MISM GIS Project - MIWatch.org</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="title" content="MISM GIS Project - MIWatch.org" />
    <meta name="author" content="MISM GIS Project Team: Todd Eichel, Ryan Keane, Zhizhou Liu, Kevin Purtell" />
    
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $api_key; ?>" type="text/javascript"></script>
    
    <script type="text/javascript">

      //<![CDATA[

      function load() {
        if (GBrowserIsCompatible()) {
          var map = new GMap2(document.getElementById("map"));
          
          // set map center (required)
          map.setCenter(new GLatLng(40.71256,-74.00505), 12);
          
          // add standard map controls
          map.addControl(new GLargeMapControl());
          map.addControl(new GMapTypeControl());
          map.addControl(new GScaleControl());
          map.addControl(new GOverviewMapControl());
          
          // pull data from php/mysql
          GDownloadUrl("data.php", function(data) {
            var xml = GXml.parse(data);
            var markers = xml.documentElement.getElementsByTagName("marker");
            for (var i = 0; i < markers.length; i++) {
              var name = markers[i].getAttribute("name");
              var address = markers[i].getAttribute("address");
              var type = markers[i].getAttribute("type");
              var point = new GLatLng(parseFloat(markers[i].getAttribute("lat")),
                                      parseFloat(markers[i].getAttribute("lng")));
              var marker = createMarker(point, name, address, type);
              map.addOverlay(marker);
            }
          });
          
        }
      }
      
      function createMarker(point, name, address, type) {
        var marker = new GMarker(point);
        var html = "<b>" + name + "</b> <br/>" + address;
        GEvent.addListener(marker, 'click', function() {
          marker.openInfoWindowHtml(html);
        });
        return marker;
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