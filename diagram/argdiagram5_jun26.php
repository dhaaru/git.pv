<?php
if (!isset($delta)) {
    $delta = 0;
}

$sourcetable = " _devicedatavalue ";
if ($compressed) {
    $sourcetable = " _devicedatacompressedvalue ";
}

set_time_limit(900);

require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

$anyArgs = false;
$now = mktime();

date_default_timezone_set('UTC');

if (!isset($stamp)) {
    $endstamp = mktime(18, 30, 0);
    $stamp = $endstamp - 24 * 3600;
}

if (!isset($resolution)) {
    $resolution = 2;
}

$stamp = $stamp;
$endstamp = $endstamp;



if (is_null($args)) {
    return;
}

$argString = "";
if ($echoArgs == 1) {
    $argString = '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=' . $args . '&defaults=' . $defaults . '&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
    $count++;
}

$defaultNames = array();
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

foreach ($diffs as $avg) {
    $avgItems = split(",", $avg);
    if (sizeof($avgItems) != 14) {

        continue;
    }
    $currentValue = array();
    $erg = 0;
    $query = "select max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts < $stamp and ts > ($stamp-24*3600)";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $erg -= $row_ds2[value];
    }

    mysql_free_result($ds2);

    $query = "select max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < ($endstamp)";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $erg += $row_ds2[value];
    }


    $values = 0;
    $avgValue = 0;


    $currentValue[value] = $erg;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];

    $displayItems[$avgItems[6]][] = $currentValue;

    mysql_free_result($ds2);
}


foreach ($avgs as $avg) {
    $avgItems = split(",", $avg);
    if (sizeof($avgItems) != 14) {
        continue;
    }
    $currentValue = array();
    $query = "select floor(ts) as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < $endstamp";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $currentValue[$row_ds2[ts]][] = $row_ds2[value];
    }

    $values = 0;
    $avgValue = 0;
    foreach ($currentValue as $value) {
        $avgValue+= array_sum($value) / sizeof($value);
        $values++;
    }

    $avgValue/=$values;

    $currentValue[value] = $avgValue;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];

    $displayItems[$avgItems[6]][] = $currentValue;

    mysql_free_result($ds2);
}
/*Energy Yield Calculation start did by saradha */ 
foreach ($etotal as $avg) {
     $avgItems = split(",", $avg);
    if (sizeof($avgItems) == 14) {
        $avgItems[] = 0;
    }
    if (sizeof($avgItems) != 15) {

        continue;
    }
    $currentValue = array();
    $arrv2=explode("_",$avgItems[3]);
    $currentValue = array();
	if(count($arrv2)>1)
	{
		$devid=implode(",",$arrv2);
	    $query = "select ROUND(sum(value),2) as value FROM _devicedatacompressedvalue3 where ts >= $stamp and ts < $endstamp and device in (".$devid.") and (field='".$avgItems[4]."') group by ts";
    }
    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $currentValue[$row_ds2[ts]][] = $row_ds2[value];
    }
    $values = 0;
    $avgValue = 0;
    foreach ($currentValue as $value) {
       $avgValue = array_sum($value);
       // $values++;
	  
    }
    //$avgValue/=$values;
    $currentValue[value] = $avgValue;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];

    $displayItems[$avgItems[6]][] = $currentValue;

    mysql_free_result($ds2);
}

/*Energy Yield Calculation end did by saradha */ 


//sums=;0,13,0 ,451,U3, null,6,Total%20Irradiation,yellow,2,W/m²
//&avgs=-22.68,1.15,0 ,451,U4, null,5,Avg.%20Temperature,red,2,°C;&sums=0,13,0,451,U3,null

