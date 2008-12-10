<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/php/init.php"); // do site init stuff ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>MISM GIS Project - MIWatch.org</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="title" content="MISM GIS Project - MIWatch.org" />
    <meta name="author" content="MISM GIS Project Team: Todd Eichel, Ryan Keane, Zhizhou Liu, Kevin Purtell" />
    
    <!-- Google AJAX Libraries API -->
    <script  type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo $api_key; ?>"></script>  
    <script> //<![CDATA[
    
      // load jQuery
      google.load("jquery", "1.2.6");
      // load Maps
      google.load("maps", "2");

      // this function gets called when the page has been loaded
      function load() {
        
        if (google.maps.BrowserIsCompatible()) {
          loadMap();
        }
        
      }
      
      // function to initialize the map, pull data, and add the markers
      function loadMap () {
        var map = new google.maps.Map2(document.getElementById("map"));
        
        // set map center (required)
        map.setCenter(new google.maps.LatLng(40.71256,-74.00505), 12);
        
        // add standard map controls
        map.addControl(new google.maps.LargeMapControl());
        map.addControl(new google.maps.MapTypeControl());
        map.addControl(new google.maps.ScaleControl());
        map.addControl(new google.maps.OverviewMapControl());
        
        // start spinner and pull data from php/mysql
        $("#spinner").show();
        google.maps.DownloadUrl("data.php", function(data) {
          var xml = google.maps.Xml.parse(data);
          var markers = xml.documentElement.getElementsByTagName("marker");
          for (var i = 0; i < markers.length; i++) {
            var name = markers[i].getAttribute("name");
            var address = markers[i].getAttribute("address");
            var type = markers[i].getAttribute("type");
            var point = new google.maps.LatLng(parseFloat(markers[i].getAttribute("lat")),
                                    parseFloat(markers[i].getAttribute("lng")));
            var marker = createMarker(point, name, address, type);
            map.addOverlay(marker);
          }
          // done adding markers, stop spinner
          $("#spinner").hide();
        });
      }
      
      // function to create custom google maps markers and info boxes
      function createMarker(point, name, address, type) {
        var marker = new google.maps.Marker(point);
        var html = "<b>" + name + "</b> <br/>" + address;
        google.maps.Event.addListener(marker, 'click', function() {
          marker.openInfoWindowHtml(html);
        });
        return marker;
      }
      
      google.setOnLoadCallback(load);
      
      //]]>
    </script>
  </head>

  <body>
    <h1>MISM GIS Project - MIWatch.org</h1>

    <div id="spinner" style="display: none; position: absolute; top: 0; right: 0; color: black; background: #FFFE9B; padding: 0 1em;">
      <p><img src="/img/spinner.gif"> Loading...</p>
    </div>
    
    <div id="map" style="width: 800px; height: 500px"></div>
    
    <div id="footer">
      <p>&copy; 2008 MIWatch.org</p>
    </div>
  </body>
</html>