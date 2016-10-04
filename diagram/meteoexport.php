<?php
header ("Content-Type:text/xml"); 

$export = $export;



$ln = "\r\n";
//$ln="<br>";

set_time_limit(600);

require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
$query = "select e.name as nam, e.target as tar, e.offset as off, e.interval as inter, m.* from __meteoexport as e, __meteoexportitem as m  where e.export=$export and m.export=$export";
if ($showQueries == 1) {
    echo $query . $ln;
}

$name = "";
$offset = 0;
$interval = 900;
$target = "";
$fields = array();


$ds1 = mysql_query($query, $verbindung) or die(mysql_error());

while ($row_ds1 = mysql_fetch_array($ds1)) {
    $field = array();
    $offset = $row_ds1[off];

    $interval = (int)$row_ds1[inter];
    $name = $row_ds1[nam];
    $target = $row_ds1[tar];

    $field[igate] = $row_ds1[igate];
    $field[sn] = $row_ds1[sn];
    $field[field] = $row_ds1[field];
    $field[name] = $row_ds1[name];
    $field[delta] = $row_ds1[delta];
    $field[type] = $row_ds1[type];
    $field[ts] = $row_ds1[ts];
    $field[values] = array();
    $fields[] = $field;
}

header('Content-Disposition: attachment; filename="'.$name.'.xml"');


$values = array();


foreach ($fields as $field) {
    $query = "select (ts+$offset) as ts, $field[field] from $field[igate]$field[type] where sn='$field[sn]' and ts>$field[ts] order by ts";
    if ($showQueries == 1) {
        echo $query . $ln;
    }
    $ts = 0;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

    $lastValue = null;
    $lastTs = 0;
    while ($row_ds1 = mysql_fetch_array($ds1)) {
 
        if ($lastTs == (int) floor($row_ds1[ts] / $interval)) {
            continue;
        } else {
            $lastTs = (int) floor($row_ds1[ts] / $interval);
        }

        $value = null;
        if ($lastValue == null) {
            $lastValue = $row_ds1[$field[field]];
        }

        if ($field[delta] == 1) {
            $value = $row_ds1[$field[field]] - $lastValue;
        } else {
            $value = $row_ds1[$field[field]];
        }

        $helperType = "";
        if ($field[type] = "_messdaten_s0") {
            $helpertype = "meter";
        } else if ($field[type] = "_messdaten_wr") {
            $helpertype = "inverter";
        } else {
            $helpertype = "meteo";
        }

        $values[(int) floor($row_ds1[ts] / $interval)][$helpertype]["" . $field[igate] . $field[sn]][$field[name]] = $value;
        $ts = max($ts, $row_ds1[ts]);

        $lastValue = $row_ds1[$field[field]];
    }

    $query = "update __meteoexportitem set ts = $ts where export=$export and igate = $field[igate] and sn = '$field[sn]' and field='$field[field]'";
    if ($showQueries == 1) {
        echo $query . $ln;
    }
    mysql_query($query, $verbindung) or die(mysql_error());
}


//////////////////77

echo '<?xml version="1.0"?>'.$ln;
echo '<root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.meteocontrol.de/extern/xml/xsd/data_definition_1_0.xsd">'.$ln;


$utcOffset = "" . $offset / 3600;
if ($offset > 0) {
    $utcOffset = "+" . $utcOffset;
}

echo '<system serial="' . $name . '" utcOffset="' . $utcOffset . '" interval = "'.$interval.'">'.$ln;
echo '  <md>'.$ln;

$meterIndex = 0;
$meteIndex = 0;

foreach ($values as $ts => $value) {

    $stamp = date("Y-m-d h:i", $ts * $interval);

    echo '    <dp timestamp="'.$stamp.'" interval="'.$interval.'">'.$ln;
    //echo ($ts*$interval).$ln;
    //key = type

    foreach ($value as $typekey => $value2) {
        
        foreach ($value2 as $device => $field) {
            
            if ($typekey == "inverter"){
                echo '      <'.$typekey.' serial="'.$device.'">'.$ln;
            }else {
                echo '      <'.$typekey.'>'.$ln;
            }
            
           // echo "..$device" . $ln;

            foreach ($field as $id => $value) {
                echo '        <mv type="'.$id.'">'.$value.'</mv>'.$ln;
                //echo "....$id=$value" . $ln;
            }
            echo "      </".$typekey.">".$ln;
        }
    }

    echo "    </dp>".$ln;
}

echo "  </md>".$ln;
echo "</system>".$ln;
echo "</root>".$ln;
?>
