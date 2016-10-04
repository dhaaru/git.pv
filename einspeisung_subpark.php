<?php

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Asia/Calcutta');
}
//echo $anl_id;

if (!$_SESSION['id']) {
    session_start();
    $_SESSION['id'] = session_id();
}



$jahr_heute = date('Y');
$monat_heute = date('n');
$tag_heute = date('d');


if (!isset($park_no)) {
    if (!isset($_SESSION['park_no_s'])) {
        $park_no = 0;
    } else {
        $park_no = $_SESSION['park_no_s'];
    }
}
$_SESSION['park_no_s'] = $park_no;

if (!isset($subpark_id)) {
    if (!isset($_SESSION['subpark_s'])) {
        $subpark_id = 0;
    } else {
        $subpark_id = $_SESSION['subpark_s'];
    }
}
$_SESSION['subpark_s'] = $subpark_id;

$area_id = 0;
$_SESSION['area_s'] = $area_id;


if (!isset($phase)) {
    if (!isset($_SESSION['phase_s'])) {
        $phase = "tag";
    } else {
        $phase = $_SESSION['phase_s'];
    }
}
$_SESSION['phase_s'] = $phase;


if (!isset($jahr)) {
    if (!isset($_SESSION['jahr_s'])) {
        $jahr = $jahr_heute;
    } else {
        $jahr = $_SESSION['jahr_s'];
    }
}
$_SESSION['jahr_s'] = $jahr;

if (!isset($mon)) {
    if (!isset($_SESSION['mon_s'])) {
        $mon = $monat_heute;
    } else {
        $mon = $_SESSION['mon_s'];
    }
}
$_SESSION['mon_s'] = $mon;

if (!isset($tag)) {
    if (!isset($_SESSION['tag_s'])) {
        $tag = $tag_heute;
    } else {
        $tag = $_SESSION['tag_s'];
    }
}
$_SESSION['tag_s'] = $tag;


if (!isset($phase)) {

    if (!isset($_SESSION['phase_s'])) {
        $phase = "tag";
    } else {
        $phase = $_SESSION['phase_s'];
    }
}
//include('functions/dgr_func_jpgraph.php');
//$wert = get_capacity_subpark_wr($subpark_id);
//echo $wert;
//return;
//echo "Phase: ".$phase."<br>";
//Datum
$jahr_heute = date('Y');
$monat_heute = date('n');
$tag_heute = date('d');

//Wenn Startzustand
if (!isset($mon)) {
    if (!isset($_SESSION['mon_s'])) {
        $mon = $monat_heute;
    } elseif ($phase != 'jahr') {
        $mon = $_SESSION['mon_s'];
    }
}
if (!isset($tag)) {
    if (!isset($_SESSION['tag_s'])) {
        $tag = $tag_heute;
    } elseif ($phase == 'tag') {
        $tag = $_SESSION['tag_s'];
    }
}
if (!isset($jahr)) {
    if (!isset($_SESSION['jahr_s'])) {
        $jahr = $jahr_heute;
    } else {
        $jahr = $_SESSION['jahr_s'];
    }
}
$_SESSION['tag_s'] = $tag;
$_SESSION['mon_s'] = $mon;
$_SESSION['jahr_s'] = $jahr;
$_SESSION['phase_s'] = $phase;

$stamp = mktime(0, 0, 0, $mon, $tag, $jahr);
$endstamp = 0;

if ($phase == "tag") {
    $endstamp = $stamp + 3600 * 24;
} else if ($phase == "mon") {
    $stamp = mktime(0, 0, 0, $mon, 1, $jahr);
    $endstamp = mktime(0, 0, 0, $mon + 1, 1, $jahr);
} else {
    $stamp = mktime(0, 0, 0, 1, 1, $jahr);
    $endstamp = mktime(0, 0, 0, 1, 1, $jahr + 1);
}

