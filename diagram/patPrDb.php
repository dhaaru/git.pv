<?php

## ###################
# influx version
# ######################

require_once ('../connections/queriesMysql2.php');
require_once ('../connections/queriesInflux2.php');
require_once ('../connections/influxResolver.php');

$now = mktime();
$pr_value = null;
$info = getCalcprInfo($park_no, $showQueries);

########################################################
## check $info for missing fields. If missing, return instantly!
########################################################
$missingInfoKey = array();
if (!array_key_exists('park_no',$info)    or !$info['park_no'])    $missingInfoKey[] = "park_no";
if (!array_key_exists('resolution',$info) or !$info['resolution']) $missingInfoKey[] = "time resolution";
if (!array_key_exists('park',$info)       or !$info['park'])       $info['park'] = "undefined park name";
if (!array_key_exists('offset',$info)     or $info['offset']==null)$missingInfoKey[] = "timezone-offset";
if (!array_key_exists('capacity',$info)   or !$info['capacity'])   $missingInfoKey[] = "park capacity";
if (!array_key_exists('prfield',$info)    or !$info['prfield'])    $missingInfoKey[] = "prfield";
if (!array_key_exists('minIrr',$info)     or $info['minIrr']==null)$missingInfoKey[] = "minIrr";
if (!array_key_exists('calcFactor',$info) or !$info['calcFactor']) $missingInfoKey[] = "calcFactor";
if (!array_key_exists('meters',$info)     or !$info['meters'])     $missingInfoKey[] = "meters";
if (!array_key_exists('sensors',$info)    or !$info['sensors'])    $missingInfoKey[] = "sensors";
if (count($missingInfoKey)){
  print("<p>Plant info is incomplete. These Fields are missing for park_no:".
    $info['park_no'] . ": " .
    implode(",",$missingInfoKey).
    "<br> check mysql tables <b>_calcpr</b>".
    "</p>");
  return;
}
$missingInfoKey = array();
foreach($info['meters'] as $meter){
  if (!array_key_exists('offset',$meter) or $meter['offset']==null) $missingInfoKey[] = "_field offset";
  if (!array_key_exists('factor',$meter) or !$meter['factor']) $missingInfoKey[] = "_field factor";
  if (!array_key_exists('device',$meter) or !$meter['device']) $missingInfoKey[] = "device id";
  if (!array_key_exists('field',$meter)  or !$meter['field'])  $missingInfoKey[] = "field name";
  if (!array_key_exists('iid',$meter)    or !$meter['iid'])    $missingInfoKey[] = "igate id";
  if (!array_key_exists('d',$meter)      or !$meter['d'])      $missingInfoKey[] = "device sn";
}
if (count($missingInfoKey)){
  print("<p>Plant info is incomplete. These Meter Fields are missing for park ".
    $info['park_no'] . " " .$info['park'] . ":".
    implode(",",$missingInfoKey).
    "<br> check mysql tables <b>_calcpr_meter, _device</b>".
    "</p>");
  return;
}
$missingInfoKey = array();
foreach($info['sensors'] as $sensor){
  if (!array_key_exists('offset',$sensor) or $sensor['offset']==null) $missingInfoKey[] = "_field offset";
  if (!array_key_exists('factor',$sensor) or !$sensor['factor']) $missingInfoKey[] = "_field factor";
  if (!array_key_exists('device',$sensor) or !$sensor['device']) $missingInfoKey[] = "device id";
  if (!array_key_exists('field',$sensor)  or !$sensor['field'])  $missingInfoKey[] = "field name";
  if (!array_key_exists('iid',$sensor)    or !$sensor['iid'])    $missingInfoKey[] = "igate id";
  if (!array_key_exists('d',$sensor)      or !$sensor['d'])      $missingInfoKey[] = "device sn";
}
if (count($missingInfoKey)){
  print("<p>Plant info is incomplete. These Sensor Fields are missing for park ".
    $info['park_no'] . " " .$info['park'] . ":".
    implode(",",$missingInfoKey) .
    "<br> check mysql tables <b>_calcpr_sensor, _device</b>".
    "</p>");
  return;
}
print("<p>OK Plant info for park ".
    $info['park_no'] . " " .$info['park']. " is complete.</p>");
##############################################
# get influxDBName for each park $info
#         assume all igates and devices per park 
#         are assigned to the same Influx DB
##############################################
$info['influxDBName'] = getInfluxDBName($info['sensors'][0]['iid']);

if (!$info['influxDBName']){
  print("<p>InfluxDBName not found for" .
    " igate:" . $info['sensors'][0]['iid'] .
    ", park no:" . $info['park_no'] .
    ", park name:" . $info['park'] .
    "<br> include this park in '/connections/influxResolver' or successor.".
    "</p>");
  return; ### important ###
}

