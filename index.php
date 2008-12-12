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
    <link rel="stylesheet" type="text/css" href="style/style.css" />
    
    <!-- Google AJAX Libraries API -->
    <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo $api_key; ?>"></script>  
    <script type="text/javascript"> //<![CDATA[
    
      // load jQuery
      google.load("jquery", "1.2.6");
      // load Maps
      google.load("maps", "2");

      // this function gets called when the page has been loaded
      function load() {
        
        // initialize map
        if (google.maps.BrowserIsCompatible()) {
          var map = new google.maps.Map2(document.getElementById("map"));

          // set map center (required)
          map.setCenter(new google.maps.LatLng(40.76,-73.95), 12);

          // add standard map controls
          map.addControl(new google.maps.LargeMapControl());
          map.addControl(new google.maps.MapTypeControl());
          map.addControl(new google.maps.ScaleControl());
          map.addControl(new google.maps.OverviewMapControl());
        }

        // attach events to filter parameters
        $("#filters input").change(function() {
          // compose params, a string of GET variables to pass to the data-generating PHP
          params = '?';
          $("#filters input").each(function(){
            if ($(this).attr('checked')) {
              params += '&filter['+ $(this).val() +']=true';
            }
          });
          populateMap(map, params);
        });
        
        // trigger one change to get initial population
        $("#filters input:first").change();
      }
      
      // function to pull data from PHP/MySQL and add markers to the map
      function populateMap (map, params) {
        // start spinner
        $("#spinner").show();
        // clear the map and sidebar
        map.clearOverlays();
        $("#sidebar").empty();
        // pull xml from php/mysql
        google.maps.DownloadUrl("data.php" + params, function(data) {
          var xml = google.maps.Xml.parse(data);
          var markers = xml.documentElement.getElementsByTagName("marker");
          for (var i = 0; i < markers.length; i++) {
            var marker_data = markers[i];
            var point = new google.maps.LatLng(parseFloat(markers[i].getAttribute("lat")),
                                               parseFloat(markers[i].getAttribute("lng")));
            
            var marker = createMarker(point, marker_data);
            map.addOverlay(marker);

            var sidebar = createSidebarEntry(marker, marker_data);
            $("#sidebar").append(sidebar);
          }
          // done adding markers, stop spinner
          $("#spinner").hide();
        });
      }
      
      // function to create custom google maps markers and info boxes
      function createMarker(point, data) {
        var marker = new google.maps.Marker(point);
        var html = createShortDescription(
          data.getAttribute("name"), 
          data.getAttribute("address_1"),
          data.getAttribute("address_2"),
          data.getAttribute("phone"),
          data.getAttribute("website")
        );
        google.maps.Event.addListener(marker, 'click', function() {
          marker.openInfoWindowHtml(html);
        });
        return marker;
      }
      
      // function to create entries in the sidebar location listing
      function createSidebarEntry(marker, data) {
        var div = document.createElement('div');
        div.className = 'location';
        div.innerHTML = createShortDescription(
          data.getAttribute("name"), 
          data.getAttribute("address_1"),
          data.getAttribute("address_2"),
          data.getAttribute("phone"),
          data.getAttribute("website")
        );
        google.maps.Event.addDomListener(div, 'click', function() {
          google.maps.Event.trigger(marker, 'click');
        });
        google.maps.Event.addDomListener(div, 'mouseover', function() {
          div.style.backgroundColor = '#eee';
        });
        google.maps.Event.addDomListener(div, 'mouseout', function() {
          div.style.backgroundColor = '#fff';
        });
        return div;
      }
      
      // function to create a short description of a location (for both marker small infoboxes and sidebar entries)
      function createShortDescription (name, address_1, address_2, phone, website) {
        var html = '';
        html += '<h4>' + name + '</h4>'
        html += '<p>' + address_1 + '<br />' + address_2 + '</p>';
        html += '<p>' + phone + '<br />' + '<a href="http://' + website + '">' + website + '</a>' + '</p>';
        return html;
      }
      
      // function to create longer description of location for large marker infobox
      function createLongDescription (name, address, phone, website, details) {
        return '';
      }
      
      google.setOnLoadCallback(load);
      
      //]]>
    </script>
  </head>

  <body onunload="google.maps.Unload()">    
    <div id="doc2" class="yui-t2">

      <div id="hd">
        <div id="logo">
          <h1>MISM GIS Project - MIWatch.org</h1>          
        </div>
        
        <!-- In the future, this categories list should be generated dynamically from the database column 'category_1', but static is fine for now. -->
        <div id="filters">
          <h4>Show categories:</h4>
          <!-- Every INPUT field in this DIV will have its ID passed to data.php if it is checked. -->
          <p>
            <input type="checkbox" name="filter" value="mental_health" id="mental_health" checked="checked" />
            <label for="mental_health">Mental Health</label>
          </p>
          <p>            
            <input type="checkbox" name="filter" value="substance_abuse" id="substance_abuse" />
            <label for="substance_abuse">Substance Abuse</label>
          </p>
        </div>
      </div>

      <div id="bd"> 
        <div id="yui-main"> 
          <div class="yui-b"><div class="yui-g"> 
            <!-- Google Map will be placed in this DIV. -->
            <div id="map" style="width: 755px; height: 500px"></div>
          </div></div>
        </div>
        <div class="yui-b">
          <!-- This DIV is kept hidden until we have a need to show the loading message. -->
          <div id="spinner" style="display: none;">Loading&hellip;</div>
          
          <div id="sidebar">
            <!-- As sidebar elements are loaded from the database via AJAX, they are inserted into the HTML here. -->
          </div>
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