include('functions/de_datum.php');
require_once('connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
user_check();
include('functions/allg_functions.php');

$user_typ = get_user_attribut('usertyp');


if (!$diagtyp_sel || $diagtyp_sel == '') {

    if ($user_typ == 'User') {

        $_SESSION['diagtyp_s'] = 'pdc';
    } elseif ($wr_typ_park[$park_no] == 'siemens') {

        $_SESSION['diagtyp_s'] = 'idc';
    } elseif ($wr_typ_park[$park_no] == 'msb') {
        $_SESSION['diagtyp_s'] = 'wrspec';
    } elseif ($wr_typ_park[$park_no] == 'voltwerk') {
        $_SESSION['diagtyp_s'] = 'wrspec';
    } else {
        $_SESSION['diagtyp_s'] = 'e_total_spec';
    }
}


include('locale/gettext_header.php');

//echo "Phase :".$phase."<br>";

include('functions/b_breite.php');
//
include('functions/dgr_func_jpgraph.php');
include('functions/datum_formate.php');

$bezeichnung = get_property('bezeichnung', subparks, $subpark_id);
if ($bezeichnung == _('Subpark')) {
    $bezeichnung = _('Subpark') . $subpark_id;
}
//$bezeichnung="Treia ".$park_no.": ".$bezeichnung;
$bezeichnung = $_SESSION['park_name'] . ": " . $bezeichnung;
$_SESSION['subpark_name'] = $bezeichnung;
$_SESSION['teil_bez'] = $bezeichnung;

$meter_existent = get_attribute('meters', subparks, id, $subpark_id);
//echo "Meter: ".$meter_existent;
$capacity_sp = get_capacity_subpark($subpark_id);

if ($capacity_sp == 0) {
    $ueberschrift = $bezeichnung;
} else {
    if ($park_no == "20") {
        $capacity_sp = "2030";
        if ($subpark_id == 152 || $subpark_id == 153) {
            $capacity_sp = "1900";
        }

        $ueberschrift = $bezeichnung . " (" . $capacity_sp . " kWp)";
    } 
	else if ($park_no == "50") {
        $capacity_sp = "2000";
        $ueberschrift = $bezeichnung . " (" . $capacity_sp . " kWp)";
    }
	else {

        if ($capacity_sp > 0) {
            $ueberschrift = $bezeichnung . " (" . $capacity_sp . " kWp)";
        } else {
            $ueberschrift = $bezeichnung;
        }
    }
}
//Tage in jedem Monat des jahres ermitteln

$days_sum = tage_monat($jahr);
//Wieviel Tage hat der aufgerufene Monat
$tage2 = $days_sum[$mon];
$mx_tage2 = $tage2 + 1; //F�r die For-Schleife, da ab 1 und nicht 0...
$_SESSION['anz_tage_s'] = $tage2;

$abstand = 300; //Zwischen select und Titel
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
    <HEAD>
        <TITLE>-- Solarportal iPLON --</TITLE>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="css/scroll.css" rel="stylesheet" type="text/css">
        <link href="css/text.css" rel="stylesheet" type="text/css">
        <link href="css/style_add.css" rel="stylesheet" type="text/css">

    </HEAD>

    <?php
    if ($park_no == 10) {
        if ($subpark_id == 123) {
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,713,PAC,Inverter 1 Frame 1 AC Power,15,kW,1;0,1,0,678,PAC,Inverter 1 Frame 2 AC%20Power,15,kW,2;0,1,0,703,PAC,Inverter 2 Frame 1 AC%20Power,15,kW,0;0,1,0,709,PAC,Inverter 2 Frame 2 AC%20Power,15,kW,19&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,0.5,0,727,U3,Wind Speed,15,m/s,6;0,13,0,732,U1,Tilted Irradiation,15,W/m²,\'Gold\';0,13,0,732,U3,Horizontal Irradiation,15,W/m²,0;-30,1,0,727,U2,Ambient Temperature,15,°C,\'darkred\';0,1,0,727,T1,Module Surface Temperature,15,°C,\'indianred\'&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';

				$count = 2;
			}else{
			//
				$args = "0,0.001,0,678,E_Total,Block%201%20I1%20Master,1440,MWh,'green',1;0,0.001,0,713,E_Total,Block%201%20I1%20Slave,1440,MWh,'green',1;0,0.001,0,703,E_Total,Block%201%20I2%20Master,1440,MWh,'green',1;0,0.001,0,709,E_Total,Block%201%20I2%20Slave,1440,MWh,'green',1;0,1,0,732,U1,Tilted%20Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';

				$count = 1;
			}
        } else if ($subpark_id == 138) {
			if ($phase =="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,651,PAC,Inverter 3 Frame 1 AC Power,15,kW,1;0,1,0,567,PAC,Inverter 3 Frame 2 AC%20Power,15,kW,2;0,1,0,616,PAC,Inverter 4 Frame 1 AC%20Power,15,kW,0;0,1,0,621,PAC,Inverter 4 Frame 2 AC%20Power,15,kW,19&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,0.5,0,727,U3,Wind Speed,15,m/s,6;0,13,0,732,U1,Tilted Irradiation,15,W/m²,\'Gold\';0,13,0,732,U3,Horizontal Irradiation,15,W/m²,0;-30,1,0,727,U2,Ambient Temperature,15,°C,\'darkred\';0,1,0,727,T1,Module Surface Temperature,15,°C,\'indianred\'&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
				
				$count = 2;
			}else{
				$args = "0,0.001,0,651,E_Total,Block%202%20I3%20Master,1440,MWh,'blue',1;0,0.001,0,567,E_Total,Block%202%20I3%20Slave,1440,MWh,'blue',1;0,0.001,0,621,E_Total,Block%202%20I4%20Master,1440,MWh,'blue',1;0,0.001,0,616,E_Total,Block%202%20I4%20Slave,1440,MWh,'blue',1;0,1,0,732,U1,Tilted%20Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
					
				$count = 1;
			}			
        } else if ($subpark_id == 139) {
			if ($phase == "tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,607,PAC,Inverter 5 Frame 1 AC Power,15,kW,1;0,1,0,516,PAC,Inverter 5 Frame 2 AC%20Power,15,kW,2;0,1,0,479,PAC,Inverter 6 Frame 1 AC%20Power,15,kW,0;0,1,0,577,PAC,Inverter 6 Frame 2 AC%20Power,15,kW,19&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,0.5,0,727,U3,Wind Speed,15,m/s,6;0,13,0,732,U1,Tilted Irradiation,15,W/m²,\'Gold\';0,13,0,732,U3,Horizontal Irradiation,15,W/m²,0;-30,1,0,727,U2,Ambient Temperature,15,°C,\'darkred\';0,1,0,727,T1,Module Surface Temperature,15,°C,\'indianred\'&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
				
				$count = 2;
			}else{
				$args = "0,0.001,0,516,E_Total,Block%203%20I5%20Master,1440,MWh,'brown',1;0,0.001,0,607,E_Total,Block%203%20I5%20Slave,1440,MWh,'brown',1;0,0.001,0,577,E_Total,Block%203%20I6%20Master,1440,MWh,'brown',1;0,0.001,0,479,E_Total,Block%203%20I6%20Slave,1440,MWh,'brown',1;0,1,0,732,U1,Tilted%20Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				
				$count = 1;
			}				
        } else if ($subpark_id == 140) {
			if($phase == "tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,592,PAC,Inverter 1 Frame 1 AC Power,15,kW,1;0,1,0,660,PAC,Inverter 7 Frame 2 AC%20Power,15,kW,2;&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,0.5,0,727,U3,Wind Speed,15,m/s,6;0,13,0,732,U1,Tilted Irradiation,15,W/m²,\'Gold\';0,13,0,732,U3,Horizontal Irradiation,15,W/m²,0;-30,1,0,727,U2,Ambient Temperature,15,°C,\'darkred\';0,1,0,727,T1,Module Surface Temperature,15,°C,\'indianred\'&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';

				$count = 2;
			}else{
				$args = "0,0.001,0,660,E_Total,Block%204%20I7%20Master,1440,MWh,'red',1;0,0.001,0,592,E_Total,Block%204%20I7%20Slave,1440,MWh,'red',1;0,1,0,732,U1,Tilted%20Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				
				$count = 1;
			}
        } else if ($subpark_id == 141) {
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,529,PAC,Inverter 8 Frame 1 AC Power,15,kW,1;0,1,0,542,PAC,Inverter 8 Frame 2 AC%20Power,15,kW,2;0,1,0,519,PAC,Inverter 9 Frame 1 AC%20Power,15,kW,0;0,1,0,570,PAC,Inverter 9 Frame 2 AC%20Power,15,kW,19&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,0.5,0,727,U3,Wind Speed,15,m/s,6;0,13,0,732,U1,Tilted Irradiation,15,W/m²,\'Gold\';0,13,0,732,U3,Horizontal Irradiation,15,W/m²,0;-30,1,0,727,U2,Ambient Temperature,15,°C,\'darkred\';0,1,0,727,T1,Module Surface Temperature,15,°C,\'indianred\'&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
				
				$count = 2;
			 }else{
				$args = "0,0.001,0,542,E_Total,Block%205%20I8%20Master,1440,MWh,'orange',1;0,0.001,0,529,E_Total,Block%205%20I8%20Slave,1440,MWh,'orange',1;0,0.001,0,570,E_Total,Block%205%20I9%20Master,1440,MWh,'orange',1;0,0.001,0,519,E_Total,Block%205%20I9%20Slave,1440,MWh,'orange',1;0,1,0,732,U1,Tilted%20Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				
				$count=1;
			}
        }
        
    } else if ($park_no == 32){ //Padayala
		if ($subpark_id==238){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6259,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6259,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6259,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		else if ($subpark_id==239){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6463,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6463,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6463,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
	else if ($subpark_id==240){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6126,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6126,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6126,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
	else if ($subpark_id==241){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6053,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6053,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6053,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
	else if ($subpark_id==242){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6090,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6090,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6090,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
	else if ($subpark_id==243){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5997,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5997,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5997,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
	else if ($subpark_id==244){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6387,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6387,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6387,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
	else if ($subpark_id==245){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6296,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6296,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6296,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
	else if ($subpark_id==246){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6169,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6169,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6169,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
	else if ($subpark_id==247){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6089,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6089,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6089,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
	else if ($subpark_id==248){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6082,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6082,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6082,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
	else if ($subpark_id==249){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6446,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6446,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6446,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
	else if ($subpark_id==250){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,6030,PAC,AC%20Power,15,MW,1&diffs=0,1,0,6030,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,6030,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}		
		
	}else if ($park_no == 50) {
		if ($subpark_id == 168) {
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,1945,EM_Accord_Act_Pow,Active%20Power,15,MW,1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,\'Gold\';&diffs=0,1000,0,1945,EM_Accord_Act_Energy_Exp,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_Accord" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,3324,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,3324,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,3324,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,3324,Day_Rain,Day Rain,15,mm,3;0,1,0,3324,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,3324,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,3324,Outside_Humidity,Outside Humidity,15,%,\'Violet\';0,1,0,3325,AI_SPARE1,Module Surface Temperature Block3,15,°C,\'indianred\';0,1,0,4600,AI_SPARE1,Module Surface Temperature Block23,15,°C,\'orange\';0,1,0,1530,AI_SPARE1,Module Surface Temperature Block26,15,°C,\'blue\';0,1,0,2208,AI_SPARE1,Module Surface Temperature Block49,15,°C,\'red\'&etotal=0,1,0,1939_1946,EM_Accord_Act_Energy_Exp,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,1945,EM_Accord_Act_Energy_Exp,Yield (EM),1440,MWh,'green',1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		} else if ($subpark_id == 170) {
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,1953,EM_Accord_Act_Pow,Active%20Power,15,MW,1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,\'Gold\';&diffs=0,1000,0,1953,EM_Accord_Act_Energy_Exp,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_Accord" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,3324,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,3324,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,3324,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,3324,Day_Rain,Day Rain,15,mm,3;0,1,0,3324,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,3324,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,3324,Outside_Humidity,Outside Humidity,15,%,\'Violet\';0,1,0,3325,AI_SPARE1,Module Surface Temperature Block3,15,°C,\'indianred\';0,1,0,4600,AI_SPARE1,Module Surface Temperature Block23,15,°C,\'orange\';0,1,0,1530,AI_SPARE1,Module Surface Temperature Block26,15,°C,\'blue\';0,1,0,2208,AI_SPARE1,Module Surface Temperature Block49,15,°C,\'red\'&etotal=0,1,0,1939_1946,EM_Accord_Act_Energy_Exp,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,1953,EM_Accord_Act_Energy_Exp,Yield (EM),1440,MWh,'green',1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		} else if ($subpark_id == 171) {
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,1951,EM_Accord_Act_Pow,Active%20Power,15,MW,1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,\'Gold\';&diffs=0,1000,0,1951,EM_Accord_Act_Energy_Exp,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_Accord" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,3324,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,3324,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,3324,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,3324,Day_Rain,Day Rain,15,mm,3;0,1,0,3324,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,3324,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,3324,Outside_Humidity,Outside Humidity,15,%,\'Violet\';0,1,0,3325,AI_SPARE1,Module Surface Temperature Block3,15,°C,\'indianred\';0,1,0,4600,AI_SPARE1,Module Surface Temperature Block23,15,°C,\'orange\';0,1,0,1530,AI_SPARE1,Module Surface Temperature Block26,15,°C,\'blue\';0,1,0,2208,AI_SPARE1,Module Surface Temperature Block49,15,°C,\'red\'&etotal=0,1,0,1939_1946,EM_Accord_Act_Energy_Exp,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,1951,EM_Accord_Act_Energy_Exp,Yield (EM),1440,MWh,'green',1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		} else if ($subpark_id == 172) {
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,1948,EM_Accord_Act_Pow,Active%20Power,15,MW,1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,\'Gold\';&diffs=0,1000,0,1948,EM_Accord_Act_Energy_Exp,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_Accord" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,3324,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,3324,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,3324,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,3324,Day_Rain,Day Rain,15,mm,3;0,1,0,3324,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,3324,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,3324,Outside_Humidity,Outside Humidity,15,%,\'Violet\';0,1,0,3325,AI_SPARE1,Module Surface Temperature Block3,15,°C,\'indianred\';0,1,0,4600,AI_SPARE1,Module Surface Temperature Block23,15,°C,\'orange\';0,1,0,1530,AI_SPARE1,Module Surface Temperature Block26,15,°C,\'blue\';0,1,0,2208,AI_SPARE1,Module Surface Temperature Block49,15,°C,\'red\'&etotal=0,1,0,1939_1946,EM_Accord_Act_Energy_Exp,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,1948,EM_Accord_Act_Energy_Exp,Yield (EM),1440,MWh,'green',1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';

				$count = 1;
			}
		}else if ($subpark_id == 173) {
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,1943,EM_Accord_Act_Pow,Active%20Power,15,MW,1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,\'Gold\';&diffs=0,1000,0,1943,EM_Accord_Act_Energy_Exp,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_Accord" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,3324,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,3324,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,3324,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,3324,Day_Rain,Day Rain,15,mm,3;0,1,0,3324,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,3324,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,3324,Outside_Humidity,Outside Humidity,15,%,\'Violet\';0,1,0,3325,AI_SPARE1,Module Surface Temperature Block3,15,°C,\'indianred\';0,1,0,4600,AI_SPARE1,Module Surface Temperature Block23,15,°C,\'orange\';0,1,0,1530,AI_SPARE1,Module Surface Temperature Block26,15,°C,\'blue\';0,1,0,2208,AI_SPARE1,Module Surface Temperature Block49,15,°C,\'red\'&etotal=0,1,0,1939_1946,EM_Accord_Act_Energy_Exp,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
			$args = "0,1,0,1943,EM_Accord_Act_Energy_Exp,Yield (EM),1440,MWh,'green',1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}else if ($subpark_id == 174) {
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,1954,EM_Accord_Act_Pow,Active%20Power,15,MW,1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,\'Gold\';&diffs=0,1000,0,1954,EM_Accord_Act_Energy_Exp,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_Accord" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,3324,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,3324,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,3324,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,3324,Day_Rain,Day Rain,15,mm,3;0,1,0,3324,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,3324,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,3324,Outside_Humidity,Outside Humidity,15,%,\'Violet\';0,1,0,3325,AI_SPARE1,Module Surface Temperature Block3,15,°C,\'indianred\';0,1,0,4600,AI_SPARE1,Module Surface Temperature Block23,15,°C,\'orange\';0,1,0,1530,AI_SPARE1,Module Surface Temperature Block26,15,°C,\'blue\';0,1,0,2208,AI_SPARE1,Module Surface Temperature Block49,15,°C,\'red\'&etotal=0,1,0,1939_1946,EM_Accord_Act_Energy_Exp,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,1954,EM_Accord_Act_Energy_Exp,Yield (EM),1440,MWh,'green',1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}else if ($subpark_id == 175) {
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,1941,EM_Accord_Act_Pow,Active%20Power,15,MW,1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,\'Gold\';&diffs=0,1000,0,1941,EM_Accord_Act_Energy_Exp,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_Accord" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,3324,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,3324,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,3324,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,3324,Day_Rain,Day Rain,15,mm,3;0,1,0,3324,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,3324,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,3324,Outside_Humidity,Outside Humidity,15,%,\'Violet\';0,1,0,3325,AI_SPARE1,Module Surface Temperature Block3,15,°C,\'indianred\';0,1,0,4600,AI_SPARE1,Module Surface Temperature Block23,15,°C,\'orange\';0,1,0,1530,AI_SPARE1,Module Surface Temperature Block26,15,°C,\'blue\';0,1,0,2208,AI_SPARE1,Module Surface Temperature Block49,15,°C,\'red\'&etotal=0,1,0,1939_1946,EM_Accord_Act_Energy_Exp,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,1941,EM_Accord_Act_Energy_Exp,Yield (EM),1440,MWh,'green',1;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}	
		}	
	}
	else if ($park_no == 31) {
		if ($subpark_id == 226) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5306,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5306,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5306,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		else if ($subpark_id == 227) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5283,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5283,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
		    $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5283,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		else if ($subpark_id == 228) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5811,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5811,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5811,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		else if ($subpark_id == 229) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5705,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5705,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5705,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		else if ($subpark_id == 230) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5233,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5233,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5233,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
		else if ($subpark_id == 231) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5622,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5622,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5622,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
		else if ($subpark_id == 232) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5474,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5474,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5474,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}	
		}	
		else if ($subpark_id == 233) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5939,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5622,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5939,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		else if ($subpark_id == 234) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5608,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5608,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5608,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		else if ($subpark_id == 236) {
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,5541,PAC,AC%20Power,15,MW,1&diffs=0,1,0,5541,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,5541,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
	}	
	else if ($park_no == 35) {
        if ($subpark_id == 162) {
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?diffs=0,1,0,478,E_Total,null,8,Yield (EM),black,2,%20kWh,10,0,Arial;&sums=;0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&defaults=0,7&args=0,1,0,478,PAC,EM%20AC%20Power,15,kW,12;0,1,0,478,IAC1,EM%20AC%20Current%201,15,A,4;0,1,0,478,IAC2,EM%20AC%20Current%202,15,A,5;0,1,0,478,IAC3,EM%20AC%20Current%203,15,A,6;0,1,0,478,UAC1,EM%20AC%20Voltage%201,15,V,11;0,1,0,478,UAC2,EM%20AC%20Voltage%202,15,V,12;0,1,0,478,UAC3,EM%20AC%20Voltage%203,15,V,13;0,1,0,477,PAC,Kaco%20AC%20Power,15,kW,10;0,1,0,477,IAC1,Kaco%20AC%20Current%201,15,A,19;0,1,0,477,IAC2,Kaco%20AC%20Current%202,15,A,0;0,1,0,477,IAC3,Kaco%20AC%20Current%203,15,A,18;0,1,0,477,UAC1,Kaco%20AC%20Voltage%201,15,V,12;0,1,0,477,UAC2,Kaco%20AC%20Voltage%202,15,V,13;0,1,0,477,UAC3,Kaco%20AC%20Voltage%203,15,V,14;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame3" width="99%" height="99%" SRC="diagram/argdiagram5.php?diffs=0,1,0,478,E_Total,null,8,Yield (EM),black,2,%20kWh,10,0,Arial;&sums=;0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&args=-22.68,1.15,0,451,U4,Temperature,15,%C2%B0C,\'red\';0,13,0,451,U3,Irradiation,15,W/m%C2%B2,0&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame2" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2,3,4&diffs=0,1,0,478,E_Total,null,8,Yield (EM),black,2,%20kWh,10,0,Arial;&sums=;0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&args=0,1,0,452,SolingLossSimple,Soiling%20Loss,5,%,\'green\';0,1,0,452,Tref_1,Reference%20Temperature,5,°C,\'FireBrick\';0,1,0,452,Tsoil_1,Temperature,5,°C,\'orange\';0,1,0,452,Iscr_1,Reference%20current,5,A,\'blue\';0,1,0,452,Isc_1,Current,5,A,\'Cyan\';0,1,0,452,SoilingLossComplex,Soiling%20Loss%20Complex,5,%,\'purple\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
            $count = 3;
        } else if ($subpark_id == 163) {
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,3,6&sums=0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&args=0,0.001,0,749,Pac,SMA%201%20AC%20Power,15,kW,5;0,1,0,749,Uac,SMA 1 AC%20Voltage,15,V,16;0,1,0,749,Iac-Ist,SMA 1 AC Current,15,A,17;0,0.001,0,750,Pac,SMA%202%20AC%20Power,15,kW,18;0,1,0,750,Uac,SMA 2 AC%20Voltage,15,V,19;0,1,0,750,Iac-Ist,SMA 2 AC Current,15,A,20;0,0.001,0,751,Pac,SMA%203%20AC%20Power,15,kW,2;0,1,0,751,Uac,SMA 3 AC%20Voltage,15,V,22;0,1,0,751,Iac-Ist,SMA 3 AC Current,15,A,23&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame3" width="99%" height="99%" SRC="diagram/argdiagram5.php?sums=;0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&args=-22.68,1.15,0,451,U4,Temperature,15,%C2%B0C,\'red\';0,13,0,451,U3,Irradiation,15,W/m%C2%B2,0&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';

            $count = 2;
        }
    }else if ($park_no == 34) {
        if ($subpark_id == 253) {
           if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7157,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7157,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELMEASURE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7268,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7268,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7268,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7268,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&diffs=0,1,0,7157,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7268,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7268,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7157,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7268,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
        } 
	}else if ($park_no == 36) {
        if ($subpark_id == 268) {
			/*$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=inv1&args=0,1,0,7792,AC_Power,AC%20Power,15,kW,4;0,1,0,7792,DC_Current,DC%20Current,15,A,15&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverter 1" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=inv2&args=0,1,0,7791,AC_Power,AC%20Power,15,kW,4;0,1,0,7791,DC_Current,DC%20Current,15,A,15&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverter 2" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=inv3&args=0,1,0,7794,AC_Power,AC%20Power,15,kW,4;0,1,0,7794,DC_Current,DC%20Current,15,A,15&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverter 3" border="0"></iframe></td></tr><br>';
			$count = 3;*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=graph1&args=0,1,0,7792,AC_Power,AC%20Power-%201,15,kW,4;0,1,0,7791,AC_Power,AC%20Power-%202,15,kW,5;0,1,0,7794,AC_Power,AC%20Power-%203,15,kW,6;0,1,0,7794,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=graph2&args=0,1,0,7792,DC_Voltage,DC%20Voltage-%201,15,V,8;0,1,0,7791,DC_Voltage,DC%20Voltage-%202,15,V,9;0,1,0,7794,DC_Voltage,DC%20Voltage-%203,15,V,6;0,1,0,7792,DC_Current,DC%20Current-%201,15,A,7;0,1,0,7791,DC_Current,DC%20Current-%202,12,A,15;0,1,0,7794,DC_Current,DC%20Current-%203,15,A,13&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=graph3&args=0,1,0,7792,AC_Voltage,AC%20Voltage-%201,15,V,8;0,1,0,7791,AC_Voltage,AC%20Voltage-%202,15,V,9;0,1,0,7794,AC_Voltage,AC%20Voltage-%203,15,V,6;0,1,0,7792,AC_Frequency1,Frequency-%201,15,Hz,7;0,1,0,7791,AC_Frequency1,Frequency-%202,12,Hz,15;0,1,0,7794,AC_Frequency1,Frequency-%203,15,Hz,13&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=36&phase=energyg3&defaults=0,1,2,3,4&args=0,1,0,7792,Inv_PR,Inverter1%20PR,15,%,4;0,1,0,7791,Inv_PR,Inverter2%20PR,15,%,5;0,1,0,7794,Inv_PR,Inverter3%20PR,15,%,6;0,1,0,7794,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=36&phase=graph4&args=0,1,0,7792,INV_EFF,Inverter 1,15,%,8;0,1,0,7791,INV_EFF,Inverter 2,15,%,9;0,1,0,7794,INV_EFF,Inverter 3,15,%,6;0,1,0,7794,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=36&phase=energy4&defaults=0,1,2,3,4&args=0,1,0,7792,Inv_AC_PR,Inverter1%20PR,15,%,4;0,1,0,7791,Inv_AC_PR,Inverter2%20PR,15,%,5;0,1,0,7794,Inv_AC_PR,Inverter3%20PR,15,%,6;0,1,0,7794,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=36&phase=energy5&defaults=0,1,2,3,4&args=0,1,0,7792,DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,7791,DC_Vol_Coeff,Inv 2(DC_Voltage),15,V,5;0,1,0,7794,DC_Vol_Coeff,Inv 3(DC_Voltage),15,V,6;0,1,0,7794,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
			
			$count = 7;
        }else if($subpark_id == 269){
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=smu&args=0,1,0,7786,IDC1,String%20Current%201.1,5,A,15;0,1,0,7786,IDC2,String%20Current%201.2,5,A,1;0,1,0,7786,IDC3,String%20Current%201.3,5,A,2;0,1,0,7786,IDC4,String%20Current%201.4,5,A,3;0,1,0,7786,IDC5,String%20Current%202.1,5,A,4;0,1,0,7786,IDC6,String%20Current%202.2,5,A,5;0,1,0,7786,IDC7,String%20Current%202.3,5,A,6;0,1,0,7786,IDC8,String%20Current%202.4,5,A,7;0,1,0,7786,IDC9,String%20Current%203.1,5,A,8;0,1,0,7786,IDC10,String%20Current%203.2,5,A,9;0,1,0,7786,IDC11,String%20Current%203.3,5,A,10;0,1,0,7786,IDC12,String%20Current%203.4,5,A,11&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU " border="0"></iframe></td></tr>';
			$count = 1;
		}else if($subpark_id == 270){
			if ($phase=="tag"){
			/*$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=tag&defaults=0,1&args=0,1,0,7795,Activepower_Total,Active Power,15,kW,4;0,1,0,7794,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,7795_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Energy%20Meter" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=weather&defaults=0,1&args=0,1,0,7794,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,Module_Temperature,Module Temperature,15,&deg;C,\'darkred\'&etotal=0,1,0,7795_0,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,7794,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m�,10,0,Arial&avgs=0,1,0,7794,Module_Temperature,null,9,Avg.%20Module%20Temperature,red,2,%20�C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;*/
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=tag&defaults=0,1&args=0,1,0,7795,Activepower_Total,Active Power,15,kW,4;0,1,0,7794,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,7795_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=36&phase=energyg2&defaults=0,1,2,3&args=0,1,0,7795,System_PR,System PR,15,%,1;0,1,0,7794,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			//$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=36&phase=energyg3&defaults=0,1,2,3,4&args=0,1,0,7792,Inv_PR,Inverter1%20PR,15,%,4;0,1,0,7791,Inv_PR,Inverter2%20PR,15,%,5;0,1,0,7794,Inv_PR,Inverter3%20PR,15,%,6;0,1,0,7794,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-3" border="0"></iframe></td></tr><br>';
			//$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=36&phase=energy4&defaults=0,1,2,3,4&args=0,1,0,7792,Inv_AC_PR,Inverter1%20PR,15,%,4;0,1,0,7791,Inv_AC_PR,Inverter2%20PR,15,%,5;0,1,0,7794,Inv_AC_PR,Inverter3%20PR,15,%,6;0,1,0,7794,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr><br>';
			//$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=36&phase=energy5&defaults=0,1,2,3,4&args=0,1,0,7792,DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,7791,DC_Vol_Coeff,Inv 2(DC_Voltage),15,V,5;0,1,0,7794,DC_Vol_Coeff,Inv 3(DC_Voltage),15,V,6;0,1,0,7794,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-5" border="0"></iframe></td></tr><br>';
			$count = 2;
			
			}
			else{
			$args = "0,1,0,7795,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,8149,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		} else if($subpark_id == 271){
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=weather&defaults=0,1&args=0,1,0,7794,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7794,Module_Temperature,Module Temperature,15,°C,\'darkred\'&etotal=0,1,0,7795_0,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,7794,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7794,Module_Temperature,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 1;
		}		
	}
	else if ($park_no == 41) {// CWET
		if($subpark_id == 273){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1&args=0,1,0,8148,Inverter_Output_Tot_Power,AC%20Power,15,kW,1;0,1,0,8148,Grid_Freq,Grid%20Frequency,15,Hz,2;0,1,0,8148,Grid_Freq,Grid%20Frequency,15,Hz,2;0,1,0,8148,Inverter_Output_Current_S,Inverter_Output_Current_S,15,A,3;0,1,0,8148,Inverter_Output_Current_T,Inverter_Output_Current_T,15,A,4;0,1,0,8148,Inverter_Output_Current_R,Inverter_Output_Current_R,15,A,9;0,1,0,8148,AC_GridRMS_Voltage_R,AC_GridRMS_Voltage_R,15,V,6;0,1,0,8148,AC_GridRMS_Voltage_S,AC_GridRMS_Voltage_S,15,V,7;0,1,0,8148,AC_GridRMS_Voltage_T,AC_GridRMS_Voltage_T,15,V,8&sums=&diffs=0,1,0,8148,Tot_Energy,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverter" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
			else{
			$args = "0,1,0,8148,Tot_Energy,Yield (EM),1440,MWh,'green',1;0,1,0,8149,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		if($subpark_id == 274){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?phase=weather&defaults=0,1&args=0,1,0,8149,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8149,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\'&etotal=0,1,0,8148_0,Tot_Energy,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,8149,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8149,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 1;
		}		
	}
	else if ($park_no == 33) {
		if ($subpark_id == 255) {// Block 1
		   if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7980,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7980,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7980,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}else if ($subpark_id == 256) {// Block 2
           if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7346,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7346,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7346,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
        }else if($subpark_id == 257){// Block 3
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7287,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7287,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7287,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}else if($subpark_id == 258){// Block 4
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7734,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7734,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7734,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}else if($subpark_id == 259){// Block 5
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7880,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7880,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7880,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}		
		}else if($subpark_id == 260){// Block 6
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7981,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7981,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7981,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}else if($subpark_id == 261){// Block 7
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7574,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7574,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7574,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}else if($subpark_id == 262){// Block 8
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7979,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7979,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7979,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}else if($subpark_id == 263){// Block 9
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7683,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7683,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7683,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}else if($subpark_id == 264){// Block 10
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,7978,PAC,AC%20Power,15,MW,1&diffs=0,1,0,7978,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_ELITE" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,7978,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
	}else if($park_no == 42){ // Clarke School
		if($subpark_id == 276){
		if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8147,PAC,AC%20Power,15,kW,1&diffs=0,1,0,8147,E_Total,null,8,Energy Export,black,2, kWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverter" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
			else{
				$args = "0,1,0,8147,E_Total,Energy Export,1440,kWh,'green',1";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		if($subpark_id == 277){
		if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8155,PAC,AC%20Power,15,kW,1;0,1,0,8155,PAC1,AC%20Power1,15,kW,2;0,1,0,8155,PAC2,AC%20Power2,15,kW,3;0,1,0,8155,PAC3,AC%20Power3,15,kW,4;0,1,0,8155,PowerSetpoint,PowerSetpoint,15,%,\'Gold\'&diffs=0,1,0,8147,E_Total,null,8,Energy Export,black,2, kWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Energy Meter PV+EB" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
	}else if($park_no == 43){ // Amplus Pune
			if($subpark_id == 279){ // Inverter

				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=INV1G1&args=0,1,0,8379,AC_Power,AC%20Power,15,kW,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=INV1G2&args=0,1,0,8379,DC_Voltage,DC%20Voltage,15,v,5;0,1,0,8379,DC_Current,DC%20Current,15,A,6;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=INV1G3&args=0,1,0,8379,AC_Voltage,AC%20Voltage,15,v,7;0,1,0,8379,AC_Frequency1,Frequency,15,Hz,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=43&phase=INV1G4&defaults=0,1,2,3,4&args=0,1,0,8458,Inv_PR,Inv1(REFSU1)%20PR,15,%,4;0,1,0,8458,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8458,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=043&phase=INV1G5&args=0,1,0,8379,Inv_Eff,Inverter 1,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=43&phase=INV1G6&defaults=0,1,2,3,4&args=0,1,0,8379,Inv_AC_PR_600_PR,Inverter1%20PR,15,%,4;0,1,0,8414,Inv1_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=43&phase=INV1G7&defaults=0,1,2,3,4&args=0,1,0,8379,Inv_DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,8414,Inv1_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
				$count = 7;
			}	
			else if($subpark_id == 280){// SMU
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=smu1inv1&args=0,1,0,8459,IDC1,String%20Current%201.1,5,A,15;0,1,0,8459,IDC2,String%20Current%201.2,5,A,1;0,1,0,8459,IDC3,String%20Current%201.3,5,A,2;0,1,0,8459,IDC4,String%20Current%201.4,5,A,3;0,1,0,8459,IDC5,String%20Current%201.5,5,A,4;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV1/080161339/REFU20/SPR435/11X5" border="0"></iframe></td></tr>';
			
				 $diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=smu1inv2&args=0,1,0,8459,IDC6,String%20Current%202.1(20°),5,A,15;0,1,0,8459,IDC7,String%20Current%202.2(20°),5,A,1;0,1,0,8459,IDC8,String%20Current%202.3(20°),5,A,2;0,1,0,8459,IDC9,String%20Current%202.4(10°),5,A,3;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'brown\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV2/2007307583/SMA11/SPR435/7X4" border="0"></iframe></td></tr>';
					
				 $diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=smu1inv3&args=0,1,0,8459,IDC10,String%20Current%203.1,5,A,15;0,1,0,8459,IDC11,String%20Current%203.2,5,A,1;0,1,0,8459,IDC12,String%20Current%203.3,5,A,2;0,1,0,8414,Solar_Radiation,20°%20Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV3/2007307578/SMA11/TRINA300/14X3" border="0"></iframe></td></tr>';
					
				 $diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=smu1inv4&args=0,1,0,8459,IDC13,String%20Current%204.1,5,A,15;0,1,0,8459,IDC14,String%20Current%204.2,5,A,1;0,1,0,8459,IDC15,String%20Current%204.3,5,A,2;0,1,0,8412,Solar_Radiation,10°%20Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV4/2007307494/SMA11/TRINA300/14X3" border="0"></iframe></td></tr>';
				 $count = 4;
			}
			else if($subpark_id == 283){ // Energy Meter
				if ($phase=="tag"){

					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=tag&defaults=0,1&args=0,1,0,8240,Activepower_Total,Active Power,15,kW,4;0,1,0,8414_8412,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,8240_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
					//$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=36&phase=energyg2&defaults=0,1,2,3&args=0,1,0,0,System_PR,System PR,15,%,1;0,1,0,0,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,0,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
					$count = 1;

				}
				else{
					$args = "0,1,0,8240,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,8414_8412,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
					$count = 1;
				}

			}	
			else if($subpark_id == 284){ // Weather Station
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=weather20&defaults=0,1&args=0,1,0,8414,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8414,Module_Temperature,Module Temperature,15,°C,\'darkred\'&etotal=0,1,0,8240_0,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,8414,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8414,Module_Temperature,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station%2020%20Degrees" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=43&phase=weather10&defaults=0,1&args=0,1,0,8412,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8412,Module_Temperature,Module Temperature,15,°C,\'darkred\'&etotal=0,1,0,8240_0,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,8412,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8412,Module_Temperature,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station%2010%20Degrees" border="0"></iframe></td></tr><br>';
				$count = 2;
			}	
    }else if($park_no==44){ // MOSER baer
			if($subpark_id == 285){
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?&args=0,1,0,8374,IDC1,String%20Current%201,5,A,15;0,1,0,8374,IDC2,String%20Current%202,5,A,1;0,1,0,8374,IDC3,String%20Current%203,5,A,2;0,1,0,8374,IDC4,String%20Current%204,5,A,3;0,1,0,8374,UDC1,DC%20Voltage%201,5,V,4;0,1,0,8374,UDC2,DC%20Voltage%202,5,V,5;0,1,0,8374,UDC3,DC%20Voltage%203,5,V,6;0,1,0,8374,UDC4,DC%20Voltage%204,5,V,7&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU" border="0"></iframe></td></tr>';
			}
			else if($subpark_id == 286){
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?&args=0,1,0,8369,IDC1,String%20Current%201,5,A,15;0,1,0,8369,IDC2,String%20Current%202,5,A,1;0,1,0,8369,IDC3,String%20Current%203,5,A,2;0,1,0,8369,IDC4,String%20Current%204,5,A,3;0,1,0,8369,UDC1,DC%20Voltage%201,5,V,4;0,1,0,8369,UDC2,DC%20Voltage%202,5,V,5;0,1,0,8369,UDC3,DC%20Voltage%203,5,V,6;0,1,0,8369,UDC4,DC%20Voltage%204,5,V,7&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU" border="0"></iframe></td></tr>';
			}

	} 
	else if($park_no == 39){ // Amplus Mumbai
		
			if($subpark_id == 296){ // SMU
					$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=39&phase=ampsmu1inv1&args=0,1,0,9330,IDC1,String%20Current%201.1,5,A,15;0,1,0,9330,IDC2,String%20Current%201.2,5,A,1;0,1,0,9330,IDC3,String%20Current%201.3,5,A,2;0,1,0,9330,IDC4,String%20Current%201.4,5,A,3;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV1/1900705898/SMA1" border="0"></iframe></td></tr>';

					$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=39&phase=ampsmu1inv2&args=0,1,0,9330,IDC5,String%20Current%202.1,5,A,15;0,1,0,9330,IDC6,String%20Current%202.2,5,A,1;0,1,0,9330,IDC7,String%20Current%202.3,5,A,2;0,1,0,9330,IDC8,String%20Current%202.4,5,A,3;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV2/1900705882/SMA2" border="0"></iframe></td></tr>';

					$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=39&phase=ampsmu1inv3&args=0,1,0,9330,IDC9,String%20Current%203.1,5,A,15;0,1,0,9330,IDC10,String%20Current%203.2,5,A,1;0,1,0,9330,IDC11,String%20Current%203.3,5,A,2;0,1,0,9330,IDC12,String%20Current%203.4,5,A,3;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV3/1900705878/SMA3" border="0"></iframe></td></tr>';

					$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=39&phase=ampsmu1inv4&args=0,1,0,9330,IDC13,String%20Current%204.1,5,A,15;0,1,0,9330,IDC14,String%20Current%204.2,5,A,1;0,1,0,9330,IDC15,String%20Current%204.3,5,A,2;0,1,0,9330,IDC16,String%20Current%204.4,5,A,3;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV4/1900705201/SMA4" border="0"></iframe></td></tr>';
					$count = 4;
			}
			else if($subpark_id == 320){ // Energy Meter
				if ($phase=="tag"){

					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=39&phase=Mumtag&defaults=0,1&args=0,1,0,9317,Activepower_Total,Active Power,15,kW,4;0,1,0,9316,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,9317_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=39&phase=energyg2&defaults=0,1,2,3&args=0,1,0,9317,System_PR,System PR,15,%,1;0,1,0,9329,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9329,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
					$count = 2;

				}
				else{
					$args = "0,1,0,9317,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,9316,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
					$count = 1;
				}

			}
			else if($subpark_id == 294){ // Inverter
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=39&phase=SMA1M1&args=0,1,0,9329,Pac,AC%20Power,15,kW,4;0,1,0,9316,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=39&phase=SMA1M2&args=0,1,0,9329,A.Ms.Vol-B.Ms.Vol,(DC%201%2BDC%202)/2,15,v,5;0,1,0,9329,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,6;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=39&phase=SMA1M3&args=0,1,0,9329,GridMs.PhV.phsA,AC%20Voltage%20L1,15,v,1;0,1,0,9329,GridMs.PhV.phsB,AC%20Voltage%20L2,15,v,9;0,1,0,9329,GridMs.PhV.phsC,AC%20Voltage%20L3,15,v,7;0,1,0,9329,GridMs.Hz,Frequency,15,Hz,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';//
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=39&phase=SMA1M4&defaults=0,1,2,3,4&args=0,1,0,9329,Inv_PR,Inv1%20PR,15,%,4;0,1,0,9329,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9329,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram10.php?park_no=39&phase=SMA1M5&args=0,1,0,9329,Inv_Eff,Inverter1,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
				$diagrammCode .='<tr><td>&nbsp;</td></tr>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram10.php?park_no=39&phase=SMA1M6&defaults=0,1,2,3,4&args=0,1,0,9329,Inv_AC_PR_600,Inverter1%20PR,15,%,4;0,1,0,9324,Inv_AC_PR,Inverter2%20PR,15,%,4;0,1,0,9325,Inv_AC_PR,Inverter3%20PR,15,%,4;0,1,0,9316,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram10.php?park_no=39&phase=SMA1M7&defaults=0,1,2,3,4&args=0,1,0,9329,DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,9316,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
				$count = 7;
				
			}
			else if($subpark_id == 321){//Weather Station
				$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=39&phase=ampweather&defaults=0,1,3&args=0,1,0,9316,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9316,Module_Temperature,Module Temperature,15,°C,\'darkred\';0,1,0,9330,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9316,Ambient_Temperature,Ambient Temperature,15,°C,\'green\';&etotal=0,1,0,9317,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,9316,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9316,Module_Temperature,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
			
	}
	else if ($park_no==40){
	if ($phase=="tag"){
		$args="0,1,0,772,DirtyCurrent,772.Current%20of%20dirty%20panel,15,A,1;0,1,0,772,CleanCurrent2,772.Current%20of%20clean%20panel,15,A,0;0,1,0,772,SoilingLoss,772.Soiling%20loss,15,%,2;";
		$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
        $count = 1;
       } 
	}else if ($park_no == 45) {// Goa 
		if($subpark_id == 289){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1&args=0,1,0,8724,Inverter_Output_Tot_Power,AC%20Power,15,kW,1;0,1,0,8724,Grid_Freq,Grid%20Frequency,15,Hz,2;0,1,0,8724,Grid_Freq,Grid%20Frequency,15,Hz,2;0,1,0,8724,Inverter_Output_Current_S,Inverter_Output_Current_S,15,A,3;0,1,0,8724,Inverter_Output_Current_T,Inverter_Output_Current_T,15,A,4;0,1,0,8724,Inverter_Output_Current_R,Inverter_Output_Current_R,15,A,9;0,1,0,8724,AC_GridRMS_Voltage_R,AC_GridRMS_Voltage_R,15,V,6;0,1,0,8724,AC_GridRMS_Voltage_S,AC_GridRMS_Voltage_S,15,V,7;0,1,0,8724,AC_GridRMS_Voltage_T,AC_GridRMS_Voltage_T,15,V,8&sums=&diffs=0,1,0,8724,Tot_Energy,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverter1" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
			else{
			$args = "0,1,0,8724,Tot_Energy,Yield (EM),1440,MWh,'green',1;0,1,0,8728,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
			
		}else if($subpark_id == 290){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?phase=weather&defaults=0,1,2,3&args=0,1,0,8728,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8728,Module_surface_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,8728,Wind_Speed,Wind Speed,15,m/s,\'blue\';0,1,0,8728,Humidity,Humidity,15,%,\'green\'&sums=0,1,0,8728,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8728,Module_surface_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 1;
		}	
	}else if(false && $park_no == 25){
            if ($phase != "tag") {
        
        
        $args = "0,1,0,109,E_Total,Inverter 1,1440,kWh,0,1;0,1,0,110,E_Total,Inverter 2,1440,kWh,1,1;0,1,0,111,E_Total,Inverter 3,1440,kWh,2,1;0,1,0,112,E_Total,Inverter 4,1440,kWh,3,1;0,1,0,113,E_Total,Energy Meter,1440,kWh,5,2;0,1,0,108,SR2,Tilted%20Solar%20Radiation,1440,Wh/mSQUA,'yellow',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
        $count = 1;
    } else {

        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,113,PAC,AC Power,5,kW,3;0,1,0,113,IAC1,AC Current 1,5,A,7;0,1,0,113,IAC2,AC Current 2,5,A,8;0,1,0,113,IAC3,AC Current 3,5,A,9;0,1,0,113,UAC1,AC Voltage 1,5,V,4;0,1,0,113,UAC2,AC Voltage 2,5,V,5;0,1,0,113,UAC3,AC Voltage 3,5,V,6&defaults=0&diffs=0,1000,0,113,E_DAY,null,8,Yield%20(EM),black,2,%20kWh,10,0,Arial;&sums=0,13,0,108,SR2_60,null,5,Total%20Irradiation%20(tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,108,SR1_60,null,5,Total%20Irradiation%20(horizontal),brown,2,%20Wh/m²,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';

        $args = "0,1,0,109,PAC,Inverter 1 AC Power,15,kW,0;0,1,0,109,IAC1,Inverter 1 AC Current 1,15,A,0;0,1,0,109,IAC2,Inverter 1 AC Current 2,15,A,0;0,1,0,109,IAC3,Inverter 1 AC Current 3,15,A,0;0,1,0,109,IDC1,Inverter 1 DC Current 1,15,A,0;0,1,0,109,UAC1,Inverter 1 AC Voltage 1,15,V,0;0,1,0,109,UAC2,Inverter 1 AC Voltage 2,15,V,0;0,1,0,109,UAC3,Inverter 1 AC Voltage 3,15,V,0;0,1,0,109,UDC1,Inverter 1 DC Voltage 1,15,V,0;0,1,0,110,PAC,Inverter 2 AC Power,15,kW,1;0,1,0,110,IAC1,Inverter 2 AC Current 1,15,A,1;0,1,0,110,IAC2,Inverter 2 AC Current 2,15,A,1;0,1,0,110,IAC3,Inverter 2 AC Current 3,15,A,1;0,1,0,110,IDC1,Inverter 2 DC Current 1,15,A,1;0,1,0,110,UAC1,Inverter 2 AC Voltage 1,15,V,1;0,1,0,110,UAC2,Inverter 2 AC Voltage 2,15,V,1;0,1,0,110,UAC3,Inverter 2 AC Voltage 3,15,V,1;0,1,0,110,UDC1,Inverter 2 DC Voltage 1,15,V,1;0,1,0,111,PAC,Inverter 3 AC Power,15,kW,2;0,1,0,111,IAC1,Inverter 3 AC Current 1,15,A,2;0,1,0,111,IAC2,Inverter 3 AC Current 2,15,A,2;0,1,0,111,IAC3,Inverter 3 AC Current 3,15,A,2;0,1,0,111,IDC1,Inverter 3 DC Current 1,15,A,2;0,1,0,111,UAC1,Inverter 3 AC Voltage 1,15,V,2;0,1,0,111,UAC2,Inverter 3 AC Voltage 2,15,V,2;0,1,0,111,UAC3,Inverter 3 AC Voltage 3,15,V,2;0,1,0,111,UDC1,Inverter 3 DC Voltage 1,15,V,2;0,1,0,112,PAC,Inverter 4 AC Power,15,kW,3;0,1,0,112,IAC1,Inverter 4 AC Current 1,15,A,3;0,1,0,112,IAC2,Inverter 4 AC Current 2,15,A,3;0,1,0,112,IAC3,Inverter 4 AC Current 3,15,A,3;0,1,0,112,IDC1,Inverter 4 DC Current 1,15,A,3;0,1,0,112,UAC1,Inverter 4 AC Voltage 1,15,V,3;0,1,0,112,UAC2,Inverter 4 AC Voltage 2,15,V,3;0,1,0,112,UAC3,Inverter 4 AC Voltage 3,15,V,3;0,1,0,112,UDC1,Inverter 4 DC Voltage 1,15,V,3";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args='.$args.'&defaults=0,9,18,27&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,108,SR1,Irradiation,5,W/mSQUA,0;0,1,0,108,SR2,Irradiation inclined,5,W/mSQUA,3;0,1,0,108,AT,Temperature,5,DEGC,0&defaults=0,1,2&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';

        $count = 3;
    }
    }else if ($park_no == 37){ //Punjab12
		if ($subpark_id==297){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8862,PAC,AC%20Power,15,MW,1&diffs=0,1,0,8862,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,8857,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,8857,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8857,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,8857,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,8860,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,8857,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8857,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,8862,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,8857,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		if ($subpark_id==298){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8864,PAC,AC%20Power,15,MW,1&diffs=0,1,0,8864,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,8857,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,8857,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8857,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,8857,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,8860,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,8857,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8857,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,8864,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,8857,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		if ($subpark_id==299){ // Block 3
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8984,PAC,AC%20Power,15,MW,1&diffs=0,1,0,8984,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,8857,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,8857,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8857,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,8857,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,8860,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,8857,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8857,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,8984,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,8857,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		if ($subpark_id==300){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8863,PAC,AC%20Power,15,MW,1&diffs=0,1,0,8863,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,8857,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,8857,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8857,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,8857,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,8860,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,8857,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8857,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,8863,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,8857,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		if ($subpark_id==301){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8859,PAC,AC%20Power,15,MW,1&diffs=0,1,0,8859,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,8857,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,8857,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8857,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,8857,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,8860,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,8857,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8857,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,8859,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,8857,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		if ($subpark_id==302){
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8861,PAC,AC%20Power,15,MW,1&diffs=0,1,0,8861,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,8857,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,8857,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8857,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,8857,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,8860,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,8857,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8857,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,8861,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,8857,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		
	}	else if ($park_no == 38){ //Punjab20
		if ($subpark_id==303){ // Blk 1
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9077,PAC,AC%20Power,15,MW,1&diffs=0,1,0,9077,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9172,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,9077,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		
		if ($subpark_id==304){ // Blk 2
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9027,PAC,AC%20Power,15,MW,1&diffs=0,1,0,9027,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9172,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,9027,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		if ($subpark_id==305){ // Blk 3
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8984,PAC,AC%20Power,15,MW,1&diffs=0,1,0,8984,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9172,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,8984,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
		if ($subpark_id==306){ // Blk 4
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9120,PAC,AC%20Power,15,MW,1&diffs=0,1,0,9120,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9172,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,9120,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
		if ($subpark_id==307){ // Blk 5
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9193,PAC,AC%20Power,15,MW,1&diffs=0,1,0,9193,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9193,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,9193,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
		if ($subpark_id==308){ // Blk 6
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9109,PAC,AC%20Power,15,MW,1&diffs=0,1,0,9109,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9172,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,9109,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
		if ($subpark_id==309){ // Blk 7
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9073,PAC,AC%20Power,15,MW,1&diffs=0,1,0,9073,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9073,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,9073,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
		if ($subpark_id==310){ // Blk 8
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9039,PAC,AC%20Power,15,MW,1&diffs=0,1,0,9039,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9172,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,9039,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}	
		if ($subpark_id==311){ // Blk 9
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9216,PAC,AC%20Power,15,MW,1&diffs=0,1,0,9216,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9216,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,9216,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		if ($subpark_id==312){ // Blk 10
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9160,PAC,AC%20Power,15,MW,1&diffs=0,1,0,9160,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9172,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,9160,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}		
	}
	else if($park_no == 51){// Kolkata
	
		if($subpark_id==317){ // Inverter Graph
		
				if ($phase=="tag"){
					
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9902,pac,INV1%20AC%20Power,15,kW,1;0,1,0,9901,pac,INV2%20AC%20Power,15,kW,2;0,1,0,9903,pac,INV3%20AC%20Power,15,kW,3;0,1,0,9905,pac,INV4%20AC%20Power,15,kW,4;0,1,0,9904,pac,INV5%20AC%20Power,15,kW,5;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverters" border="0"></iframe></td></tr><br>';
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2,3&args=0,1,0,9301,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9301,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9301,Air_Temperature,Air Temperature,15,°C,\'darkred\';0,1,0,9301,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,9301,Humidity ,Humidity,15,%,\'green\'&diffs=0,1,0,9315,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,9301,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9301,Air_Temperature,null,9,Avg.%20Air%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
					$count = 2;
				}
				else{
					$args = "0,1,0,9902,AC_Energy,INV1 Yield(EM),1440,kWh,'green',1;0,1,0,9901,AC_Energy,INV2 Yield(EM),1440,kWh,'Black',1;0,1,0,9903,AC_Energy,INV3 Yield(EM),1440,kWh,'Blue',1;0,1,0,9905,AC_Energy,INV4 Yield(EM),1440,kWh,'red',1;0,1,0,9904,AC_Energy,INV5 Yield(EM),1440,kWh,'Brown',1;0,1,0,9301,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
					$count = 1;
				}
		
		}
		if($subpark_id==318){ // Energy meter Graph
		
			if ($phase=="tag"){
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9315,Total_Active_Power,AC%20Power,15,kW,1&sums=0,1,0,9315_0,Total_Active_Power,null,5,Current%20Generation,red,2,%20kW,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Energy Meter" border="0"></iframe></td></tr><br>';
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2,3&args=0,1,0,9301,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9301,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9301,Air_Temperature,Air Temperature,15,°C,\'darkred\';0,1,0,9301,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,9301,Humidity ,Humidity,15,%,\'green\'&diffs=0,1,0,9315,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,9301,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9301,Air_Temperature,null,9,Avg.%20Air%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
					$count = 2;		
			}
			else{
				$args = "0,1,0,9315,Forward_Active_Energy,Yield (EM),1440,kWh,'green',1;0,1,0,9301,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		
		if($subpark_id==319){ // weather station
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2,3&args=0,1,0,9301,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9301,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9301,Air_Temperature,Air Temperature,15,°C,\'darkred\';0,1,0,9301,Humidity ,Humidity,15,%,\'green\'&diffs=0,1,0,9315,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,9301,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9301,Air_Temperature,null,9,Avg.%20Air%20Temperature,red,2,%20°C,10,0,Arial;0,1,0,9301,Humidity ,Humidity,15,%,\'green\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
		
	}
	else if($park_no == 46){
	
	if($subpark_id==323){ // Inverter1
	
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=46&phase=raigraph1&args=0,1,0,9971,Pac,AC%20Power-%201,15,kW,4;0,1,0,9972,Pac,AC%20Power-%202,15,kW,5;0,1,0,9973,Pac,AC%20Power-%203,15,kW,6;0,1,0,9957,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=46&phase=raigraph2&args=0,1,0,9971,A.Ms.Vol-B.Ms.Vol,DC%20Voltage-%201,15,V,8;0,1,0,9972,A.Ms.Vol-B.Ms.Vol,DC%20Voltage-%202,15,V,9;0,1,0,9973,A.Ms.Vol-B.Ms.Vol,DC%20Voltage-%203,15,V,6;0,1,0,9971,A.Ms.Amp-B.Ms.Amp,DC%20Current-%201,15,A,7;0,1,0,9972,A.Ms.Amp-B.Ms.Amp,DC%20Current-%202,12,A,15;0,1,0,9973,A.Ms.Amp-B.Ms.Amp,DC%20Current-%203,15,A,13&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=46&phase=raigraph3&args=0,1,0,9971,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,9971,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,11;0,1,0,9971,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,12;0,1,0,9972,GridMs.PhV.phsA,INV2%20AC%20Voltage-%20L1,15,V,9;0,1,0,9972,GridMs.PhV.phsB,INV2%20AC%20Voltage-%20L2,15,V,21;0,1,0,9972,GridMs.PhV.phsC,INV2%20AC%20Voltage-%20L3,15,V,19;0,1,0,9973,GridMs.PhV.phsA,INV3%20AC%20Voltage-%20L1,15,V,17;0,1,0,9973,GridMs.PhV.phsB,INV3%20AC%20Voltage-%20L2,15,V,28;0,1,0,9973,GridMs.PhV.phsC,INV3%20AC%20Voltage-%20L3,15,V,2;0,1,0,9971,GridMs.Hz,Frequency-%201,15,Hz,7;0,1,0,9972,GridMs.Hz,Frequency-%202,12,Hz,15;0,1,0,9973,GridMs.Hz,Frequency-%203,15,Hz,1&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=46&phase=INVPR&defaults=0,1,2,3,4&args=0,1,0,9971,Inv_PR,Inv1%20PR,15,%,4;0,1,0,9972,Inv_PR,Inv2%20PR,15,%,11;0,1,0,9973,Inv_PR,Inv3%20PR,15,%,8;0,1,0,9973,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9973,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram11.php?park_no=46&phase=INVEFF&args=0,1,0,9971,Inv1_Eff,Inverter1,15,%,8;0,1,0,9972,Inv2_Eff,Inverter2,15,%,9;0,1,0,9973,Inv3_Eff,Inverter3,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';

			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram11.php?park_no=46&phase=INVACPR&defaults=0,1,2,3,4&args=0,1,0,9971,Inv_AC_PR,Inverter1%20PR,15,%,4;0,1,0,9972,Inv_AC_PR,Inverter2%20PR,15,%,5;0,1,0,9973,Inv_AC_PR,Inverter3%20PR,15,%,4;0,1,0,9957,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9957,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram11.php?park_no=46&phase=INVDCPR&defaults=0,1,2,3,4&args=0,1,0,9971,DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,9972,DC_Vol_Coeff,Inv 2(DC_Voltage),15,V,5;0,1,0,9931,DC_Vol_Coeff,Inv 3(DC_Voltage),15,V,6;0,1,0,9957,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9957,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';

			$count = 7;
	}
	if($subpark_id==325){ // Energy Meter
	if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=46&phase=Rai3tag&defaults=0,1&args=0,1,0,9958,Activepower_Total,Active Power,15,kW,4;0,1,0,9957,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,9958_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=46&phase=energyg2&defaults=0,1,2,3&args=0,1,0,9958,System_PR,System PR,15,%,1;0,1,0,9973,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9973,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			//$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=46&phase=energyg2&defaults=0,1,2,3&args=0,1,0,9317,System_PR,System PR,15,%,1;0,1,0,9316,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,9316,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,9958,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,9957,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			
			
			}
	}	
	
	else if($subpark_id == 324){ // SMU
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=46&phase=smu&args=0,1,0,9959,IDC1,String%20Current%201.1,5,A,15;0,1,0,9959,IDC2,String%20Current%201.2,5,A,1;0,1,0,9959,IDC3,String%20Current%201.3,5,A,2;0,1,0,9959,IDC4,String%20Current%201.4,5,A,3;0,1,0,9959,IDC5,String%20Current%201.5,5,A,4;0,1,0,9959,IDC6,String%20Current%201.6,5,A,5;0,1,0,9959,IDC7,String%20Current%201.7,5,A,6;0,1,0,9959,IDC8,String%20Current%201.8,5,A,7;0,1,0,9959,IDC9,String%20Current%201.9,5,A,8;0,1,0,9959,IDC10,String%20Current%201.10,5,A,9;0,1,0,9959,IDC11,String%20Current%201.11,5,A,10;0,1,0,9959,IDC12,String%20Current%201.12,5,A,11;0,1,0,9959,IDC13,String%20Current%201.13,5,A,12;0,1,0,9959,IDC14,String%20Current%201.14,5,A,14;0,1,0,9959,IDC15,String%20Current%201.15,5,A,15;0,1,0,9959,IDC16,String%20Current%201.16,5,A,16&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU " border="0"></iframe></td></tr>';
			$count = 1;
		}
		
		else if($subpark_id == 326){ // Weather Station
		$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&park_no=46&phase=ws&args=0,1,0,9957,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9957,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9957,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9957,Module_Temperature,Module_Temperature,15,°C,\'green\';&diffs=0,1,0,9958,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,9957,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9957,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
		$count = 1;
		}	
}
else if($park_no == 47){

	if($subpark_id == 328){ // Weather Station
		$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,9974,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9974,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9974,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9974,Module_Temperature,Module_Temperature,15,°C,\'green\';&diffs=0,1,0,9983,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,9974,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9974,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
		$count = 1;
		}	

}
else if ($park_no == 81) {
		if ($subpark_id == 333) { // Block A
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10081,pac,Active%20Power,15,MW,1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,9892,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block A(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,9892_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10081,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10108,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		} 
		
		if ($subpark_id == 334) { // Block B
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10078,pac,Active%20Power,15,MW,1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10078,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block B(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,9892_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10078,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		if ($subpark_id == 335) { // Block C
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10083,pac,Active%20Power,15,MW,1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10083,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block C(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,9892_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10083,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		if ($subpark_id == 336) { // Block D
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10076,pac,Active%20Power,15,MW,1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10076,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block D(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,9892_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10076,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10108,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
}	

else if ($park_no == 82) { // TTPET
		if ($subpark_id == 339) { // Block A
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10126,pac,Active%20Power,15,MW,1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10126,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block A(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10118_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10126,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10108,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		} 
		
		if ($subpark_id == 340) { // Block B
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10114,pac,Active%20Power,15,MW,1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10114,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block B(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10118_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10114,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		if ($subpark_id == 341) { // Block C
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10116,pac,Active%20Power,15,MW,1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10116,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block C(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10118_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10116,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		if ($subpark_id == 342) { // Block D
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10115,pac,Active%20Power,15,MW,1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10115,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block D(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10118_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10115,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10108,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
}	else if ($park_no == 83) { // Panchapatty
		if ($subpark_id == 343) { // Block A
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10450,pac,Active%20Power,15,MW,1;0,1,0,10731,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10450,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block A(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10731,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10731,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10731,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10731,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10731,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10445_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10731,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10450,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10731,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		} 
		
		if ($subpark_id == 345) { // Block B
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10448,pac,Active%20Power,15,MW,1;0,1,0,10731,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10448,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block B(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10731,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10731,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10731,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10731,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10731,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10445_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10731,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10448,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10731,Solar_Radiation,Irradiation,1440,W/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		if ($subpark_id == 346) { // Block C
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10451,pac,Active%20Power,15,MW,1;0,1,0,10731,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10451,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block C(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10731,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10731,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10731,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10731,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10731,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10445_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10731,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10451,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10731,Solar_Radiation,Irradiation,1440,W/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		if ($subpark_id == 347) { // Block D
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10449,pac,Active%20Power,15,MW,1;0,1,0,10731,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,10449,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block D(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10731,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10731,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10731,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10731,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10731,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10445_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10731,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,10449,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,10731,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
}	
else if ($park_no == 63) { // AP 30MW
		if ($subpark_id == 387) { // Block A
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,12058,pac,Active%20Power,15,MW,1;0,1,0,10108,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,12058,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block A(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,12039,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12039,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12039,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,12039,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,12039,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,12051_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,12039,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,12058,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,12039,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		} 
		
		if ($subpark_id == 388) { // Block B
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,12061,pac,Active%20Power,15,MW,1;0,1,0,12039,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,12061,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block B(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,12039,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12039,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12039,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,12039,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,12039,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,12051_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,12039,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,12061,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,12039,Solar_Radiation,Irradiation,1440,W/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		if ($subpark_id == 389) { // Block C
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,12053,pac,Active%20Power,15,MW,1;0,1,0,12039,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,12053,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block C(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,12039,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12039,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12039,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,12039,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,12039,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,12051_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,12039,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,12053,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,12039,Solar_Radiation,Irradiation,1440,W/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		if ($subpark_id == 390) { // Block D
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,12054,pac,Active%20Power,15,MW,1;0,1,0,12039,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,12054,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block D(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,12039,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12039,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12039,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,12039,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,12039,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,12051_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,12039,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,12054,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,12039,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
}	

else if ($park_no == 61) { // Rajasthan
		if ($subpark_id == 314) { // Block 1
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9282,pac,Active%20Power,15,MW,1;0,1,0,9279,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,9282,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block 1(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,9279,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9279,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9279,Ambient_Temperature,Ambient_Temperature,15,°C,\'darkred\';0,1,0,9279,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,9279,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,9288_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,9279,Ambient_Temperature,null,9,Avg.%20Ambient_Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,9282,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,9279,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		} 
		
		if ($subpark_id == 315) { // Block 2
			if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9283,pac,Active%20Power,15,MW,1;0,1,0,9279,Solar_Radiation,Irradiation,1440,W/mSQUA,\'Gold\';&diffs=0,1,0,9283,EAE,null,8,Active%20Energy%20Export,black,2,%20MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Block 2(EM)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,9279,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9279,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9279,Ambient_Temperature,Ambient_Temperature,15,°C,\'darkred\';0,1,0,9279,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,9279,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,9288_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,9279,Ambient_Temperature,null,9,Avg.%20Ambient_Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 2;
			}
			else{
				$args = "0,1,0,9283,EAE,Yield (EM),1440,MWh,'green',1;0,1,0,9279,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		} 
}
else if($park_no == 56){ // Amplus Rudrapur


if($subpark_id==370){ // Inverter1

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=56&phase=RUDINV1graph1&args=0,1,0,11621,Pac,AC%20Power,15,kW,4;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=56&phase=RUDINV1graph2&args=0,1,0,11621,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11621,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=56&phase=RUDINV1graph3&args=0,1,0,11621,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,11624,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,11624,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,11624,GridMs.Hz,Frequency,15,Hz,7;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=56&phase=RUDINV1PR&defaults=0,1,2,3,4&args=0,1,0,11621,Inv_PR,Inv1%20PR,15,%,4;0,1,0,11621,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11621,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramRud.php?park_no=56&phase=RUDINV1EFF&args=0,1,0,11621,Inv_Eff,Inverter1,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
			
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramRud.php?park_no=56&phase=RUDINV1ACPR&defaults=0,1,2,3,4&args=0,1,0,11621,Inv_AC_PR_600,Inv1%20AC%20PR,15,%,4;0,1,0,11621,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11621,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramRud.php?park_no=56&phase=RUDINV1DCPR&defaults=0,1,2,3,4&args=0,1,0,11621,DC_Vol_Coeff,Inv1(DC_Voltage),15,V,4;0,1,0,12690,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12690,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
			
			$count = 7;
		}
if($subpark_id==372){ // Energy Meter
	 if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=56&phase=Rudtag&defaults=0,1&args=0,1,0,7772,Activepower_Total,Active Power,15,kW,4;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,7772_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=56&phase=RUDenergyg2&defaults=0,1,2,3&args=0,1,0,7772,System_PR,System PR,15,%,1;0,1,0,11621,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11621,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,7772,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,12690,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}
	else if($subpark_id == 371){ // SMU
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=56&phase=Rudsmu1inv1&args=0,1,0,11590,IDC1,String%20Current%201.1,5,A,15;0,1,0,11590,IDC2,String%20Current%201.2,5,A,2;0,1,0,11590,IDC3,String%20Current%201.3,5,A,3;0,1,0,11590,IDC4,String%20Current%201.4,5,A,4;0,1,0,11590,IDC5,String%20Current%201.5,5,A,15;0,1,0,12690,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV1/1900732799/SMA1" border="0"></iframe></td></tr>';

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=56&phase=Rudsmu1inv2&args=0,1,0,11590,IDC6,String%20Current%202.1,5,A,1;0,1,0,11590,IDC7,String%20Current%202.2,5,A,2;0,1,0,11590,IDC8,String%20Current%202.3,5,A,3;0,1,0,11590,IDC9,String%20Current%202.4,5,A,4;0,1,0,11590,IDC10,String%20Current%202.5,5,A,15;0,1,0,12690,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV2/1900732898/SMA2" border="0"></iframe></td></tr>';

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=56&phase=Rudsmu1inv3&args=0,1,0,11590,IDC11,String%20Current%203.1,5,A,1;0,1,0,11590,IDC12,String%20Current%203.2,5,A,2;0,1,0,11590,IDC13,String%20Current%203.3,5,A,3;0,1,0,11590,IDC14,String%20Current%203.4,5,A,4;0,1,0,11590,IDC15,String%20Current%203.5,5,A,15;0,1,0,12690,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV3/1900732554/SMA3" border="0"></iframe></td></tr>';
			$count = 3;
		}
	if($subpark_id==373){ // WS
	$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&park_no=56&phase=RUDweather&args=0,1,0,12690,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12690,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12690,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12690,Module_Temperature,Module_Temperature,15,°C,\'green\';&diffs=0,1,0,7772,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,12690,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,12690,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
	$count = 1;
	}	

}
else if($park_no == 55){ // Amplus lalpur
	
	if($subpark_id==366){ // Inverter1

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=55&phase=LAPINV1graph1&args=0,1,0,12669,PAC_Total,AC%20Power,15,kW,4;0,1,0,10142,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=55&phase=LAPINV1graph2&args=0,1,0,12669,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12669,IDC_Mittelwert,DC%20Current,15,A,7&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=55&phase=LAPINV1graph3&args=0,1,0,12669,UAC_L1/N,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12669,UAC_L2/N,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12669,UAC_L3/N,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12669,Frequenz,Frequency,15,Hz,7;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=55&phase=LAPINV1PR&defaults=0,1,2,3,4&args=0,1,0,12672,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12672,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12672,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramLap.php?park_no=55&phase=LAPINV1EFF&args=0,1,0,12669,Inv_Eff,Inverter1,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';

/*			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramLap.php?park_no=55&phase=LAPINV1EFF&args=0,1,0,12669,INV_EFF,Inverter 1,15,%,8;0,1,0,12671,INV_EFF,Inverter 2,15,%,9;0,1,0,12672,INV_EFF,Inverter 3,15,%,6;0,1,0,12669,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';*/
			
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramLap.php?park_no=55&phase=LAPINV1ACPR&defaults=0,1,2,3,4&args=0,1,0,12669,Inv_AC_PR_600,Inv1%20AC%20PR,15,%,4;0,1,0,12672,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12672,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramLap.php?park_no=55&phase=LAPINV1DCPR&defaults=0,1,2,3,4&args=0,1,0,12669,DC_Vol_Coeff,Inv1(DC_Voltage),15,V,4;0,1,0,12672,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12672,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
			
			$count = 7;
		}

	if($subpark_id==368){ // Energy Meter
	 if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=55&phase=LALtag&defaults=0,1&args=0,1,0,11588,Activepower_Total,Active Power,15,kW,4;0,1,0,10142,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,11588_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=55&phase=Lalenergyg&defaults=0,1,2,3&args=0,1,0,11588,System_PR,System PR,15,%,1;0,1,0,12672,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12672,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,11588,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,10142,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}
	
	else if($subpark_id == 369){ // Weather Station
		$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&park_no=55&phase=tag&args=0,1,0,10142,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10142,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10142,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,10142,Module_Temperature,Module_Temperature,15,°C,\'green\';&diffs=0,1,0,11588,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,10142,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,10142,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
		$count = 1;
		}	

}

else if($park_no == 52){ // Amplus Dominos Nagpur

			if($subpark_id==353){ // Inverter1

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=DOMINV1graph1&args=0,1,0,11624,Pac,AC%20Power,15,kW,4;0,1,0,11623,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=DOMINV1graph2&args=0,1,0,11624,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11624,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=DOMINV1graph3&args=0,1,0,11624,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,11624,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,11624,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,11624,GridMs.Hz,Frequency,15,Hz,7;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=52&phase=INV1PR&defaults=0,1,2,3,4&args=0,1,0,11624,Inv_PR,Inv1%20PR,15,%,4;0,1,0,11624,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11624,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramdom.php?park_no=52&phase=INV1EFF&args=0,1,0,11624,Inv_Eff,Inverter1,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
			
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramdom.php?park_no=52&phase=INV1ACPR&defaults=0,1,2,3,4&args=0,1,0,11624,Inv_AC_PR,Inv1%20AC%20PR,15,%,4;0,1,0,11624,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11623,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramdom.php?park_no=52&phase=INV1DCPR&defaults=0,1,2,3,4&args=0,1,0,11624,DC_Vol_Coeff,Inv1(DC_Voltage),15,V,4;0,1,0,11624,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11623,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
			
			$count = 7;
		}

		else if($subpark_id == 354){ // SMU
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=domsmu1inv1&args=0,1,0,11622,IDC1,String%20Current%201.1,5,A,15;0,1,0,11622,IDC2,String%20Current%201.2,5,A,1;0,1,0,11622,IDC3,String%20Current%201.3,5,A,2;0,1,0,11622,IDC4,String%20Current%201.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV1/1900723491/SMA1" border="0"></iframe></td></tr>';

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=domsmu1inv2&args=0,1,0,11622,IDC5,String%20Current%202.1,5,A,15;0,1,0,11622,IDC6,String%20Current%202.2,5,A,1;0,1,0,11622,IDC7,String%20Current%202.3,5,A,2;0,1,0,11622,IDC8,String%20Current%202.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV2/1900723458/SMA2" border="0"></iframe></td></tr>';

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=domsmu1inv3&args=0,1,0,11622,IDC9,String%20Current%203.1,5,A,15;0,1,0,11622,IDC10,String%20Current%203.2,5,A,1;0,1,0,11622,IDC11,String%20Current%203.3,5,A,2;0,1,0,11622,IDC12,String%20Current%203.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV3/1900723466/SMA3" border="0"></iframe></td></tr>';

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=domsmu1inv4&args=0,1,0,11623,IDC1,String%20Current%204.1,5,A,15;0,1,0,11623,IDC2,String%20Current%204.2,5,A,1;0,1,0,11623,IDC3,String%20Current%204.3,5,A,2;0,1,0,11623,IDC4,String%20Current%204.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV4/1900723427/SMA4" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=domsmu1inv5&args=0,1,0,11623,IDC5,String%20Current%205.1,5,A,15;0,1,0,11623,IDC6,String%20Current%205.2,5,A,1;0,1,0,11623,IDC7,String%20Current%205.3,5,A,2;0,1,0,11623,IDC8,String%20Current%205.4,5,A,3;0,1,0,11623,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV5/1900724435/SMA5" border="0"></iframe></td></tr>';
			$count = 5;
		}
	
		else if($subpark_id==355){ // Energy Meter
		 if ($phase=="tag"){

				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=Domtag&defaults=0,1&args=0,1,0,10070,Activepower_Total,Active Power,15,kW,4;0,1,0,11623,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,10070_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=52&phase=energyg2&defaults=0,1,2,3&args=0,1,0,10070,System_PR,System PR,15,%,1;0,1,0,11628,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,11628,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
				$count = 2;

			}
			else{
				$args = "0,1,0,10070,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,11623,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		
		else if($subpark_id == 356){ // Weather Station
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=52&phase=domweather&defaults=0,1,2&args=0,1,0,11623,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,11623,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,11623,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,11622,Wind_Speed_Act,Wind Speed,15,m/s,6;&diffs=0,1,0,10070,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,11623,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,11623,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 1;
		}
		
		

}else if($park_no == 54){ // Amplus Indus Nagpur

			if($subpark_id==361){ // Inverter1

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=54&phase=IndINV1G1&args=0,1,0,12667,PAC_Total,AC%20Power,15,kW,4;0,1,0,10452,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=54&phase=IndINV1G2&args=0,1,0,12667,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12667,IDC_Mittelwert,DC%20Current,15,A,7&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=54&phase=IndINV1G3&args=0,1,0,12667,UAC_L1/N,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12667,UAC_L2/N,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12667,UAC_L3/N,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12667,Frequenz,Frequency,15,Hz,7;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=54&phase=IndINV1PR&defaults=0,1,2,3,4&args=0,1,0,12667,Inv_PR,Inv%20PR,15,%,4;0,1,0,13004,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,13004,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramIndus.php?park_no=54&phase=IndINV1EFF&args=0,1,0,12667,Inv1_Eff,Inverter1,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
			
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramIndus.php?park_no=54&phase=IndINV1ACPR&defaults=0,1,2,3,4&args=0,1,0,12667,Inv_AC_PR_600,Inverter%20PR,15,%,4;0,1,0,10452,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,10452,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramIndus.php?park_no=54&phase=IndINV1DCPR&defaults=0,1,2,3,4&args=0,1,0,12667,DC_Vol_Coeff,Inv(DC_Voltage),15,V,4;0,1,0,10452,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,10452,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
			
			$count = 7;
		}

		else if($subpark_id == 362){ // SMU
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=54&phase=indsmu1inv1&args=0,1,0,11629,IDC1,String%20Current%201.1,5,A,15;0,1,0,11629,IDC2,String%20Current%201.2,5,A,1;0,1,0,11629,IDC3,String%20Current%201.3,5,A,2;0,1,0,11629,IDC4,String%20Current%201.4,5,A,3;0,1,0,11629,IDC5,String%20Current%201.5,5,A,6;0,1,0,11629,IDC6,String%20Current%201.6,5,A,6;0,1,0,11629,IDC7,String%20Current%201.7,5,A,7;0,1,0,11629,IDC8,String%20Current%201.8,5,A,8;0,1,0,11629,IDC9,String%20Current%201.9,5,A,9;0,1,0,11629,IDC10,String%20Current%201.10,5,A,11;0,1,0,10452,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU1/139F5003392201N275/SMA1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=54&phase=indsmu1inv2&args=0,1,0,11714,IDC1,String%20Current%202.1,5,A,15;0,1,0,11714,IDC2,String%20Current%202.2,5,A,1;0,1,0,11714,IDC3,String%20Current%202.3,5,A,2;0,1,0,11714,IDC4,String%20Current%202.4,5,A,3;0,1,0,11714,IDC5,String%20Current%202.5,5,A,6;0,1,0,11714,IDC6,String%20Current%202.6,5,A,6;0,1,0,11714,IDC7,String%20Current%202.7,5,A,7;0,1,0,11714,IDC8,String%20Current%202.8,5,A,8;0,1,0,11714,IDC9,String%20Current%202.9,5,A,9;0,1,0,11714,IDC10,String%20Current%202.10,5,A,11;0,1,0,10452,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU2/139F5003386601N275/SMA2" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=54&phase=indsmu3inv3&args=0,1,0,13395,IDC1,String%20Current%203.1,5,A,15;0,1,0,13395,IDC2,String%20Current%203.2,5,A,1;0,1,0,13395,IDC3,String%20Current%203.3,5,A,2;0,1,0,13395,IDC4,String%20Current%203.4,5,A,3;0,1,0,13395,IDC5,String%20Current%203.5,5,A,6;0,1,0,13395,IDC6,String%20Current%203.6,5,A,6;0,1,0,13395,IDC7,String%20Current%203.7,5,A,7;0,1,0,13395,IDC8,String%20Current%203.8,5,A,8;0,1,0,13395,IDC9,String%20Current%203.9,5,A,9;0,1,0,13395,IDC10,String%20Current%203.10,5,A,11;0,1,0,13395,IDC11,String%20Current%203.11,5,A,15;0,1,0,10452,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU3/139F5003396201N285/SMA3" border="0"></iframe></td></tr>';
			$count = 3;
		}
	
		else if($subpark_id==364){ // Energy Meter
		  if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=54&phase=IndEM1&defaults=0,1&args=0,1,0,10151,Activepower_Total,Active Power(EM1),15,kW,4;0,1,0,10452,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,10151_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=54&phase=energyg1Indus&defaults=0,1,2,3&args=0,1,0,10151,System_PR,System PR1,15,%,1;0,1,0,12668,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,13004,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

			}
			else{
				$args = "0,1,0,10151,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,10452,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		
		else if($subpark_id == 365){ // Weather Station
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=54&phase=indweather&defaults=0,1,2&args=0,1,0,10452,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10452,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,10452,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,10452,Wind_Speed,Wind Speed,15,m/s,6;&diffs=0,1,0,10151,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,10452,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,10452,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 1;
		}
}
else if($park_no == 53){ // Amplus Royal Heritage Pune

			if($subpark_id==357){ // Inverter1

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=RYINV1G1&args=0,1,0,11721,Pac,AC%20Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=RYINV1G2&args=0,1,0,11721,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,11721,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=RYINV1G3&args=0,1,0,11721,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,11721,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,11721,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,11721,GridMs.Hz,Frequency,15,Hz,7;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=53&phase=INV1PR&defaults=0,1,2,3,4&args=0,1,0,11722,Inv_PR,Inv%20PR,15,%,4;0,1,0,11722,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramroy.php?park_no=53&phase=RINV1EFF&args=0,1,0,11721,Inv_Eff,Inverter1,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
			
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramroy.php?park_no=53&phase=RINV1ACPR&defaults=0,1,2,3,4&args=0,1,0,11721,Inv_AC_PR,Inverter%20PR,15,%,4;0,1,0,12692,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramroy.php?park_no=53&phase=RINV1DCPR&defaults=0,1,2,3,4&args=0,1,0,11721,DC_Vol_Coeff,Inv(DC_Voltage),15,V,4;0,1,0,12692,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
			
			$count = 7;
		}

		else if($subpark_id == 358){ // SMU
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=roysmu1inv1&args=0,1,0,11724,IDC1,String%20Current%201.1,5,A,15;0,1,0,11724,IDC2,String%20Current%201.2,5,A,1;0,1,0,11724,IDC3,String%20Current%201.3,5,A,2;0,1,0,11724,IDC4,String%20Current%201.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU1/1900724647/SMA1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=roysmu1inv2&args=0,1,0,11724,IDC5,String%20Current%202.1,5,A,15;0,1,0,11724,IDC6,String%20Current%202.2,5,A,1;0,1,0,11724,IDC7,String%20Current%202.3,5,A,2;0,1,0,11724,IDC8,String%20Current%202.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU1/1900723471/SMA2" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=roysmu1inv3&args=0,1,0,11724,IDC9,String%20Current%203.1,5,A,15;0,1,0,11724,IDC10,String%20Current%203.2,5,A,1;0,1,0,11724,IDC11,String%20Current%203.3,5,A,2;0,1,0,11724,IDC12,String%20Current%203.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU1/1900723352/SMA3" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=roysmu1inv4&args=0,1,0,11724,IDC13,String%20Current%204.1,5,A,15;0,1,0,11724,IDC14,String%20Current%204.2,5,A,1;0,1,0,11724,IDC15,String%20Current%204.3,5,A,2;0,1,0,11724,IDC16,String%20Current%204.4,5,A,3;0,1,0,12692,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU1/1900725115/SMA4" border="0"></iframe></td></tr>';
			$count = 4;
		}
	
		else if($subpark_id==359){ // Energy Meter
		  if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=Roytag&defaults=0,1&args=0,1,0,10071,Activepower_Total,Active Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,10071_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=53&phase=energyg2&defaults=0,1,2,3&args=0,1,0,10071,System_PR,System PR,15,%,1;0,1,0,12692,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

			}
			else{
				$args = "0,1,0,10071,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,12692,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
		}
		
		else if($subpark_id == 360){ // Weather Station
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=royweather&defaults=0,1,2&args=0,1,0,12692,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12692,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12692,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,12692,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12692,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&diffs=0,1,0,10071,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,12692,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,12692,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 1;
		}
		else if($subpark_id == 423){ // Load
				//$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=Royload&defaults=0,1&args=0,1,0,10071,Activepower_Total,Solar Active Power,15,kW,4;0,1,0,10071,PAC_G,Grid Active Power,15,kW,6;0,1,0,12694,Activepower_Total,DG power,15,kW,8;0,1,0,10071,LOAD,Load,15,kW;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=Royload&defaults=0,1&args=0,1,0,10071,Activepower_Total,Solar Active Power,15,kW,4;0,1,0,10071,PAC_G,Grid Active Power,15,kW,6;0,1,0,12694,Activepower_Total,DG power,15,kW,8;0,1,0,10071,LOAD,Load,15,kW,\'Gold\';0,1,0,12692,Solar_Radiation,Irradiation,15,W/m&sup2,\'Violet\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
				$count = 1;
		}
		
		

}else if ($park_no == 62){ //Punjab4
		if ($subpark_id==375){ // Blk 1
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,11813,PAC,AC%20Power,15,MW,1;&diffs=0,1,0,11813,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,11751_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,11813,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		if ($subpark_id==376){ // Blk 2
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,11867,PAC,AC%20Power,15,MW,1&diffs=0,1,0,11867,E_Total_Export,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=EM_HTP" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,11751_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,11867,E_Total_Export,Yield (EM),1440,MWh,'green',1;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
}
else if($park_no == 57){ // Amplus Origami Bangaluru 

			if($subpark_id==378){ // Inverter1

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=ORGINV1graph1&args=0,1,0,12028,Pac,AC%20Power,15,kW,4;0,1,0,12029,Solar_Radiation_1,0°Irradiation,15,W/m²,\'Gold\';0,1,0,12029,Solar_Radiation,10°Irradiation,15,W/m²,\'Red\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=ORGINV1graph2&args=0,1,0,12028,A.Ms.Vol-B.Ms.Vol,DC%20Voltage,15,V,8;0,1,0,12028,A.Ms.Amp-B.Ms.Amp,DC%20Current,15,A,7&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=ORGINV1graph3&args=0,1,0,12028,GridMs.PhV.phsA,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12028,GridMs.PhV.phsB,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12028,GridMs.PhV.phsC,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12028,GridMs.Hz,Frequency,15,Hz,7;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=57&phase=ORGINV1PR&defaults=0,1,2,3,4&args=0,1,0,12028,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12021,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12028,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramorg.php?park_no=57&phase=ORGINV1EFF&args=0,1,0,12028,Inv_Eff,Inverter1,15,V,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
			
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramorg.php?park_no=57&phase=ORGINV1ACPR&defaults=0,1,2,3,4&args=0,1,0,12028,Inv_AC_PR_600,Inv1%20AC%20PR,15,%,4;0,1,0,12028,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12028,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramorg.php?park_no=57&phase=ORGINV1DCPR&defaults=0,1,2,3,4&args=0,1,0,12028,DC_Vol_Coeff,Inv1(DC_Voltage),15,V,4;0,1,0,12028,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12028,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
			
			$count = 7;
		}

		else if($subpark_id == 379){ // SMU
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=orgsmu1inv1&args=0,1,0,12031,IDC1,String%20Current%201.1,5,A,15;0,1,0,12031,IDC2,String%20Current%201.2,5,A,1;0,1,0,12031,IDC3,String%20Current%201.3,5,A,2;0,1,0,12031,IDC4,String%20Current%201.4,5,A,3;0,1,0,12029,Solar_Radiation_1,0°Irradiation,15,W/m²,\'Gold\';0,1,0,12029,Solar_Radiation,10°Irradiation,15,W/m²,\'Red\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV1/1900741419/SMA1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=orgsmu1inv2&args=0,1,0,12031,IDC5,String%20Current%202.1,5,A,15;0,1,0,12031,IDC6,String%20Current%202.2,5,A,1;0,1,0,12031,IDC7,String%20Current%202.3,5,A,2;0,1,0,12031,IDC8,String%20Current%202.4,5,A,3;0,1,0,12029,Solar_Radiation_1,0°Irradiation,15,W/m²,\'Gold\';0,1,0,12029,Solar_Radiation,10°Irradiation,15,W/m²,\'Red\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV2/1900741251/SMA2" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=orgsmu1inv3&args=0,1,0,12031,IDC9,String%20Current%203.1,5,A,15;0,1,0,12031,IDC10,String%20Current%203.2,5,A,1;0,1,0,12031,IDC11,String%20Current%203.3,5,A,2;0,1,0,12031,IDC12,String%20Current%203.4,5,A,3;0,1,0,12029,Solar_Radiation_1,0°Irradiation,15,W/m²,\'Gold\';0,1,0,12029,Solar_Radiation,10°Irradiation,15,W/m²,\'Red\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV3/1900741379/SMA3" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=orgsmu1inv4&args=0,1,0,12031,IDC13,String%20Current%204.1,5,A,15;0,1,0,12031,IDC14,String%20Current%204.2,5,A,1;0,1,0,12031,IDC15,String%20Current%204.3,5,A,2;0,1,0,12031,IDC16,String%20Current%204.4,5,A,3;0,1,0,12029,Solar_Radiation_1,0°Irradiation,15,W/m²,\'Gold\';0,1,0,12029,Solar_Radiation,10°Irradiation,15,W/m²,\'Red\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV4/1900741315/SMA4" border="0"></iframe></td></tr>';
			$count = 4;
		}
	
		else if($subpark_id==380){ // Energy Meter
			  if ($phase=="tag"){

				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=Orgtag&defaults=0,1&args=0,1,0,11615,Activepower_Total,Active Power,15,kW,4;0,1,0,12029,Solar_Radiation_1,0°Irradiation,15,W/m²,\'Gold\';0,1,0,12029,Solar_Radiation,10°Irradiation,15,W/m²,\'Red\';&sums=0,1,0,11615_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=57&phase=Orgenergyg2&defaults=0,1,2,3&args=0,1,0,11615,System_PR,System PR,15,%,1;0,1,0,12028,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12028,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
				$count = 2;

			}
			else{
				$args = "0,1,0,11615,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,12029,Solar_Radiation_1,0°Irradiation,15,W/m²,\'Gold\';0,1,0,12029,Solar_Radiation,10°Irradiation,15,W/m²,\'Red\';,0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
			}
	}	
	else if($subpark_id == 381){ // Weather Station
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=OMGweather&defaults=0,1,2&args=0,1,0,12029,Solar_Radiation_1,0°Irradiation,15,W/m²,\'Gold\';0,1,0,12029,Solar_Radiation,10°Irradiation,15,W/m²,\'Red\';0,1,0,12029,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12029,Module_Temperature,Module_Temperature,15,°C,\'green\';&diffs=0,1,0,11615,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,12029,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,12029,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 1;
		}
		
		

}
else if($park_no == 58){ // Amplus Polyer Nagpur 

			if($subpark_id==419){ // Inverter1

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=58&phase=PolyINV1graph1&args=0,1,0,12660,PAC_Total,AC%20Power,15,kW,4;0,1,0,12417,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=58&phase=PolyINV1graph2&args=0,1,0,12660,UAC_Mittelwert,DC%20Voltage,15,V,8;0,1,0,12660,IDC_Mittelwert,DC%20Current,15,A,7&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=58&phase=PolyINV1graph3&args=0,1,0,12660,UAC_L1/N,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,12660,UAC_L2/N,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,12660,UAC_L3/N,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,12660,Frequenz,Frequency,15,Hz,7;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=58&phase=PolyINV1PR&defaults=0,1,2,3,4&args=0,1,0,12660,Inv_PR,Inv1%20PR,15,%,4;0,1,0,12660,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12660,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramPoly.php?park_no=58&phase=PolyINV1EFF&args=0,1,0,12660,Inv_Eff,Inverter1,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
			
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramPoly.php?park_no=58&phase=PolyINV1ACPR&defaults=0,1,2,3,4&args=0,1,0,12660,Inv_AC_PR_600,Inv1%20AC%20PR,15,%,4;0,1,0,12417,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramPoly.php?park_no=58&phase=PolyINV1DCPR&defaults=0,1,2,3,4&args=0,1,0,12660,DC_Vol_Coeff,Inv1(DC_Voltage),15,V,4;0,1,0,12417,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
			
			$count = 7;
		}

		else if($subpark_id == 420){ // SMU
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=58&phase=polysmu1inv1&args=0,1,0,12633,IDC1,String%20Current%201.1,5,A,15;0,1,0,12633,IDC2,String%20Current%201.2,5,A,1;0,1,0,12633,IDC3,String%20Current%201.3,5,A,2;0,1,0,12633,IDC4,String%20Current%201.4,5,A,3;0,1,0,12633,IDC5,String%20Current%201.5,5,A,4;0,1,0,12633,IDC6,String%20Current%201.6,5,A,6;0,1,0,12633,IDC7,String%20Current%201.7,5,A,7;0,1,0,12633,IDC8,String%20Current%201.8,5,A,8;0,1,0,12633,IDC9,String%20Current%201.9,5,A,9;0,1,0,12633,IDC10,String%20Current%201.10,5,A,10;0,1,0,12633,IDC11,String%20Current%201.11,5,A,11;0,1,0,12417,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMCB1/139F5003535101N445/INV1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=58&phase=polysmu2inv2&args=0,1,0,12420,IDC1,String%20Current%202.1,5,A,15;0,1,0,12420,IDC2,String%20Current%202.2,5,A,1;0,1,0,12031,IDC3,String%20Current%202.3,5,A,2;0,1,0,12420,IDC4,String%20Current%202.4,5,A,3;0,1,0,12420,IDC5,String%20Current%202.5,5,A,4;0,1,0,12420,IDC6,String%20Current%202.6,5,A,6;0,1,0,12420,IDC7,String%20Current%202.7,5,A,7;0,1,0,12420,IDC8,String%20Current%202.8,5,A,8;0,1,0,12420,IDC9,String%20Current%202.9,5,A,9;0,1,0,12420,IDC10,String%20Current%202.10,5,A,10;0,1,0,12420,IDC11,String%20Current%202.11,5,A,11;0,1,0,12417,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMCB1/139F5003534601N445/INV2" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=58&phase=polysmu3inv3&args=0,1,0,12419,IDC1,String%20Current%203.1,5,A,15;0,1,0,12419,IDC2,String%20Current%203.2,5,A,1;0,1,0,12419,IDC3,String%20Current%203.3,5,A,2;0,1,0,12419,IDC4,String%20Current%203.4,5,A,3;0,1,0,12419,IDC5,String%20Current%203.5,5,A,4;0,1,0,12419,IDC6,String%20Current%203.6,5,A,6;0,1,0,12419,IDC7,String%20Current%203.7,5,A,7;0,1,0,12419,IDC8,String%20Current%202.8,5,A,8;0,1,0,12419,IDC9,String%20Current%203.9,5,A,9;0,1,0,12419,IDC10,String%20Current%203.10,5,A,10;0,1,0,12419,IDC11,String%20Current%203.11,5,A,11;0,1,0,12417,Solar_Radiation,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMCB1/139F5003534901N445/INV3" border="0"></iframe></td></tr>';
			$count = 3;
		}
	
		else if($subpark_id==421){ // Energy Meter
			  if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=58&phase=Polytag&defaults=0,1&args=0,1,0,12037,Activepower_Total,Active Power,15,kW,4;0,1,0,12417,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,12037_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=58&phase=Polyenergyg2&defaults=0,1,2,3&args=0,1,0,12037,System_PR,System PR,15,%,1;0,1,0,12660,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12660,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,12037,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,12417,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}	
	else if($subpark_id == 422){ // Weather Station
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=58&phase=Polyweather&defaults=0,1,2&args=0,1,0,12417,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12417,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,12417,Module_Temperature,Module_Temperature,15,°C,\'green\';0,1,0,12417,Wind_Speed,Wind Speed,15,m/s,6;&diffs=0,1,0,12037,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,12417,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,12417,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 1;
		}
		
		

}
else if($park_no == 59){ // Amplus Yamaha

if($subpark_id==425){ // Block A
		if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=59&phase=YamahaBlkA&defaults=0,1&args=0,1,0,12895,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,12895_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=59&phase=YamahaEnBlkA&defaults=0,1,2,3&args=0,1,0,12210,System_PR,System PR,15,%,1;0,1,0,12903,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12903,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,12896,Forward_Active_Energy,Yield(EM),1440,kWh,'green',1;0,1,0,12209,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}
	if($subpark_id==426){ // Block B
		if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=59&phase=YamahaBlkB&defaults=0,1&args=0,1,0,12899,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,12899_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=59&phase=YamahaEnBlkB&defaults=0,1,2,3&args=0,1,0,12899,System_PR,System PR,15,%,1;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,12899,Forward_Active_Energy,Yield(EM),1440,kWh,'green',1;0,1,0,12209,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}
	if($subpark_id==427){ // Block C
		if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=59&phase=YamahaBlkC&defaults=0,1&args=0,1,0,12897,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,12897_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=59&phase=YamahaEnBlkC&defaults=0,1,2,3&args=0,1,0,12897,System_PR,System PR,15,%,1;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,12897,Forward_Active_Energy,Yield(EM),1440,kWh,'green',1;0,1,0,12209,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}
	if($subpark_id==428){ // Block D
		if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=59&phase=YamahaBlkD&defaults=0,1&args=0,1,0,12898,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,12898_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=59&phase=YamahaEnBlkD&defaults=0,1,2,3&args=0,1,0,12898,System_PR,System PR,15,%,1;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,12898,Forward_Active_Energy,Yield(EM),1440,kWh,'green',1;0,1,0,12209,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}

	if($subpark_id==429){ // Block E
		if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=59&phase=YamahaBlkE&defaults=0,1&args=0,1,0,12896,Activepower_Total,AC Power,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,12896_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=59&phase=YamahaEnBlkE&defaults=0,1,2,3&args=0,1,0,12896,System_PR,System PR,15,%,1;0,1,0,12209,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12209,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,12895,Forward_Active_Energy,Yield(EM),1440,kWh,'green',1;0,1,0,12209,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}
}
else if($park_no == 21){// MAS SOLAR
	
		if($subpark_id==431){ // Inverter Graph
		
				if ($phase=="tag"){
					
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=21&phase=MasINV&args=0,1,0,12909,Pac,INV1%20AC%20Power,15,kW,1;0,1,0,12911,Pac,INV2%20AC%20Power,15,kW,2;0,1,0,12910,Pac,INV3%20AC%20Power,15,kW,3;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverters" border="0"></iframe></td></tr><br>';
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=21&phase=MASWS&defaults=0,1,2&args=0,1,0,12907,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12907,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,12907,MODULE_TEMP ,Module Temperature,15,°C,\'green\';&sums=0,1,0,12907,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,12908,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&avgs=0,1,0,12907,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
					$count = 2;
				}
				else{
					$args = "0,1,0,12909,E_Total,INV1 Yield(EM),1440,kWh,'green',1;0,1,0,12911,E_Total,INV2 Yield(EM),1440,kWh,'Black',1;0,1,0,12910,E_Total,INV3 Yield(EM),1440,kWh,'Blue',1;0,1,0,12907,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
					$count = 1;
				}
		
		}
		if($subpark_id==432){ // Energy meter Graph
		
			if ($phase=="tag"){
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=21&phase=MasEM&args=0,1,0,12908,Activepower_Total,AC%20Power,15,kW,1&sums=0,1,0,12908_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Energy Meter" border="0"></iframe></td></tr><br>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=21&phase=MASWS&defaults=0,1,2&args=0,1,0,12907,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12907,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,12907,MODULE_TEMP ,Module Temperature,15,°C,\'green\';&sums=0,1,0,12907,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,12908,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&avgs=0,1,0,12907,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
			$count = 2;
			}
			else{
			$args = "0,1,0,12908,Forward_Active_Energy,Yield (EM),1440,kWh,'green',1;0,1,0,12907,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
			}
		}
		
		if($subpark_id==433){ // weather station
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=21&phase=MASWS&defaults=0,1,2&args=0,1,0,12907,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12907,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,12907,MODULE_TEMP ,Module Temperature,15,°C,\'green\';&sums=0,1,0,12907,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,12908,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&avgs=0,1,0,12907,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
		
	}
	else if($park_no == 72){ // Amplus Forties

			if($subpark_id==435){ // Inverter1

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=72&phase=FortINV1graph1&args=0,1,0,13478,PAC,AC%20Power,15,kW,4;0,1,0,13475,Irradiance,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=72&phase=FortINV1graph2&args=0,1,0,13478,UDC,DC%20Voltage,15,V,8;0,1,0,13478,IDC,DC%20Current,15,A,7&&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=72&phase=FortINV1graph3&args=0,1,0,13478,UAC_1N,INV1%20AC%20Voltage-%20L1,15,V,8;0,1,0,13478,UAC_2N,INV1%20AC%20Voltage-%20L2,15,V,9;0,1,0,13478,UAC_3N,INV1%20AC%20Voltage-%20L3,15,V,5;0,1,0,13478,FAC,Frequency,15,Hz,7;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=72&phase=FortINV1PR&defaults=0,1,2,3,4&args=0,1,0,13478,Inv_PR,Inv1%20PR,15,%,4;0,1,0,13478,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,13478,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramFort.php?park_no=72&phase=FortINV1EFF&args=0,1,0,13478,Inv1_Eff,Inverter1,15,%,8;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 5" border="0"></iframe></td></tr>';
			
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramFort.php?park_no=72&phase=FortINV1ACPR&defaults=0,1,2,3,4&args=0,1,0,13478,Inv_AC_PR,Inv1%20AC%20PR,15,%,4;0,1,0,13475,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,13475,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramFort.php?park_no=72&phase=FortINV1DCPR&defaults=0,1,2,3,4&args=0,1,0,13478,DC_Vol_Coeff,Inv1(DC_Voltage),15,V,4;0,1,0,13475,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,13475,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-7" border="0"></iframe></td></tr>';
			
			$count = 7;
		}

		else if($subpark_id == 436){ // SMU
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=72&phase=Fortsmu1inv1&args=0,1,0,13477,IDC1,String%20Current%201.1,5,A,15;0,1,0,13477,IDC2,String%20Current%201.2,5,A,1;0,1,0,13477,IDC3,String%20Current%201.3,5,A,2;0,1,0,13477,IDC4,String%20Current%201.4,5,A,3;0,1,0,13475,Irradiance,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV/FRONIUS/27042143" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=72&phase=Fortsmu2inv2&args=0,1,0,13477,IDC5,String%20Current%202.1,5,A,15;0,1,0,13477,IDC6,String%20Current%202.2,5,A,1;0,1,0,13477,IDC7,String%20Current%202.3,5,A,2;0,1,0,13477,IDC8,String%20Current%202.4,5,A,3;0,1,0,13475,Irradiance,Tilt%20Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV/FRONIUS/27042139" border="0"></iframe></td></tr>';
			$count = 2;
		}
	
		else if($subpark_id==437){ // Energy Meter
			  if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=72&phase=Forttag&defaults=0,1&args=0,1,0,13476,Activepower_Total,Active Power,15,kW,4;0,1,0,13475,Irradiance,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,12037_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=72&phase=Fortenergyg2&defaults=0,1,2,3&args=0,1,0,13476,System_PR,System PR,15,%,1;0,1,0,13479,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,13479,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$count = 2;

		}
		else{
			$args = "0,1,0,13476,Forward_Active_Energy,EM_CONZERV,1440,kWh,'green',1;0,1,0,13475,Irradiance,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}	
	else if($subpark_id == 438){ // Weather Station
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=72&phase=Fortweather&defaults=0,1,2&args=0,1,0,13475,Irradiance,Irradiation,15,W/m²,\'Gold\';0,1,0,13475,External Temp_2.,Ambient Temperature,15,°C,\'darkred\';0,1,0,13475,T_cell_2,Module_Temperature,15,°C,\'green\';0,1,0,13475,Wind Speed,Wind Speed,15,m/s,6;&diffs=0,1,0,13476,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,13475,Irradiance,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,13475,External Temp_2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 1;
		}
		
		

}
else if($park_no == 22){ // Sowkur Durga
        if($subpark_id==440){
            $diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV1graph1&args=0,1,0,14019,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 1" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv1*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV2graph1&args=0,1,0,14034,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 2" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv2*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV3graph1&args=0,1,0,14016,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 3" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv3*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV4graph1&args=0,1,0,14026,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 4" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv4*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV5graph1&args=0,1,0,14040,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 5" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv5*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV6graph1&args=0,1,0,14020,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 6" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv6*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV7graph1&args=0,1,0,14038,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 7" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv7*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV8graph1&args=0,1,0,14024,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 8" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv8*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV9graph1&args=0,1,0,14033,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 9" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv9*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV10graph1&args=0,1,0,14030,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 10" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv10*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV11graph1&args=0,1,0,14022,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 11" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv11*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV12graph1&args=0,1,0,14031,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 12" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv12*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV13graph1&args=0,1,0,14032,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 13" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv13*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV14graph1&args=0,1,0,14025,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 14" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv14*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV15graph1&args=0,1,0,14021,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 15" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv15*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV16graph1&args=0,1,0,14027,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 16" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv16*/
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurrINV17graph1&args=0,1,0,14015,PAC,AC%20Power,15,kW,4;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=INV- 17" border="0"></iframe></td></tr>';
			$diagrammCode .='<tr><td>&nbsp;</td></tr>';/*inv17*/
			$count = 17;
		}
          			

			if($subpark_id==441){ // INV 58kWShed (600mod)
			
				if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurINV58kWshed&args=0,1,0,14034,PAC,AC%20Power%202,15,kW,1;0,1,0,14040,PAC,AC%20Power%205,15,kW,5;0,1,0,14038,PAC,AC%20Power%207,15,kW,7;0,1,0,14022,PAC,AC%20Power%2011,15,kW,9;0,1,0,14032,PAC,AC%20Power%2013,15,kW,13;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title= INV 58kWShed (600mod)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=22&phase=Sowkurweather&defaults=1,2&args=0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,14029,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,14029,MODULE_TEMP,Module Temperature,15,°C,\'Red\';0,1,0,14023,HUMIDITY,Humidity,15,%,\'Violet\';&etotal=0,1,0,14028_0,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,14029,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,14029,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
				}
				else{
				$args = "0,1,0,14034,E_TOTAL,Yield (EM2),1440,kWh,'green',1;0,1,0,14040,E_TOTAL,Yield (EM5),1440,kWh,'Burlywood',1;0,1,0,14038,E_TOTAL,Yield (EM7),1440,kWh,'darkred',1;0,1,0,14022,E_TOTAL,Yield (EM11),1440,kWh,'Black',1;0,1,0,14032,E_TOTAL,Yield (EM13),1440,kWh,'Blue',1;0,1,0,14029,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
				}
			}
			
			if($subpark_id==442){ // INV 58kWShed (560mod)
			
				if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurINV560mod&args=0,1,0,14019,PAC,AC%20Power%201,15,kW,1;0,1,0,14026,PAC,AC%20Power%204,15,kW,5;0,1,0,14024,PAC,AC%20Power%208,15,kW,7;0,1,0,14030,PAC,AC%20Power%2010,15,kW,9;0,1,0,14025,PAC,AC%20Power%2014,15,kW,13;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title= INV 58kWShed (560mod)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,14029,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,14029,MODULE_TEMP,Module Temperature,15,°C,\'Red\';0,1,0,14023,HUMIDITY,Humidity,15,%,\'Violet\';&etotal=0,1,0,14028_0,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,14029,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,14029,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
				}
				else{
				$args = "0,1,0,14019,E_TOTAL,Yield (EM1),1440,kWh,'green',1;0,1,0,14026,E_TOTAL,Yield (EM4),1440,kWh,'Burlywood',1;0,1,0,14024,E_TOTAL,Yield (EM8),1440,kWh,'darkred',1;0,1,0,14030,E_TOTAL,Yield (EM10),1440,kWh,'Black',1;0,1,0,14025,E_TOTAL,Yield (EM14),1440,kWh,'Blue',1;0,1,0,14029,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
				}
			}
			
			if($subpark_id==443){ // INV 90kWShed (600mod)
			
				if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=22&phase=Sowkur90kWshed&args=0,1,0,14021,PAC,AC%20Power%2015,15,kW,1;0,1,0,14027,PAC,AC%20Power%2016,15,kW,5;0,1,0,14015,PAC,AC%20Power%2017,15,kW,7;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title= INV 90kWShed (600mod)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,14029,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,14029,MODULE_TEMP,Module Temperature,15,°C,\'Red\';0,1,0,14023,HUMIDITY,Humidity,15,%,\'Violet\';&etotal=0,1,0,14028_0,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,14029,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,14029,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
				}
				else{
				$args = "0,1,0,14021,E_TOTAL,Yield (EM15),1440,kWh,'green',1;0,1,0,14027,E_TOTAL,Yield (EM16),1440,kWh,'Burlywood',1;0,1,0,14015,E_TOTAL,Yield (EM17),1440,kWh,'darkred',1;0,1,0,14029,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
				}
			}
			
			if($subpark_id==444){ // INV 30kWShed (240mod)
			
				if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=22&phase=Sowkur30kWshed&args=0,1,0,14016,PAC,AC%20Power%203,15,kW,1;0,1,0,14020,PAC,AC%20Power%206,15,kW,5;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title= INV 30kWShed (240mod)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,14029,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,14029,MODULE_TEMP,Module Temperature,15,°C,\'Red\';0,1,0,14023,HUMIDITY,Humidity,15,%,\'Violet\';&etotal=0,1,0,14028_0,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,14029,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,14029,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
				}
				else{
				$args = "0,1,0,14016,E_TOTAL,Yield (EM3),1440,kWh,'green',1;0,1,0,14020,E_TOTAL,Yield (EM6),1440,kWh,'Burlywood',1;0,1,0,14029,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
				}
			}
			if($subpark_id==445){ // INV 30kWShed (200mod)
			
				if ($phase=="tag"){
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurINV200mod&args=0,1,0,14033,PAC,AC%20Power%209,15,kW,1;0,1,0,14031,PAC,AC%20Power%2012,15,kW,5;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title= INV 30kWShed (200mod)" border="0"></iframe></td></tr><br>';
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,14029,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,14029,MODULE_TEMP,Module Temperature,15,°C,\'Red\';0,1,0,14023,HUMIDITY,Humidity,15,%,\'Violet\';&etotal=0,1,0,14028_0,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,14029,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&avgs=0,1,0,14029,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
				$count = 2;
				}
				else{
				$args = "0,1,0,14033,E_TOTAL,Yield (EM9),1440,kWh,'green',1;0,1,0,14031,E_TOTAL,Yield (EM12),1440,kWh,'Burlywood',1;0,1,0,14029,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
				$count = 1;
				}
			}
			
			if($subpark_id==446){// Energy Meter
					if ($phase != "tag") {
					$args = "0,1,0,14037,EAE,EM1,1440,kWh,'Blue',1;0,1,0,14039,EAE,EM2,1440,kWh,'BlueViolet',2;0,1,0,14035,EAE,EM3,1440,kWh,'Violet',2;0,1,0,14018,EAE,EM4,1440,kWh,'CadetBlue',2;0,1,0,14042,EAE,EM5,1440,kWh,'orange',2;0,1,0,14036,EAE,EM6,1440,kWh,'red',2;0,1,0,14041,EAE,EM7,1440,kWh,'Pink',2;0,1,0,14017,EAE,EM8,1440,kWh,'Black',2;0,1,0,14028,EAE,EM9,1440,kWh,'Blue',2;0,1,0,14029,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
					$count = 1;
				} else {
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=22&phase=Sowkurtag&args=0,1,0,14037,PAC,EM1,15,kW,4;0,1,0,14039,PAC,EM2,15,kW,5;0,1,0,14035,PAC,EM3,15,kW,6;0,1,0,14018,PAC,EM4,15,kW,7;0,1,0,14042,PAC,EM5,15,kW,8;0,1,0,14036,PAC,EM6,15,kW,19;0,1,0,14041,PAC,EM7,15,kW,12;0,1,0,14017,PAC,EM8,15,kW,29;0,1,0,14028,PAC,EM9,15,kW,13;0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&sums=0,1,0,14029,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,14028_0,PAC,null,5,Current%20Generation,red,2,%20Kw,10,0,Arial&avgs=0,1,0,14029,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,14029,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,14029,MODULE_TEMP,Module Temperature,15,°C,\'Red\';0,1,0,14023,HUMIDITY,Humidity,15,%,\'Violet\';&etotal=0,1,0,14028_0,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,14029,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,14029,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
					$count = 2; 
				}
			}
		
			if($subpark_id==451){// WS
					$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=22&phase=SowkurWS&defaults=0,1,2&args=0,1,0,14029,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,14029,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,14029,MODULE_TEMP,Module Temperature,15,°C,\'Red\';0,1,0,14023,HUMIDITY,Humidity,15,%,\'Violet\';&diffs=0,1,0,14028,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,14029,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&avgs=0,1,0,14029,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
					$count = 1; 
		
			}
		}
	else if($park_no == 23){ // Knorr Bremse

			if($subpark_id==448){ // Inverter1

			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=23&phase=KnorrINVgraph1&args=0,1,0,14650,PAC,AC%20Power-INV East,15,kW,4;0,1,0,14006,PAC,AC%20Power-INV West,15,kW,5;0,1,0,14008,PAC,AC%20Power-INV South,15,kW,6;0,1,0,14007,Global_Radiation_Act,Irradiation,15,W/m²,\'Gold\';0,1,0,14007,Pyranometer_Radiation_Act,Tilted irradiation,15,W/m²,\'Red\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 1" border="0"></iframe></td></tr>';
			//$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=23&phase=KnorrINVgraph2&args=0,1,0,14650,UDC_1-UDC_2,DC%20Voltage-INV East,15,V,8;0,1,0,14006,UDC_1-UDC_2,DC%20Voltage-INV West,15,V,9;0,1,0,14008,UDC_1-UDC_2,DC%20Voltage-INV South,15,V,6;0,1,0,14650,IDC_1-IDC_2,DC%20Current-INV East,15,A,7;0,1,0,14006,IDC_1-IDC_2,DC%20Current-INV West,15,A,15;0,1,0,14008,IDC_1-IDC_2,DC%20Current-INV South,15,A,13&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 2" border="0"></iframe></td></tr>';
			//$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=23&phase=KnorrINVgraph3&args=0,1,0,14650,UAC,AC%20Voltage-INV East,15,V,8;0,1,0,14006,UAC,AC%20Voltage-INV West,15,V,9;0,1,0,14008,UAC,AC%20Voltage-INV South,15,V,6;0,1,0,14650,IAC,AC%20Current-INV East,15,A,1;0,1,0,14006,IAC,AC%20Current-INV West,15,A,2;0,1,0,14008,IAC,AC%20Current-INV South,15,A,3;0,1,0,14650,PAC,AC%20Power-INV East,15,kW,4;0,1,0,14006,PAC,AC%20Power-INV West,15,kW,5;0,1,0,14008,PAC,AC%20Power-INV South,15,kW,6;0,1,0,14650,FAC,Frequency-INV East,15,Hz,7;0,1,0,14006,FAC,Frequency-INV West,15,Hz,15;0,1,0,14008,FAC,Frequency-INV South,15,Hz,13;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 3" border="0"></iframe></td></tr>';
			//$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			//$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=23&phase=KnorrINVPR&defaults=0,1,2,3,4&args=0,1,0,14650,Inv_PR,Inv1%20PR,15,%,4;0,1,0,14006,Inv_PR,Inv2%20PR,15,%,5;0,1,0,14008,Inv_PR,Inv3%20PR,15,%,6;0,1,0,14007,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,14007,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr>';
			//$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramKnorr.php?park_no=23&phase=KnorrINVEFF&args=0,1,0,14650,Inv1_Eff,Inverter1,15,%,8;0,1,0,14006,Inv2_Eff,Inverter1,15,%,9;0,1,0,14008,Inv3_Eff,Inverter1,15,%,6;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph- 4" border="0"></iframe></td></tr>';
			//$diagrammCode .='<tr><td>&nbsp;</td></tr>';
			//$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramKnorr.php?park_no=23&phase=KnorrINVACPR&defaults=0,1,2,3,4&args=0,1,0,14650,Inv_AC_PR,Inv1%20AC%20PR,15,%,4;0,1,0,14006,Inv_AC_PR,Inv2%20AC%20PR,15,%,5;0,1,0,14008,Inv_AC_PR,Inv3%20AC%20PR,15,%,6;0,1,0,14007,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,14007,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-6" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagramKnorr.php?park_no=23&phase=KnorrINVDCPR&defaults=0,1,2,3,4&args=0,1,0,14650,DC_Vol_Coeff,Inv1(DC_Voltage),15,V,4;0,1,0,14006,DC_Vol_Coeff,Inv2(DC_Voltage),15,V,5;0,1,0,14008,DC_Vol_Coeff,Inv3(DC_Voltage),15,V,6;0,1,0,14007,Inv_irrad_600,Irradiation,15,W/m&sup2;0,1,0,14007,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-5" border="0"></iframe></td></tr>';
		
			$count = 5;
		}
		else if($subpark_id == 449){ // SMU
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=23&phase=Knorrsmu1inv1&args=0,1,0,14650,UDC_1A,StringVoltage%201A,5,V,15;0,1,0,14650,UDC_1B,StringVoltage%201B,5,V,1;0,1,0,14650,UDC_1C,StringVoltage%201C,5,V,2;0,1,0,14650,UDC_2A,StringVoltage%202A,5,V,15;0,1,0,14650,UDC_2B,StringVoltage%202B,5,V,1;0,1,0,14650,IDC_1A,StringCurrent%201A,5,A,5;0,1,0,14650,IDC_1B,StringCurrent%201B,5,A,9;0,1,0,14650,IDC_1C,StringCurrent%201C,5,A,10;0,1,0,14650,IDC_2A,StringCurrent%202A,5,A,11;0,1,0,14650,IDC_2B,StringCurrent%202B,5,A,13;0,1,0,14650,PDC,String%20DC%20Power,5,kW,18;0,1,0,14007,Global_Radiation_Act,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,14007,Pyranometer_Radiation_Act,Tilted irradiation,15,W/m²,\'Red\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU1/INV East" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=23&phase=Knorrsmu2inv2&args=0,1,0,14006,UDC_1A,StringVoltage%201A,5,V,15;0,1,0,14006,UDC_1B,StringVoltage%201B,5,V,1;0,1,0,14006,UDC_1C,StringVoltage%201C,5,V,2;0,1,0,14006,UDC_2A,StringVoltage%202A,5,V,15;0,1,0,14006,UDC_2B,StringVoltage%202B,5,V,1;0,1,0,14006,IDC_1A,StringCurrent%201A,5,A,5;0,1,0,14006,IDC_1B,StringCurrent%201B,5,A,9;0,1,0,14006,IDC_1C,StringCurrent%201C,5,A,10;0,1,0,14006,IDC_2A,StringCurrent%202A,5,A,11;0,1,0,14006,IDC_2B,StringCurrent%202B,5,A,13;0,1,0,14006,PDC,String%20DC%20Power,5,kW,18;0,1,0,14007,Global_Radiation_Act,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,14007,Pyranometer_Radiation_Act,Tilted irradiation,15,W/m²,\'Red\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU2/INV West" border="0"></iframe></td></tr>';
			
			$diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=23&phase=Knorrsmu3inv3&args=0,1,0,14008,UDC_1A,StringVoltage%201A,5,V,15;0,1,0,14008,UDC_1B,StringVoltage%201B,5,V,1;0,1,0,14008,UDC_2A,StringVoltage%202A,5,V,15;0,1,0,14008,IDC_1A,StringCurrent%201A,5,A,5;0,1,0,14008,IDC_1B,StringCurrent%201B,5,A,9;0,1,0,14008,IDC_2A,StringCurrent%202A,5,A,11;0,1,0,14008,PDC,String%20DC%20Power,5,kW,18;0,1,0,14007,Global_Radiation_Act,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,14007,Pyranometer_Radiation_Act,Tilted irradiation,15,W/m²,\'Red\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU3/INV South" border="0"></iframe></td></tr>';
			

			$count = 3;
		}
		
        else if($subpark_id==450){ // Energy Meter
			  if ($phase=="tag"){

			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=23&phase=Knorrtag&defaults=0,1&args=0,1,0,14005,Activepower_Total,Active Power,15,kW,4;0,1,0,14007,Global_Radiation_Act,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,14007,Pyranometer_Radiation_Act,Tilted irradiation,15,W/m²,\'Red\';&sums=0,1,0,14005_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=23&phase=Knorrenergyg2&defaults=0,1,2,3&args=0,1,0,14005,System_PR,System PR,15,%,1;0,1,0,14007,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,14007,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=23&phase=Knorren3&defaults=0,4,5&args=0,1,0,14005,Power_Factor_average,Average Power Factor,15,,4;0,1,0,14005,Powerfactor_phase1,Phase1 Power Factor,15,,5;0,1,0,14005,Powerfactor_phase2,Phase2 Power Factor,15,,6;0,1,0,14005,Powerfactor_phase3,Phase3 Power Factor,15,,7;0,1,0,14007,Global_Radiation_Act,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,14007,Pyranometer_Radiation_Act,Tilted irradiation,15,W/m²,\'Red\';hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-3" border="0"></iframe></td></tr>';
			$count = 3;

		}
		else{
			$args = "0,1,0,14005,Forward_Active_Energy,EM_CONZERV,1440,kWh,'green',1;0,1,0,14007,Global_Radiation_Act,Irradiation,1440,Wh/mSQUA,'Gold',0";
			$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
			$count = 1;
		}
	}
	else if($subpark_id == 452){ // Weather Station
				$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=23&phase=Knorrweather&defaults=0,1,2&args=0,1,0,14005,Activepower_Total,Active Power,15,kW,4;0,1,0,14007,Global_Radiation_Act,Irradiation,15,W/m²,\'Gold\';0,1,0,14007,Pyranometer_Radiation_Act,Tilted irradiation,15,W/m²,\'Red\';0,1,0,14007,Ambient_Temperature_Act,Ambient Temperature,15,°C,\'darkred\';0,1,0,14007,Module1_Temperature_Act,Module_Temperature,15,°C,\'green\';0,1,0,14007,Wind_Direction_Act,Wind Direction,15,°,\'Burlywood\';0,1,0,14007,Wind_Speed_Act,Wind Speed,15,m/s,6;&diffs=0,1,0,14005,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,14007,Global_Radiation_Act,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,14007,Ambient_Temperature_Act,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
				$count = 1;
		}
		
	
	}

	else {

        if ($park_no == 99) {
            $park_no = 0;
        }
        $query = "select bezeichnung, type from diagramm where (portal='0' or portal = '$park_no') and (subpark=0 or subpark=$subpark_id) and area=-1 order by nr";
        $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

        $count = 0;
        $diagrammCode = "";

        while ($row_ds1 = mysql_fetch_array($ds1)) {
            $diagrammCode .= '<tr><td><iframe id="frame' . $count . '" width="99%" height="99%" SRC="diagram/mydiagram.php' . $row_ds1['type'] . '&title=' . $row_ds1['bezeichnung'] . '" border="0"></iframe></td></tr><br>';
            $count++;
        }
    }
    $count*=100;
    ?>

    <BODY bgcolor="#ffffff" leftmargin="20" topmargin="5" onLoad="if(parent.navigation.location != 'navigation.php?park_no=<?php echo $park_no; ?>&subpark=<?php echo $subpark_id; ?>&meter_existent=<?php echo $meter_existent; ?>')  parent.navigation.location.href='navigation.php?park_no=<?php echo $park_no; ?>&subpark=<?php echo $subpark_id; ?>&meter_existent=<?php echo $meter_existent; ?>';">
        <form name=ThisForm method="post" action="" target="_self">
    <?php
    echo '<TABLE height="' . ($count) . '%" width="100%" cellpadding="0" cellspacing="0" border="0" align="left">';
    ?>
            <TR>
                <td style="height: 1px;" align="left" valign="top" class="grundtext" bgcolor="#ffffff">
<?php
echo "<span class=\"ueberschriftbraun\">" . $ueberschrift . "</span>";
echo "<br>";
?>
                </td>
            </TR>
            <TR>
                <td style="height: 1px;" align="left">
                    <div class="months">
                    <?php
                    $startjahr = 2011;
                    $endjahr = date('Y') + 1;
                    for ($yy = $startjahr; $yy < $endjahr; $yy++) {
                        echo "<A HREF=\"einspeisung_subpark.php?diagtyp_sel=" . $diagtyp_sel . "&phase=jahr&jahr=" . $yy . "\"";
                        if ($jahr == $yy) {
                            if ($jahr == $jahr_heute) {

                                echo "class=\"mylink_current_hit\"";
                            } else {
                                echo "class=\"mylink_hit\"";
                            }
                        } elseif ($jahr_heute == $yy) {
                            echo "class=\"mylink_current\"";
                        } else {
                            echo "class=\"mylink_normal\"";
                        }
                        echo ">";
                        echo "&nbsp;" . $yy . "&nbsp;";
                        echo "</a>&nbsp;&nbsp;";
                    }
                    ?>
                    </div>
                    <div class="months">
                        <?php
                        for ($zahl = 1; $zahl < 13; $zahl++) {
                            if ($jahr < $jahr_heute || $zahl <= $monat_heute) {
                                echo "<A HREF=\"einspeisung_subpark.php?diagtyp_sel=" . $diagtyp_sel . "&phase=mon&mon=" . $zahl . "&jahr=" . $jahr . "\"";
                                if ($zahl == $mon && ($phase == "mon" || $phase == "tag")) {
                                    if ($mon == $monat_heute) {

                                        echo "class=\"mylink_current_hit\"";
                                    } else {
                                        echo "class=\"mylink_hit\"";
                                    }
                                } elseif ($jahr_heute == $jahr && $monat_heute == $zahl) {
                                    echo "class=\"mylink_current\"";
                                } else {
                                    echo "class=\"mylink_normal\"";
                                }
                            } else {
                                echo "<a ";
                                echo "class=\"mylink_bad\"";
                            }
                            echo ">";
                            echo $d_monat[$zahl] . "</a>&nbsp;&nbsp; ";
                        }
                        ?>
                    </div>
                    <div class="days">
                        <?php
                        if ($phase != 'jahr') {
                            for ($i_t = 1; $i_t < $mx_tage2; $i_t++) {
                                if ($jahr < $jahr_heute || $mon < $monat_heute || $i_t <= $tag_heute) {
                                    echo "<a href=\"einspeisung_subpark.php?diagtyp_sel=" . $diagtyp_sel . "&phase=tag&tag=" . $i_t . "&mon=" . $mon . "&jahr=" . $jahr . "\"";
                                    if ($i_t == $tag && ($phase == "tag")) {
                                        if ($tag == $tag_heute) {

                                            echo "class=\"mylink_current_hit\"";
                                        } else {
                                            echo "class=\"mylink_hit\"";
                                        }
                                    } elseif ($jahr_heute == $jahr && $monat_heute == $mon && $tag_heute == $i_t) {
                                        echo "class=\"mylink_current\"";
                                    } else {
                                        echo "class=\"mylink_normal\"";
                                    }
                                } else {
                                    echo "<a ";
                                    echo "class=\"mylink_bad\"";
                                }
                                echo ">";
                                echo $i_t . "</a>&nbsp;&nbsp;";
                            }
                        }
                        ?>
                    </div>
                </td>
            </TR>

                        <?php echo $diagrammCode;?>
						
						
        </TABLE>
    </form>
			<?php if($park_no == 36 && $subpark_id == 268 )
						{?>
						<!--<div style="padding:0px; margin:0;">
								<iframe id="frame" style="width: 99%; height: 98%" src="Overview/amplus_inveff_graph.php"></iframe>
							  </div>-->
							
						<?php }?>
</BODY>
</HTML>
