<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/php/init.php"); // do site init stuff ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>MISM GIS Project - MIWatch.org</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="title" content="MISM GIS Project - MIWatch.org" />
    <meta name="author" content="MISM GIS Project Team: Todd Eichel, Ryan Keane, Zhizhou Liu, Kevin Purtell" />
    
    <!-- Individual YUI CSS files --> 
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/yui/2.6.0/build/reset-fonts-grids/reset-fonts-grids.css" /> 
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/yui/2.6.0/build/base/base-min.css" />
    
    <!-- Our CSS file -->
    <link rel="stylesheet" href="style/style.css" type="text/css" />
    
    <!-- Google AJAX Libraries API -->
    <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo $api_key; ?>"></script>  
    <script type="text/javascript"> //<![CDATA[
    
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
        map.setCenter(new google.maps.LatLng(40.76,-73.95), 12);
        
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
    <div id="doc2" class="yui-t2">

      <div id="hd">
        <h1>MISM GIS Project - MIWatch.org</h1>
      </div>

      <div id="bd"> 
        <div id="yui-main"> 
          <div class="yui-b"><div class="yui-g"> 
            <div id="spinner" style="display: none;">
              <img src="/img/spinner.gif" alt="Loading" /> Loading...
            </div>

            <div id="map" style="width: 755px; height: 500px"></div>
          </div></div>
        </div>
        <div class="yui-b" style="overflow: auto; height: 500px">
          <p>Sidebar stuff...</p>
          
          <p>This sidebar will never grow longer than the map to the right.</p>
          
        </div> 
      </div>

      <div id="ft">
        <p>
          &copy; 2008 <a href="http://www.miwatch.org/">MIWatch.org</a> |
          All work performed by <a href="http://ism.cmu.edu/">Carnegie Mellon University MISM</a> students |
          Maps by <a href="http://code.google.com/apis/maps/">Google</a> | 
          YUI CSS by <a href="http://developer.yahoo.com/yui/">Yahoo! User Interface Library</a> |
          jQuery JavaScript by <a href="http://jquery.com">jQuery</a>
        </p>
      </div>
    </div>
  </body>
</html>