foreach ($sums as $sum) {
    $avgItems = split(",", $sum);
    if (sizeof($avgItems) == 14) {
        $avgItems[] = 0;
    }
    if (sizeof($avgItems) != 15) {

        continue;
    }
    $currentValue = array();
    $arrv2=explode("_",$avgItems[3]);
	if(count($arrv2)>1)
	{
		$devid=implode(",",$arrv2);
	    $query = "select floor(ts/3600) as ts,sum((((value+$avgItems[0])*$avgItems[1])+$avgItems[2]))as value from _devicedatavalue where device IN (".$devid.") and field='$avgItems[4]' and ts = (select max(ts) from _devicedatavalue where device IN (".$devid.") and field='$avgItems[4]'and ts > $stamp and ts < $endstamp)";
	}
	elseif(count($arrv2) == 0)
	{
		$query = "select floor(ts/3600) as ts,max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as value from _devicedatavalue where device IN (".$devid.") and field='$avgItems[4]' and ts = (select max(ts) from _devicedatavalue where device IN (".$devid.") and field='$avgItems[4]'and ts > $stamp and ts < $endstamp)";
	}
	else
	{
		$query = "select floor(ts/3600) as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < $endstamp";
	}
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }

    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $currentValue[$row_ds2[ts]][] = $row_ds2[value];
    }

    $avgValue = 0;
    foreach ($currentValue as $value) {
	
        if ($avgItems[14] == 1) {
            $avgValue+= array_sum($value);
        } else {
		   
           $avgValue+= array_sum($value) / sizeof($value);
        }
    }

    mysql_free_result($ds2);
    if ($avgItems[14] == 1) {
        $avgValue = $avgValue / 4;
    }
    $currentValue[value] = $avgValue;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];

    $displayItems[$avgItems[6]][] = $currentValue;
}

ksort($displayItems);


$values = array();
$axis = array();
$devices = array();

$digitals = 0;


$argIndex = 0;
foreach ($args as $arg) {
    $words = split(',', $arg);
    if (sizeof($words) != 9) {
        continue;
    }

    $words[5] = str_replace("PLUS", "+", $words[5]);

    $anyArgs = true;
    if (!array_key_exists($words[3], $devices)) {
        $devices[$words[3]][name] = $words[5];

        if (is_numeric($words[8])) {
            $devices[$words[3]][color] = $words[8];
        } else {
            $devices[$words[3]][color] = "'" . $words[8] . "'";
        }

        $devices[$words[3]][values] = array();
    }
    if (!in_array($words[7], $axis)) {
        $axis[] = $words[7];
    }

    $translatedField = $words[4];
    $translatedField = str_replace("PLUS", "+", $translatedField);
    $translatedField = str_replace("RAUTE", "#", $translatedField);
	
	$arrvl=explode("_",$words[3]);
	if(count($arrvl)>1)
	{
		$ids=implode(",",$arrvl);
    $query = "select ts+19800 as ts, sum((((value+$words[0])*$words[1])+$words[2])) as value from $sourcetable where value is not null and device IN (".$ids.") and field = '$translatedField' and ts > $stamp and ts < $endstamp group by ts";
	}
	else
	{
		$query = "select ts+19800 as ts, (((value+$words[0])*$words[1])+$words[2]) as value from $sourcetable where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
	}
    if ($showQueries == 1) {
        echo $query . "<br>";
    } else {
        $showQueries = 0;
    }

    $ax = array_search($words[7], $axis);
    if ($words[7] == "DIGITAL") {
        $digitals++;
    }

    if ($defaults && in_array($argIndex, $defaults)) {
        $defaultNames[] = $words[5];
    }
    $values[$words[5]][axis] = $ax;
    $values[$words[5]][color] = $words[8];

    $lastTs = 0;
    $lastValuechangeTs = 0;
    $lastValue = 0;
    $first = true;

    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        if ($first) {
            $first = false;
            $lastTs = 0;
            $lastValue = $row_ds2[value];
        } else if (!$compressed && ($row_ds2[ts] - $lastTs) > (60 * ($words[6] + 1))) {
            $values[$words[5]][data][$lastTs + 1][value] = "null";
        }
        if ($words[7] == "DIGITAL") {
            $values[$words[5]][data][$row_ds2[ts]][value] = $row_ds2[value] + (2 * ($digitals - 1));
        }
        if ($delta == 0) {
            $values[$words[5]][data][$row_ds2[ts]][value] = $row_ds2[value];
        } elseif ($row_ds2[value] != null && $row_ds2[value] != $lastValue) {
            if ($delta != 0) {
                $values[$words[5]][data][$row_ds2[ts]][value] = (3600 * ($row_ds2[value] - $lastValue)) / ($row_ds2[ts] - $lastValuechangeTs);
            }
            $lastValuechangeTs = $row_ds2[ts];
            $lastValue = $row_ds2[value];
        }
        $lastTs = $row_ds2[ts];
    }
    mysql_free_result($ds2);
    $argIndex++;
}

if ($speed != 0) {
    echo "data: " . (mktime() - $now) . "<br>";
}


$deviceString = "(null ";
$firstDevice = false;
foreach ($devices as $key => $device) {
    if ($firstDevice) {
        $firstDevice = false;
        $deviceString.=$key;
    } else {
        $deviceString .=",$key";
    }
}
$deviceString .= ")";

