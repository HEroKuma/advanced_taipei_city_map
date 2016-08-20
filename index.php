
<!DOCTYPE html>
<?php
  if(@isset($_GET['traffic'])) {
      $str = file_get_contents('traffic.json');
      $json = json_decode($str, true); // decode the JSON into an associative array
      $json = $json['vd:ExchangeData']['vd:SectionDataSet']['vd:SectionData'];
  }

  $waterlogging = file_get_contents('waterlogging.json');
  $json_waterlogging = json_decode($waterlogging, true);
  $json_waterlogging = $json_waterlogging['features'];

  $mode = (@isset($_GET['mode'])) ? $_GET['mode'] : "driving" ;

  if($mode === "driving") {
    $str_accident = file_get_contents('car_accident.json');
    $json_accident = json_decode($str_accident, true);
  } else {
    $site = file_get_contents('site.json');
    $json_site = json_decode('[ { "Riverside_Park": "福和河濱公園", "Scenic_Spot": "公館水岸", "Latitude": "25.01117348", "Longitude": "121.5283483", "Description": "福和攀岩場位於福和橋附近，難易適中；福和橋下休憩區提供舒適怡人休息空間", "Photo": "" }, { "Riverside_Park": "華中河濱公園", "Scenic_Spot": "華中露營場", "Latitude": "25.01484182", "Longitude": "121.4965325", "Description": "帳棚露營區座落在綠草如茵的草地上", "Photo": "" }, { "Riverside_Park": "大佳河濱公園", "Scenic_Spot": "大佳水岸", "Latitude": "25.07393573", "Longitude": "121.5376443", "Description": "大佳河濱公園是2010臺北國際花博的展場之一", "Photo": "" }, { "Riverside_Park": "大稻埕河濱公園", "Scenic_Spot": "大稻埕水岸", "Latitude": "25.05690936", "Longitude": "121.507872", "Description": "大稻埕碼頭的夜景十分迷人", "Photo": "" }, { "Riverside_Park": "社子島", "Scenic_Spot": "社子島島頭", "Latitude": "25.11008205", "Longitude": "121.4661504", "Description": "島頭觀景平台可觀賞兩河交匯的壯闊景象", "Photo": "" }, { "Riverside_Park": "成美河濱公園", "Scenic_Spot": "彩虹橋", "Latitude": "25.0522625", "Longitude": "121.5766647", "Description": "彩虹橋全長167公尺，造型特殊，由橋中央的四條主要鋼纜吊索支撐，橋的結構為S型的曲線橋體，鮮紅色鋼肋拱型梁結構，為一座新穎的地標性公共設施。入夜後，彩虹橋上燈影投射，為台北的夜景增添不少美麗，成為不少攝影愛好者捕捉台北夜景的好題材。", "Photo": "" }, { "Riverside_Park": "百齡右岸河濱公園", "Scenic_Spot": "三腳渡擺渡口", "Latitude": "25.08091626", "Longitude": "121.5173273", "Description": "三腳渡因做為葫蘆堵、劍潭及大龍峒三地的對渡碼頭而得名", "Photo": "" }, { "Riverside_Park": "北投", "Scenic_Spot": "自行車越野場", "Latitude": "25.11311889", "Longitude": "121.4981243", "Description": "喜愛刺激越野的民眾，可到北投焚化爐堤外的越野場衝刺一番", "Photo": "" }, { "Riverside_Park": "關渡水岸公園", "Scenic_Spot": "關渡碼頭", "Latitude": "25.12082877", "Longitude": "121.4619177", "Description": "關渡碼頭木棧道造型雅緻", "Photo": "" }, { "Riverside_Park": "道南河濱公園", "Scenic_Spot": "道南水岸", "Latitude": "24.99100445", "Longitude": "121.5715525", "Description": "道南河濱公園是由環保署評定為優質空氣品質淨化區之環保公園。草地上點綴著起伏的驚喜，石雕、石凳錯落排放，父母親微笑的看著小朋友們在兒童遊戲區征服各式玩具恐龍及翹翹板。", "Photo": "" } ]', true);
  }
  
?>

