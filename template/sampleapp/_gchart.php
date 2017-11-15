<?php
function load_gjs(){
  echo '  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
  define("GCHARTJS_LOADED",true);
}
function count_item($table,$label){
  $ret = array();
  foreach($table as $n=>$r){
    if (isset($ret[$r[$label]])){
      $ret[$r[$label]]++;
    } else {
      $ret[$r[$label]] = 1;
    }
  }
  return $ret;
}
function replace_key($orig, $labels, $prefix="", $postfix=""){
  $ret = array();
  foreach($orig as $k=>$v){
    $ret[$labels[$prefix.$k]] = $v;
  }
  return $ret;
}
function pie($items=[], $title="No Title", $width=660, $height=300){
  if (!defined("GCHARTJS_LOADED")){
    load_gjs();
  }
  $rand = rand(10000,50000);
  echo '<script type="text/javascript"> ';
  echo "google.charts.load('current', {'packages':['corechart']}); ";
  echo "google.charts.setOnLoadCallback(drawChart{$rand}); ";
  echo "function drawChart{$rand}() {
    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Topping');
    data.addColumn('number', 'Slices');
    data.addRows([ ";
  foreach($items as $t=>$c){
    echo "['{$t}', {$c}], ";
  }
echo "   ]);
    // Set chart options
    var options = {'title':'{$title}',
                   'width':{$width},
                   'height':{$height}};
    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('chart_div{$rand}'));
    chart.draw(data, options);
  }
  </script>";
  echo "<div id=\"chart_div{$rand}\" style=\"margin: 5px; border: 2px solid #ddd;\"></div>";
}