if ($showAlarms != 0) {
    $query = "select igate, sn, deviceid from _device where deviceid in $deviceString";
    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $sn = $row_ds2[sn];
        $sn = substr($sn, 3);
        $query = "select * from alarm where igate_id = $row_ds2[igate] and seriennummer='$sn' and tstamp < $endstamp and tstamp > $stamp";
        if ($showQueries == 1) {
            echo $query . "<br>";
        }
        $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
        $first = true;
        $devices[$row_ds2[deviceid]][displaydata] = "[";
        while ($row_ds1 = mysql_fetch_array($ds1)) {
            if ($first == true) {
                $first = false;
                $devices[$row_ds2[deviceid]][displaydata].="[($row_ds1[tstamp]+19800)*1000, 0]";
            } else {
                $devices[$row_ds2[deviceid]][displaydata].=", [($row_ds1[tstamp]+19800)*1000, 0]";
            }
            //$devices[$row_ds2[deviceid]][values][($row_ds1[tstamp] + 19800) * 1000][nr] = $row_ds1[fehler_nr];
            $devices[$row_ds2[deviceid]][values][($row_ds1[tstamp] + 19800) * 1000][txt] = $row_ds1[fehler_txt];
        }
        $devices[$row_ds2[deviceid]][displaydata].="]";
        mysql_free_result($ds1);
    }
    mysql_free_result($ds2);
}