<?php
    function twd97_to_latlng($x, $y) {
        $a = 6378137.0;
        $b = 6356752.314245;
        $lng0 = 121 * M_PI / 180;
        $k0 = 0.9999;
        $dx = 250000;
        
        $dy = 0;
        $e = pow((1 - pow($b, 2) / pow($a, 2)), 0.5);
        $x -= $dx;
        $y -= $dy;
        $M = $y / $k0;
        $mu = $M / ($a * (1.0 - pow($e, 2) / 4.0 - 3 * pow($e, 4) / 64.0 - 5 * pow($e, 6) / 256.0));
        $e1 = (1.0 - pow((1.0 - pow($e, 2)), 0.5)) / (1.0 + pow((1.0 - pow($e, 2)), 0.5));
        $J1 = (3 * $e1 / 2 - 27 * pow($e1, 3) / 32.0);
        $J2 = (21 * pow($e1, 2) / 16 - 55 * pow($e1, 4) / 32.0);
        $J3 = (151 * pow($e1, 3) / 96.0);
        $J4 = (1097 * pow($e1, 4) / 512.0);
        $fp = $mu + $J1 * sin(2 * $mu) + $J2 * sin(4 * $mu) + $J3 * sin(6 * $mu) + $J4 * sin(8 * $mu);
        $e2 = pow(($e * $a / $b), 2);
        $C1 = pow($e2 * cos($fp), 2);
        $T1 = pow(tan($fp), 2);
        $R1 = $a * (1 - pow($e, 2)) / pow((1 - pow($e, 2) * pow(sin($fp), 2)), (3.0 / 2.0));
        $N1 = $a / pow((1 - pow($e, 2) * pow(sin($fp), 2)), 0.5);
        
        $D = $x / ($N1 * $k0);
        $Q1 = $N1 * tan($fp) / $R1;
        $Q2 = (pow($D, 2) / 2.0);
        $Q3 = (5 + 3 * $T1 + 10 * $C1 - 4 * pow($C1, 2) - 9 * $e2) * pow($D, 4) / 24.0;
        $Q4 = (61 + 90 * $T1 + 298 * $C1 + 45 * pow($T1, 2) - 3 * pow($C1, 2) - 252 * $e2) * pow($D, 6) / 720.0;
        $lat = $fp - $Q1 * ($Q2 - $Q3 + $Q4);
        $Q5 = $D;
        $Q6 = (1 + 2 * $T1 + $C1) * pow($D, 3) / 6;
        $Q7 = (5 - 2 * $C1 + 28 * $T1 - 3 * pow($C1, 2) + 8 * $e2 + 24 * pow($T1, 2)) * pow($D, 5) / 120.0;
        $lng = $lng0 + ($Q5 - $Q6 + $Q7) / cos($fp);
        $lat = ($lat * 180) / M_PI;
        $lng = ($lng * 180) / M_PI;
        
        return array(
                'lat' => $lat,
                'lng' => $lng
            );
    }
?>

