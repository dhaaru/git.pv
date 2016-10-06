<?php
//argdiagram5.php
require __DIR__ . '/../vendor/autoload.php';

use InfluxDB\Client;
use InfluxDB\Driver\Guzzle;
use InfluxDB\Point;


if (!isset($delta)) {
  $delta = 0;
}

set_time_limit(900);

require_once('../connections/queriesInflux.php');
require_once('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

$anyArgs = false;

date_default_timezone_set('UTC');

if( !isset($offset)){

  $offset = 19800;
}


if (!isset($resolution)) {
  $resolution = 2;
}

$stamp = $stamp;
$endstamp = $endstamp;
if (!isset($echoArgs)){
  $echoArgs = 0;
}
if (!isset($showQueries)){
  $showQueries = 0;
}
if (!isset($etotal)){
  $etotal="0";
}
if (!isset($infos)){
  $infos="";
}
if (!isset($speed)){
  $speed="0";
}
if (!isset($showAlarms)){
  $showAlarms=0;
}


if (is_null($args)) {
  return;
}

$argString = "";
if ($echoArgs == 1) {
  $argString = '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=' . $args . '&defaults=' . $defaults . '&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
  $count++;
}

if (is_null($defaults) || strlen($defaults) == 0) {
  $defaults = false;
} else {
  $defaults = split(",", $defaults);
}

$hideClear = $hideClear;
if (is_null($hideClear)) {
  $hideClear = 0;
}

$hideDelta = $hideDelta;
if (is_null($hideDelta)) {
  $hideDelta = 0;
}

$argBack = $args;
$args = split(";", $args);
$infos = split(";;", $infos);

$diffs = split(";", $diffs);
$sums = split(";", $sums);
$avgs = split(";", $avgs);
$etotal = split(";", $etotal);

$displayItems = array();

$databasename = getInfluxDBNameForArgs($args, $showQueries);
if ($databasename==="null"){
  if ($showQueries){
    echo "database null";
  }
  return;
}

if (sizeof($diffs)==1 && sizeof($diffs[0])==1){
}else {
  $currentValues = getDiffValues($diffs, $showQueries, $stamp, $endstamp, $verbindung, $databasename);
  foreach ($currentValues as $currentValue){
    $displayItems[$currentValue["position"]][] = $currentValue;
  }
}

if (sizeof($avgs)==1 && sizeof($avgs[0])==1){
}else {

  $currentValues = getAvgValues($avgs, $showQueries, $stamp, $endstamp, $verbindung, $databasename);
  foreach ($currentValues as $currentValue){
    $displayItems[$currentValue["position"]][] = $currentValue;
  }
}

if (sizeof($sums)==1 && sizeof($sums[0])==1){
}else {

  $currentValues = getSumValues($sums, $showQueries, $stamp, $endstamp, $verbindung, $databasename);
  foreach ($currentValues as $currentValue){
    $displayItems[$currentValue["position"]][] = $currentValue;
  }
}
ksort($displayItems);


$values = array();

$axis = array();
$devices = array();

$digitals = 0;


$tmpseries = getDiagramValues($args, &$values, $showQueries, &$axis, $stamp, $endstamp, $verbindung, $databasename);
//print_r($tmpseries);
//$tmpseries = $results->getSeries();

//print_r(json_encode($resultsss[0][0]['values']));






      
      


$series = array();


$id = 0;
for($dualDpAry=0;$dualDpAry<sizeof($args);$dualDpAry++){
foreach ($tmpseries[$dualDpAry] as $serie){

  $found=false;
  
  for ($i = 0; $i < sizeof($series); $i++){
    if ($series[$i]["tags"]["d"] == $serie["tags"]["d"]&&$series[$i]["tags"]["iid"] == $serie["tags"]["iid"] && $series[$i]["tags"]["f"] == $serie["tags"]["f"]){
      $found = true;
      foreach ( $serie["values"] as $value ){
        if ($value[1]!=""){
          $series[$i]["values"][]=$value;
        }
      }
    }
  }
  if (!$found){
    $tmp = array();
    $tmp["name"]=$serie["name"];
    $tmp["tags"]["d"] = $serie["tags"]["d"];
    $tmp["tags"]["iid"] = $serie["tags"]["iid"];
    $tmp["tags"]["f"] = $serie["columns"][1];
    $tmp["columns"][] = "time";
    $tmp["columns"][] = "mean";
    foreach ( $serie["values"] as $value ){
      if ($value[1]!=""){
        $tmp["values"][]=$value;
      }
    }
    $series[]=$tmp;

  }
}
}


$factors = getFactors($args, $verbindung, $showQueries);

for ($i1 = 0; $i1<sizeof($series); $i1++){
  $preOffset = 0;
  $factor=1;
  $postOffset = 0;
  $hit=false;
  foreach ($factors as $gatekey => $gate){
    foreach ($gate as $dkey => $device){
      foreach ($device as $fkey => $field){


        if ($series[$i1]["tags"]["iid"] == $gatekey && $series[$i1]["tags"]["d"]==$dkey &&$series[$i1]["tags"]["f"]==$fkey ){
          $preOffset = $field["preoffset"];
          $factor = $field["factor"];
          $postOffset = $field["postoffset"];
          $hit=true;
          break;
        }
        if ($hit){
          break;
        }

      }
      if ($hit){
        break;
      }
    }
  }

  for ($i2 = 0; $i2<sizeof($series[$i1]["values"]); $i2++){

    $series[$i1]["values"][$i2][0]+=$offset*1000;
    $series[$i1]["values"][$i2][1]=($series[$i1]["values"][$i2][1]+$preOffset)*$factor+$postOffset;
  }
}

$position = 0;
$nextPosition=0;

$index=0;

$results=array();



foreach($values as $value){
  $noHit=1;
  if ($value[0]["queryType"]==0){//single value

    //print_r($value);
    foreach($series as $serie){
      if($value[0]['deviceName'] == $serie['tags']['d'] && $value[0]["field"]==$serie['tags']['f'] &&$value[0]["igate"]==$serie['tags']['iid']){
        $noHit=0;
        $value[0]["values"]=json_encode($serie["values"]);
      }
    }
  }
  else{//multi value
     
    $firstone=True;
    foreach($value as $valuePart){
      foreach($series as $serie){
        if($valuePart['deviceName'] == $serie['tags']['d'] && $valuePart["field"]==$serie['tags']['f'] &&$value[0]["igate"]==$serie['tags']['iid']){
          $noHit=0;
          if ($firstone){
            $value[0]["values"]=$serie["values"];
            $firstone=false;
            break;
          }else{
            $valueIndex=0;
            foreach($serie["values"] as $point){
              $value[0]["values"][$valueIndex][1]+=$point[1];
              $valueIndex++;
            }
            break;
          }
        }
      }
    }
    if ($value[0]["queryType"]==2){//average
      for($i = 0; $i< sizeof($value[0]["values"]); $i++){
        $value[0]["values"][$i][1]/=sizeof($value);
      }
    }
    $value[0]["values"]=json_encode($value[0]["values"]);

  }
  if ($noHit){
    $value[0]["values"]=json_encode(array());
  }
  $results[]=$value[0];
  $index++;
}

if($showQueries==1){

print_r($results);

}


for ($i=0; $i<sizeof($results); $i++){

  if ($results[$i]["values"]==="null"){


    $results[$i]["values"]="[[".($stamp*1000).", 0], [".(($endstamp+20000)*1000).", 0]]";

  }
}


$deviceString = "(null ";
foreach ($devices as $key => $device) {

  $deviceString .=",$key";
}
$deviceString .= ")";

if ($showAlarms != 0) {
  getAlarms($deviceString, $devices, $showQueries, $stamp, $endstamp, $verbindung);
}

?>

<html>
<head>
  <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
  <link rel="stylesheet" href="style.css" type="text/css" />
  <!--[if lte IE 9]><script type="text/javascript" src="../functions/flot/excanvas.js"></script><![endif]-->
  <script type="text/javascript" src="../functions/flot/jquery.js"></script>
  <script type="text/javascript" src="jscolor/jscolor.js"></script>
  <script type="text/javascript"
  src="../functions/flot/jquery.flot.js"></script>
  <script type="text/javascript"
  src="../functions/flot/jquery.flot.time.js"></script>
  <script type="text/javascript"
  src="../functions/flot/jquery.flot.selection.js"></script>
</head>
<body>

  <div style="height: 98%; width: 100%">
    <div style="float: left; height: 99%; width: 84%; padding-top: 2px">
      <form name=ThisForm method="post" action="" target="_self">

        <div>
          <div style="font-family:Verdana, Geneva, sans-serif; font-size:13px; color:darkblue; font-weight:bold; padding-bottom:2px">
            <?php
            echo $title;
            ?>
          </div>
        </div>
        <div>
          <div id="placeholder" style="font-size: 95%; width: 99%; height: 98%"></div>
        </div>
      </form>
    </div>
    <div style="float: left; height: 99%; width: 16%; text-align: center">
      <div
      style="background-color: BlanchedAlmond; font-size: 85%; width: 99%; height: 60%; overflow: auto;"
      id="legend"> <!-- displayheight-->

    </div>

    <div style="height: 43%; background-color: beige; width: 99%; overflow: auto;"
    id="buttons">
    <?php

    foreach ($displayItems as $myitem) {
      foreach ($myitem as $myelement) {
        echo '<p style = "color:' . $myelement["color"] . ';font-family:' . $myelement["font"] . ';font-size:' . $myelement["fontSize"] . 'px">' . $myelement["text"] . '<br>' . number_format($myelement["value"], $myelement["decimals"], '.', '') . $myelement["unit"] . '</p>';
      }
    }
    ?>
    <input title="Reset the zoom"    style="flow: left;" id="resetZoom" onClick="resetZoom()" type="image"    src="../imgs/lupe_grey.png" disabled>

    <?php

    $phpArg = "?stamp=" . $stamp . "&endstamp=" . $endstamp;
    $deltaNew = 0;
    if ($delta == 0) {
      $deltaNew = "&delta=1";
    } else {
      $deltaNew = "&delta=0";
    }
    $endString = "&yearO=$yearO&monO=$monO&dayO=$dayO";
    $startString = "&yearI=$yearI&monI=$monI&dayI=$dayI";

    $phaseWord = "&showQueries=$showQueries&showAll=$showAll" . $endString . $startString;


    ///export access permission
    $user_name = $_SESSION['user'];

    if(isUserExport($user_name, $verbindung)){
      if (sizeof($values)>0) {
        if ($hideClear == 0) {
          echo '<a href="../' . $sourcefile . $phpArg . $phaseWord . '&igate=' . $igate . '" target="_parent">';
          echo '<img title="Reset Diagram" src="clear.png">';
          echo '</a>';
        }

        echo '<a href="export2.php' . $phpArg . '&args=' . $argBack . $phaseWord . '&delta=' . $delta . '" target="_parent">';
        echo '<img title="Export selection as Excel file" src="xls.png">';
        echo '</a>';

        if ($phase == "tag" && $park_no=="10") {
          echo '<a href="patPr.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&print=true&park_no=' . $park_no . '">';
          echo '    <img title="Export Performance Ratio as .csv file" src="../imgs/xls_pr.png">';
          echo '</a>';
        }
      }
    } //export access permission end

    if ($hideDelta == 0) {
      if ($delta == 1) {
        echo '<a href="../' . $sourcefile . $phpArg . '&args=' . $argBack . $phaseWord . $deltaNew . '&igate=' . $igate . '" target="_parent">';
        echo '<img title="Toggle absolute values and d/dt" src="ddt1.png">';
        echo '</a>';
      } else {

        echo '<a href="../' . $sourcefile . $phpArg . '&args=' . $argBack . $phaseWord . $deltaNew . '&igate=' . $igate . '" target="_parent">';
        echo '<img title="Toggle absolute values and d/dt" src="ddt0.png">';
        echo '</a>';
      }
    }
    if ($echoArgs == 1) {
      ?>
      <input width ="99%" type="text" name="unit" class="textfeld"
      value='$diagrammCode .= <?php echo $argString; ?>'>
      <?php
    }
    ?>
  </div>


</div>
<script type="text/javascript">

var zeigeKurve = {};
var alarmTexts = new Array();
<?php
echo "var start = ($stamp+19800)*1000;\n";
echo "var min = start;\n";
echo "var end = ($endstamp+16200)*1000;\n";
echo "var max = end;\n";

if ($showAlarms != 0) {
  foreach ($devices as $device) {
    foreach ($device["values"] as $ts => $data) {
      echo "alarmTexts[$ts]='".$data["txt"]."';\n";
    }
  }
}
?>


var miny=null;
var maxy=null;
var resolution = <?= $resolution ?>;

$(function () {
  $("#frame").resize();

  plotWithOptions();

});

function toggleKurve(index){
  zeigeKurve[index] = !zeigeKurve[index];
  plotWithOptions();
}

function resetZoom(){
  min = start;
  max = end;
  miny=null;
  maxy=null;
  document.getElementById("resetZoom").disabled=true;
  document.getElementById("resetZoom").src="../imgs/lupe_grey.png";
  plotWithOptions();
}

function showTooltip(x, y, contents, ts) {
  var content = contents;
  if (contents.indexOf("undefined") == 0){
    content = alarmTexts[ts];

  }
  $('<div id="tooltip">' + content + '</div>').css( {
    position: 'absolute',
    display: 'none',
    top: y + 5,
    left: x + 5,
    border: '1px solid #fdd',
    padding: '2px',
    'background-color': '#fee',
    opacity: 0.80
  }).appendTo("body").fadeIn(200);
}

var previousPoint = null;
$("#placeholder").bind("plothover", function (event, pos, item) {

  $("#x").text(pos.x);
  $("#y").text(pos.y);

  if (item) {
    if (previousPoint != item.dataIndex) {
      previousPoint = item.dataIndex;

      $("#tooltip").remove();
      var x = new Date(item.datapoint[0]);
      var  y = item.datapoint[1].toFixed(resolution);

      var xmin = (x.getUTCMinutes());
      if (xmin<10){
        xmin="0"+xmin;
      }
      var xh = x.getUTCHours();
      if (xh<10){
        xh="0"+xh;
      }

      var label = item.series.label;
      var unit = "";
      if (item.series.label.indexOf("DIGITAL")!=-1){
        if (y%2==0){
          y="LOW";
        }else {
          y="HIGH";
        }
      }else {
        label = item.series.label.substring(0, item.series.label.indexOf("("));
        unit = " "+item.series.label.substring(item.series.label.indexOf("(")+1, item.series.label.indexOf(")"));
      }


      showTooltip(item.pageX, item.pageY,
        label +" ( "+xh+":"+xmin + " ) = " + y+unit, item.datapoint[0]);
      }
    }
    else {
      $("#tooltip").remove();
      previousPoint = null;
    }
  });

  $("#placeholder").bind("plotselected", function(event, ranges)
  {
    document.getElementById("resetZoom").src="../imgs/lupe.png";
    document.getElementById("resetZoom").disabled=false;
    min = ranges.xaxis.from;
    max = ranges.xaxis.to;

    <?php
    echo "max = Math.max(max, min+3600000*2);";
    ?>
    plotWithOptions();
  }
);

<?php
$index = 0;

foreach ($results as $devkey => $device) {
  if ($defaults == false) {
    echo "zeigeKurve['$device[graphTitle]']=true;\n";
  } else {
    if (in_array($devkey, $defaults)) {
      echo "zeigeKurve['$device[graphTitle]']=true;\n";
    } else {
      echo "zeigeKurve['$device[graphTitle]']=false;\n";
    }
  }

  $index++;
}


?>

function plotWithOptions(){

  var options =
  {
    xaxis:
    {
      mode: "time" ,
      min: min,
      max: max
    }
    ,grid: { hoverable: true, autoHighlight: true }
    ,lines: {show: true}
    ,points: {show: true}
    ,yaxes: [
      <?php
      $first = true;
      foreach ($axis as $ax) {
        if ($first) {
          $first = false;
        } else {
          echo ",";
        }
        $ax2 = str_replace("DEG", "&deg;", $ax);
        $ax2 = str_replace("%B0", "&deg;", $ax2);


        $ax2 = str_replace("SQUA", "&sup2;", $ax2);
        if ($ax2 != "DIGITAL" && $delta == 1) {
          $ax2 = "&#916;(" . $ax2 . ")/h";
        }
        if ($ax2 == "DIGITAL") {
          echo "{ticks: [";

            for ($index = 0; $index < $digitals; $index++) {
              if ($index > 0) {
                echo ",";
              }
              echo "[" . ($index * 2 + 0.15) . ", 'LOW'], [" . ($index * 2 + 0.85) . ", 'HIGH']";
            }
            echo "]}";
          } else {
            echo "{tickFormatter: function(v, axis){return v.toFixed(axis.tickDecimals) +' $ax2'}}";
          }
        }
        ?>
      ]
      ,selection: { mode: "x"}

      ,legend: {container: $("#legend"),
      labelFormatter: function (label, series) {
        var cutLabel = label.substr(0, label.indexOf(" ("));

        var zeige = ""
        if (zeigeKurve[cutLabel]){
          zeige = 'checked="checked"';

        }

        var cb = '<input type="checkbox" name="' + cutLabel + '" '+zeige+' id="id' + cutLabel + '" onClick="toggleKurve(\''+cutLabel+'\');"> ' + label+'</input>';
        return cb;
      }
    }
  };


  $.plot($("#placeholder"), [



      <?php


    $index=0;
    foreach ($results as $value){




      if ($index){
        echo ",";
      }
      echo "{ lines: {show: zeigeKurve['" .$value["graphTitle"]."']}, points: {show: false}, yaxis: ".(array_search($value["unit"], $axis)+1)." , color: ".$value["color"].", label: '".$value["graphTitle"]." (".$value["unit"].")', data: ".$value["values"]." }";
      $index++;
    }
    ?>



   ],options);

}

window.onresize = plotWithOptions;
</script>







</div>
</body>
</html>