if ($speed != 0) {
    echo "alarms: " . (mktime() - $now) . "<br>";
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
        <link rel="stylesheet" href="style.css" type="text/css" />
        <!--[if lte IE 9]><script type="text/javascript" src="../functions/flot/excanvas.js"></script><![endif]-->
        <script type="text/javascript" src="../functions/flot/jquery-1.7.min.js"></script>
        <script type="text/javascript" src="jscolor/jscolor.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.selection.min.js"></script>
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
                                 echo '<p style = "color:' . $myelement[color] . ';font-family:' . $myelement[font] . ';font-size:' . $myelement[size] . 'px">' . $myelement[text] . '<br>' . number_format($myelement[value], $myelement[decimals], '.', '') . $myelement[unit] . '</p>';
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

                    if ($exception != 25) {
                        if ($anyArgs) {
                            if ($hideClear == 0) {
                                echo '<a href="../' . $sourcefile . $phpArg . $phaseWord . '&igate=' . $igate . '" target="_parent">';
                                echo '<img title="Reset Diagram" src="clear.png">';
                                echo '</a>';
                            }
							if ($phase == "tag" && $park_no=="36") {       
								$type="energy";		
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase='.$type.'&args=0,1,0,7795,Activepower_Total,Active Power,5,kW,4;0,1,0,7794,Solar_Radiation,Irradiation,5,W/m²,\'Gold\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
                    		}
							else if($phase == "weather" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,7794,Solar_Radiation,Irradiation,5,W/m²,\'Gold\';0,1,0,7794,Module_Temperature,Module Temperature,5,°C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,7786,IDC1,String%20Current%201.1,5,A,15;0,1,0,7786,IDC2,String%20Current%201.2,5,A,1;0,1,0,7786,IDC3,String%20Current%201.3,5,A,2;0,1,0,7786,IDC4,String%20Current%201.4,5,A,3;0,1,0,7786,IDC5,String%20Current%202.1,5,A,4;0,1,0,7786,IDC6,String%20Current%202.2,5,A,5;0,1,0,7786,IDC7,String%20Current%202.3,5,A,6;0,1,0,7786,IDC8,String%20Current%202.4,5,A,7;0,1,0,7786,IDC9,String%20Current%203.1,5,A,8;0,1,0,7786,IDC10,String%20Current%203.2,5,A,9;0,1,0,7786,IDC11,String%20Current%203.3,5,A,10;0,1,0,7786,IDC12,String%20Current%203.4,5,A,11;0,1,0,7786,UDC,DC%20Voltage,5,V,8">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "graph1" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase . '&args=0,1,0,7792,AC_Power,AC%20Power-%201,15,kW,4;0,1,0,7791,AC_Power,AC%20Power-%202,15,kW,5;0,1,0,7794,AC_Power,AC%20Power-%203,15,kW,6;0,1,0,7794,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "graph2" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,7792,DC_Voltage,DC%20Voltage-%201,15,V,8;0,1,0,7791,DC_Voltage,DC%20Voltage-%202,15,V,9;0,1,0,7794,DC_Voltage,DC%20Voltage-%203,15,V,6;0,1,0,7792,DC_Current,DC%20Current-%201,15,A,7;0,1,0,7791,DC_Current,DC%20Current-%202,12,A,15;0,1,0,7794,DC_Current,DC%20Current-%203,15,A,13">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "graph3" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,7792,AC_Voltage,AC%20Voltage-%201,15,V,8;0,1,0,7791,AC_Voltage,AC%20Voltage-%202,15,V,9;0,1,0,7794,AC_Voltage,AC%20Voltage-%203,15,V,6;0,1,0,7792,AC_Frequency1,Frequency-%201,15,,7;0,1,0,7791,AC_Frequency1,Frequency-%202,12,,15;0,1,0,7794,AC_Frequency1,Frequency-%203,15,,13">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "system" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args='.$argBack.'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else {
								echo '<a href="export2.php' . $phpArg . '&args=' . $argBack . $phaseWord . '&delta=' . $delta . '" target="_parent">';
								echo '<img title="Export selection as Excel file" src="xls.png">';
								echo '</a>';
							}
							
                                                
		                    if ($phase == "tag" && $park_no=="50") {                     
                        	echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&args=0,1,0,3324,Solar_Radiation,Irradiation,1440,Wh/mSQUA,1;0,1,0,1939,EM_Accord_Act_Energy_Exp,Line%20Feeder1,1440,MWh,1;0,1,0,1946,EM_Accord_Act_Energy_Exp,Line%20Feeder2,1440,MWh,1;0,1,0,1947,EM_Accord_Act_Energy_Exp,132-KV%20Trafo1,1440,MWh,1;0,1,0,1944,EM_Accord_Act_Energy_Exp,132-KV%20Trafo2,1440,MWh,1;0,1,0,1955,EM_Accord_Act_Energy_Exp,132-KV%20Trafo3,1440,MWh,1;0,1,0,1945,EM_Accord_Act_Energy_Exp,Block%20A,1440,MWh,1;0,1,0,1953,EM_Accord_Act_Energy_Exp,Block%20B,1440,MWh,1;0,1,0,1951,EM_Accord_Act_Energy_Exp,Block%20C,1440,MWh,1;0,1,0,1948,EM_Accord_Act_Energy_Exp,Block%20D,1440,MWh,1;0,1,0,1943,EM_Accord_Act_Energy_Exp,Block%20E,1440,MWh,1;0,1,0,1954,EM_Accord_Act_Energy_Exp,Block%20F,1440,MWh,1;0,1,0,1941,EM_Accord_Act_Energy_Exp,Block%20G,1440,MWh,1;0,1,0,1942,EM_Accord_Act_Energy_Exp,Spare,1440,MWh,1;0,1,0,1940,EM_Accord_Act_Energy_Exp,33-KV%20Trafo1,1440,MWh,1;0,1,0,1950,EM_Accord_Act_Energy_Exp,33-KV%20Trafo2,1440,MWh,1;0,1,0,1949,EM_Accord_Act_Energy_Exp,33-KV%20Trafo3,1440,MWh,1;0,1,0,4954,E_Total_Export,Block%204,15,MW,1;0,1,0,3992,E_Total_Export,Block%205,15,MW,1;0,1,0,4717,E_Total_Export,Block%207,15,MW,1;0,1,0,4733,E_Total_Export,Block%208,15,MW,1;0,1,0,4655,E_Total_Export,Block%2010,15,MW,1;0,1,0,4201,E_Total_Export,Block%2011,15,MW,1;0,1,0,4575,E_Total_Export,Block%2015,15,MW,1;0,1,0,4168,E_Total_Export,Block%2021,15,MW,1;0,1,0,4964,E_Total_Export,Block%2022,15,MW,1;0,1,0,4608,E_Total_Export,Block%2023,15,MW,1;0,1,0,2807,E_Total_Export,Block%2012,15,MW,1;0,1,0,4040,E_Total_Export,Block%2013,15,MW,1;0,1,0,2774,E_Total_Export,Block%2014,15,MW,1;0,1,0,4144,E_Total_Export,Block%2019,15,MW,1;0,1,0,4119,E_Total_Export,Block%2054,15,MW,1;0,1,0,4227,E_Total_Export,Block%2020,15,MW,1;0,1,0,3392,E_Total_Export,Block%201,15,MW,1;0,1,0,3330,E_Total_Export,Block%202,15,MW,1;0,1,0,3286,E_Total_Export,Block%203,15,MW,1;0,1,0,2627,E_Total_Export,Block%206,15,MW,1;0,1,0,4811,E_Total_Export,Block%209,15,MW,1;0,1,0,4899,E_Total_Export,Block%2017,15,MW,1;0,1,0,2291,E_Total_Export,Block%2018,15,MW,1;0,1,0,2066,E_Total_Export,Block%2053,15,MW,1;0,1,0,3218,E_Total_Export,Block%2026,15,MW,1;0,1,0,1971,E_Total_Export,Block%2027,15,MW,1;0,1,0,2049,E_Total_Export,Block%2028,15,MW,1;0,1,0,2244,E_Total_Export,Block%2024,15,MW,1;0,1,0,2672,E_Total_Export,Block%2025,15,MW,1;0,1,0,3421,E_Total_Export,Block%2016,15,MW,1;0,1,0,3133,E_Total_Export,Block%2030,15,MW,1;0,1,0,3790,E_Total_Export,Block%2031,15,MW,1;0,1,0,2721,E_Total_Export,Block%2036,15,MW,1;0,1,0,2093,E_Total_Export,Block%2037,15,MW,1;0,1,0,2594,E_Total_Export,Block%2041,15,MW,1;0,1,0,2557,E_Total_Export,Block%2042,15,MW,1;0,1,0,2299,E_Total_Export,Block%2046,15,MW,1;0,1,0,2129,E_Total_Export,Block%2047,15,MW,1;0,1,0,4525,E_Total_Export,Block%2048,15,MW,1;0,1,0,2163,E_Total_Export,Block%2049,15,MW,1;0,1,0,2329,E_Total_Export,Block%2038,15,MW,1;0,1,0,2396,E_Total_Export,Block%2039,15,MW,1;0,1,0,2513,E_Total_Export,Block%2040,15,MW,1;0,1,0,5060,E_Total_Export,Block%2043,15,MW,1;0,1,0,4860,E_Total_Export,Block%2044,15,MW,1;0,1,0,3839,E_Total_Export,Block%2045,15,MW,1;0,1,0,3729,E_Total_Export,Block%2029,15,MW,1;0,1,0,4106,E_Total_Export,Block%2032,15,MW,1;0,1,0,3475,E_Total_Export,Block%2033,15,MW,1;0,1,0,3545,E_Total_Export,Block%2034,15,MW,1;0,1,0,3503,E_Total_Export,Block%2035,15,MW,1;0,1,0,4021,E_Total_Export,Block%2050,15,MW,1;0,1,0,3950,E_Total_Export,Block%2051,15,MW,1;0,1,0,2489,E_Total_Export,Block%2052,15,MW,1">';
                        	echo '    <img title="Export Meter data as .csv file" src="../imgs/xls_pr.png">';
                        	echo '</a>';
                    		}
							
							

                    //$_SESSION['user'] != "charanka" &&
                    if ($phase == "tag" && $park_no=="10") {
                        echo '<a href="patPr.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&print=true&park_no=' . $park_no . '">';
                        echo '    <img title="Export Performance Ratio as .csv file" src="../imgs/xls_pr.png">';
                        echo '</a>';
                    }
					if ($phase == "tag" && $park_no=="34") {
                        echo '<a href="patPr.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&print=true&park_no=' . $park_no . '">';
                        echo '    <img title="Export Performance Ratio as .csv file" src="../imgs/xls_pr.png">';
                        echo '</a>';
                    }

                    


                        } else {
                            if ($hideClear == 0) {
                                echo '<img title="Reset Diagram" src="clear0.png">';
                            }
                            echo '<img title="Export selection as Excel file" src="xls0.png">';
                        }
                    } else {

                        if (!isset($park_no)) {
                            if (!isset($_SESSION ['park_no_s'])) {
                                $park_no = 0;
                            } else {
                                $park_no = $_SESSION ['park_no_s'];
                            }
                        }
                        $_SESSION ['park_no_s'] = $park_no;

                        if (!isset($subpark_id)) {
                            if (!isset($_SESSION ['subpark_s'])) {
                                $subpark_id = 0;
                            } else {
                                $subpark_id = $_SESSION ['subpark_s'];
                            }
                        }
                        $_SESSION ['subpark_s'] = $subpark_id;

                        if (!isset($area_id)) {
                            if (!isset($_SESSION ['area_s'])) {
                                $area_id = 0;
                            } else {
                                $area_id = $_SESSION ['area_s'];
                            }
                        }
                        $_SESSION ['area_s'] = $area_id;

                        if (!isset($phase)) {
                            if (!isset($_SESSION ['phase_s'])) {
                                $phase = "tag";
                            } else {
                                $phase = $_SESSION ['phase_s'];
                            }
                        }
                        $_SESSION ['phase_s'] = $phase;

                        if (!isset($jahr)) {
                            if (!isset($_SESSION ['jahr_s'])) {
                                $jahr = $jahr_heute;
                            } else {
                                $jahr = $_SESSION ['jahr_s'];
                            }
                        }
                        $_SESSION ['jahr_s'] = $jahr;

                        if (!isset($mon)) {
                            if (!isset($_SESSION ['mon_s'])) {
                                $mon = $monat_heute;
                            } else {
                                $mon = $_SESSION ['mon_s'];
                            }
                        }
                        $_SESSION ['mon_s'] = $mon;

                        if (!isset($tag)) {
                            if (!isset($_SESSION ['tag_s'])) {
                                $tag = $tag_heute;
                            } else {
                                $tag = $_SESSION ['tag_s'];
                            }
                        }
                        $_SESSION ['tag_s'] = $tag;

                        if ($phase == "tag") {
                            echo '<a href="pat.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&portal=' . $park_no . '&phase=' . $phase . '">';

                            echo '     <img title="Export diagram as .csv file" src="../imgs/xls.png">';
                            echo '</a>';

                            echo '<a href="patPyr.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&name=WeatherStation&device=108&offset=19800&useTs=0&showQueries=0">';
                            echo '    <img title="Export Pyranometer as .csv file" src="../imgs/sun.png">';
                            echo '</a>';

                            echo '<a href="patPr.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&print=true&park_no=' . $park_no . '">';
                            echo '    <img title="Export Performance Ratio as .csv file" src="../imgs/xls_pr.png">';
                            echo '</a>';
                        }
                        
                    }

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

        foreach ($device[values] as $ts => $data) {
            echo "alarmTexts[$ts]='$data[txt]';\n";
        }
    }
}
?>
        
        
    var miny=null;
    var maxy=null;
    var resolution = <?php echo $resolution; ?>;
                            
    $(function () {
        $("#frame").resize();
        plotWithOptions();			
    				
    });
        
    function toggleKurve(label){
        zeigeKurve[label] = !zeigeKurve[label];
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
    var devValues=new Array();

<?php
$index = 0;
foreach ($values as $devkey => $device) {
    echo "devValues['$index']=new Array();\n";
    if ($defaults == false) {

        echo "zeigeKurve['$devkey']=true;\n";
    } else {
        if (in_array($devkey, $defaultNames)) {
            echo "zeigeKurve['$devkey']=true;\n";
        } else {
            echo "zeigeKurve['$devkey']=false;\n";
        }
    }
    foreach ($device[data] as $tskey => $value) {

        echo "devValues['" . $index . "'].push([" . ($tskey * 1000) . ", " . $value[value] . "]);\n";
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
$first = true;

$index = 0;
foreach ($values as $key => $value) {
    if (!$first) {
        echo ",";
    } else {
        $first = false;
    }
    $axisWord = "";
    if (isset($axis[$value[axis]])) {
        $axisWord = " (" . $axis[$value[axis]] . ")";
        $axisWord = str_replace("DEG", "&deg;", $axisWord);
        $axisWord = str_replace("SQUA", "&sup2;", $axisWord);
        $axisWord.="'";
        if ($axis[$value[axis]] == "DIGITAL") {
            $axisWord.=", lines: {show: zeigeKurve['" . $key . "'], steps: true}";
        }
    }

    echo "{ lines: {show: zeigeKurve['" . $key . "']}, points: {show: false}, yaxis: " . ($value[axis] + 1) . ", color: $value[color], label: '" . $key . $axisWord . ", data: devValues['" . ($index++) . "']}\n";
}

$first = true;
/*
  foreach ($devices as $key => $device) {
  echo ", { points: {show: true}, lines: {show: false}, color: $device[color], data: $device[displaydata] }\n";
  }
 */
?>
        ],options);

    }
        
    window.onresize = plotWithOptions;
<?php
if ($speed != 0) {
    echo "alert('total: " . (mktime() - $now) . "');";
}
?>
            </script>
        </div>
    </body>
</html>