<html>
<head>
  <title>Advanced Taipei City Map &mdash; the easiest way to check Taipei City Maps</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=true"></script>
  <script type="text/javascript" src="gmaps.js"></script>
  <script type="text/javascript" src="prettify/prettify.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="js/dat.gui.min.js"></script>
  <script src="js/tether.min.js"></script>
  <script src="js/three.min.js"></script>
  <script src="js/GSVPano.js"></script>
  <script src="js/Hyperlapse.js"></script>
  <script src="js/sweet-alert.min.js"></script>
  <link rel="stylesheet" type="text/css" href="sweet-alert.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js" integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
  <link href='//fonts.googleapis.com/css?family=Convergence|Bitter|Droid+Sans|Ubuntu+Mono' rel='stylesheet' type='text/css' />
  <link href='styles.css' rel='stylesheet' type='text/css' />
  <link href='prettify/prettify.css' rel='stylesheet' type='text/css' />
  <link rel="stylesheet" type="text/css" href="examples.css" />
  <script type="text/javascript">

  function init() {

      var directions_service, directions_renderer;

      hyperlapse = new Hyperlapse(document.getElementById('pano'), {
                                lookat: map,
                                fov: 80,
                                zoom: 2,
                                use_lookat: false,
                                width: 610,
                                height: 300,
                                distance_between_points: 5,
                                elevation: 50,
                                millis: 100,
                                max_points: 100,
                                elevation: _elevation
                              });

      /* Dat GUI */
      var gui = new dat.GUI();
      var o = {
        distance_between_points:10, 
        max_points:100, 
        fov: 80, 
        elevation:Math.floor(_elevation), 
        tilt:0, 
        millis:200, 
        offset_x:0,
        offset_y:0,
        offset_z:0,
        position_x:0,
        position_y:0,
        use_lookat:true,
        screen_width: 610,
        screen_height: 300,
      };
      var scn = gui.addFolder('screen');
      scn.add(o, 'screen_width', 610).listen();
      scn.add(o, 'screen_height', 300).listen();
      var parameters = gui.addFolder('parameters');
      var distance_between_points_control = parameters.add(o, 'distance_between_points', 5, 100);
      distance_between_points_control.onChange(function(value) {
        hyperlapse.setDistanceBetweenPoint(value);
      });
      var max_points = parameters.add(o, 'max_points', 10, 300);
      max_points.onChange(function(value) {
        hyperlapse.setMaxPoints(value);
      });
      var fov_control = parameters.add(o, 'fov', 1, 180);
      fov_control.onChange(function(value) {
        hyperlapse.setFOV(value);
      });
      var pitch_control = parameters.add(o, 'elevation', -1000, 1000);
      pitch_control.onChange(function(value) {
        _elevation = value;
        hyperlapse.elevation_offset = value;
      });
      var millis_control = parameters.add(o, 'millis', 10, 250);
      millis_control.onChange(function(value) {
        hyperlapse.millis = value;
      });
      var offset_x_control = parameters.add(o, 'offset_x', -360, 360);
      offset_x_control.onChange(function(value) {
        hyperlapse.offset.x = value;
      });
      var offset_y_control = parameters.add(o, 'offset_y', -180, 180);
      offset_y_control.onChange(function(value) {
        hyperlapse.offset.y = value;
      });
      var offset_z_control = parameters.add(o, 'offset_z', -360, 360);
      offset_z_control.onChange(function(value) {
        hyperlapse.offset.z = value;
      });
      var position_x_control = parameters.add(o, 'position_x', -360, 360).listen();
      position_x_control.onChange(function(value) {
        hyperlapse.position.x = value;
      });
      var position_y_control = parameters.add(o, 'position_y', -180, 180).listen();
      position_y_control.onChange(function(value) {
        hyperlapse.position.y = value;
      });
      var tilt_control = parameters.add(o, 'tilt', -Math.PI, Math.PI);
      tilt_control.onChange(function(value) {
        hyperlapse.tilt = value;
      });
      var lookat_control = parameters.add(o, 'use_lookat')
      lookat_control.onChange(function(value) {
        hyperlapse.use_lookat = value;
      });
      parameters.open();
      
      var play_controls = gui.addFolder('play controls');
      play_controls.add(hyperlapse, 'play');
      play_controls.add(hyperlapse, 'pause');
      play_controls.add(hyperlapse, 'next');
      play_controls.add(hyperlapse, 'prev');
      play_controls.open();

      window.addEventListener('resize', function(){
        hyperlapse.setSize(610, 300);
        o.screen_width = 610;
        o.screen_height = 300;
      }, false);
    }

    var show_ui = false;
    document.addEventListener( 'keydown', onKeyDown, false );
    function onKeyDown ( event ) {
      switch( event.keyCode ) {
        case 72: /* H */
          show_ui = !show_ui;
          document.getElementsByClassName("dg ac")[0].style.opacity = (show_ui)?1:0;
          break;
        case 190: /* > */
          hyperlapse.next();
          break;
        case 188: /* < */
          hyperlapse.prev();
          break;
      }
    };

  </script>
  <script type="text/javascript">
    function check(element, index, array) {
      for(var i = 0; i < polys.length; i++) {
        if(polys[i].containsLatLng(element)) {
          console.log("detected");
          swal("Oops...", "您的路線包含易積水地區，請小心！", "error");
        } else {
          console.log("not detected");
        }
      }
    }
    function timeout() {
      setTimeout(function(){
        if(route.step_count < route.steps_length) {
          $('#steps').append('<li class="alert-success">'+route.steps[route.step_count].instructions+'</li>');
          $('#steps li div').wrap("<ul><li class='alert-danger'></li></ul>");
          map.setCenter(route.steps[route.step_count].end_location.lat(), route.steps[route.step_count].end_location.lng());
          route.steps[route.step_count].path.forEach(check);
          route.forward();
          timeout();
        } else {
          for(var i = 1; i < route.route.legs.length; i++) {
            for(var j = 0; j < route.route.legs[i].steps.length; j++) {
              $('#steps').append('<li class="alert-success">'+route.route.legs[i].steps[j].instructions+'</li>');
              $('#steps li div').wrap("<ul><li class='alert-danger'></li></ul>");
              //$('#steps li:eq('+j+')').delay(450*j).fadeIn(200, function() {
                map.setCenter(route.route.legs[i].steps[j].end_location.lat(), route.route.legs[i].steps[j].end_location.lng());
                route.route.legs[i].steps[j].path.forEach(check);
                map.drawPolyline({
                  path: route.route.legs[i].steps[j].path,
                  strokeColor: '#131540',
                  strokeOpacity: 0.6,
                  strokeWeight: 6
                });
              //});
            }
          }
          return;
        }
      }, 400);
    };

    function snapToRoad(point, callback) {
      var request = { origin: point, destination: point, travelMode: 'DRIVING' };
      map.getRoutes(request, function(e) {
        callback(e[0].overview_path[0]);
      });
    }

    function show(msg) {
      $("#status").html(msg);
    }
  </script>
  <script type="text/javascript">

    var start_point = new google.maps.LatLng(25.0170517, 121.5382344);
    var end_point = new google.maps.LatLng(25.0170517, 121.5382344);
    var lookat_point = new google.maps.LatLng(0, 0);
    var streetview_service;
    var start_pin, end_pin, pivot_pin, camera_pin;
    var _elevation = 0;
    var _route_markers = [];

    var trans = <?php
        $a = 303164.2827;
        $b = 2772867.926;
        echo json_encode(twd97_to_latlng($a, $b));
      ?>;
    var map;
    var polys = [];
    var hyperlapse;
    $(document).ready(function(){

      prettyPrint();

      map = new GMaps({
        div: '#map',
        lat: 25.0487623,
        lng: 121.537751
      });

      // events

      camera_pin = map.addMarker({
        position: lookat_point,
      });

      pivot_pin = map.addMarker({
        position: lookat_point,
        draggable: true,
      });

      google.maps.event.addListener (pivot_pin, 'dragend', function (event) {
        hyperlapse.setLookat( pivot_pin.getPosition() );
      });

      <?php if($mode === "driving") { ?>
        <?php foreach ($json_accident as $accident_point) { ?>
        <?php $out = twd97_to_latlng(floatval($accident_point['FIELD6']), floatval($accident_point['FIELD7'])); ?>
          /*map.addMarker({
            lat: <?php echo $out['lat'] ?>,
            lng: <?php echo $out['lng'] ?>,
            icon: 'rsz_1rsz_1marker.png'
          });*/
        <?php } ?>
      <?php } else{ ?>
        <?php foreach ($json_site as $object) { ?>
	  var icon = {
		url: "image/bike.gif",
		scaledSize: new google.maps.Size(30, 30),
		origin: new google.maps.Point(0, 0),
		archor: new google.maps.Point(0, 0)
	  };
          map.addMarker({
            lat: <?php echo floatval($object['Latitude']) ?>,
            lng: <?php echo floatval($object['Longitude']) ?>,
            icon: icon,
	    suppressInfoWindows: true,
            infoWindow: {
              content: <?php echo '"<p>' . $object['Riverside_Park'] . ':' . $object['Scenic_Spot'] . '</p>' . '</br>' . $object['Description'] . '"'; ?>
            },
            click: function(e) {
              var lat = e.position.lat();
              var lng = e.position.lng();
              if($(<?php echo '\'#exampleSelect2 option[value="[' . $object['Latitude'] . "," . $object['Longitude'] . ']\'' ?>).size()) {
                $(<?php echo '\'#exampleSelect2 option[value="[' . $object['Latitude'] . "," . $object['Longitude'] . ']\'' ?>).remove();
              } else {
                $('#exampleSelect2').append(<?php echo '"' . "<option value='[" . $object['Latitude'] . "," . $object['Longitude'] . "]'>" . $object['Scenic_Spot'] . "</option>" . '"'; ?>);
              }
            }
          });
        <?php } ?>
      <?php } ?>

      <?php foreach ($json_waterlogging as $object) {
        $poly_collection = array();
        $ele = $object['geometry']['coordinates'][0];
        foreach ($ele as $object2) {
          array_push($poly_collection, array_reverse($object2));
        } ?>
        polys.push(map.drawPolygon({
          paths: <?php echo json_encode($poly_collection) ?>,
          fillColor: '#00B3FF',
          fillOpacity: 0.6,
          strokeColor: '#00B3FF',
          strokeOpacity: 1,
          clickable: true,
        }));
        <?php unset($poly_collection); ?>
      <?php } ?>

      <?php if (@isset($_POST['origin'])) { ?>
        var latlng_origin, lating_destination;
        setTimeout(function() {
          GMaps.geocode({
            address: <?php echo "'" . $_POST['origin'] . "'" ?>,
            callback: function(results, status) {
              if (status == 'OK') {
                latlng_origin = results[0].geometry.location;
                map.setCenter(latlng_origin.lat(), latlng_origin.lng());
                start_pin = map.addMarker({
                  lat: latlng_origin.lat(),
                  lng: latlng_origin.lng(),
                  draggable: true
                });
                google.maps.event.addListener (start_pin, 'dragend', function (event) {
                  snapToRoad(start_pin.getPosition(), function(result) {
                    start_pin.setPosition(result);
                    start_point = result;
                  });
                });
              }
              setTimeout(function() {
                GMaps.geocode({
                  address: <?php echo "'" . $_POST['destination'] . "'" ?>,
                  callback: function(results, status) {
                    if (status == 'OK') {
                      lating_destination = results[0].geometry.location;
                      map.setCenter(lating_destination.lat(), lating_destination.lng());
                      end_pin = map.addMarker({
                        lat: lating_destination.lat(),
                        lng: lating_destination.lng(),
                        draggable: true
                      });
                      google.maps.event.addListener (end_pin, 'dragend', function (event) {
                        snapToRoad(end_pin.getPosition(), function(result) {
                          end_pin.setPosition(result);
                          end_point = result;
                        });
                      });
                        setTimeout(function() {
                          map.getRoutes({
                            origin: [latlng_origin.lat(), latlng_origin.lng()],
                            destination: [lating_destination.lat(), lating_destination.lng()],
                            travelMode: 'driving',
                            provideRouteAlternatives: true,
                            <?php echo ($mode === "driving") ? "" : "optimizeWaypoints: true," . "\n" ; ?>
                            <?php
                              if(@isset($_POST['waystops'])) {
                                $waypts = array();
                                foreach ($_POST['waystops'] as $value) {
                                  $tmp = json_decode($value, true);
                                  $tmp2 = array("lat" => $tmp[0], "lng" => $tmp[1]);
                                  array_push($waypts, array(
                                    "location" => $tmp2,
                                    "stopover" => true
                                  ));
                                }
                              }
                            ?>
                            <?php echo ($mode === "driving") ? "" : "waypoints: " . json_encode($waypts) . "," . "\n" ; ?>
                            callback: function(e){
                              route = new GMaps.Route({
                                map: map,
                                route: e[0],
                                strokeColor: '#131540',
                                strokeOpacity: 0.6,
                                strokeWeight: 6
                              });
                              init();
                              document.getElementsByClassName("dg ac")[0].style.opacity = 0;
                              hyperlapse.onError = function(e) {
                                console.log(e);
                              };
                              hyperlapse.onRouteProgress = function(e) {
                                _route_markers.push( map.addMarker({
                                  position: e.point.location,
                                  draggable: false,
                                  icon: "dot_marker.png",
                                  })
                                );
                              };
                              hyperlapse.onRouteComplete = function(e) {
                                hyperlapse.load();
                              };
                              hyperlapse.onLoadComplete = function(e) {
                                hyperlapse.play();
                              };
                              hyperlapse.onFrame = function(e) {
                                show( "" +
                                  "Start: " + start_pin.getPosition().toString() + 
                                  "<br>End: " + end_pin.getPosition().toString() + 
                                  "<br>Lookat: " + pivot_pin.getPosition().toString() + 
                                  "<br>Position: "+ (e.position+1) +" of "+ hyperlapse.length() );
                                camera_pin.setPosition(e.point.location);
                              };
                              hyperlapse.generate( {route: e} );
                              timeout();
                            }
                          });
                        }, 100);
                        
                    }
                  }
                });
              }, 500);
            }
          });
        }, 100);

      <?php } ?>

      $('#start_travel').click(function(e){
        e.preventDefault();
        $('#pano').css("display", "block")
        document.getElementsByClassName("dg ac")[0].style.opacity = 1;
      });
      <?php
        if(@isset($_GET['traffic'])) {
            $i = 1;
            $top = array();
            foreach ($json as $object) {
              $ele1 = array(floatval($object['vd:StartWgsY']), floatval($object['vd:StartWgsX']));
              $ele2 = array(floatval($object['vd:EndWgsY']), floatval($object['vd:EndWgsX']));
              $speed = floatval($object['vd:AvgSpd']);
              if ($speed >= 20 && $speed < 40) {
                $color = '#FF0000';
              } else if ($speed >= 40 && $speed < 60) {
                $color = '#BC00FF';
              } else if ($speed >= 60 && $speed < 80) {
                $color = '#5E00FF';
              } else if ($speed >= 80 && $speed < 100) {
                $color = '#0091FF';
              } else {
                $color = '#00FF2B';
              }
              array_push($top, $ele1, $ele2);
   ?>   
      setTimeout(function(){
        map.drawRoute({
        origin: <?php echo json_encode($ele1) ?>,
        destination: <?php echo json_encode($ele2) ?>,
        travelMode: 'driving',
        strokeColor: <?php echo "'" . $color . "'" ?>,
        strokeOpacity: 0.6,
        strokeWeight: 6
       })
      }, <?php echo $i*100; $i++; ?> ); 
      <?php      
              unset($top); unset($ele1); unset($ele2);
              $top = array();
          }
        }
      ?>
        /*map.travelRoute({
          origin: [25.0170517, 121.5382344],
          destination: [25.032933, 121.562591],
          travelMode: 'driving',
          step: function(e){
            $('#instructions').append('<li>'+e.instructions+'</li>');
            $('#instructions li:eq('+e.step_number+')').delay(450*e.step_number).fadeIn(200, function(){
              map.setCenter(e.end_location.lat(), e.end_location.lng());
              map.drawPolyline({
                path: e.path,
                strokeColor: '#131540',
                strokeOpacity: 0.6,
                strokeWeight: 6
              });
            });
          }
        });
        map.getRoutes({
          origin: [25.0170517, 121.5382344],
          destination: [25.032933, 121.562591],
          callback: function (e) {
            var time = 0;
            var distance = 0;
            for (var i=0; i<e[0].legs.length; i++) {
              time += e[0].legs[i].duration.value;
              distance += e[0].legs[i].distance.value;
            }
            alert("cost time: " + time + "s " + "total distance :" + distance + "m");
          }
        });*/
      //});
    });
  </script>