##############################################
#      $info =  Array ( 
#                   [park_no] => 50 
#                   [resolution] => 300 
#                   [park] => Neemuch 
#                   [offset] => 19800 
#                   [capacity] => 121 
#                   [prfield] => neemuch-pr 
#                   [minIrr] => 0 
#                   [calcFactor] => 1 
#                   [meters] => Array ( 
#                     [0] => Array ( 
#                       [offset] => 0 
#                       [factor] => 1 
#                       [device] => 1939 
#                       [field] => EM_Accord_Act_Energy_Exp 
#                       [iid] => 3094 
#                       [d] => ACC_16 
#                     ) 
#                     [1] => Array ( 
#                       [offset] => 0 
#                       [factor] => 1 
#                       [device] => 1946 
#                       [field] => EM_Accord_Act_Energy_Exp 
#                       [iid] => 3094 
#                       [d] => ACC_14 
#                     ) 
#                   ) 
#                   [sensors] => Array ( 
#                     [0] => Array ( 
#                       [offset] => 0 
#                       [factor] => 1 
#                       [device] => 7126 
#                       [field] => Solar_Radiation 
#                       [iid] => 3096 
#                       [d] => WS_01 
#                     ) 
#                   ) 
#                   [influxDBName] => testdb 
#                )
##############################################
if ($showQueries){
  echo '<h2>patPr started.  $info:</h2><p>';
  print_r($info);
  echo "</p>";
}

if ($speed != 0) {
  echo "<br>postCalcPr: " . (mktime() - $now) . "<br>";
}

$ln="<br>";
if (false && $showQueries) {
  $ln="\r\n";
  header('Content-Disposition: attachment; filename="' . $info["park"] . '-PR-' . $jahr . '-' . $mon . '-' . $tag . '.xls"');
}

mysql_select_db($database_verbindung, $verbindung);

date_default_timezone_set('UTC');
$stamp = mktime(0, 0, 0, $mon, $tag, $jahr);
$endstamp = mktime(14, 0, 0, $mon, $tag, $jahr);
set_time_limit(600);

if ($showQueries) {
  echo "Timestamp\tMeter Energy total\tAverage Irradiation\r\n";
  echo "DD/MM/YYYY HH:MM\tkWh\t15 Min avg\r\n";
}
$time = 0;
$irr = 0;
$deltaE = 0;

$metervalues = array();
$weather = array();
$curr = 0;

if ($speed != 0) {
  echo "<br>preMeters: " . (mktime() - $now) . "<br>";
}
if ($showQueries){
  echo "<hr>";
}

#############################################################
# energy  meters (E-Total)
#############################################################
foreach ($info["meters"] as $meter){

  $queryInflux = "SELECT MAX(value) as max " .
    "FROM v " .
    "WHERE iid='" . $meter['iid'] . "'" .
    "AND d='" . $meter['d'] . "'" .
    "AND f='" . $meter['field'] . "'" .
    "AND time<".$stamp."s AND time>=" . ($stamp-24*3600*3) . "s " . 
    ";";
  if ($showQueries) {
    echo "<p>query Influx meters (1):$queryInflux ; ln:$ln </p>";
  }
  $series = influx_query_execute($queryInflux, $info['influxDBName'], $showQueries);

  ############ $series  example  %%%%%%%%%%%%%%%%%%%%%%%%%%%%
  #            Array (
  #                [0] => Array (
  #                        [name] => v
  #                        [columns] => Array (
  #                                [0] => time
  #                                [1] => max
  #                            )
  #                        [values] => Array (
  #                                [0] => Array (
  #                                        [0] => 1452688509
  #                                        [1] => 10912.95
  #                                    )
  #                            )
  #                    )
  #            )
  foreach ($series[0]['values'] as $tsval){
    $curr += $tsval[1];
  }

  $queryInflux = "SELECT MEAN(value) as value " .
    "FROM v " .
    "WHERE value>0 " . 
    "AND iid='" . $meter['iid'] . "' " .
    "AND d='" . $meter['d'] . "' " .
    "AND f='" . $meter['field'] . "' " .
    "AND time<" . $endstamp."s AND time>=" . $stamp."s " . 
    "GROUP BY time(".$info['resolution']."s) " .
    ";";
  if ($showQueries) {
    echo "<p>query Influx meters (2):$queryInflux ; ln:$ln </p>";
  }
  $series = influx_query_execute($queryInflux, $info['influxDBName'], $showQueries);
  foreach ($series[0]['values'] as &$tsval){
    # apply timezone offset teh second time!
    $tsval[0] += $info['offset'];
    # apply _field  offset, factor
    $tsval[1] = ($tsval[1] + $meter['offset']) * $meter['factor'];
  }
  
  foreach ($series[0]['values'] as $tsval){
    $metervalues[$tsval[0]][$meter['device']] = $tsval[1] ;
  }
}
if ($speed != 0) {
  echo "<br>preSensors: " . (mktime() - $now) . "<br>";
}

