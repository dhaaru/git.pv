<?php
############################################################
# transited, Influx-clean.
# 'amplus_all_calculations' 
############################################################ 
# dropped usage of _devicedatavalue
# dropped Activepower_Total
#    only occurence of _devicedatavalue was:
#    line 160:  if $args[4]=="Activepower_Total"
############################################################


if (!isset($delta)) {
    $delta = 0;
}
//if($park_no == 43)
//{
//$sourcetable = "pune_calculation ";
//}
//if($park_no == 36){
//$sourcetable = "amplus_calculation ";
//}
//if($park_no == 39){
//$sourcetable = "mumbai_calculation ";
//}
//if($park_no == 46){
//$sourcetable = "raisoni3_calculation ";
//}
//if($park_no == 52){
//$sourcetable = "ampdominos_calculation ";
//}
//if($park_no == 53){
//$sourcetable = "ampRoyal_calculation ";
//}
//if($park_no == 54){
//$sourcetable = "ampIndus_calculation ";
//}
//if($park_no == 57){
//$sourcetable = "origami_calculation ";
//}
//if($park_no == 55){
//$sourcetable = "ampLalpur_calculation ";
//}
//if($park_no == 56){
//$sourcetable = "amprudrapur_calculation ";
//}
//if($park_no == 58){
//$sourcetable = "polymers_calculation ";
//}
//if($park_no == 59){
//$sourcetable = "yamaha_calculation ";
//}
$sourcetable = "amplus_all_calculations";


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
$displayItems = array();
ksort($displayItems);
$values = array();
$axis = array();
$devices = array();
$digitals = 0;
$argIndex = 0;
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

		//if($translatedField=="Activepower_Total"){
      // _fieldvalue  the only one occurence of $sourcetable1
			// $query = "select ts+19800 as ts, (((value+$words[0])*$words[1])+$words[2]) as value from $sourcetable1 where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
    //  }
		//else {
     $query = "SELECT ts+19800 as ts, (((value+$words[0])*$words[1])+$words[2]) as value 
      FROM $sourcetable 
      WHERE park_no=$park_no
      AND value is not null and device = $words[3] and field = '$translatedField' 
      AND ts > $stamp and ts < $endstamp";
		//}
	//exit;
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

			if($translatedField=="Solar_Radiation"){
			 $lastValue= ($row_ds2[value] * 84 * (1.956*0.992) * (15.5/100))/1000;
			 echo "<br>". $row_ds2[value];
			 echo "<br>".  $lastValue;
			 echo "test1";
			}
		else {
            $lastValue = $row_ds2[value];
			echo "test2";
		}
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
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.time.min.js"></script>
    </head>
    <body>

        <div style="height: 98%; width: 100%">
            <div style="float: left; height: 99%; width: 84%; padding-top: 2px">
                <form name=ThisForm method="post" action="" target="_self">

                    <div>
                       <div style="font-family:Verdana, Geneva, sans-serif; font-size:13px; color:darkblue; font-weight:bold; padding-bottom:2px">
                            <?php
                            echo $title;
                            print (" - energydiagram influx-clean version 'amplus_calculations_all'");

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
							else if($phase == "energyg2" && $park_no=="36"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,7795,System_PR,System PR,5,%,1;0,1,0,7794,En_irradiation,Irradiation,5,W/m&sup2;,\'Gold\';0,1,0,7794,Energy_Module_Temp,Module Temperature,5,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "energyg3" && $park_no=="36"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,7792,Inv_PR,Inverter1%20PR,5,%,4;0,1,0,7791,Inv_PR,Inverter2%20PR,5,%,5;0,1,0,7794,Inv_PR,Inverter3%20PR,5,%,6;0,1,0,7794,Inv_irrad_250,Irradiation,5,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_250,Module Temperature,5,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							//Pune energy diagram
							else if($phase == "INV1G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8379,Inv_PR,Inv1(REFSU1)%20PR,15,%,4;0,1,0,8414,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV2G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8408,Inv2_PR,Inv2(SMA1)%20PR,15,%,4;0,1,0,8414.8412,Inv2_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414.8412,Inv2_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV3G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8410,Inv3_PR,Inv3(SMA2)%20PR,15,%,4;0,1,0,8414,Inv3_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv3_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "INV4G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8409,Inv4_PR,Inv4(SMA3)%20PR,15,%,4;0,1,0,8412,Inv4_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv4_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "INV5G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8458,Inv5_PR,Inv5(SMA4)%20PR,15,%,5;0,1,0,8456,Inv6_PR,Inv6(SMA5)%20PR,15,%,4;0,1,0,8412,Inv5_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv5_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV7G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8381,Inv7_PR,Inv7(REFSU2)%20PR,15,%,4;0,1,0,8414.8412,Inv7_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414.8412,Inv7_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "INV8G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12976,Inv8_PR,Inv8(SM6)%20PR,15,%,4;0,1,0,8414,Inv8_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv8_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV9G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8378,Inv9_PR,Inv9(REFSU3)%20PR,15,%,4;0,1,0,8412,Inv9_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv9_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "INV10G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8384,Inv10_PR,Inv10(SMA7)%20PR,15,%,4;0,1,0,8382,Inv11_PR,Inv11(SMA8)%20PR,15,%,5;0,1,0,8383,Inv12_PR,Inv12(SMA9)%20PR,15,%,8;0,1,0,8414,Inv10_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv8_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "INV13G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8380,Inv13_PR,Inv13(REFSU4)%20PR,15,%,4;0,1,0,8414,Inv13_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv13_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "INV14G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8386,Inv14_PR,Inv14(SMA10)%20PR,15,%,4;0,1,0,8414.8412,Inv14_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414.8412,Inv14_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "INV15G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8387,Inv15_PR,Inv15(SMA11)%20PR,15,%,4;0,1,0,8412,Inv15_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv15_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "INV16G4" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8388,Inv16_PR,Inv16(SMA11)%20PR,15,%,4;0,1,0,8412,Inv16_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv16_AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Amplus Mumbai start */


							else if($phase == "energyg2" && $park_no=="39"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9317,System_PR,System PR,15,%,1;0,1,0,9316,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA1M4" && $park_no=="39"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9323,Inv_PR,Inv1%20PR,15,%,4;0,1,0,9316,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA2M4" && $park_no=="39"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9328,Inv_PR,Inv2%20PR,15,%,4;0,1,0,9316,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA3M4" && $park_no=="39"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9329,Inv_PR,Inv3%20PR,15,%,4;0,1,0,9316,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA4M4" && $park_no=="39"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9322,Inv_PR,Inv4%20PR,15,%,4;0,1,0,9316,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA5M4" && $park_no=="39"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9324,Inv_PR,Inv5%20PR,15,%,4;0,1,0,9316,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA6M4" && $park_no=="39"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9326,Inv_PR,Inv6%20PR,15,%,4;0,1,0,9316,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA7M4" && $park_no=="39"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9325,Inv7_PR,Inverter7%20PR,15,%,4;0,1,0,9316,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "SMA8M4" && $park_no=="39"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9327,Inv_PR,Inv8%20PR,15,%,4;0,1,0,9316,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							// Amplus Mumbai End */

							// Amplus Raisoni3 Nagpur start
							else if($phase == "energyg2" && $park_no=="46"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9958,System_PR,System PR,15,%,1;0,1,0,9957,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9957,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INVPR" && $park_no=="46"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9971,Inv_PR,Inv1%20PR,15,%,4;0,1,0,9972,Inv_PR,Inv2%20PR,15,%,11;0,1,0,9973,Inv_PR,Inv3%20PR,15,%,8;0,1,0,9957,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9957,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Amplus Raisoni3 Nagpur End

							// Amplus Dominos Nagpur start
							else if($phase == "energyg2" && $park_no=="52"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,10070,System_PR,System PR,15,%,1;0,1,0,11623,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11623,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV1PR" && $park_no=="52"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11624,Inv_PR,Inv1%20PR,15,%,4;0,1,0,11623,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11623,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV2PR" && $park_no=="52"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11627,Inv_PR,Inv2%20PR,15,%,4;0,1,0,11623,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11623,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV3PR" && $park_no=="52"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11625,Inv_PR,Inv3%20PR,15,%,4;0,1,0,11623,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11623,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV4PR" && $park_no=="52"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11626,Inv_PR,Inv4%20PR,15,%,4;0,1,0,11623,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11623,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV5PR" && $park_no=="52"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11628,Inv_PR,Inv5%20PR,15,%,4;0,1,0,11623,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11623,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Amplus Dominos Nagpur End

							// Amplus Royal  Pune start
							else if($phase == "energyg2" && $park_no=="53"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,10071,System_PR,System PR,15,%,1;0,1,0,12692,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV1PR" && $park_no=="53"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11721,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12692,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV2PR" && $park_no=="53"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11718,Inv_PR,Inv2%20PR,15,%,4;0,1,0,12692,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV3PR" && $park_no=="53"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11719,Inv_PR,Inv3%20PR,15,%,4;0,1,0,12692,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV4PR" && $park_no=="53"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11715,Inv_PR,Inv4%20PR,15,%,4;0,1,0,12692,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV5PR" && $park_no=="53"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11720,Inv_PR,Inv5%20PR,15,%,4;0,1,0,12692,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV6PR" && $park_no=="53"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11716,Inv_PR,Inv6%20PR,15,%,4;0,1,0,12692,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV7PR" && $park_no=="53"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11717,Inv_PR,Inv7%20PR,15,%,4;0,1,0,12692,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV8PR" && $park_no=="53"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11722,Inv_PR,Inv8%20PR,15,%,4;0,1,0,12692,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							//Amplus Royal Pune End
							// Amplus Raisoni3 Nagpur start
							else if($phase == "energyg1Indus" && $park_no=="54"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,10151,System_PR,System PR,15,%,1;0,1,0,12668,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12668,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INVPR" && $park_no=="54"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,9971,Inv_PR,Inv1%20PR,15,%,4;0,1,0,9972,Inv_PR,Inv2%20PR,15,%,11;0,1,0,9973,Inv_PR,Inv3%20PR,15,%,8;0,1,0,9957,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9957,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Orgenergyg2" && $park_no=="57"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,11615,System_PR,System PR,15,%,1;0,1,0,12029,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12029,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV1PR" && $park_no=="57"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12028,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12029,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12029,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV2PR" && $park_no=="57"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12024,Inv_PR,Inv2%20PR,15,%,4;0,1,0,12029,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12029,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV3PR" && $park_no=="57"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12021,Inv_PR,Inv3%20PR,15,%,4;0,1,0,12029,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12029,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV4PR" && $park_no=="57"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12023,Inv_PR,Inv4%20PR,15,%,4;0,1,0,12029,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12029,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV5PR" && $park_no=="57"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12027,Inv_PR,Inv5%20PR,15,%,4;0,1,0,12029,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12029,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "ORGINV6PR" && $park_no=="57"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12026,Inv_PR,Inv6%20PR,15,%,4;0,1,0,12029,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12029,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Lalenergyg" && $park_no=="55"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11588,System_PR,System PR,15,%,1;0,1,0,10142,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,10142,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV1PR" && $park_no=="55"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12669,Inv_PR,Inv1%20PR,15,%,4;0,1,0,10142,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,10142,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV2PR" && $park_no=="55"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12672,Inv_PR,Inv1%20PR,15,%,4;0,1,0,10142,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,10142,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV3PR" && $park_no=="55"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12670,Inv_PR,Inv1%20PR,15,%,4;0,1,0,10142,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,10142,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "LAPINV4PR" && $park_no=="55"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12675,Inv_PR,Inv1%20PR,15,%,4;0,1,0,10142,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,10142,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							else if($phase == "Polyenergyg2" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12037,System_PR,System PR,15,%,1;0,1,0,12417,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "PolyINV1PR" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12660,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12417,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "PolyINV2PR" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12659,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12417,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "PolyINV3PR" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12658,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12417,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "IndINV1PR" && $park_no=="54"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12667,Inv_PR,Inv%20PR,15,%,4;0,1,0,10452,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,10452,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "IndINV2PR" && $park_no=="54"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12668,Inv_PR,Inv%20PR,15,%,4;0,1,0,10452,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,10452,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "Yamahaenergyg2" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12895,System_PR,Block A PR,15,%,1;0,1,0,12899,System_PR,Block B PR,15,%,2;0,1,0,12897,System_PR,Block C PR,15,%,3;0,1,0,12898,System_PR,Block D PR,15,%,5;0,1,0,12896,System_PR,Block E PR,15,%,4;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamahaEnBlkA" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12895,System_PR,System PR,15,%,1;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "YamahaEnBlkB" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12899,System_PR,System PR,15,%,1;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "YamahaEnBlkC" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12897,System_PR,System PR,15,%,1;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "YamahaEnBlkD" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12898,System_PR,System PR,15,%,1;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}else if($phase == "YamahaEnBlkE" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12896,System_PR,System PR,15,%,1;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}



							else if($phase == "YamaINV1PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12900,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12900,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12900,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}

							else if($phase == "YamaINV2PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12901,Inv_PR,Inv2%20PR,15,%,4;0,1,0,12209,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV3PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12902,Inv_PR,Inv3%20PR,15,%,4;0,1,0,12209,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV4PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12900,Inv_PR,Inv4%20PR,15,%,4;0,1,0,12209,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV5PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12740,Inv_PR,Inv5%20PR,15,%,4;0,1,0,12209,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV6PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12739,Inv_PR,Inv6%20PR,15,%,4;0,1,0,12209,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV7PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12741,Inv_PR,Inv7%20PR,15,%,4;0,1,0,12209,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV8PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12737,Inv_PR,Inv8%20PR,15,%,4;0,1,0,12209,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV9PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12738,Inv_PR,Inv9%20PR,15,%,4;0,1,0,12209,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "YamaINV10PR" && $park_no=="59"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,12736,Inv_PR,Inv10%20PR,15,%,4;0,1,0,12209,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV1PR" && $park_no=="56"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11621,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12690,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12690,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV2PR" && $park_no=="56"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11618,Inv_PR,Inv2%20PR,15,%,4;0,1,0,12690,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12690,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV3PR" && $park_no=="56"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11617,Inv_PR,Inv3%20PR,15,%,4;0,1,0,12690,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12690,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV4PR" && $park_no=="56"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11620,Inv_PR,Inv4%20PR,15,%,4;0,1,0,12690,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12690,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDINV5PR" && $park_no=="56"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3&args=0,1,0,11619,Inv_PR,Inv5%20PR,15,%,4;0,1,0,12690,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12690,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "RUDenergyg2" && $park_no=="56"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,7772,System_PR,System PR,15,%,1;0,1,0,12690,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12690,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'">';
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
					}///export access permission end

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