</head>
<body>
  <div id="header">
    <h1>
      <a href="../">Advanced Taipei City Map</a>
    </h1>
    <h2>&mdash; the easiest way to check Taipei City Maps</h2>
  </div>
  <div id="body">
    <h3>Routes</h3>
    <a href="/?mode" class="btn btn-success">景點模式</a>&nbsp;&nbsp;&nbsp;<a href="/?traffic" class="btn btn-success">觀看車流量</a>
    <div class="row">
      <div class="span11">
        <div class="popin">
          <div id="map"></div>
          <div style="display: none;" id="pano"></div>
          <div id="controls" style=""></div>
        </div>
        <div class="row">
          <a href="#" id="start_travel" class="btn btn-warning">帶我走！！</a>
          <ol id="steps">
          </ol>
        </div>
      </div>
      <div class="span6">
        <form method="post">
          <fieldset class="form-group">
            <label>起點</label>
            <input type="text" class="form-control" name="origin" placeholder="請輸入起點">
          </fieldset>
          <fieldset class="form-group">
            <label>終點</label>
            <input type="text" class="form-control" name="destination" placeholder="請輸入終點">
          </fieldset>
          <fieldset class="form-group">
            <label>狀態</label>
            <div class="alert-info" style="height: 100px;" id="status" placeholder="狀態"></div>
          </fieldset>
          <fieldset class="form-group">
            <label for="exampleSelect2">景點（可多選）</label>
            <select multiple="multiple" class="form-control" id="exampleSelect2" name="waystops[]">
            </select>
          </fieldset>
          <!-- <div class="radio">
            <label>
              <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
              Option one is this and that&mdash;be sure to include why it's great
            </label>
          </div>
          <div class="radio">
            <label>
              <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
              Option two can be something else and selecting it will deselect option one
            </label>
          </div>
          <div class="radio disabled">
            <label>
              <input type="radio" name="optionsRadios" id="optionsRadios3" value="option3" disabled>
              Option three is disabled
            </label>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox"> Check me out
            </label>
          </div> -->
          <button type="submit" class="btn btn-primary">規劃路線</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
