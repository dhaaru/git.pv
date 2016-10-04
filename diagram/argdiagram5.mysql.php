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

	///for amplus pune avg of 20 & 10 deg
	if($words[3]=="8414_8412"){
		$arrv2=explode("_",$words[3]);
		$devid=implode(",",$arrv2);
		$query = "select ts+19800 as ts, ROUND(sum(value),2)/2 as value FROM $sourcetable where ts >= $stamp and ts < $endstamp and device in (".$devid.") and (field='".$translatedField."') group by ts";
    } ///
	else if($translatedField=='Upv-Ist'){
			$query = "select ts+19800 as ts, ROUND((((value+$words[0])*$words[1])+$words[2]),2) as value from $sourcetable where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";

	}
	else{
	$arrvl=explode("_",$words[3]);
	if(count($arrvl)>1)
	{
		$ids=implode(",",$arrvl);
		$query = "select ts+19800 as ts, sum((((value+$words[0])*$words[1])+$words[2])) as value from $sourcetable where value is not null and device IN (".$ids.") and field = '$translatedField' and ts > $stamp and ts < $endstamp group by ts";
	}
	else
	{
		if($translatedField == 'Pac'){
			$query = "select ts+19800 as ts, ROUND((((value+$words[0])*$words[1])+$words[2])/1000,2) as value from $sourcetable where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
		}
		else if($translatedField == 'PAC_Total'){
				$query = "select ts+19800 as ts, ROUND((((value+$words[0])*$words[1])+$words[2])/100,2) as value from $sourcetable where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
		}
		else{
				$arrv3=explode("-",$translatedField);
				$numItems = count($arrv3);
					if(count($arrv3)>1){
					$fields='';
					$i=0;
					foreach($arrv3 as $key=>$value){
						$fields.="'".$value."'";
						if($numItems!=++$i){$fields.=",";}
					}
						$query = "select ts+19800 as ts, ROUND(sum(value),2)/2 as value FROM $sourcetable where ts >= $stamp and ts < $endstamp and device = $words[3] and field IN(".$fields.") group by ts";
						$fields='';
					}
				else {
					$query = "select ts+19800 as ts, ROUND((((value+$words[0])*$words[1])+$words[2]),2) as value from $sourcetable where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
				}
		}
	}
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
        <script type="text/javascript" src="../functions/flot/jquery.flot.time.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.selection.min.js"></script>
        <!--<script type="text/javascript"
        src="../functions/flot/jquery.flot.time.min.js"></script>-->
        
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


					///export access permission
					$user_name = $_SESSION['user'];
					$query_ds = "select export FROM users WHERE user = '$user_name' and export=1";
					$ds = mysql_query($query_ds, $verbindung) or die(mysql_error());
					$row_ds = mysql_num_rows($ds);
					if($row_ds==1){
                    if ($exception != 25) {
                        if ($anyArgs) {
                            if ($hideClear == 0) {
                                echo '<a href="../' . $sourcefile . $phpArg . $phaseWord . '&igate=' . $igate . '" target="_parent">';
                                echo '<img title="Reset Diagram" src="clear.png">';
                                echo '</a>';
                            }
							/*if ($phase == "tag" && $park_no=="36") {
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
							}*/
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
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,7786,IDC1,String%20Current%201.1,5,A,15;0,1,0,7786,IDC2,String%20Current%201.2,5,A,1;0,1,0,7786,IDC3,String%20Current%201.3,5,A,2;0,1,0,7786,IDC4,String%20Current%201.4,5,A,3;0,1,0,7786,IDC5,String%20Current%202.1,5,A,4;0,1,0,7786,IDC6,String%20Current%202.2,5,A,5;0,1,0,7786,IDC7,String%20Current%202.3,5,A,6;0,1,0,7786,IDC8,String%20Current%202.4,5,A,7;0,1,0,7786,IDC9,String%20Current%203.1,5,A,8;0,1,0,7786,IDC10,String%20Current%203.2,5,A,9;0,1,0,7786,IDC11,String%20Current%203.3,5,A,10;0,1,0,7786,IDC12,String%20Current%203.4,5,A,11">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "graph1" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase . '&args=0,1,0,7792,AC_Power,AC%20Power-%201,15,kW,4;0,1,0,7791,AC_Power,AC%20Power-%202,15,kW,5;0,1,0,7794,AC_Power,AC%20Power-%203,15,kW,6;0,1,0,7794,Solar_Radiation,Irradiation,15,W/m²,\'Gold\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "graph2" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase . '&args=0,1,0,7792,DC_Voltage,DC%20Voltage-%201,15,V,8;0,1,0,7791,DC_Voltage,DC%20Voltage-%202,15,V,9;0,1,0,7794,DC_Voltage,DC%20Voltage-%203,15,V,6;0,1,0,7792,DC_Current,DC%20Current-%201,15,A,7;0,1,0,7791,DC_Current,DC%20Current-%202,12,A,15;0,1,0,7794,DC_Current,DC%20Current-%203,15,A,13">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "graph3" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase . '&args=0,1,0,7792,AC_Voltage,AC%20Voltage-%201,15,V,8;0,1,0,7791,AC_Voltage,AC%20Voltage-%202,15,V,9;0,1,0,7794,AC_Voltage,AC%20Voltage-%203,15,V,6;0,1,0,7792,AC_Frequency1,Frequency-%201,15,Hz,7;0,1,0,7791,AC_Frequency1,Frequency-%202,12,Hz,15;0,1,0,7794,AC_Frequency1,Frequency-%203,15,Hz,13">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "inv1" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase . '&args=0,1,0,7792,AC_Power,AC%20Power,15,kW,4;0,1,0,7792,DC_Current,DC%20Current,15,A,15">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "inv2" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,7791,AC_Power,AC%20Power,15,kW,4;0,1,0,7791,DC_Current,DC%20Current,15,A,15">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "inv3" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,7794,AC_Power,AC%20Power,15,kW,4;0,1,0,7794,DC_Current,DC%20Current,15,A,15">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "system" && $park_no=="36"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args='.$argBack.'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							/*Amplus Pune start*/


							// SMU 1
							else if($phase == "smu1inv1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8459,IDC1,String%20Current%201.1,5,A,15;0,1,0,8459,IDC2,String%20Current%201.2,5,A,1;0,1,0,8459,IDC3,String%20Current%201.3,5,A,2;0,1,0,8459,IDC4,String%20Current%201.4,5,A,3;0,1,0,8459,IDC5,String%20Current%201.5,5,A,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu1inv2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8459,IDC6,String%20Current%202.1(20°),5,A,15;0,1,0,8459,IDC7,String%20Current%202.2(20°),5,A,1;0,1,0,8459,IDC8,String%20Current%202.3(20°),5,A,2;0,1,0,8459,IDC9,String%20Current%202.4(10°),5,A,3;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'brown\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu1inv3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8459,IDC10,String%20Current%203.1,5,A,15;0,1,0,8459,IDC11,String%20Current%203.2,5,A,1;0,1,0,8459,IDC12,String%20Current%203.3,5,A,2;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu1inv4" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8459,IDC13,String%20Current%204.1,5,A,15;0,1,0,8459,IDC14,String%20Current%204.2,5,A,1;0,1,0,8459,IDC15,String%20Current%204.3,5,A,2;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}// SMU 2
							else if($phase == "smu2inv1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8412,IDC5,String%20Current%205.1,5,A,15;0,1,0,8412,IDC6,String%20Current%205.2,5,A,1;0,1,0,8412,IDC7,String%20Current%205.3,5,A,2;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu2inv2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8412,IDC8,String%20Current%206.1,5,A,15;0,1,0,8412,IDC9,String%20Current%206.2,5,A,1;0,1,0,8412,IDC10,String%20Current%206.3,5,A,2;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu2inv3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8412,IDC1,String%20Current%207.1(20°),5,A,15;0,1,0,8412,IDC2,String%20Current%207.2(10°),5,A,1;0,1,0,8412,IDC3,String%20Current%207.3(10°),5,A,2;0,1,0,8412,IDC4,String%20Current%207.4(10°),5,A,1;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'brown\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu2inv4" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8412,IDC13,String%20Current%208.1,5,A,15;0,1,0,8412,IDC14,String%20Current%208.2,5,A,1;0,1,0,8412,IDC15,String%20Current%208.3,5,A,4;0,1,0,8412,IDC16,String%20Current%208.4,5,A,2;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}// SMU 3
							else if($phase == "smu3inv1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8414,IDC13,String%20Current%209.1,5,A,15;0,1,0,8414,IDC14,String%20Current%209.2,5,A,1;0,1,0,8414,IDC15,String%20Current%209.3,5,A,2;0,1,0,8414,IDC16,String%20Current%209.4,5,A,2;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu3inv2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8414,IDC9,String%20Current%2010.1,5,A,15;0,1,0,8414,IDC10,String%20Current%2010.2,5,A,1;0,1,0,8414,IDC11,String%20Current%2010.3,5,A,2;0,1,0,8414,IDC12,String%20Current%2010.4,5,A,2;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu3inv3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8414,IDC5,String%20Current%2011.1,5,A,15;0,1,0,8414,IDC6,String%20Current%2011.2,5,A,1;0,1,0,8414,IDC7,String%20Current%2011.3,5,A,2;0,1,0,8414,IDC8,String%20Current%2011.4,5,A,2;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu3inv4" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8414,IDC1,String%20Current%2012.1,5,A,15;0,1,0,8414,IDC2,String%20Current%2012.2,5,A,1;0,1,0,8414,IDC3,String%20Current%2012.3,5,A,2;0,1,0,8414,IDC4,String%20Current%2012.4,5,A,2;0,1,0,8459,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}// SMU 4
							else if($phase == "smu4inv1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8413,IDC12,String%20Current%2013.1,5,A,15;0,1,0,8413,IDC13,String%20Current%2013.2,5,A,1;0,1,0,8413,IDC14,String%20Current%2013.3,5,A,2;0,1,0,8413,IDC15,String%20Current%2013.4,5,A,2;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu4inv2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8413,IDC1,String%20Current%2014.1(20°),5,A,15;0,1,0,8413,IDC2,String%20Current%2014.2(20°),5,A,1;0,1,0,8413,IDC3,String%20Current%2014.3(10°),5,A,2;0,1,0,8413,IDC4,String%20Current%2014.4(10°),5,A,2;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'brown\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu4inv3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8413,IDC5,String%20Current%2015.1,5,A,15;0,1,0,8413,IDC6,String%20Current%2015.2,5,A,1;0,1,0,8413,IDC7,String%20Current%2015.3,5,A,2;0,1,0,8413,IDC8,String%20Current%2015.4,5,A,2;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu4inv4" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8413,IDC9,String%20Current%2016.1,5,A,15;0,1,0,8413,IDC10,String%20Current%2016.2,5,A,1;0,1,0,8413,IDC11,String%20Current%2016.3,5,A,2;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter Graph
							/* INV1 Start*/
							else if($phase == "INV1G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8379,AC_Power,AC%20Power,15,kW,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV1G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8379,DC_Voltage,DC%20Voltage,15,v,5;0,1,0,8379,DC_Current,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV1G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8379,AC_Voltage,AC%20Voltage,15,v,7;0,1,0,8379,AC_Frequency1,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV1 End */
							 /* INV2 Start*/
							else if($phase == "INV2G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8408,Pac,AC%20Power,15,kW,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Brown\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV2G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8408,Upv-Ist,DC%20Voltage,15,v,5;0,1,0,8408,Ipv,DC%20Current,15,A,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV2G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8408,Uac,AC%20Voltage,15,v,4;0,1,0,8408,Fac,Frequency,15,Hz,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV2 End */
							 /* INV3 Start*/
							else if($phase == "INV3G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8410,Pac,AC%20Power,15,kW,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV3G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8410,Upv-Ist,DC%20Voltage,15,v,5;0,1,0,8410,Ipv,DC%20Current,15,A,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV3G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8410,Uac,AC%20Voltage,15,v,4;0,1,0,8410,Fac,Frequency,15,Hz,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV3 End */
							 /* INV4 Start*/
							else if($phase == "INV4G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8409,Pac,AC%20Power,15,kW,4;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV4G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8409,Upv-Ist,DC%20Voltage,15,v,5;0,1,0,8409,Ipv,DC%20Current,15,A,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV4G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8409,Uac,AC%20Voltage,15,v,4;0,1,0,8409,Fac,Frequency,15,Hz,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV4 End */
							  /* INV5,6 Start*/
							else if($phase == "INV5G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8458,Pac,AC%20Power%205,15,kW,4;0,1,0,8456,Pac,AC%20Power%206,15,kW,5;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV5G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8458,Upv-Ist,DC%20Voltage%205,15,v,6;0,1,0,8456,Upv-Ist,DC%20Voltage%206,15,v,7;0,1,0,8458,Ipv,DC%20Current%205,15,A,12;14,A,8;0,1,0,8456,Ipv,DC%20Current%206,15,A,9;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV5G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8458,Uac,AC%20Voltage%205,15,v,4;0,1,0,8456,Uac,AC%20Voltage%206,15,v,12;0,1,0,8458,Fac,Frequency%205,13,Hz,13;0,1,0,8456,Fac,Frequency%206,15,Hz,14;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV5,6 End */
							  /* INV7Start*/
							else if($phase == "INV7G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8381,AC_Power,AC%20Power,15,kW,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Brown\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV7G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8381,DC_Voltage,DC%20Voltage,15,v,5;0,1,0,8381,DC_Current,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV7G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8381,AC_Voltage,AC%20Voltage,15,v,7;0,1,0,8381,AC_Frequency1,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV7 End */
							  /* INV8Start*/
							else if($phase == "INV8G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12976,Pac,AC%20Power,15,kW,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV8G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12976,Upv-Ist,DC%20Voltage,15,v,5;0,1,0,12976,Ipv,DC%20Current,15,A,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV8G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12976,Uac,AC%20Voltage,15,v,4;0,1,0,12976,Fac,Frequency,15,Hz,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV8 End */
							  /* INV9Start*/
							else if($phase == "INV9G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8378,AC_Power,AC%20Power,15,kW,4;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV9G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8378,DC_Voltage,DC%20Voltage,15,v,5;0,1,0,8378,DC_Current,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV9G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8378,AC_Voltage,AC%20Voltage,15,v,7;0,1,0,8378,AC_Frequency1,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV9 End */
							  /* INV10,11,12 Start*/
							else if($phase == "INV10G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8384,Pac,AC%20Power%2010,15,kW,4;0,1,0,8382,Pac,AC%20Power%2011,15,kW,5;0,1,0,8383,Pac,AC%20Power%2012,15,kW,6;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV10G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8384,Upv-Ist,DC%20Voltage%2010,15,v,7;0,1,0,8382,Upv-Ist,DC%20Voltage%2011,15,v,8;0,1,0,8383,Upv-Ist,DC%20Voltage%2012,15,v,9;0,1,0,8384,Ipv,DC%20Current%2010,15,A,10;0,1,0,8382,Ipv,DC%20Current%2011,15,A,11;0,1,0,8383,Ipv,DC%20Current%2012,15,A,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV10G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8384,Uac,AC%20Voltage%2010,15,v,4;0,1,0,8382,Uac,AC%20Voltage%2011,15,v,7;0,1,0,8383,Uac,AC%20Voltage%2012,15,v,6;0,1,0,8384,Fac,Frequency%2010,15,Hz,8;0,1,0,8382,Fac,Frequency%2011,15,Hz,9;0,1,0,8383,Fac,Frequency%2012,15,Hz,11;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV10,11,12 End */
							  /* INV13 Start*/
							else if($phase == "INV13G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8380,AC_Power,AC%20Power,15,kW,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV13G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8380,DC_Voltage,DC%20Voltage,15,v,5;0,1,0,8380,DC_Current,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV13G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8380,AC_Voltage,AC%20Voltage,15,v,7;0,1,0,8380,AC_Frequency1,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV13 End */
							  /* INV14 Start*/
							else if($phase == "INV14G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8386,Pac,AC%20Power,15,kW,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Brown\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV14G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8386,Upv-Ist,DC%20Voltage,15,v,5;0,1,0,8386,Ipv,DC%20Current,15,A,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV14G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8386,Uac,AC%20Voltage,15,v,4;0,1,0,8386,Fac,Frequency,15,Hz,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV14 End */
							  /* INV15 Start*/
							else if($phase == "INV15G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8387,Pac,AC%20Power,15,kW,4;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV15G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8387,Upv-Ist,DC%20Voltage,15,v,5;0,1,0,8387,Ipv,DC%20Current,15,A,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV15G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8387,Uac,AC%20Voltage,15,v,4;0,1,0,8387,Fac,Frequency,15,Hz,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV15 End */
							  /* INV16 Start*/
							else if($phase == "INV16G1" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8388,Pac,AC%20Power,15,kW,4;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV16G2" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8388,Upv-Ist,DC%20Voltage,15,v,5;0,1,0,8388,Ipv,DC%20Current,15,A,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV16G3" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8388,Uac,AC%20Voltage,15,v,4;0,1,0,8388,Fac,Frequency,15,Hz,12;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							 /* INV16 End */



							//weather station 20 deg
							else if($phase == "weather20" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8414,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8414,Module_Temperature,Module Temperature,15,°C,\'darkred\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}//weather station 10 deg
							else if($phase == "weather10" && $park_no=="43"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,8412,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8412,Module_Temperature,Module Temperature,15,°C,\'darkred\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Energy meter
							else if ($phase == "tag" && $park_no=="43") {
								$type="energy";
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase='.$type.'&args=0,1,0,8240,Activepower_Total,Active Power,15,kW,4;0,1,0,8414_8412,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
                    		}
							/*Amplus Pune end*/

							/*Amplus Mumbai start*/


							// SMU 1
							else if($phase == "ampsmu1inv1" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9330,IDC1,String%20Current%201.1,5,A,15;0,1,0,9330,IDC2,String%20Current%201.2,5,A,1;0,1,0,9330,IDC3,String%20Current%201.3,5,A,2;0,1,0,9330,IDC4,String%20Current%201.4,5,A,3;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ampsmu1inv2" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9330,IDC5,String%20Current%202.1,5,A,15;0,1,0,9330,IDC6,String%20Current%202.2,5,A,1;0,1,0,9330,IDC7,String%20Current%202.3,5,A,2;0,1,0,9330,IDC8,String%20Current%202.4,5,A,3;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ampsmu1inv3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9330,IDC9,String%20Current%203.1,5,A,15;0,1,0,9330,IDC10,String%20Current%203.2,5,A,1;0,1,0,9330,IDC11,String%20Current%203.3,5,A,2;0,1,0,9330,IDC12,String%20Current%203.4,5,A,2;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ampsmu1inv4" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9330,IDC13,String%20Current%204.1,5,A,15;0,1,0,9330,IDC14,String%20Current%204.2,5,A,1;0,1,0,9330,IDC15,String%20Current%204.3,5,A,2;0,1,0,9330,IDC16,String%20Current%204.4,5,A,2;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							// SMU2

							else if($phase == "ampsmu2inv5" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9316,IDC1,String%20Current%205.1,5,A,15;0,1,0,9316,IDC2,String%20Current%205.2,5,A,1;0,1,0,9316,IDC3,String%20Current%205.3,5,A,2;0,1,0,9316,IDC4,String%20Current%205.4,5,A,3;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ampsmu2inv6" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9316,IDC5,String%20Current%206.1,5,A,15;0,1,0,9316,IDC6,String%20Current%206.2,5,A,1;0,1,0,9316,IDC7,String%20Current%206.3,5,A,2;0,1,0,9316,IDC8,String%20Current%206.4,5,A,3;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ampsmu2inv7" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9316,IDC9,String%20Current%207.1,5,A,15;0,1,0,9316,IDC10,String%20Current%207.2,5,A,1;0,1,0,9316,IDC11,String%20Current%207.3,5,A,2;0,1,0,9316,IDC12,String%20Current%207.4,5,A,2;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ampsmu2inv8" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9316,IDC13,String%20Current%208.1,5,A,15;0,1,0,9316,IDC14,String%20Current%208.2,5,A,1;0,1,0,9316,IDC15,String%20Current%208.3,5,A,2;0,1,0,9316,IDC16,String%20Current%208.4,5,A,2;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ampweather" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9316,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9316,Module_Temperature,Module Temperature,15,°C,\'darkred\';0,1,0,9330,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9316,Ambient_Temperature,Ambient Temperature,15,°C,\'green\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter1 start
							else if($phase == "SMA1M1" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9323,Pac,AC%20Power,15,kW,4;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA1M2" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9323,A.Ms.Vol-B.Ms.Vol,(DC%201%2BDC%202)/2,15,v,5;0,1,0,9323,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA1M3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9323,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9323,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9323,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9323,GridMs.Hz ,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter1 End
							//Inverter2 start
							else if($phase == "SMA2M1" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9328,Pac,AC%20Power,15,kW,4;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA2M2" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9328,A.Ms.Vol-B.Ms.Vol,(DC%201%2BDC%202)/2,15,v,5;0,1,0,9328,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA2M3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9328,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9328,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9328,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9328,GridMs.Hz ,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter2 End
							//Inverter3 start
							else if($phase == "SMA3M1" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9329,Pac,AC%20Power,15,kW,4;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA3M2" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9329,A.Ms.Vol-B.Ms.Vol,(DC%201%2BDC%202)/2,15,v,5;0,1,0,9329,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA3M3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9329,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9329,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9329,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9329,GridMs.Hz ,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter3 End
							//Inverter4 start
							else if($phase == "SMA4M1" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9322,Pac,AC%20Power,15,kW,4;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA4M2" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9322,A.Ms.Vol-B.Ms.Vol,(DC%201%2BDC%202)/2,15,v,5;0,1,0,9322,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA4M3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9322,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9322,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9322,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9322,GridMs.Hz ,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter4 End
							//Inverter5 start
							else if($phase == "SMA5M1" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9324,Pac,AC%20Power,15,kW,4;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA5M2" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9324,A.Ms.Vol-B.Ms.Vol,(DC%201%2BDC%202)/2,15,v,5;0,1,0,9324,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA5M3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9324,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9324,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9324,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9324,GridMs.Hz ,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter5 End
							//Inverter6 start
							else if($phase == "SMA6M1" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9326,Pac,AC%20Power,15,kW,4;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA6M2" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9326,A.Ms.Vol-B.Ms.Vol,(DC%201%2BDC%202)/2,15,v,5;0,1,0,9326,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA6M3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9326,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9326,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9326,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9326,GridMs.Hz,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter6 End
							//Inverter7 start
							else if($phase == "SMA7M1" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9325,Pac,AC%20Power,15,kW,4;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA7M2" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9325,A.Ms.Vol-B.Ms.Vol,(DC%201%2BDC%202)/2,15,v,5;0,1,0,9325,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA7M3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9325,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9325,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9325,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9325,GridMs.Hz ,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter7 End
							//Inverter1 start
							else if($phase == "SMA8M1" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9327,Pac,AC%20Power,15,kW,4;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA8M2" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9327,A.Ms.Vol-B.Ms.Vol,(DC%201%2BDC%202)/2,15,v,5;0,1,0,9327,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA8M3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9327,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9327,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9327,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9327,GridMs.Hz ,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter1 End
							else if($phase == "SMA8M3" && $park_no=="39"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9327,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9327,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9327,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9327,GridMs.Hz ,Frequency,15,Hz,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Mumtag"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9317,Activepower_Total,Active Power,15,kW,4;0,1,0,9316,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							/*Amplus Mumbai end*/

							/*Amplus Dominos nagpur start*/
							//Energy meter
							else if ($phase == "Domtag") {
								$type="energy";
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase='.$type.'&args=0,1,0,10070,Activepower_Total,Active Power,15,kW,4;0,1,0,11623,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
                    		}
							//Energy meter
							else if ($phase == "weather" && $park_no=="52") {
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase='.$type.'&args=0,1,0,11623,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,11623,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,11623,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,11623,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,11623,Wind_Direction,Wind Direction,15,°,\'Burlywood\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
                    		}

							// SMU
							else if($phase == "domsmu1inv1" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11622,IDC1,String%20Current%201.1,5,A,15;0,1,0,11622,IDC2,String%20Current%201.2,5,A,1;0,1,0,11622,IDC3,String%20Current%201.3,5,A,2;0,1,0,11622,IDC4,String%20Current%201.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "domsmu1inv2" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11622,IDC5,String%20Current%202.1,5,A,15;0,1,0,11622,IDC6,String%20Current%202.2,5,A,1;0,1,0,11622,IDC7,String%20Current%202.3,5,A,2;0,1,0,11622,IDC8,String%20Current%202.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "domsmu1inv3" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11622,IDC9,String%20Current%203.1,5,A,15;0,1,0,11622,IDC10,String%20Current%203.2,5,A,1;0,1,0,11622,IDC11,String%20Current%203.3,5,A,2;0,1,0,11622,IDC12,String%20Current%203.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "domsmu1inv4" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11623,IDC1,String%20Current%204.1,5,A,15;0,1,0,11623,IDC2,String%20Current%204.2,5,A,1;0,1,0,11623,IDC3,String%20Current%204.3,5,A,2;0,1,0,11623,IDC4,String%20Current%204.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "domsmu1inv5" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11623,IDC5,String%20Current%205.1,5,A,15;0,1,0,11623,IDC6,String%20Current%205.2,5,A,1;0,1,0,11623,IDC7,String%20Current%205.3,5,A,2;0,1,0,11623,IDC8,String%20Current%205.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "domweather" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11623,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,11623,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,11623,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,11623,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,11623,Wind_Direction,Wind Direction,15,°,\'Burlywood\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Inverter1 start
							else if($phase == "DOMINV1graph1" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11624,Pac,AC%20Power,15,kW,4;0,1,0,11623,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV1graph2" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11624,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11624,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV1graph3" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11624,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,11624,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,11624,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,11624,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter1 End
							//Inverter2 start
							else if($phase == "DOMINV2graph1" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11627,Pac,AC%20Power,15,kW,4;0,1,0,11623,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV2graph2" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11627,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11627,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV2graph3" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11627,GridMs.PhV.phsA,INV2%20AC%20Voltage-%20L1,15,V,8;0,1,0,11627,GridMs.PhV.phsB,INV2%20AC%20Voltage-%20L2,15,V,9;0,1,0,11627,GridMs.PhV.phsC,INV2%20AC%20Voltage-%20L3,15,V,5;0,1,0,11627,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter2 End

							//Inverter3 start
							else if($phase == "DOMINV3graph1" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11625,Pac,AC%20Power,15,kW,4;0,1,0,11623,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV3graph2" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11625,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11627,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV3graph3" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11625,GridMs.PhV.phsA,INV3%20AC%20Voltage-%20L1,15,V,8;0,1,0,11627,GridMs.PhV.phsB,INV3%20AC%20Voltage-%20L2,15,V,9;0,1,0,11627,GridMs.PhV.phsC,INV3%20AC%20Voltage-%20L3,15,V,5;0,1,0,11627,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter3 End


							//Inverter4 start
							else if($phase == "DOMINV4graph1" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11626,Pac,AC%20Power,15,kW,4;0,1,0,11623,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV4graph2" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11626,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11627,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV4graph3" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11626,GridMs.PhV.phsA,INV4%20AC%20Voltage-%20L1,15,V,8;0,1,0,11627,GridMs.PhV.phsB,INV4%20AC%20Voltage-%20L2,15,V,9;0,1,0,11627,GridMs.PhV.phsC,INV4%20AC%20Voltage-%20L3,15,V,5;0,1,0,11627,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter4 End

							//Inverter5 start
							else if($phase == "DOMINV5graph1" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11628,Pac,AC%20Power,15,kW,4;0,1,0,11623,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV5graph2" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11628,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11627,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "DOMINV5graph3" && $park_no=="52"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11628,GridMs.PhV.phsA,INV5%20AC%20Voltage-%20L1,15,V,8;0,1,0,11627,GridMs.PhV.phsB,INV5%20AC%20Voltage-%20L2,15,V,9;0,1,0,11627,GridMs.PhV.phsC,INV5%20AC%20Voltage-%20L3,15,V,5;0,1,0,11627,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter5 End
							/*Amplus Dominos nagpur end*/

							/*Amplus Royal Pune start*/
							//Energy meter
							else if ($phase == "Roytag") {
								$type="energy";
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase='.$type.'&args=0,1,0,10071,Activepower_Total,Active Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
                    		}
							else if ($phase == "Roytag") {
								$type="energy";
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase='.$type.'&args=0,1,0,10071,Activepower_Total,Active Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
                    		}

							// SMU
							else if($phase == "roysmu1inv1" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11724,IDC1,String%20Current%201.1,5,A,15;0,1,0,11724,IDC2,String%20Current%201.2,5,A,1;0,1,0,11724,IDC3,String%20Current%201.3,5,A,2;0,1,0,11724,IDC4,String%20Current%201.4,5,A,3;0,1,0,11724,IDC5,String%20Current%201.5,5,A,6;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "roysmu1inv2" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11724,IDC6,String%20Current%202.1,5,A,15;0,1,0,11724,IDC7,String%20Current%202.2,5,A,1;0,1,0,11724,IDC8,String%20Current%202.3,5,A,2;0,1,0,11724,IDC9,String%20Current%202.4,5,A,3;0,1,0,11724,IDC10,String%20Current%202.5,5,A,6;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "roysmu1inv3" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11724,IDC11,String%20Current%203.1,5,A,15;0,1,0,11724,IDC12,String%20Current%203.2,5,A,1;0,1,0,11724,IDC13,String%20Current%203.3,5,A,2;0,1,0,11724,IDC14,String%20Current%203.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "roysmu1inv4" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11724,IDC15,String%20Current%204.1,5,A,15;0,1,0,11724,IDC16,String%20Current%204.2,5,A,1;0,1,0,11724,IDC17,String%20Current%204.3,5,A,2;0,1,0,11724,IDC18,String%20Current%204.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "roysmu1inv5" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11723,IDC1,String%20Current%205.1,5,A,15;0,1,0,11723,IDC2,String%20Current%205.2,5,A,1;0,1,0,11723,IDC3,String%20Current%205.3,5,A,2;0,1,0,11723,IDC4,String%20Current%205.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "roysmu1inv6" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11723,IDC5,String%20Current%206.1,5,A,15;0,1,0,11723,IDC6,String%20Current%206.2,5,A,1;0,1,0,11723,IDC7,String%20Current%206.3,5,A,2;0,1,0,11723,IDC8,String%20Current%206.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "roysmu1inv7" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11723,IDC10,String%20Current%207.1,5,A,15;0,1,0,11723,IDC11,String%20Current%207.2,5,A,1;0,1,0,11723,IDC12,String%20Current%207.3,5,A,2;0,1,0,11723,IDC13,String%20Current%207.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "roysmu1inv8" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11723,IDC14,String%20Current%208.1,5,A,15;0,1,0,11723,IDC15,String%20Current%208.2,5,A,1;0,1,0,11723,IDC16,String%20Current%208.3,5,A,2;0,1,0,11723,IDC17,String%20Current%208.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							// Weather Station
							else if($phase == "royweather" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11724,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12692,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12692,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,12692,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12692,Wind_Direction,Wind Direction,15,°,\'Burlywood\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter1 start
							else if($phase == "RYINV1G1" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11721,Pac,AC%20Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV1G2" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11721,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11721,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV1G3" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11721,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,11721,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,11721,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,11721,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter1 End
							//Inverter2 start
							else if($phase == "RYINV2G1" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11718,Pac,AC%20Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV2G2" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11718,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11718,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV2G3" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11718,GridMs.PhV.phsA,INV2%20AC%20Voltage-%20L1,15,V,8;0,1,0,11718,GridMs.PhV.phsB,INV2%20AC%20Voltage-%20L2,15,V,9;0,1,0,11718,GridMs.PhV.phsC,INV2%20AC%20Voltage-%20L3,15,V,5;0,1,0,11718,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter2 End

							//Inverter3 start
							else if($phase == "RYINV3G1" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11719,Pac,AC%20Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV3G2" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11719,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11719,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV3G3" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11719,GridMs.PhV.phsA,INV3%20AC%20Voltage-%20L1,15,V,8;0,1,0,11719,GridMs.PhV.phsB,INV3%20AC%20Voltage-%20L2,15,V,9;0,1,0,11719,GridMs.PhV.phsC,INV3%20AC%20Voltage-%20L3,15,V,5;0,1,0,11719,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter3 End


							//Inverter4 start
							else if($phase == "RYINV4G1" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11715,Pac,AC%20Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV4G2" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11715,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11715,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV4G3" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11715,GridMs.PhV.phsA,INV2%20AC%20Voltage-%20L1,15,V,8;0,1,0,11715,GridMs.PhV.phsB,INV2%20AC%20Voltage-%20L2,15,V,9;0,1,0,11715,GridMs.PhV.phsC,INV2%20AC%20Voltage-%20L3,15,V,5;0,1,0,11715,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter4 End

							//Inverter5 start
							else if($phase == "RYINV5G1" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11720,Pac,AC%20Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV5G2" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11720,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11720,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV5G3" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11720,GridMs.PhV.phsA,INV5%20AC%20Voltage-%20L1,15,V,8;0,1,0,11720,GridMs.PhV.phsB,INV5%20AC%20Voltage-%20L2,15,V,9;0,1,0,11720,GridMs.PhV.phsC,INV5%20AC%20Voltage-%20L3,15,V,5;0,1,0,11720,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter5 End

							//Inverter6 start
							else if($phase == "RYINV6G1" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11716,Pac,AC%20Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV6G2" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11716,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11716,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV6G3" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11716,GridMs.PhV.phsA,INV6%20AC%20Voltage-%20L1,15,V,8;0,1,0,11716,GridMs.PhV.phsB,INV6%20AC%20Voltage-%20L2,15,V,9;0,1,0,11716,GridMs.PhV.phsC,INV6%20AC%20Voltage-%20L3,15,V,5;0,1,0,11716,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter6 End

							//Inverter7 start
							else if($phase == "RYINV7G1" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11717,Pac,AC%20Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV7G2" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11717,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11717,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV7G3" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11717,GridMs.PhV.phsA,INV7%20AC%20Voltage-%20L1,15,V,8;0,1,0,11717,GridMs.PhV.phsB,INV7%20AC%20Voltage-%20L2,15,V,9;0,1,0,11717,GridMs.PhV.phsC,INV7%20AC%20Voltage-%20L3,15,V,5;0,1,0,11717,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter7 End

							//Inverter8 start
							else if($phase == "RYINV8G1" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11722,Pac,AC%20Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV8G2" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11722,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11722,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RYINV8G3" && $park_no=="53"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11722,GridMs.PhV.phsA,INV8%20AC%20Voltage-%20L1,15,V,8;0,1,0,11722,GridMs.PhV.phsB,INV8%20AC%20Voltage-%20L2,15,V,9;0,1,0,11722,GridMs.PhV.phsC,INV8%20AC%20Voltage-%20L3,15,V,5;0,1,0,11722,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Inverter8 End

							/*Amplus Royal Pune end*/

							/* Amplus Indus Nagpur start*/
							//Energy meter
							else if ($phase == "Indtag") {
								$type="energy";
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase='.$type.'&args=0,1,0,10151,Activepower_Total,Active Power,15,kW,4;0,1,0,10452,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
                    		}
							// Weather Station
							else if ($phase == "indweather" && $park_no=="54") {
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase='.$phase.'&args=0,1,0,10452,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10452,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,10452,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,10452,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10452,Wind_Direction,Wind Direction,15,°,\'Burlywood\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
                    		}
							//SMU
							else if($phase == "indsmu1inv1" && $park_no=="54"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11629,IDC1,String%20Current%201.1,5,A,15;0,1,0,11629,IDC2,String%20Current%201.2,5,A,1;0,1,0,11629,IDC3,String%20Current%201.3,5,A,2;0,1,0,11629,IDC4,String%20Current%201.4,5,A,3;0,1,0,11629,IDC5,String%20Current%201.5,5,A,6;0,1,0,11629,IDC6,String%20Current%201.6,5,A,6;0,1,0,11629,IDC7,String%20Current%201.7,5,A,7;0,1,0,11629,IDC8,String%20Current%201.8,5,A,8;0,1,0,11629,IDC9,String%20Current%201.9,5,A,9;0,1,0,11629,IDC10,String%20Current%201.10,5,A,11;0,1,0,10452,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "indsmu1inv2" && $park_no=="54"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11714,IDC1,String%20Current%202.1,5,A,15;0,1,0,11714,IDC2,String%20Current%202.2,5,A,1;0,1,0,11714,IDC3,String%20Current%202.3,5,A,2;0,1,0,11714,IDC4,String%20Current%202.4,5,A,3;0,1,0,11714,IDC5,String%20Current%202.5,5,A,6;0,1,0,11714,IDC6,String%20Current%202.6,5,A,6;0,1,0,11714,IDC7,String%20Current%202.7,5,A,7;0,1,0,11714,IDC8,String%20Current%202.8,5,A,8;0,1,0,11714,IDC9,String%20Current%202.9,5,A,9;0,1,0,11714,IDC10,String%20Current%202.10,5,A,11;0,1,0,10452,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							else if($phase == "IndINV1G1" && $park_no=="54"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12667,PAC_Total,AC%20Power,15,kW,4;0,1,0,10452,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "IndINV1G2" && $park_no=="54"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12667,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12667,IDC_Mittelwert,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "IndINV1G3" && $park_no=="54"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12667,UAC_L1/N,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12667,UAC_L2/N,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12667,UAC_L3/N,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12667,Frequenz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "IndINV2G1" && $park_no=="54"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12668,PAC_Total,AC%20Power,15,kW,4;0,1,0,10452,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "IndINV2G2" && $park_no=="54"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12668,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12668,IDC_Mittelwert,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "IndINV2G3" && $park_no=="54"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12668,UAC_L1/N,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12668,UAC_L2/N,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12668,UAC_L3/N,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12668,Frequenz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							/* Amplus Indus Nagpur end*/
							/*Amplus Raisoni3 Pune start*/

							else if($phase == "Rai3tag"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9958,Activepower_Total,Active Power,15,kW,4;0,1,0,9957,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "smu" && $park_no=="46"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9959,IDC1,String%20Current%201.1,5,A,15;0,1,0,9959,IDC2,String%20Current%201.2,5,A,1;0,1,0,9959,IDC3,String%20Current%201.3,5,A,2;0,1,0,9959,IDC4,String%20Current%201.4,5,A,3;0,1,0,9959,IDC5,String%20Current%201.5,5,A,4;0,1,0,9959,IDC6,String%20Current%201.6,5,A,5;0,1,0,9959,IDC7,String%20Current%201.7,5,A,6;0,1,0,9959,IDC8,String%20Current%201.8,5,A,7;0,1,0,9959,IDC9,String%20Current%201.9,5,A,8;0,1,0,9959,IDC10,String%20Current%201.10,5,A,9;0,1,0,9959,IDC11,String%20Current%201.11,5,A,10;0,1,0,9959,IDC12,String%20Current%201.12,5,A,11;0,1,0,9959,IDC13,String%20Current%201.13,5,A,12;0,1,0,9959,IDC14,String%20Current%201.14,5,A,14;0,1,0,9959,IDC15,String%20Current%201.15,5,A,15;0,1,0,9959,IDC16,String%20Current%201.16,5,A,16;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ws" && $park_no=="46"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9957,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9957,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9957,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9957,Module_Temperature,Module_Temperature,15,°C,\'green\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "raigraph1" && $park_no=="46"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9971,Pac,AC%20Power-%201,15,kW,4;0,1,0,9972,Pac,AC%20Power-%202,15,kW,5;0,1,0,9973,Pac,AC%20Power-%203,15,kW,6;0,1,0,9957,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "raigraph2" && $park_no=="46"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9971,A.Ms.Vol-B.Ms.Vol,DC%20Voltage-%201,15,V,8;0,1,0,9972,A.Ms.Vol-B.Ms.Vol,DC%20Voltage-%202,15,V,9;0,1,0,9973,A.Ms.Vol-B.Ms.Vol,DC%20Voltage-%203,15,V,6;0,1,0,9971,A.Ms.Amp-B.Ms.Amp,DC%20Current-%201,15,A,7;0,1,0,9972,A.Ms.Amp-B.Ms.Amp,DC%20Current-%202,12,A,15;0,1,0,9973,A.Ms.Amp-B.Ms.Amp,DC%20Current-%203,15,A,13;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "raigraph3" && $park_no=="46"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,9971,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,9971,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,11;0,1,0,9971,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,12;0,1,0,9972,GridMs.PhV.phsA,INV2%20AC%20Voltage-%20L1,15,V,9;0,1,0,9972,GridMs.PhV.phsB,INV2%20AC%20Voltage-%20L2,15,V,21;0,1,0,9972,GridMs.PhV.phsC,INV2%20AC%20Voltage-%20L3,15,V,19;0,1,0,9973,GridMs.PhV.phsA,INV3%20AC%20Voltage-%20L1,15,V,17;0,1,0,9973,GridMs.PhV.phsB,INV3%20AC%20Voltage-%20L2,15,V,28;0,1,0,9973,GridMs.PhV.phsC,INV3%20AC%20Voltage-%20L3,15,V,2;0,1,0,9971,GridMs.Hz,Frequency-%201,15,Hz,7;0,1,0,9972,GridMs.Hz,Frequency-%202,12,Hz,15;0,1,0,9973,GridMs.Hz,Frequency-%203,15,Hz,1;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							/*Amplus Raisoni3 Pune end*/

							/*Amplus UPCL1 Start*/

							else if($phase == "Rudtag"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11588,Activepower_Total,Active Power,15,kW,4;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDweather" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12690,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12690,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12690,Module_Temperature,Module_Temperature,15,°C,\'green\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//SMU
							else if($phase == "Rudsmu1inv1" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11590,IDC1,String%20Current%201.1,5,A,15;0,1,0,11590,IDC2,String%20Current%201.2,5,A,2;0,1,0,11590,IDC3,String%20Current%201.3,5,A,3;0,1,0,11590,IDC4,String%20Current%201.4,5,A,4;0,1,0,11590,IDC5,String%20Current%201.5,5,A,15;0,1,0,12690,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Rudsmu1inv2" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11590,IDC6,String%20Current%202.1,5,A,1;0,1,0,11590,IDC7,String%20Current%202.2,5,A,2;0,1,0,11590,IDC8,String%20Current%202.3,5,A,3;0,1,0,11590,IDC9,String%20Current%202.4,5,A,4;0,1,0,11590,IDC10,String%20Current%202.5,5,A,15;0,1,0,12690,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Rudsmu1inv3" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11590,IDC11,String%20Current%203.1,5,A,1;0,1,0,11590,IDC12,String%20Current%203.2,5,A,2;0,1,0,11590,IDC13,String%20Current%203.3,5,A,3;0,1,0,11590,IDC14,String%20Current%203.4,5,A,4;0,1,0,11590,IDC15,String%20Current%203.5,5,A,15;0,1,0,12690,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Rudsmu2inv4" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11590,IDC1,String%20Current%204.1,5,A,15;0,1,0,11590,IDC2,String%20Current%204.2,5,A,2;0,1,0,11590,IDC3,String%20Current%204.3,5,A,3;0,1,0,11590,IDC4,String%20Current%204.4,5,A,4;0,1,0,11590,IDC5,String%20Current%204.5,5,A,15;0,1,0,12690,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Rudsmu2inv5" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11590,IDC6,String%20Current%205.1,5,A,1;0,1,0,11590,IDC7,String%20Current%205.2,5,A,2;0,1,0,11590,IDC8,String%20Current%205.3,5,A,3;0,1,0,12690,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							/*Amplus UPCL1 end*/

							/*Amplus UPCL2 Start*/

							else if($phase == "LALtag"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11588,Activepower_Total,Active Power,15,kW,4;0,1,0,10142,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							/*Amplus UPCL2 end*/
							/*Amplus Origami Start*/

							else if($phase == "Orgtag"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11615,Activepower_Total,Active Power,15,kW,4;0,1,0,12029,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "OMGweather"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12029,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12029,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,12029,Wind_Speed,Wind Speed,15,m/s,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter1
							else if($phase == "ORGINV1graph1" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12028,Pac,AC%20Power,15,kW,4;0,1,0,12029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV1graph2" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12028,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12028,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV1graph3" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12028,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12028,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12028,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12028,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter 2
							else if($phase == "ORGINV2graph1" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12024,Pac,AC%20Power,15,kW,4;0,1,0,12029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV2graph2" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12024,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12024,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV2graph3" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12024,GridMs.PhV.phsA,INV2%20AC%20Voltage-%20L1,15,V,8;0,1,0,12024,GridMs.PhV.phsB,INV2%20AC%20Voltage-%20L2,15,V,9;0,1,0,12024,GridMs.PhV.phsC,INV2%20AC%20Voltage-%20L3,15,V,5;0,1,0,12024,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter 3
							else if($phase == "ORGINV3graph1" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12021,Pac,AC%20Power,15,kW,4;0,1,0,12029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV3graph2" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12021,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12021,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV3graph3" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12021,GridMs.PhV.phsA,INV3%20AC%20Voltage-%20L1,15,V,8;0,1,0,12021,GridMs.PhV.phsB,INV3%20AC%20Voltage-%20L2,15,V,9;0,1,0,12021,GridMs.PhV.phsC,INV3%20AC%20Voltage-%20L3,15,V,5;0,1,0,12021,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//inverter 4
							else if($phase == "ORGINV4graph1" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12023,Pac,AC%20Power,15,kW,4;0,1,0,12029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV4graph2" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12023,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12023,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV4graph3" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12023,GridMs.PhV.phsA,INV4%20AC%20Voltage-%20L1,15,V,8;0,1,0,12023,GridMs.PhV.phsB,INV4%20AC%20Voltage-%20L2,15,V,9;0,1,0,12023,GridMs.PhV.phsC,INV4%20AC%20Voltage-%20L3,15,V,5;0,1,0,12023,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//inverter 5
							else if($phase == "ORGINV5graph1" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12027,Pac,AC%20Power,15,kW,4;0,1,0,12029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV5graph2" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12027,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12027,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV5graph3" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12027,GridMs.PhV.phsA,INV5%20AC%20Voltage-%20L1,15,V,8;0,1,0,12027,GridMs.PhV.phsB,INV5%20AC%20Voltage-%20L2,15,V,9;0,1,0,12027,GridMs.PhV.phsC,INV5%20AC%20Voltage-%20L3,15,V,5;0,1,0,12027,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//inverter 6
							else if($phase == "ORGINV6graph1" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12026,Pac,AC%20Power,15,kW,4;0,1,0,12029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV6graph2" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12026,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12026,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV6graph3" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12026,GridMs.PhV.phsA,INV6%20AC%20Voltage-%20L1,15,V,8;0,1,0,12026,GridMs.PhV.phsB,INV6%20AC%20Voltage-%20L2,15,V,9;0,1,0,12026,GridMs.PhV.phsC,INV6%20AC%20Voltage-%20L3,15,V,5;0,1,0,12026,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							// SMU
							else if($phase == "orgsmu1inv1" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12031,IDC1,String%20Current%201.1,5,A,15;0,1,0,12031,IDC2,String%20Current%201.2,5,A,1;0,1,0,12031,IDC3,String%20Current%201.3,5,A,2;0,1,0,12031,IDC4,String%20Current%201.4,5,A,3;0,1,0,12029,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "orgsmu1inv2" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12031,IDC5,String%20Current%202.1,5,A,15;0,1,0,12031,IDC6,String%20Current%202.2,5,A,1;0,1,0,12031,IDC7,String%20Current%202.3,5,A,2;0,1,0,12031,IDC8,String%20Current%202.4,5,A,3;0,1,0,12029,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "orgsmu1inv3" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12031,IDC9,String%20Current%203.1,5,A,15;0,1,0,12031,IDC10,String%20Current%203.2,5,A,1;0,1,0,12031,IDC11,String%20Current%203.3,5,A,2;0,1,0,12031,IDC12,String%20Current%203.4,5,A,3;0,1,0,12029,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "orgsmu1inv4" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12031,IDC13,String%20Current%204.1,5,A,15;0,1,0,12031,IDC14,String%20Current%204.2,5,A,1;0,1,0,12031,IDC15,String%20Current%204.3,5,A,2;0,1,0,12031,IDC16,String%20Current%204.4,5,A,3;0,1,0,12029,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "orgsmu2inv1" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12032,IDC1,String%20Current%205.1,5,A,15;0,1,0,12032,IDC2,String%20Current%205.2,5,A,1;0,1,0,12032,IDC3,String%20Current%205.3,5,A,2;0,1,0,12032,IDC4,String%20Current%205.4,5,A,2;0,1,0,12029,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "orgsmu2inv2" && $park_no=="57"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12032,IDC5,String%20Current%206.1,5,A,15;0,1,0,12032,IDC6,String%20Current%206.2,5,A,1;0,1,0,12032,IDC7,String%20Current%206.3,5,A,2;0,1,0,12032,IDC8,String%20Current%206.4,5,A,2;0,1,0,12029,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							/*Amplus Origami end*/
							/*Amplus Polymer Start*/

							else if($phase == "Polytag"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12037,Activepower_Total,Active Power,15,kW,4;0,1,0,12417,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Polyweather"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12417,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12417,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12417,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,12417,Wind_Speed,Wind Speed,15,m/s,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "polysmu1inv1" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12421,IDC1,String%20Current%201.1,5,A,15;0,1,0,12421,IDC2,String%20Current%201.2,5,A,1;0,1,0,12421,IDC3,String%20Current%201.3,5,A,2;0,1,0,12421,IDC4,String%20Current%201.4,5,A,3;0,1,0,12421,IDC5,String%20Current%201.5,5,A,4;0,1,0,12421,IDC6,String%20Current%201.6,5,A,6;0,1,0,12421,IDC7,String%20Current%201.7,5,A,7;0,1,0,12421,IDC8,String%20Current%201.8,5,A,8;0,1,0,12421,IDC9,String%20Current%201.9,5,A,9;0,1,0,12421,IDC10,String%20Current%201.10,5,A,10;0,1,0,12421,IDC11,String%20Current%201.11,5,A,11;0,1,0,12417,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "polysmu2inv2" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12420,IDC1,String%20Current%202.1,5,A,15;0,1,0,12420,IDC2,String%20Current%202.2,5,A,1;0,1,0,12031,IDC3,String%20Current%202.3,5,A,2;0,1,0,12420,IDC4,String%20Current%202.4,5,A,3;0,1,0,12420,IDC5,String%20Current%202.5,5,A,4;0,1,0,12420,IDC6,String%20Current%202.6,5,A,6;0,1,0,12420,IDC7,String%20Current%202.7,5,A,7;0,1,0,12420,IDC8,String%20Current%202.8,5,A,8;0,1,0,12420,IDC9,String%20Current%202.9,5,A,9;0,1,0,12420,IDC10,String%20Current%202.10,5,A,10;0,1,0,12420,IDC11,String%20Current%202.11,5,A,11;0,1,0,12417,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "polysmu3inv3" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12419,IDC1,String%20Current%203.1,5,A,15;0,1,0,12419,IDC2,String%20Current%203.2,5,A,1;0,1,0,12419,IDC3,String%20Current%203.3,5,A,2;0,1,0,12419,IDC4,String%20Current%203.4,5,A,3;0,1,0,12421,IDC5,String%20Current%203.5,5,A,4;0,1,0,12419,IDC6,String%20Current%203.6,5,A,6;0,1,0,12419,IDC7,String%20Current%203.7,5,A,7;0,1,0,12419,IDC8,String%20Current%202.8,5,A,8;0,1,0,12419,IDC9,String%20Current%203.9,5,A,9;0,1,0,12419,IDC10,String%20Current%203.10,5,A,10;0,1,0,12419,IDC11,String%20Current%203.11,5,A,11;0,1,0,12417,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter1
							else if($phase == "PolyINV1graph1" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12660,PAC_Total,AC%20Power,15,kW,4;0,1,0,12417,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "PolyINV1graph2" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12660,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12660,IDC_Mittelwert,DC%20Current,15,A,7">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "PolyINV1graph3" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12660,UAC_L1/N,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12660,UAC_L2/N,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12660,UAC_L3/N,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12660,Frequenz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter 2
							else if($phase == "PolyINV2graph1" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12659,PAC_Total,AC%20Power,15,kW,4;0,1,0,12417,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "PolyINV2graph2" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12659,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12659,IDC_Mittelwert,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "PolyINV2graph3" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12659,UAC_L1/N,INV2%20AC%20Voltage-%20L1,15,V,8;0,1,0,12659,UAC_L2/N,INV2%20AC%20Voltage-%20L2,15,V,9;0,1,0,12659,UAC_L3/N,INV2%20AC%20Voltage-%20L3,15,V,5;0,1,0,12660,Frequenz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter 3
							else if($phase == "PolyINV3graph1" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12658,PAC_Total,AC%20Power,15,kW,4;0,1,0,12417,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "PolyINV3graph2" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12658,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12658,IDC_Mittelwert,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "PolyINV3graph3" && $park_no=="58"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12658,UAC_L1/N,INV3%20AC%20Voltage-%20L1,15,V,8;0,1,0,12658,UAC_L2/N,INV3%20AC%20Voltage-%20L2,15,V,9;0,1,0,12658,UAC_L3/N,INV3%20AC%20Voltage-%20L3,15,V,5;0,1,0,12660,Frequenz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							/*Amplus Polymer end*/
							/*Amplus Lalpur Start*/

							else if($phase == "LALtag"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11588,Activepower_Total,Active Power,15,kW,4;0,1,0,10142,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter1
							else if($phase == "LAPINV1graph1" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12669,PAC_Total,AC%20Power,15,kW,4;0,1,0,10142,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV1graph2" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12669,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12669,IDC_Mittelwert,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV1graph3" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12669,UAC_L1/N,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12669,UAC_L2/N,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12669,UAC_L3/N,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12669,Frequenz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter 2
							else if($phase == "LAPINV2graph1" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12672,PAC_Total,AC%20Power,15,kW,4;0,1,0,10142,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV2graph2" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12672,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12672,IDC_Mittelwert,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV2graph3" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12672,UAC_L1/N,INV2%20AC%20Voltage-%20L1,15,V,8;0,1,0,12672,UAC_L2/N,INV2%20AC%20Voltage-%20L2,15,V,9;0,1,0,12672,UAC_L3/N,INV2%20AC%20Voltage-%20L3,15,V,5;0,1,0,12672,Frequenz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter 3
							else if($phase == "LAPINV3graph1" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12670,PAC_Total,AC%20Power,15,kW,4;0,1,0,10142,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV3graph2" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12670,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12670,IDC_Mittelwert,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV3graph3" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12670,UAC_L1/N,INV3%20AC%20Voltage-%20L1,15,V,8;0,1,0,12670,UAC_L2/N,INV3%20AC%20Voltage-%20L2,15,V,9;0,1,0,12670,UAC_L3/N,INV3%20AC%20Voltage-%20L3,15,V,5;0,1,0,12670,Frequenz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//inverter 4
							else if($phase == "LAPINV4graph1" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12675,PAC_Total,AC%20Power,15,kW,4;0,1,0,10142,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV4graph2" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12675,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12675,IDC_Mittelwert,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV4graph3" && $park_no=="55"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12675,UAC_L1/N,INV3%20AC%20Voltage-%20L1,15,V,8;0,1,0,12675,UAC_L2/N,INV3%20AC%20Voltage-%20L2,15,V,9;0,1,0,12675,UAC_L3/N,INV3%20AC%20Voltage-%20L3,15,V,5;0,1,0,12675,Frequenz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							/*Amplus Yamaha Start*/

							else if($phase == "Yamahatag"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12895,Activepower_Total,Block A,15,kW,1;0,1,0,12899,Activepower_Total,Block B,15,kW,2;0,1,0,12897,Activepower_Total,Block C,15,kW,3;0,1,0,12898,Activepower_Total,Block D,15,kW,5;0,1,0,12896,Activepower_Total,Block E,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamahaBlkA"){// EM 1
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12895,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamahaBlkB"){// EM 2
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12899,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamahaBlkC"){// EM 3
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12897,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamahaBlkD"){// EM 4
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12898,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamahaBlkE"){// EM 5
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12896,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Yamaweather"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12209,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12209,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,12209,Wind_Speed,Wind Speed,15,m/s,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							/* Block B SMU Start*/
							else if($phase == "Yamasmu4inv4" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12627,IDC01,String%20Current%204.1,5,A,15;0,1,0,12627,IDC02,String%20Current%204.2,5,A,1;0,1,0,12627,IDC03,String%20Current%204.3,5,A,2;0,1,0,12627,IDC04,String%20Current%204.4,5,A,3;0,1,0,12209,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Yamasmu3inv5" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12628,IDC01,String%20Current%205.1,5,A,15;0,1,0,12628,IDC02,String%20Current%205.2,5,A,1;0,1,0,12628,IDC03,String%20Current%205.3,5,A,2;0,1,0,12628,IDC04,String%20Current%205.4,5,A,3;0,1,0,12628,IDC05,String%20Current%205.5,5,A,4;0,1,0,12628,IDC06,String%20Current%205.6,5,A,6;0,1,0,12628,IDC07,String%20Current%205.7,5,A,7;0,1,0,12628,IDC08,String%20Current%205.8,5,A,8;0,1,0,12628,IDC09,String%20Current%205.9,5,A,9;0,1,0,12628,IDC10,String%20Current%205.10,5,A,18;0,1,0,12628,IDC11,String%20Current%205.11,5,A,11;0,1,0,12209,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							/* Block B SMU Start*/

							/* Block C SMU Start*/

							else if($phase == "Yamasmu9inv6" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12893,IDC01,String%20Current%206.1,5,A,15;0,1,0,12893,IDC02,String%20Current%206.2,5,A,1;0,1,0,12893,IDC03,String%20Current%206.3,5,A,2;0,1,0,12893,IDC04,String%20Current%206.4,5,A,3;0,1,0,12893,IDC05,String%20Current%206.5,5,A,4;0,1,0,12893,IDC06,String%20Current%206.6,5,A,6;0,1,0,12893,IDC07,String%20Current%206.7,5,A,7;0,1,0,12893,IDC08,String%20Current%206.8,5,A,8;0,1,0,12893,IDC09,String%20Current%206.9,5,A,9;0,1,0,12893,IDC10,String%20Current%206.10,5,A,12;0,1,0,12209,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							/* Block C SMU Start*/
							/* Block D SMU Start*/

							else if($phase == "Yamasmu5inv7" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12894,IDC01,String%20Current%207.1,5,A,15;0,1,0,12894,IDC02,String%20Current%207.2,5,A,1;0,1,0,12894,IDC03,String%20Current%207.3,5,A,2;0,1,0,12894,IDC04,String%20Current%207.4,5,A,3;0,1,0,12894,IDC05,String%20Current%207.5,5,A,4;0,1,0,12894,IDC06,String%20Current%207.6,5,A,6;0,1,0,12894,IDC07,String%20Current%207.7,5,A,7;0,1,0,12209,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							/* Block D SMU Start*/

							/* Block E SMU Start*/
							else if($phase == "Yamasmu8inv10" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12630,IDC01,String%20Current%208.1,5,A,15;0,1,0,12630,IDC02,String%20Current%208.2,5,A,1;0,1,0,12630,IDC03,String%20Current%208.3,5,A,2;0,1,0,12630,IDC04,String%20Current%208.4,5,A,3;0,1,0,12630,IDC05,String%20Current%208.5,5,A,4;0,1,0,12630,IDC06,String%20Current%208.6,5,A,6;0,1,0,12630,IDC07,String%20Current%208.7,5,A,7;0,1,0,12630,IDC08,String%20Current%208.8,5,A,8;0,1,0,12630,IDC09,String%20Current%208.9,5,A,9;0,1,0,12630,IDC10,String%20Current%208.10,5,A,12;0,1,0,12209,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Yamasmu2inv8" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12631,IDC01,String%20Current%209.1,5,A,15;0,1,0,12631,IDC02,String%20Current%209.2,5,A,1;0,1,0,12631,IDC03,String%20Current%209.3,5,A,2;0,1,0,12631,IDC04,String%20Current%209.4,5,A,3;0,1,0,12631,IDC05,String%20Current%209.5,5,A,4;0,1,0,12631,IDC06,String%20Current%209.6,5,A,6;0,1,0,12631,IDC07,String%20Current%209.7,5,A,7;0,1,0,12631,IDC08,String%20Current%209.8,5,A,8;0,1,0,12631,IDC09,String%20Current%209.9,5,A,9;0,1,0,12631,IDC10,String%20Current%209.10,5,A,12;0,1,0,12209,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Yamasmu10inv9" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12629,IDC01,String%20Current%2010.1,5,A,15;0,1,0,12629,IDC02,String%20Current%2010.2,5,A,1;0,1,0,12629,IDC03,String%20Current%2010.3,5,A,2;0,1,0,12629,IDC04,String%20Current%2010.4,5,A,3;0,1,0,12629,IDC05,String%20Current%2010.5,5,A,4;0,1,0,12629,IDC06,String%20Current%2010.6,5,A,6;0,1,0,12629,IDC07,String%20Current%2010.7,5,A,7;0,1,0,12629,IDC08,String%20Current%2010.8,5,A,8;0,1,0,12629,IDC09,String%20Current%2010.9,5,A,9;0,1,0,12629,IDC10,String%20Current%2010.10,5,A,12;0,1,0,12209,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							/* Block E SMU Start*/
							//inverter1
							else if($phase == "YamaINV1graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12903,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV1graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12903,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12901,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV1graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12903,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12903,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12903,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12902,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter2
							else if($phase == "YamaINV2graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12901,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV2graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12901,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12901,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV2graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12901,GridMs.PhV.phsA,INV2%20AC%20Voltage-%20L1,15,V,8;0,1,0,12901,GridMs.PhV.phsB,INV2%20AC%20Voltage-%20L2,15,V,9;0,1,0,12901,GridMs.PhV.phsC,INV2%20AC%20Voltage-%20L3,15,V,5;0,1,0,12902,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter3
							else if($phase == "YamaINV3graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12902,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV3graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12902,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12902,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV3graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12902,GridMs.PhV.phsA,INV3%20AC%20Voltage-%20L1,15,V,8;0,1,0,12902,GridMs.PhV.phsB,INV3%20AC%20Voltage-%20L2,15,V,9;0,1,0,12902,GridMs.PhV.phsC,INV3%20AC%20Voltage-%20L3,15,V,5;0,1,0,12902,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//inverter4
							else if($phase == "YamaINV4graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12900,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV4graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12900,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12900,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV4graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12900,GridMs.PhV.phsA,INV4%20AC%20Voltage-%20L1,15,V,8;0,1,0,12900,GridMs.PhV.phsB,INV4%20AC%20Voltage-%20L2,15,V,9;0,1,0,12900,GridMs.PhV.phsC,INV4%20AC%20Voltage-%20L3,15,V,5;0,1,0,12900,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter5
							else if($phase == "YamaINV5graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12740,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV5graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12740,UAC,DC%20Voltage,15,V,8;0,1,0,12740,IDC,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV5graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12740,UAC1,INV5%20AC%20Voltage-%20L1,15,V,8;0,1,0,12740,UAC2,INV5%20AC%20Voltage-%20L2,15,V,9;0,1,0,12740,UAC3,INV5%20AC%20Voltage-%20L3,15,V,5;0,1,0,12740,FAC,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter6
							else if($phase == "YamaINV6graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12739,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV6graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12739,UAC,DC%20Voltage,15,V,8;0,1,0,12739,IDC,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV6graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12739,UAC1,INV6%20AC%20Voltage-%20L1,15,V,8;0,1,0,12739,UAC2,INV6%20AC%20Voltage-%20L2,15,V,9;0,1,0,12739,UAC3,INV6%20AC%20Voltage-%20L3,15,V,5;0,1,0,12739,FAC,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter7
							else if($phase == "YamaINV7graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12741,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV7graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12741,UAC,DC%20Voltage,15,V,8;0,1,0,12741,IDC,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV7graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12741,UAC1,INV7%20AC%20Voltage-%20L1,15,V,8;0,1,0,12741,UAC2,INV7%20AC%20Voltage-%20L2,15,V,9;0,1,0,12741,UAC3,INV7%20AC%20Voltage-%20L3,15,V,5;0,1,0,12741,FAC,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter8
							else if($phase == "YamaINV8graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12737,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV8graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12737,UAC,DC%20Voltage,15,V,8;0,1,0,12737,IDC,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV8graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12737,UAC1,INV8%20AC%20Voltage-%20L1,15,V,8;0,1,0,12737,UAC2,INV8%20AC%20Voltage-%20L2,15,V,9;0,1,0,12737,UAC3,INV8%20AC%20Voltage-%20L3,15,V,5;0,1,0,12737,FAC,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter 9
							else if($phase == "YamaINV9graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12738,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV9graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12738,UAC,DC%20Voltage,15,V,8;0,1,0,12738,IDC,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV9graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12738,UAC1,INV8%20AC%20Voltage-%20L1,15,V,8;0,1,0,12738,UAC2,INV8%20AC%20Voltage-%20L2,15,V,9;0,1,0,12738,UAC3,INV8%20AC%20Voltage-%20L3,15,V,5;0,1,0,12738,FAC,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter 10
							else if($phase == "YamaINV10graph1" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12736,PAC,AC%20Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV10graph2" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12736,UAC,DC%20Voltage,15,V,8;0,1,0,12736,IDC,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV10graph3" && $park_no=="59"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12737,UAC1,INV8%20AC%20Voltage-%20L1,15,V,8;0,1,0,12737,UAC2,INV8%20AC%20Voltage-%20L2,15,V,9;0,1,0,12737,UAC3,INV8%20AC%20Voltage-%20L3,15,V,5;0,1,0,12737,FAC,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							/*Amplus Yamaha end*/

							else if($phase == "MasINV" && $park_no=="21"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12909,Pac,INV1%20AC%20Power,15,kW,1;0,1,0,12911,Pac,INV2%20AC%20Power,15,kW,2;0,1,0,12910,Pac,INV3%20AC%20Power,15,kW,3;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							else if($phase == "MASWS" && $park_no=="21"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12907,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12907,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,12907,MODULE_TEMP ,Module Temperature,15,°C,\'green\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							else if($phase == "MasEM" && $park_no=="21"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12908,Activepower_Total,AC%20Power,15,kW,1;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//inverter1
							else if($phase == "RUDINV1graph1" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11621,PAC,AC%20Power,15,kW,4;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV1graph2" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11621,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11621,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV1graph3" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11621,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,11621,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12903,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12902,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter2
							else if($phase == "RUDINV2graph1" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11618,PAC,AC%20Power,15,kW,4;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV2graph2" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11618,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11618,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV2graph3" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11618,GridMs.PhV.phsA,INV2%20AC%20Voltage-%20L1,15,V,8;0,1,0,11618,GridMs.PhV.phsB,INV2%20AC%20Voltage-%20L2,15,V,9;0,1,0,11618,GridMs.PhV.phsC,INV2%20AC%20Voltage-%20L3,15,V,5;0,1,0,11618,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter3
							else if($phase == "RUDINV3graph1" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11617,PAC,AC%20Power,15,kW,4;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV3graph2" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11617,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11617,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV3graph3" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11617,GridMs.PhV.phsA,INV3%20AC%20Voltage-%20L1,15,V,8;0,1,0,11617,GridMs.PhV.phsB,INV3%20AC%20Voltage-%20L2,15,V,9;0,1,0,11617,GridMs.PhV.phsC,INV3%20AC%20Voltage-%20L3,15,V,5;0,1,0,11617,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//inverter4
							else if($phase == "RUDINV4graph1" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11620,PAC,AC%20Power,15,kW,4;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV4graph2" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11620,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11620,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV4graph3" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11620,GridMs.PhV.phsA,INV4%20AC%20Voltage-%20L1,15,V,8;0,1,0,11620,GridMs.PhV.phsB,INV4%20AC%20Voltage-%20L2,15,V,9;0,1,0,11620,GridMs.PhV.phsC,INV4%20AC%20Voltage-%20L3,15,V,5;0,1,0,11620,GridMs.Hz,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//inverter5
							else if($phase == "RUDINV5graph1" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11619,PAC,AC%20Power,15,kW,4;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV5graph2" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11619,UAC,DC%20Voltage,15,V,8;0,1,0,11619,IDC,DC%20Current,15,A,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV5graph3" && $park_no=="56"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,11619,UAC1,INV5%20AC%20Voltage-%20L1,15,V,8;0,1,0,11619,UAC2,INV5%20AC%20Voltage-%20L2,15,V,9;0,1,0,11619,UAC3,INV5%20AC%20Voltage-%20L3,15,V,5;0,1,0,11619,FAC,Frequency,15,Hz,7;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDweather"){
								echo '<a href="export3.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12690,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12690,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12690,Module_Temperature,Module_Temperature,15,°C,\'green\';">';
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