#############################################################
# irradiation sensors 
#############################################################

$tmp = "iid = -1";
$wheres = array();
foreach ($info["sensors"] as $sensor){
  
  $queryInflux = "SELECT MEAN(value) as value " .
    "FROM v " .
    "WHERE " . 
    "  iid='" . $sensor['iid'] . "' " .
    "AND d='" . $sensor['d'] . "' " .
    "AND f='" . $sensor['field'] . "' " .
    "AND time<" . $endstamp."s AND time>=" . $stamp."s " . 
    "GROUP BY time(".$info['resolution']."s) " .
    ";";
  if ($showQueries) {
    echo "<p>query Influx sensors:$queryInflux ; ln:$ln </p>";
  }
  $series = influx_query_execute($queryInflux, $info['influxDBName'], $showQueries);

  foreach ($series[0]['values'] as &$tsval){
    # apply timezone offset teh second time!
    $tsval[0] += $info['offset'];
    # apply _field  offset, factor
    $tsval[1] = ($tsval[1] + $sensor['offset']) * $sensor['factor'];
    $weather[$tsval[0]][$sensor['device']] = $tsval[1];
  }

}

if ($speed != 0) {
  echo "<br>postSensors: " . (mktime() - $now) . "<br>";
}

//get sum of all meters
foreach ($metervalues as $key => $value){
  $metervalues[$key] = array_sum($value);
}

//get average of all sensors per timestamp
foreach ($weather as $key => $value){
  $weather[$key] = array_sum($weather[$key]) / count($weather[$key]);
}

ksort($weather);
ksort($metervalues);

if ($speed != 0) {
  echo "<br>postSort: " . (mktime() - $now) . "<br>";
}

# Influx query "SELECT ... GROUP BY TIME" returns series with 
# timestamps 
# in pre-defined interval and
# guaranteed complete!

$break = true;
foreach ($metervalues as $ts => $energy ){
  $weatherval = 0;
  $energy = round($energy);
  $weatherval = round($weather[$ts], 2);

  if ($energy == 0) {
    $energy = $curr;
  }

  if ($showQueries) {
    echo date('d/m/Y H:i', $stamp + $info['offset']) . "\t" . $energy . "\t" . $weatherval;
  }

  if ($weatherval != 0 && $energy!=0 && $info["minIrr"] == 0|| $curr < $energy && $weatherval >= $info["minIrr"]) {
    if ($break) {
      $break = false;
      if ($showQueries) {
        echo "\t+";
      }
    } else {

      $time++;
      $irr+=$weatherval;
      $deltaE+= $energy - $curr;
      if ($showQueries) {
        echo "\t*";
      }
    }
  }
  else {
    $break = true;
  }

  $curr = max($energy, $curr);
  $stamp+=$info['resolution'];
}

if ($speed != 0) {
  echo "<br>postStampLoop: " . (mktime() - $now) . "<br>";
}

$reference = 1000;
if ($showQueries) {
  echo $ln.$ln."KWh \t" . $deltaE . "\r\n";
  echo "Yf=\t" . $deltaE / $info['capacity'] . "\r\n";
  echo "avg irr=\t" . round($irr / $time, 2);
  echo $ln."hours= \t" . ($time / (3600 / $info['resolution']));
  echo $ln."avg insolation= \t" . ($irr / (3600 / $info['resolution']) / $reference);
  echo $ln."pr= \t" . round((100 * (($deltaE / $info['capacity']) / ($irr / (3600 / $info['resolution']) / $reference))), 2) . "%";

  echo "{{{{{{{{";
  echo "deltaE: ".$deltaE;
  echo "capacity: ".$info['capacity'];
  echo "irradiation: ".$irr;
  echo "resolution: ".$info['resolution'];
  echo "reference: ".$reference;
echo "calcFactor: ".$info["calcFactor"];
echo "}}}}}}}";


}

$energy = $deltaE / $info['capacity'];
$irradiation = ($irr / (3600 / $info['resolution']) / $reference);
$pr_value = round(100 * ( $energy / ($irradiation*$info["calcFactor"])), 2);

if ($speed != 0) {
  echo "done: " . (mktime() - $now) . "<br>";
}


?>
