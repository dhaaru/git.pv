<?php

//einspeisungpark.php

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Asia/Calcutta');
}



//echo $anl_id;

if (!$_SESSION['id']) {
    session_start();
    $_SESSION['id'] = session_id();
}
//$_SESSION['anlagenID']=$anl_id;

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



$subpark_id = 0;
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


//echo "Phase: ".$phase."<br>";
//Datum
//Wenn Startzustand


if (!$diagtyp_sel || $diagtyp_sel == '') {
    $_SESSION['diagtyp_s'] = 'anl';
}

require_once('connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
user_check();
include('locale/gettext_header.php');

//echo "Phase :".$phase."<br>";
include('functions/de_datum.php');
include('functions/b_breite.php');
include('functions/dgr_func_jpgraph.php');
include('functions/datum_formate.php');

include('functions/allg_functions.php');
$user_typ = get_user_attribut('usertyp');

if ($park_no == '99') {
    
} else {
    //  $bezeichnung="Treia ".$park_no;
    $pos_arr = (array_keys($park_no_arr, $park_no));
    $pos = $pos_arr[0];
    $bezeichnung = $park_bez_arr[$pos];
}
$_SESSION['park_name'] = $bezeichnung;
$_SESSION['teil_bez'] = $bezeichnung;



$area_ids = get_area_ids();
$capacity_park = get_capacity_park($park_no, $area_ids);
if ($park_no == "20") {
    $capacity_park = "20057.856";
}
//$capacity_park=20000;
//Tage in jedem Monat des jahres ermitteln

$days_sum = tage_monat($jahr);
//Wieviel Tage hat der aufgerufene Monat
$tage2 = $days_sum[$mon];
$mx_tage2 = $tage2 + 1; //F�r die For-Schleife, da ab 1 und nicht 0...
$_SESSION['anz_tage_s'] = $tage2;

$abstand = 300; //Zwischen select und Titel

if ($park_no == 10) {

    if ($phase != "tag") {
        //
        $args = "0,1,0,670,EA-,Outgoing%20Yield,1440,MWh,'darkblue',3;0,1,0,737,EA-,RMU-5%20Yield,1440,MWh,'purple',2;0,1,0,738,EA-,RMU-1%20Yield,1440,MWh,'darkgreen',2;0,0.001,0,678,E_Total,Block%201%20I1%20Master,1440,MWh,'green',1;0,0.001,0,713,E_Total,Block%201%20I1%20Slave,1440,MWh,'green',1;0,0.001,0,703,E_Total,Block%201%20I2%20Master,1440,MWh,'green',1;0,0.001,0,709,E_Total,Block%201%20I2%20Slave,1440,MWh,'green',1;0,0.001,0,651,E_Total,Block%202%20I3%20Master,1440,MWh,'blue',1;0,0.001,0,567,E_Total,Block%202%20I3%20Slave,1440,MWh,'blue',1;0,0.001,0,621,E_Total,Block%202%20I4%20Master,1440,MWh,'blue',1;0,0.001,0,616,E_Total,Block%202%20I4%20Slave,1440,MWh,'blue',1;0,0.001,0,516,E_Total,Block%203%20I5%20Master,1440,MWh,'brown',1;0,0.001,0,607,E_Total,Block%203%20I5%20Slave,1440,MWh,'brown',1;0,0.001,0,577,E_Total,Block%203%20I6%20Master,1440,MWh,'brown',1;0,0.001,0,479,E_Total,Block%203%20I6%20Slave,1440,MWh,'brown',1;0,0.001,0,660,E_Total,Block%204%20I7%20Master,1440,MWh,'red',1;0,0.001,0,592,E_Total,Block%204%20I7%20Slave,1440,MWh,'red',1;0,0.001,0,542,E_Total,Block%205%20I8%20Master,1440,MWh,'orange',1;0,0.001,0,529,E_Total,Block%205%20I8%20Slave,1440,MWh,'orange',1;0,0.001,0,570,E_Total,Block%205%20I9%20Master,1440,MWh,'orange',1;0,0.001,0,519,E_Total,Block%205%20I9%20Slave,1440,MWh,'orange',1;0,1,0,732,U1,Tilted%20Irradiation,1440,Wh/mSQUA,'Gold',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';

        $count = 1;
    } else {
        //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2,3&args=0,1,0,743,PAC,LT%20Meter%20AC%20Power,15,kW,4;0,1,0,737,PAC,RMU-5%20AC%20Power,15,kW,11;0,1,0,738,PAC,RMU-1%20AC%20Power,15,kW,10;0,1,0,737_738,PAC,Total%20Generation,15,kW,\'Violet\';0,1,0,670,PAC,Outgoing%20AC%20Power,15,kW,12&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2,3&args=0,1,0,743,PAC,LT%20Meter%20AC%20Power,15,kW,4;0,1,0,737,PAC,RMU-5%20AC%20Power,15,kW,11;0,1,0,738,PAC,RMU-1%20AC%20Power,15,kW,10;0,1,0,737_738,PAC,Total%20Generation(RMU1 +RMU5),15,kW,\'Violet\'&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,739,PAC,Auxiliary%20Power,15,kW,9&hideClear=1&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,0.5,0,727,U3,Wind Speed,15,m/s,6;0,13,0,732,U1,Tilted Irradiation,15,W/m²,\'Gold\';0,13,0,732,U3,Horizontal Irradiation,15,W/m²,0;-30,1,0,727,U2,Ambient Temperature,15,°C,\'darkred\';0,1,0,727,T1,Module Surface Temperature,15,°C,\'indianred\'&diffs=0,1000,0,670,EA-,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,13,0,732,U1,null,5,Total%20Irradiation (tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,732,U3,null,5,Total%20Irradiation (horizontal),brown,2,%20Wh/m²,10,0,Arial&avgs=-30,1,0,727,U2,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';

//

        $count = 3;
    }
} 
elseif ($park_no == 50) {

    if ($phase != "tag") {
        $args = "0,1,0,1939,EM_Accord_Act_Energy_Exp,Line%20Feeder1,15,MWh,'darkblue',3;0,1,0,1946,EM_Accord_Act_Energy_Exp,Line%20Feeder2,1440,MWh,'purple',3;0,1,0,1947,EM_Accord_Act_Energy_Exp,132-KV%20Trafo1,1440,MWh,'Brown',2;0,1,0,1944,EM_Accord_Act_Energy_Exp,132-KV%20Trafo2,1440,MWh,'CadetBlue',2;0,1,0,1955,EM_Accord_Act_Energy_Exp,132-KV%20Trafo3,1440,MWh,'',2;0,1,0,1945,EM_Accord_Act_Energy_Exp,Block%20A,1440,MWh,'Chartreuse',1;0,1,0,1953,EM_Accord_Act_Energy_Exp,Block%20B,1440,MWh,'pink',1;0,1,0,1951,EM_Accord_Act_Energy_Exp,Block%20C,1440,MWh,'Chocolate',1;0,1,0,1948,EM_Accord_Act_Energy_Exp,Block%20D,1440,MWh,'red',1;0,1,0,1943,EM_Accord_Act_Energy_Exp,Block%20E,1440,MWh,'Coral',1;0,1,0,1954,EM_Accord_Act_Energy_Exp,Block%20F,1440,MWh,'Crimson',1;0,1,0,1941,EM_Accord_Act_Energy_Exp,Block%20G,1440,MWh,'black',1;0,1,0,1942,EM_Accord_Act_Energy_Exp,Spare,1440,MWh,'DarkGoldenRod',1;0,1,0,1940,EM_Accord_Act_Energy_Exp,33-KV%20Trafo1,1440,MWh,'DarkGreen',4;0,1,0,1950,EM_Accord_Act_Energy_Exp,33-KV%20Trafo2,1440,MWh,'DarkMagenta',4;0,1,0,1949,EM_Accord_Act_Energy_Exp,33-KV%20Trafo3,1440,MWh,'DarkOliveGreen',4;0,1,0,7126,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';

        $count = 1;
    } else {
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=50&phase=tag&defaults=0,1,16,18&args=0,1,0,1939,EM_Accord_Act_Pow,Line%20Feeder1,15,MW,4;0,1,0,1946,EM_Accord_Act_Pow,Line%20Feeder2,15,MW,11;0,1,0,1947,EM_Accord_Act_Pow,132-KV%20Trafo1,15,MW,\'Chocolate\';0,1,0,1944,EM_Accord_Act_Pow,132-KV%20Trafo2,15,MW,12;0,1,0,1955,EM_Accord_Act_Pow,132-KV%20Trafo3,15,MW,13;0,1,0,1945,EM_Accord_Act_Pow,Block%20A,15,MW,14;0,1,0,1953,EM_Accord_Act_Pow,Block%20B,15,MW,15;0,1,0,1951,EM_Accord_Act_Pow,Block%20C,15,MW,16;0,1,0,1948,EM_Accord_Act_Pow,Block%20D,15,MW,26;0,1,0,1943,EM_Accord_Act_Pow,Block%20E,15,MW,17;0,1,0,1954,EM_Accord_Act_Pow,Block%20F,15,MW,18;0,1,0,1941,EM_Accord_Act_Pow,Block%20G,15,MW,19;0,1,0,1942,EM_Accord_Act_Pow,Spare,15,MW,1;0,1,0,1940,EM_Accord_Act_Pow,33-KV%20Trafo1,15,MW,25;0,1,0,1950,EM_Accord_Act_Pow,33-KV%20Trafo2,15,MW,5;0,1,0,1949,EM_Accord_Act_Pow,33-KV%20Trafo3,15,MW,23;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,1939_1946,EM_Accord_Act_Pow,Total%20Generation,15,MW,\'Violet\';&diffs=0,1,0,1939,EM_Accord_Act_Energy_Exp,null,8,Line%20Feeder1,red,2,%20MWh,10,0,Arial;0,1,0,1946,EM_Accord_Act_Energy_Exp,null,8,Line%20Feeder2,red,2,%20MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,1939_1946,EM_Accord_Act_Pow,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;0,1,0,1939_1946,EM_Accord_Act_Energy_Exp,null,5,Cumulative%20export%20till%20date,red,2,%20MWh,10,0,Arial&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
         //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,16,18&args=0,1,0,1939,EM_Accord_Act_Pow,Line%20Feeder1,15,MW,4;0,1,0,1946,EM_Accord_Act_Pow,Line%20Feeder2,15,MW,11;0,1,0,1947,EM_Accord_Act_Pow,132-KV%20Trafo1,15,MW,\'Chocolate\';0,1,0,1944,EM_Accord_Act_Pow,132-KV%20Trafo2,15,MW,12;0,1,0,1955,EM_Accord_Act_Pow,132-KV%20Trafo3,15,MW,13;0,1,0,1945,EM_Accord_Act_Pow,Block%20A,15,MW,14;0,1,0,1953,EM_Accord_Act_Pow,Block%20B,15,MW,15;0,1,0,1951,EM_Accord_Act_Pow,Block%20C,15,MW,16;0,1,0,1948,EM_Accord_Act_Pow,Block%20D,15,MW,26;0,1,0,1943,EM_Accord_Act_Pow,Block%20E,15,MW,17;0,1,0,1954,EM_Accord_Act_Pow,Block%20F,15,MW,18;0,1,0,1941,EM_Accord_Act_Pow,Block%20G,15,MW,19;0,1,0,1942,EM_Accord_Act_Pow,Spare,15,MW,1;0,1,0,1940,EM_Accord_Act_Pow,33-KV%20Trafo1,15,MW,25;0,1,0,1950,EM_Accord_Act_Pow,33-KV%20Trafo2,15,MW,5;0,1,0,1949,EM_Accord_Act_Pow,33-KV%20Trafo3,15,MW,23;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,1939_1946,EM_Accord_Act_Pow,Total%20Generation,15,MW,\'Violet\';&diffs=0,1,0,1939,EM_Accord_Act_Energy_Exp,null,8,Line%20Feeder1,red,2,%20MWh,10,0,Arial;0,1,0,1946,EM_Accord_Act_Energy_Exp,null,8,Line%20Feeder2,red,2,%20MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
         $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,3324,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7126,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7126,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,3324,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,3324,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,3324,Day_Rain,Day Rain,15,mm,3;0,1,0,3324,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,3324,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,3324,Outside_Humidity,Outside Humidity,15,%,\'Violet\';0,1,0,3325,AI_SPARE1,Module Surface Temperature Block3,15,°C,\'indianred\';0,1,0,4600,AI_SPARE1,Module Surface Temperature Block23,15,°C,\'orange\';0,1,0,1530,AI_SPARE1,Module Surface Temperature Block26,15,°C,\'blue\';0,1,0,2208,AI_SPARE1,Module Surface Temperature Block49,15,°C,\'red\'&etotal=0,1,0,1939_1946,EM_Accord_Act_Energy_Exp,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7126,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7126,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
     
        $count = 2;
  }
}
elseif ($park_no == 31) {

    if ($phase != "tag") {
        $args = "0,1,0,6906,EAE,132KV%20TR2,1440,MWh,'Blue',1;0,1,0,6905,EAE,132KV%20TR1,1440,MWh,'BlueViolet',1;0,1,0,6902,EAE,33KV%20Outgoing1,1440,MWh,'Brown',2;0,1,0,6903,EAE,33KV%20Outgoing2,1440,MWh,'CadetBlue',2;0,1,0,6900,EAE,33KV%20Incomer1%20Traffo%201,1440,MWh,'purple',3;0,1,0,6897,EAE,33KV%20Bus%20Coupler,1440,MWh,'Chartreuse',1;0,1,0,6904,EAE,33KV%20Incomer2%20traffo%202,1440,MWh,'',3;0,1,0,6901,EAE,33KV%20Outgoing3,1440,MWh,'Chocolate',2;0,1,0,6899,EAE,33KV%20Outgoing4,1440,MWh,'pink',2;0,1,0,7100,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';

        $count = 1;
    } else {
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,9&args=0,1,0,6906,PAC,132KV%20TR2,15,MW,4;0,1,0,6905,PAC,132KV%20TR1,15,MW,11;0,1,0,6902,PAC,33KV%20Outgoing1,15,MW,\'Chocolate\';0,1,0,6903,PAC,33KV%20Outgoing2,15,MW,12;0,1,0,6900,PAC,33KV%20Incomer1%20Traffo%201,15,MW,13;0,1,0,6897,PAC,33KV%20Bus%20Coupler,15,MW,14;0,1,0,6904,PAC,33KV%20Incomer2%20traffo%202,15,MW,15;0,1,0,6901,PAC,33KV%20Outgoing3,15,MW,16;0,1,0,6899,PAC,33KV%20Outgoing4,15,MW,1;0,1,0,6906_6905,PAC,Total%20Generation,15,MW,\'Violet\';&diffs=0,1,0,6906,EAE,null,8,Line%20Feeder1,red,2,%20MWh,10,0,Arial;0,1,0,6905,EAE,null,8,Line%20Feeder2,red,2,%20MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,6906_6905,PAC,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;0,1,0,6906_6905,EAE,null,5,Cumulative%20export%20till%20date,red,2,%20MW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7100,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7100,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7100,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7100,Wind_Direction,Wind Direction,15,°,\'Burlywood\';&etotal=0,1,0,6906_6905,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7100,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7100,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
     
        $count = 1;
  }
}
elseif ($park_no == 32) {

    if ($phase != "tag") {
        $args = "0,1,0,6732,EAE,MCR%201%20Incommer%203,1440,MWh,'Blue',3;0,1,0,6886,EAE,MCR%201-Outgoing%202,1440,MWh,'BlueViolet',1;0,1,0,6730,EAE,MCR%201-Outgoing%201,1440,MWh,'Brown',1;0,1,0,6733,EAE,MCR%201%20Incommer%202,1440,MWh,'CadetBlue',1;0,1,0,6734,EAE,MCR%201%20Incommer%201,1440,MWh,'',1;0,1,0,6731,EAE,MCR%201%20Spare%20Feeder,1440,MWh,'Chartreuse',1;0,1,0,6735,EAE,MCR%201%20-415v%20ACDB-SYD,1440,MWh,'red',1;0,1,0,6890,EAE,MCR%202%20Incommer%203,1440,MWh,'Chocolate',1;0,1,0,6907,EAE,MCR%202-Outgoing%202,1440,MWh,'pink',3;0,1,0,6896,EAE,MCR%202-Outgoing%201,1440,MWh,'violet',3;0,1,0,6892,EAE,MCR%202%20Incommer%202,1440,MWh,'Crimson',1;0,1,0,6891,EAE,MCR%202%20Incommer%201,1440,MWh,'',1;0,1,0,6893,EAE,MCR%202%20-415v%20ACDB-SYD,1440,MWh,'DarkGoldenRod',1;0,1,0,6335,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';

        $count = 1;
    } else {
        //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,8,9,13,15&args=0,1,0,6732,PAC,MCR%201%20Incommer%203,15,MW,4;0,1,0,6886,PAC,MCR%201-Outgoing%202,15,MW,11;0,1,0,6730,PAC,MCR%201-Outgoing%201,15,MW,\'Chocolate\';0,1,0,6733,PAC,MCR%201%20Incommer%202,15,MW,12;0,1,0,6734,PAC,MCR%201%20Incommer%201,15,MW,13;0,1,0,6731,PAC,MCR%201%20Spare%20Feeder,15,MW,14;0,1,0,6735,PAC,MCR%201%20-415v%20ACDB-SYD,15,MW,15;0,1,0,6890,PAC,MCR%202%20Incommer%203,15,MW,16;0,1,0,6907,PAC,MCR%202-Outgoing%202,15,MW,26;0,1,0,6896,PAC,MCR%202-Outgoing%201,15,MW,17;0,1,0,6892,PAC,MCR%202%20Incommer%202,15,MW,18;0,1,0,6891,PAC,MCR%202%20Incommer%201,15,MW,19;0,1,0,6893,PAC,MCR%202%20-415v%20ACDB-SYD,15,MW,1;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6730_6896,PAC,Total%20Generation,15,MW,\'Violet\';&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,6886_6730_6907_6896,PAC,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;0,1,0,6730_6896,EAE,null,5,Cumulative%20export%20till%20date,red,2,%20MWh,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,8,9,13,15&args=0,1,0,6732,PAC,MCR%201%20Incommer%203,15,MW,4;0,1,0,6886,PAC,MCR%201-Outgoing%202,15,MW,11;0,1,0,6730,PAC,MCR%201-Outgoing%201,15,MW,\'Chocolate\';0,1,0,6733,PAC,MCR%201%20Incommer%202,15,MW,12;0,1,0,6734,PAC,MCR%201%20Incommer%201,15,MW,13;0,1,0,6731,PAC,MCR%201%20Spare%20Feeder,15,MW,14;0,1,0,6735,PAC,MCR%201%20-415v%20ACDB-SYD,15,MW,15;0,1,0,6890,PAC,MCR%202%20Incommer%203,15,MW,16;0,1,0,6907,PAC,MCR%202-Outgoing%202,15,MW,26;0,1,0,6896,PAC,MCR%202-Outgoing%201,15,MW,17;0,1,0,6892,PAC,MCR%202%20Incommer%202,15,MW,18;0,1,0,6891,PAC,MCR%202%20Incommer%201,15,MW,19;0,1,0,6893,PAC,MCR%202%20-415v%20ACDB-SYD,15,MW,1;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6730,PAC_T,Total%20Generation,15,MW,\'Violet\';&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,6730_0,PAC_T,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;0,1,0,6730_0,EAE_T,null,5,Cumulative%20export%20till%20date,red,2,%20MWh,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2,3,4&args=0,1,0,6335,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,6335,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,6335,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,6335,Inside_Temperature,Inside Temperature,15,°C,\'indianred\';0,1,0,6335,Outside_Temperature,Outside Temperature,15,°C,4;0,1,0,6335,Day_Rain,Day Rain,15,mm,3;0,1,0,6335,Wind_Direction,Wind Direction,15,°,\'Burlywood\';0,1,0,6335,Inside_Humidity,Inside Humidity,15,%,\'Teal\';0,1,0,6335,Outside_Humidity,Outside Humidity,15,%,\'Violet\'&etotal=0,1,0,6886_6730_6907_6896,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,6335,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,6335,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
     
        $count = 2;
  }
}
elseif ($park_no == 33) {

    if ($phase != "tag") {
        $args = "0,1,0,7281,EAE,Incommer%201,1440,MWh,'Blue',3;0,1,0,7283,EAE,Incommer%202,1440,MWh,'BlueViolet',1;0,1,0,7279,EAE,Outgoing%201,1440,MWh,'Brown',2;0,1,0,7278,EAE,Outgoing%202,1440,MWh,'CadetBlue',2;0,1,0,7282,EAE,66Kv%201%20Incommer%201,1440,MWh,'red',3;0,1,0,7280,EAE,66Kv%20Incommer%202,1440,MWh,'Chartreuse',3;0,1,0,7285,EAE,Line%20Feeder,1440,MWh,'darkred',4;0,1,0,7718,EAE,Incommer%203,1440,MWh,'Chocolate',5;0,1,0,7719,EAE,Outgoing%203,1440,MWh,'pink',6;0,1,0,7717,EAE,Outgoing%204,1440,MWh,'violet',6;0,1,0,7721,EAE,Incommer%204,1440,MWh,'Crimson',5;0,1,0,7716,EAE,Trafo%203,1440,MWh,'DarkGoldenRod',7;0,1,0,7722,EAE,Trafo%204,1440,MWh,'Cyan',7;0,1,0,7747,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';

        $count = 1;
    } else {
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=6,14&args=0,1,0,7281,PAC,Incommer%201,15,MW,4;0,1,0,7283,PAC,Incommer%202,15,MW,11;0,1,0,7279,PAC,Outgoing%201,15,MW,\'Chocolate\';0,1,0,7278,PAC,Outgoing%202,15,MW,12;0,1,0,7282,PAC,66Kv%201%20Incommer%201,15,MW,13;0,1,0,7280,PAC,66Kv%20Incommer%202,15,MW,14;0,1,0,7285,PAC,Line%20Feeder,15,MW,15;0,1,0,7718,PAC,Incommer%203,15,MW,16;0,1,0,7719,PAC,Outgoing%203,15,MW,26;0,1,0,7717,PAC,Outgoing%204,15,MW,17;0,1,0,7721,PAC,Incommer%204,15,MW,18;0,1,0,7716,PAC,Trafo%203,15,MW,1;0,1,0,7722,PAC,Trafo%204,15,MW,20;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7285,PAC,Total%20Generation,15,MW,\'Violet\';&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,7285_0,PAC,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;0,1,0,7285_0,EAE,null,5,Cumulative%20export%20till%20date,red,2,%20MWh,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7747,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7747,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7747,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7747,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7285_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7747,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7747,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
     
        $count = 2;
  }
}
elseif ($park_no == 34) {

    if ($phase != "tag") {
        /*$args = "0,1,0,7252,EAE,Elmeasure,1440,MWh,'Blue',3;0,1,0,7268,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';

        $count = 1;*/
        
        $args = "0,1,0,7252,EAE,Elmeasure,1440,kWh,'green',1;0,1,0,7268,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
    } else {
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1&args=0,1,0,7252,PAC,Elmeasure,15,MW,4;0,1,0,7268,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&sums=0,1,0,7268,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,7252_0,PAC,null,5,Current%20Generation,red,2,%20MW,10,0,Arial&avgs=0,1,0,7268,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,7268,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,7268,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,7268,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,7268,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,7252_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,7268,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7268,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
        $count = 2;
  }
}
elseif ($park_no == 37) {

        if ($phase != "tag") {
            $args = "0,1,0,8861,EAE,Incommer%206,1440,MWh,'BlueViolet',2;0,1,0,8859,EAE,Incommer%204,1440,MWh,'Violet',2;0,1,0,8863,EAE,Incommer%205,1440,MWh,'CadetBlue',2;0,1,0,8864,EAE,Incommer%202,1440,MWh,'Brown',2;0,1,0,8858,EAE,Outgoing%20Feeder,1440,MWh,'CadetBlue',1;0,1,0,8860,EAE,Line%20Feeder,1440,MWh,'green',3;0,1,0,8857,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
        } else {
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=7,8&args=0,1,0,8861,PAC,Incommer%206,15,MW,5;0,1,0,8859,PAC,Incommer%204,15,MW,6;0,1,0,8863,PAC,Incommer%205,15,MW,7;0,1,0,8864,PAC,Incommer%202,15,MW,8;0,1,0,8862,PAC,Incommer%201,15,MW,\'red\';0,1,0,8858,PAC,Outgoing%20Feeder,15,MW,16;0,1,0,8860,PAC,Line %20Feeder,15,MW,12;0,1,0,8857,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&sums=0,1,0,8857,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,8860_0,PAC,null,5,Current%20Generation,red,2,%20MW,10,0,Arial&avgs=0,1,0,8857,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,8857,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,8857,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8857,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,8857,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,8860,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,8857,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8857,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
            $count = 2;
        }
}
elseif ($park_no == 38) { // Punjab 20

        if ($phase != "tag") {
            $args = "0,1,0,9169,EAE,Incommer Feeder 6,1440,MWh,'Blue',1;0,1,0,9170,EAE,Incommer Feeder 5%20,1440,MWh,'BlueViolet',2;0,1,0,9163,EAE,Incommer Feeder 4%20,1440,MWh,'Violet',2;0,1,0,9165,EAE,Outgoing Panel 2%20,1440,MWh,'CadetBlue',2;0,1,0,9166,EAE,Outgoing Panel 1%20,1440,MWh,'Brown',2;0,1,0,9174,EAE,Incommer Feeder 3%20,1440,MWh,'CadetBlue',1;0,1,0,9167,EAE,Incommer Feeder 2%20,1440,MWh,'green',3;0,1,0,9173,EAE,Incommer Feeder 1%20,1440,MWh,'green',3;0,1,0,9172,EAE,66Kv Line Feeder%20,1440,MWh,'green',3;0,1,0,9171,EAE,Trafo 2%20,1440,MWh,'green',3;0,1,0,9168,EAE,Trafo 1%20,1440,MWh,'green',3;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
        } else {
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=9,8&args=0,1,0,9169,PAC,Incommer Feeder 6%20,15,MW,4;0,1,0,9170,PAC,Incommer Feeder 5%20,15,MW,5;0,1,0,9163,PAC,Incommer Feeder 4%20,15,MW,6;0,1,0,9165,PAC,Outgoing Panel 2%20,15,MW,7;0,1,0,9166,PAC,Outgoing Panel 1%20,15,MW,8;0,1,0,9174,PAC,Incommer Feeder 3%20,15,MW,\'red\';0,1,0,9167,PAC,Incommer Feeder 2%20,15,MW,16;0,1,0,9173,PAC,Incommer Feeder 1 %20,15,MW,12;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9172,PAC,66Kv Line Feeder%20,15,MW,17;0,1,0,9171,PAC,Trafo 2%20,15,MW,20;0,1,0,9168,PAC,Trafo 1%20,15,MW,19;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,9172,PAC,null,5,Current%20Generation,red,2,%20MW,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,9712,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
            $count = 2; 
        }
}
elseif ($park_no == 36) { // Amplus
  
  if ($phase=="tag"){
            //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=tag&defaults=0,1&args=0,1,0,7795,Activepower_Total,Active Power,15,kW,4;0,1,0,7794,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,7795_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Energy%20Meter" border="0"></iframe></td></tr><br>';
            //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=weather&defaults=0,1&args=0,1,0,7794,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,Module_Temperature,Module Temperature,15,&deg;C,\'darkred\'&etotal=0,1,0,7795_0,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,7794,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,7794,Module_Temperature,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
            //$count = 2;
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=36&phase=tag&defaults=0,1&args=0,1,0,7795,Activepower_Total,Active Power,15,kW,4;0,1,0,7794,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,7795_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=36&phase=energyg2&defaults=0,1,2,3&args=0,1,0,7795,System_PR,System PR,15,%,1;0,1,0,7794,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
            //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=36&phase=energyg3&defaults=0,1,2,3,4&args=0,1,0,7792,Inv_PR,Inverter1%20PR,15,%,4;0,1,0,7791,Inv_PR,Inverter2%20PR,15,%,5;0,1,0,7794,Inv_PR,Inverter3%20PR,15,%,6;0,1,0,7794,Inv_irrad_250,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_250,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-3" border="0"></iframe></td></tr><br>';
            //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=36&phase=energy4&defaults=0,1,2,3,4&args=0,1,0,7792,Inv_AC_PR,Inverter1%20PR,15,%,4;0,1,0,7791,Inv_AC_PR,Inverter2%20PR,15,%,5;0,1,0,7794,Inv_AC_PR,Inverter3%20PR,15,%,6;0,1,0,7794,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-4" border="0"></iframe></td></tr><br>';
            //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram9.php?park_no=36&phase=energy5&defaults=0,1,2,3,4&args=0,1,0,7792,DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,7791,DC_Vol_Coeff,Inv 2(DC_Voltage),15,V,5;0,1,0,7794,DC_Vol_Coeff,Inv 3(DC_Voltage),15,V,6;0,1,0,7794,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-5" border="0"></iframe></td></tr><br>';
            $count = 3;
            
            }
            else{
            $args = "0,1,0,7795,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,7794,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
    }elseif($park_no == 43){ // Amplus Pune
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
elseif ($park_no == 41) { // CWET
        if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1&args=0,1,0,8148,Inverter_Output_Tot_Power,AC%20Power,15,kW,1;0,1,0,8148,Grid_Freq,Grid%20Frequency,15,Hz,2;0,1,0,8148,Grid_Freq,Grid%20Frequency,15,Hz,2;0,1,0,8148,Inverter_Output_Current_S,Inverter_Output_Current_S,15,A,3;0,1,0,8148,Inverter_Output_Current_T,Inverter_Output_Current_T,15,A,4;0,1,0,8148,Inverter_Output_Current_R,Inverter_Output_Current_R,15,A,9;0,1,0,8148,AC_GridRMS_Voltage_R,AC_GridRMS_Voltage_R,15,V,6;0,1,0,8148,AC_GridRMS_Voltage_S,AC_GridRMS_Voltage_S,15,V,7;0,1,0,8148,AC_GridRMS_Voltage_T,AC_GridRMS_Voltage_T,15,V,8&sums=0,1,0,8148,Daily_Peak_power,null,5,Daily%20Peak%20Power,red,2,kW,10,0,Arial;&diffs=0,1,0,8148,Tot_Energy,null,8,Yield (EM),black,2, MWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverter" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?phase=weather&defaults=0,1&args=0,1,0,8149,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8149,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\'&etotal=0,1,0,8148_0,Tot_Energy,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,8149,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8149,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
            $count = 2;
            
            }
            else{
            $args = "0,1,0,8148,Tot_Energy,Yield (EM),1440,MWh,'green',1;0,1,0,8149,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
            
  }
elseif ($park_no == 42) { // Clarke School
        if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,8147,PAC,AC%20Power,15,kW,1&diffs=0,1,0,8147,E_Total,null,8,Yield (EM),black,2, kWh,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverter" border="0"></iframe></td></tr><br>';
            $count = 1;
            
            }
            else{
            $args = "0,1,0,8147,E_Total,Yield (EM),1440,kWh,'green',1";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
            
  }  
else if (false && $park_no == 25) {
    if ($phase != "tag") {
        $args = "0,1,0,109,E_Total,Inverter 1,1440,kWh,0,1;0,1,0,110,E_Total,Inverter 2,1440,kWh,1,1;0,1,0,111,E_Total,Inverter 3,1440,kWh,2,1;0,1,0,112,E_Total,Inverter 4,1440,kWh,3,1;0,1,0,113,E_Total,Energy Meter,1440,kWh,5,2;0,1,0,108,SR2,Tilted%20Solar%20Radiation,1440,Wh/mSQUA,'yellow',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
        $count = 1;
    } else {
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,113,PAC,AC Power,5,kW,3;0,1,0,113,IAC1,AC Current 1,5,A,7;0,1,0,113,IAC2,AC Current 2,5,A,8;0,1,0,113,IAC3,AC Current 3,5,A,9;0,1,0,113,UAC1,AC Voltage 1,5,V,4;0,1,0,113,UAC2,AC Voltage 2,5,V,5;0,1,0,113,UAC3,AC Voltage 3,5,V,6&diffs=0,1000,0,113,E_DAY,null,8,Yield%20(EM),black,2,%20kWh,10,0,Arial;&sums=0,13,0,108,SR2_60,null,5,Total%20Irradiation%20(tilted),brown,2,%20Wh/m²,10,0,Arial;0,13,0,108,SR1_60,null,5,Total%20Irradiation%20(horizontal),brown,2,%20Wh/m²,10,0,Arial&defaults=0&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';

        $args = "0,1,0,109,PAC,Inverter 1 AC Power,15,kW,0;0,1,0,109,IAC1,Inverter 1 AC Current 1,15,A,0;0,1,0,109,IAC2,Inverter 1 AC Current 2,15,A,0;0,1,0,109,IAC3,Inverter 1 AC Current 3,15,A,0;0,1,0,109,IDC1,Inverter 1 DC Current 1,15,A,0;0,1,0,109,UAC1,Inverter 1 AC Voltage 1,15,V,0;0,1,0,109,UAC2,Inverter 1 AC Voltage 2,15,V,0;0,1,0,109,UAC3,Inverter 1 AC Voltage 3,15,V,0;0,1,0,109,UDC1,Inverter 1 DC Voltage 1,15,V,0;0,1,0,110,PAC,Inverter 2 AC Power,15,kW,1;0,1,0,110,IAC1,Inverter 2 AC Current 1,15,A,1;0,1,0,110,IAC2,Inverter 2 AC Current 2,15,A,1;0,1,0,110,IAC3,Inverter 2 AC Current 3,15,A,1;0,1,0,110,IDC1,Inverter 2 DC Current 1,15,A,1;0,1,0,110,UAC1,Inverter 2 AC Voltage 1,15,V,1;0,1,0,110,UAC2,Inverter 2 AC Voltage 2,15,V,1;0,1,0,110,UAC3,Inverter 2 AC Voltage 3,15,V,1;0,1,0,110,UDC1,Inverter 2 DC Voltage 1,15,V,1;0,1,0,111,PAC,Inverter 3 AC Power,15,kW,2;0,1,0,111,IAC1,Inverter 3 AC Current 1,15,A,2;0,1,0,111,IAC2,Inverter 3 AC Current 2,15,A,2;0,1,0,111,IAC3,Inverter 3 AC Current 3,15,A,2;0,1,0,111,IDC1,Inverter 3 DC Current 1,15,A,2;0,1,0,111,UAC1,Inverter 3 AC Voltage 1,15,V,2;0,1,0,111,UAC2,Inverter 3 AC Voltage 2,15,V,2;0,1,0,111,UAC3,Inverter 3 AC Voltage 3,15,V,2;0,1,0,111,UDC1,Inverter 3 DC Voltage 1,15,V,2;0,1,0,112,PAC,Inverter 4 AC Power,15,kW,3;0,1,0,112,IAC1,Inverter 4 AC Current 1,15,A,3;0,1,0,112,IAC2,Inverter 4 AC Current 2,15,A,3;0,1,0,112,IAC3,Inverter 4 AC Current 3,15,A,3;0,1,0,112,IDC1,Inverter 4 DC Current 1,15,A,3;0,1,0,112,UAC1,Inverter 4 AC Voltage 1,15,V,3;0,1,0,112,UAC2,Inverter 4 AC Voltage 2,15,V,3;0,1,0,112,UAC3,Inverter 4 AC Voltage 3,15,V,3;0,1,0,112,UDC1,Inverter 4 DC Voltage 1,15,V,3";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args='.$args.'&defaults=0,9,18,27&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,108,SR1,Irradiation,5,W/mSQUA,0;0,1,0,108,SR2,Irradiation inclined,5,W/mSQUA,3;0,1,0,108,AT,Temperature,5,DEGC,0&defaults=0,1,2&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';

        $count = 3;
    }
} else if ($park_no == 35) {

    //$diagrammCode .= '<tr><td><iframe id="frame2" width="99%" height="99%" SRC="diagram/mydiagram.php?type=wr&title=Inverters" border="0"></iframe></td></tr><br>';
    //defaults=0,1&args=0,1,0,477,PAC,Kaco%20AC%20Power,15,kW,10;0,1,0,478,PAC,EM%20AC%20Power,15,kW,12;0,1,0,477,UAC1,Kaco%20AC%20Voltage%201,15,V,12;0,1,0,477,UAC2,Kaco%20AC%20Voltage%202,15,V,13;0,1,0,477,UAC3,Kaco%20AC%20Voltage%203,15,V,14;0,1,0,478,UAC1,EM%20AC%20Voltage%201,15,V,11;0,1,0,478,UAC2,EM%20AC%20Voltage%202,15,V,12;0,1,0,478,UAC3,EM%20AC%20Voltage%203,15,V,13;;0,1,0,477,IAC1,Kaco%20AC%20Current%201,15,A,19;0,1,0,477,IAC2,Kaco%20AC%20Current%202,15,A,0;0,1,0,477,IAC3,Kaco%20AC%20Current%203,15,A,18;0,1,0,478,IAC1,EM%20AC%20Current%201,15,A,4;0,1,0,478,IAC2,EM%20AC%20Current%202,15,A,5;0,1,0,478,IAC3,EM%20AC%20Current%203,15,A,6&stamp=1349721000&endstamp=1349983800&diffs=0,1,0,478,E_Total,null,8,Yield,black,2,%20kWh,10,0,Arial&sums=0,13,0,451,U3,50,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial
    //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?diffs=0,1,0,478,E_Total,null,8,Yield (EM),black,2,%20kWh,10,0,Arial;&sums=;0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&defaults=0,7&args=0,1,0,478,PAC,EM%20AC%20Power,15,kW,12;0,1,0,478,IAC1,EM%20AC%20Current%201,15,A,4;0,1,0,478,IAC2,EM%20AC%20Current%202,15,A,5;0,1,0,478,IAC3,EM%20AC%20Current%203,15,A,6;0,1,0,478,UAC1,EM%20AC%20Voltage%201,15,V,11;0,1,0,478,UAC2,EM%20AC%20Voltage%202,15,V,12;0,1,0,478,UAC3,EM%20AC%20Voltage%203,15,V,13;0,1,0,477,PAC,Kaco%20AC%20Power,15,kW,10;0,1,0,477,IAC1,Kaco%20AC%20Current%201,15,A,19;0,1,0,477,IAC2,Kaco%20AC%20Current%202,15,A,0;0,1,0,477,IAC3,Kaco%20AC%20Current%203,15,A,18;0,1,0,477,UAC1,Kaco%20AC%20Voltage%201,15,V,12;0,1,0,477,UAC2,Kaco%20AC%20Voltage%202,15,V,13;0,1,0,477,UAC3,Kaco%20AC%20Voltage%203,15,V,14;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
    if (false && $phase != "tag") {
        $args = "0,1,0,478,E_Total,Energy Meter,1440,kWh,0,2;0,1,0,477,E_Total,Kaco Inverter,1440,kWh,1,1;0,1,0,451,U3,Irradiation,1440,Wh/mSQUA,'yellow',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '&args=' . $args . '" border="0"></iframe></td></tr><br>';

        $args = "0,1,0,749,E_Total,SMA 1,1440,kWh,0,1;0,1,0,751,E_Total,SMA 2,1440,kWh,1,1;0,1,0,750,E_Total,SMA 3,1440,kWh,2,1;0,1,0,451,U3,Irradiation,1440,Wh/mSQUA,'yellow',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '&args=' . $args . '" border="0"></iframe></td></tr><br>';
        $count = 2;
    } else {
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?diffs=0,1,0,478,E_Total,null,8,Yield (EM),black,2,%20kWh,10,0,Arial;&sums=;0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&defaults=0,7&args=0,1,0,478,PAC,EM%20AC%20Power,15,kW,12;0,1,0,478,IAC1,EM%20AC%20Current%201,15,A,4;0,1,0,478,IAC2,EM%20AC%20Current%202,15,A,5;0,1,0,478,IAC3,EM%20AC%20Current%203,15,A,6;0,1,0,478,UAC1,EM%20AC%20Voltage%201,15,V,11;0,1,0,478,UAC2,EM%20AC%20Voltage%202,15,V,12;0,1,0,478,UAC3,EM%20AC%20Voltage%203,15,V,13;0,1,0,477,PAC,Kaco%20AC%20Power,15,kW,10;0,1,0,477,IAC1,Kaco%20AC%20Current%201,15,A,19;0,1,0,477,IAC2,Kaco%20AC%20Current%202,15,A,0;0,1,0,477,IAC3,Kaco%20AC%20Current%203,15,A,18;0,1,0,477,UAC1,Kaco%20AC%20Voltage%201,15,V,12;0,1,0,477,UAC2,Kaco%20AC%20Voltage%202,15,V,13;0,1,0,477,UAC3,Kaco%20AC%20Voltage%203,15,V,14;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,3,6&sums=0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&args=0,0.001,0,749,Pac,SMA%201%20AC%20Power,15,kW,5;0,1,0,749,Uac,SMA 1 AC%20Voltage,15,V,16;0,1,0,749,Iac-Ist,SMA 1 AC Current,15,A,17;0,0.001,0,750,Pac,SMA%202%20AC%20Power,15,kW,18;0,1,0,750,Uac,SMA 2 AC%20Voltage,15,V,19;0,1,0,750,Iac-Ist,SMA 2 AC Current,15,A,20;0,0.001,0,751,Pac,SMA%203%20AC%20Power,15,kW,2;0,1,0,751,Uac,SMA 3 AC%20Voltage,15,V,22;0,1,0,751,Iac-Ist,SMA 3 AC Current,15,A,23&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame3" width="99%" height="99%" SRC="diagram/argdiagram5.php?diffs=0,1,0,478,E_Total,null,8,Yield (EM),black,2,%20kWh,10,0,Arial;&sums=;0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&args=-22.68,1.15,0,451,U4,Temperature,15,%C2%B0C,\'red\';0,13,0,451,U3,Irradiation,15,W/m%C2%B2,0&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
        $diagrammCode .= '<tr><td><iframe id="frame2" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2,3,4&diffs=0,1,0,478,E_Total,null,8,Yield (EM),black,2,%20kWh,10,0,Arial;&sums=;0,13,0,451,U3,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=-22.68,1.15,0,451,U4,null,9,Avg.%20Temperature,red,2,%20°C,10,0,Arial&args=0,1,0,452,SolingLossSimple,Soiling%20Loss,5,%,\'green\';0,1,0,452,Tref_1,Reference%20Temperature,5,°C,\'FireBrick\';0,1,0,452,Tsoil_1,Temperature,5,°C,\'orange\';0,1,0,452,Iscr_1,Reference%20current,5,A,\'blue\';0,1,0,452,Isc_1,Current,5,A,\'Cyan\';0,1,0,452,SoilingLossComplex,Soiling%20Loss%20Complex,5,%,\'purple\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
        $count = 4;
    }
} else if ($park_no==40){
    if ($phase=="tag"){
        $args="0,1,0,772,DirtyCurrent,772.Current%20of%20dirty%20panel,15,A,1;0,1,0,772,CleanCurrent2,772.Current%20of%20clean%20panel,15,A,0;0,1,0,772,SoilingLoss,772.Soiling%20loss,15,%,2;";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
        $count = 1;
    }
}else if($park_no==44){ // MOSER baer
    $diagrammCode .= '<tr><td align="left" valign="top"><iframe id="frame1" width="99%" height="550" SRC="diagram/argdiagram5.php?&args=0,1,0,8374,IDC1,String%20Current%201,5,A,15;0,1,0,8374,IDC2,String%20Current%202,5,A,1;0,1,0,8374,IDC3,String%20Current%203,5,A,2;0,1,0,8374,IDC4,String%20Current%204,5,A,3;0,1,0,8374,UDC1,DC%20Voltage%201,5,V,4;0,1,0,8374,UDC2,DC%20Voltage%202,5,V,5;0,1,0,8374,UDC3,DC%20Voltage%203,5,V,6;0,1,0,8374,UDC4,DC%20Voltage%204,5,V,7&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=SMU PUNE" border="0"></iframe></td></tr>';
} 
elseif ($park_no == 45) { // Goa
        if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2,3,4,5&args=0,1,0,8724,Inverter_Output_Tot_Power,INV1%20AC%20Power,15,kW,1;0,1,0,8722,Inverter_Output_Tot_Power,INV2%20AC%20Power,15,kW,2;0,1,0,8727,Inverter_Output_Tot_Power,INV3%20AC%20Power,15,kW,3;0,1,0,8726,Inverter_Output_Tot_Power,INV4%20AC%20Power,15,kW,4;0,1,0,8723,Inverter_Output_Tot_Power,INV5%20AC%20Power,15,kW,5;0,1,0,8728,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Inverter" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?phase=weather&defaults=0,1,2,3&args=0,1,0,8728,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,8728,Module_surface_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,8728,Wind_Speed,Wind Speed,15,m/s,\'blue\';0,1,0,8728,Humidity,Humidity,15,%,\'green\'&sums=0,1,0,8728,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,8728,Module_surface_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
            $count = 2;
            
            }
            else{
            //$args = "0,1,0,8724,Tot_Energy,Yield (EM),'Inverter1',1440,MWh,'darkblue',1;0,1,0,8722,Tot_Energy,Yield (EM),'Inverter2',1440,MWh,'darkblue',1;0,1,0,8728,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            //$diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
             $args = "0,1,0,8724,Tot_Energy,Inverter1,15,MWh,'darkblue',3;0,1,0,8722,Tot_Energy,Inverter2,1440,MWh,'purple',3;0,1,0,8727,Tot_Energy,Inverter3,1440,MWh,'Brown',2;0,1,0,8726,Tot_Energy,Inverter4,1440,MWh,'green',2;0,1,0,8723,Tot_Energy,Inverter5,1440,MWh,'',2;0,1,0,8728,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
            
  }else if($park_no == 39){
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
  
   elseif($park_no == 46){
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
   elseif($park_no == 47){
                $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,9974,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9974,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9974,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9974,Module_Temperature,Module_Temperature,15,°C,\'green\';&diffs=0,1,0,9983,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&sums=0,1,0,9974,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9974,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
                $count = 1;
            }
  
  else if($park_no == 51){ // Kolkata
      
      if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9315,Total_Active_Power,AC%20Power,15,kW,1&sums=0,1,0,9315_0,Total_Active_Power,null,5,Current%20Generation,red,2,%20kW,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Energy Meter" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,9301,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9301,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9301,Air_Temperature,Air Temperature,15,°C,\'darkred\';0,1,0,9301,Humidity ,Humidity,15,%,\'green\';0,1,0,9301,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,9301,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,9902_9901_9903_9905_9904,AC_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&avgs=0,1,0,9301,Air_Temperature,null,9,Avg.%20Air%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
            $count = 2;
            }
            else{
            $args = "0,1,0,9315,Forward_Active_Energy,Yield (EM),1440,kWh,'green',1;0,1,0,9301,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
  }
  
  else if($park_no == 81){ // Musiri
   if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9892,PAC,110KV%20line%20 feeder,15,MW,1;0,1,0,10085,PAC,110KV trafo 1,15,MW,2;0,1,0,10084,PAC,33KV trafo 1,15,MW,3;0,1,0,10080,PAC,110KV trafo 2,15,MW,4;0,1,0,10082,PAC,33KV trafo 2,15,MW,5;0,1,0,10081,PAC,33KV Feeder 1,15,MW,6;0,1,0,10078,PAC,33KV Feeder 2,15,MW,7;0,1,0,10083,PAC,33KV Feeder 3,15,MW,8;0,1,0,10076,PAC,33KV Feeder 4,15,MW,19;0,1,0,10076,PAC,auxiliary trafo,15,MW,11;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&sums=0,1,0,9892_0,pac,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control Room" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,9892_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
            $count = 2;
            }
            else{
            $args = "0,1,0,9892,EAE,110KV%20line%20 feeder,1440,MWh,'green',1;0,1,0,10085,EAE,110KV trafo 1,1440,MWh,'BlueViolet',2;0,1,0,10084,EAE,33KV trafo 1,1440,MWh,'Violet',3;0,1,0,10080,EAE,110KV trafo 2,1440,MWh,'CadetBlue',2;0,1,0,10082,EAE,33KV trafo 2,1440,MWh,'Brown',3;0,1,0,10081,EAE,33KV Feeder 1%20,1440,MWh,'Red',4;0,1,0,10078,EAE,33KV Feeder 2%20,1440,MWh,'Blue',4;0,1,0,10083,EAE,33KV Feeder 3%20,1440,MWh,'Orange',4;0,1,0,10076,EAE,33KV Feeder 4%20,1440,MWh,'Black',4;0,1,0,10108,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            
            
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
      }
  else if($park_no == 82){ // TTPET
   if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10118,PAC,110KV line feeder,15,MW,1;0,1,0,10125,PAC,110KV trafo 1,15,MW,2;0,1,0,10127,PAC,33KV trafo 1,15,MW,3;0,1,0,10124,PAC,110KV trafo 2,15,MW,4;0,1,0,10123,PAC,33KV trafo 2,15,MW,5;0,1,0,10126,PAC,33KV Feeder 1,15,MW,6;0,1,0,10114,PAC,33KV Feeder 2,15,MW,7;0,1,0,10116,PAC,33KV Feeder 3,15,MW,8;0,1,0,10115,PAC,33KV Feeder 4,15,MW,19;0,1,0,10128,PAC,auxiliary trafo,15,MW,11;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&sums=0,1,0,10118_0,pac,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control Room" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10108,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10108,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10108,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10108,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10108,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10118_0,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&avgs=0,1,0,10108,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
            $count = 2;
            }
            else{
            $args = "0,1,0,10118,EAE,110KV line feeder,1440,MWh,'green',1;0,1,0,10125,EAE,110KV trafo 1,1440,MWh,'BlueViolet',2;0,1,0,10127,EAE,33KV trafo 1,1440,MWh,'Violet',3;0,1,0,10124,EAE,110KV trafo 2,1440,MWh,'CadetBlue',2;0,1,0,10123,EAE,33KV trafo 2,1440,MWh,'Brown',3;0,1,0,10126,EAE,33KV Feeder 1%20,1440,MWh,'Red',4;0,1,0,10114,EAE,33KV Feeder 2%20,1440,MWh,'Blue',4;0,1,0,10116,EAE,33KV Feeder 3%20,1440,MWh,'Orange',4;0,1,0,10115,EAE,33KV Feeder 4%20,1440,MWh,'Black',4;0,1,0,10108,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            
            
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
      }
      
 else if($park_no == 83){ // Panchapatty
   if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,10445,PAC,110KV line feeder,15,MW,1;0,1,0,10442,PAC,110KV trafo 1,15,MW,2;0,1,0,10443,PAC,33KV trafo 2,15,MW,3;0,1,0,10446,PAC,33kv trafo 1,15,MW,4;0,1,0,10447,PAC,33KV trafo 2,15,MW,5;0,1,0,10450,PAC,33KV Feeder 1,15,MW,6;0,1,0,10448,PAC,33KV Feeder 2,15,MW,7;0,1,0,10451,PAC,33KV Feeder 3,15,MW,8;0,1,0,10449,PAC,33KV Feeder 4,15,MW,19;0,1,0,10444,PAC,auxiliary trafo,15,MW,11;0,1,0,10731,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&sums=0,1,0,10445_0,pac,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control Room" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,10731,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,10731,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,10731,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,10731,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,10731,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,10445_0,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&avgs=0,1,0,10731,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
            $count = 2;
            }
            else{
            $args = "0,1,0,10445,EAE,110KV line feeder,1440,MWh,'green',1;0,1,0,10442,EAE,110KV trafo 1,1440,MWh,'BlueViolet',2;0,1,0,10443,EAE,33KV trafo 2,1440,MWh,'Violet',3;0,1,0,10446,EAE,33kv trafo 1,1440,MWh,'CadetBlue',2;0,1,0,10447,EAE,33KV trafo 2,1440,MWh,'Brown',3;0,1,0,10450,EAE,33KV Feeder 1%20,1440,MWh,'Red',4;0,1,0,10448,EAE,33KV Feeder 2%20,1440,MWh,'Blue',4;0,1,0,10451,EAE,33KV Feeder 3%20,1440,MWh,'Orange',4;0,1,0,10449,EAE,33KV Feeder 4%20,1440,MWh,'Black',4;0,1,0,10731,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            
            
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
      }
     else if($park_no == 63){ // AP 30MW
   if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,12051,PAC,132kV line feeder,15,MW,1;0,1,0,12055,PAC,33kV outgoing feeder,15,MW,2;0,1,0,12058,PAC,33kV Incoming Feeder 1,15,MW,3;0,1,0,12061,PAC,33kV Incoming Feeder 2,15,MW,4;0,1,0,12053,PAC,33kV Incoming Feeder 3,15,MW,5;0,1,0,12054,PAC,33kV Incoming Feeder 4,15,MW,6;0,1,0,12039,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&sums=0,1,0,12051_0,pac,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control Room" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,12039,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,12039,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12039,Module_Temp,Module Temperature,15,°C,\'darkred\';0,1,0,12039,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,12039,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,12051_0,EAE,null,8,Yield (EM),black,2, kWh,10,0,Arial;&avgs=0,1,0,12039,Module_Temp,null,9,Avg.%20Module%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
            $count = 2;
            }
            else{
            $args = "0,1,0,12051,EAE,132kV line feeder,1440,MWh,'green',1;0,1,0,12055,EAE,33kV outgoing feeder,1440,MWh,'BlueViolet',2;0,1,0,12058,EAE,33kV Incoming Feeder 1,1440,MWh,'Violet',3;0,1,0,12061,EAE,33kV Incoming Feeder 2,1440,MWh,'CadetBlue',2;0,1,0,12053,EAE,33kV Incoming Feeder 3,1440,MWh,'Brown',3;0,1,0,12054,EAE,333kV Incoming Feeder 4%20,1440,MWh,'Red',4;0,1,0,12039,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            
            
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
      }  
      else if($park_no == 61){ // Rajasthan
   if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=0,1,0,9288,PAC,33kV Outgoing,15,MW,1;0,1,0,9282,PAC,33kV incomer 1,15,MW,2;0,1,0,9283,PAC,33kV incomer 2,15,MW,3;0,1,0,9279,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&sums=0,1,0,9288_0,pac,null,5,Current%20Generation,red,2,%20MW,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control Room" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,1,2&args=0,1,0,9279,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9279,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9279,Ambient_Temperature,Ambient_Temperature,15,°C,\'darkred\';0,1,0,9279,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&sums=0,1,0,9279,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,9288_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&avgs=0,1,0,9279,Ambient_Temperature,null,9,Avg.%20Ambient_Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
            $count = 2;
            }
            else{
            $args = "0,1,0,9288,EAE,33kV Outgoing,1440,MWh,'green',1;0,1,0,9282,EAE,33kV incomer 1,1440,MWh,'BlueViolet',2;0,1,0,9283,EAE,33kV incomer 2,1440,MWh,'Violet',3;0,1,0,9279,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            
            
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
      }
    else if($park_no == 56){ // Amplus Rudrapur
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
  
  else if($park_no == 55){ // Lalpur
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
  
 else if($park_no == 52){ // Dominos Nagpur
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
  
  else if($park_no == 54){ // Indus Nagpur
      if ($phase=="tag"){

            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=54&phase=Indtag&defaults=0,1&args=0,1,0,10151,Activepower_Total,Active Power,15,kW,4;0,1,0,10452,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,10151_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=54&phase=energygIndus&defaults=0,1,2,3&args=0,1,0,10151,System_PR,System PR,15,%,1;0,1,0,12668,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,13004,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
            $count = 2;

        }
        else{
            $args = "0,1,0,10151,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,10452,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
        }
  } 
  
  else if($park_no == 53){ // Royal Heritage Pune
      if ($phase=="tag"){

            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=53&phase=Roytag&defaults=0,1&args=0,1,0,10071,Activepower_Total,Active Power,15,kW,4;0,1,0,12692,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,10071_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=53&phase=energyg2&defaults=0,1,2,3&args=0,1,0,10071,System_PR,System PR,15,%,1;0,1,0,11722,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12692,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
            $count = 2;

        }
        else{
            $args = "0,1,0,10071,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,12692,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
        }
  } 
  elseif ($park_no == 62) { // Punjab 4

        if ($phase != "tag") {
            $args = "0,1,0,11751,EAE,66kV line feeder,1440,MWh,'Blue',1;0,1,0,11752,EAE,11kV outgoing,1440,MWh,'BlueViolet',2;0,1,0,11750,EAE,11kV incommer1,1440,MWh,'Violet',2;0,1,0,11753,EAE,11kV incommer2,1440,MWh,'CadetBlue',2;0,1,0,9164,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
        } else {
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=0,4&args=0,1,0,11751,PAC,66kV line feeder,15,MW,4;0,1,0,11752,PAC,11kV outgoing,15,MW,5;0,1,0,11750,PAC,11kV incommer1,15,MW,6;0,1,0,11753,PAC,11kV incommer2,15,MW,7;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;0,1,0,11751_0,PAC,null,5,Current%20Generation,red,2,%20MW,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Control%20Room" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?defaults=1,2&args=0,1,0,9164,Wind_Speed,Wind Speed,15,m/s,6;0,1,0,9164,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,9164,Ambient_Temperature,Ambient Temperature,15,°C,\'darkred\';0,1,0,9164,Wind_Direction,Wind Direction,15,°,\'Burlywood\'&etotal=0,1,0,11751_0,EAE,null,8,Yield (EM),black,2, MWh,10,0,Arial;&sums=0,1,0,9164,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial&avgs=0,1,0,9164,Ambient_Temperature,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather%20Station" border="0"></iframe></td></tr><br>';
            $count = 2; 
        }
}
else if($park_no == 57){ // Amplus Origami
      if ($phase=="tag"){

            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=57&phase=Orgtag&defaults=0,1&args=0,1,0,11615,Activepower_Total,Active Power,15,kW,4;0,1,0,12029,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,11615_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=57&phase=Orgenergyg2&defaults=0,1,2,3&args=0,1,0,11615,System_PR,System PR,15,%,1;0,1,0,12028,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12028,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
            $count = 2;

        }
        else{
            $args = "0,1,0,11615,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,12029,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
        }
  } 
else if($park_no == 58){ // Amplus Polymers
      if ($phase=="tag"){

            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=58&phase=Polytag&defaults=0,1&args=0,1,0,12037,Activepower_Total,Active Power,15,kW,4;0,1,0,12417,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,12037_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=58&phase=Polyenergyg2&defaults=0,1,2,3&args=0,1,0,12037,System_PR,System PR,15,%,1;0,1,0,12660,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12660,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
            $count = 2;

        }
        else{
            $args = "0,1,0,11615,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,12417,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
        }
  } 
else if($park_no == 59){ // Amplus Yamaha
      if ($phase=="tag"){

            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=59&phase=Yamahatag&args=0,1,0,12895,Activepower_Total,Block A,15,kW,1;0,1,0,12899,Activepower_Total,Block B,15,kW,2;0,1,0,12897,Activepower_Total,Block C,15,kW,3;0,1,0,12898,Activepower_Total,Block D,15,kW,5;0,1,0,12896,Activepower_Total,Block E,15,kW,4;0,1,0,12209,Solar_Radiation,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,12895_12899_12897_12898_12896,Activepower_Total,null,8,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=59&phase=Yamahaenergyg2&args=0,1,0,12895,System_PR,Block A PR,15,%,1;0,1,0,12899,System_PR,Block B PR,15,%,2;0,1,0,12897,System_PR,Block C PR,15,%,3;0,1,0,12898,System_PR,Block D PR,15,%,5;0,1,0,12896,System_PR,Block E PR,15,%,4;0,1,0,12900,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12900,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
            $count = 2;

        }
        else{
            $args = "0,1,0,12895,Forward_Active_Energy,Block A(EM),1440,kWh,'green',1;0,1,0,12899,Forward_Active_Energy,Block B(EM),1440,kWh,'Blue',1;0,1,0,12897,Forward_Active_Energy,Block C(EM),1440,kWh,'Orange',1;0,1,0,12898,Forward_Active_Energy,Block D(EM),1440,kWh,'Brown',1;0,1,0,12896,Forward_Active_Energy,Block E(EM),1440,kWh,'black',1;0,1,0,12209,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
        }
  } 
else if($park_no == 21){ // MAS Solar
      if ($phase=="tag"){
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=21&phase=MasEM&args=0,1,0,12908,Activepower_Total,AC%20Power,15,kW,1&sums=0,1,0,12908_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial;&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Energy Meter" border="0"></iframe></td></tr><br>';
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=21&phase=MASWS&defaults=0,1,2&args=0,1,0,12907,Solar_Radiation,Irradiation,15,W/m²,\'Gold\';0,1,0,12907,AMBIENT_TEMP,Ambient Temperature,15,°C,\'darkred\';0,1,0,12907,MODULE_TEMP ,Module Temperature,15,°C,\'green\'&sums=0,1,0,12907,Solar_Radiation,null,5,Total%20Irradiation,brown,2,%20Wh/m²,10,0,Arial;&etotal=0,1,0,12908,Forward_Active_Energy,null,8,Yield (EM),black,2, kWh,10,0,Arial;&avgs=0,1,0,12907,AMBIENT_TEMP,null,9,Avg.%20Ambient%20Temperature,red,2,%20°C,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Weather Station" border="0"></iframe></td></tr><br>';
            $count = 2;
            }
            else{
            $args = "0,1,0,12908,Forward_Active_Energy,Yield (EM),1440,kWh,'green',1;0,1,0,12907,Solar_Radiation,Irradiation,1440,Wh/mSQUA,'Gold',0";
            $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
            $count = 1;
            }
  }
  else if($park_no == 72){ // Amplus Fortis
   if ($phase=="tag"){

    $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?park_no=72&phase=Forttag&defaults=0,1&args=0,1,0,13476,Activepower_Total,Active Power,15,kW,4;0,1,0,13475,Irradiance,Irradiation,15,W/m&sup2;,\'Gold\';&sums=0,1,0,13476_0,Activepower_Total,null,5,Current%20Generation,red,2,%20kW,10,0,Arial&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-1" border="0"></iframe></td></tr>';
    $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/energydiagram.php?park_no=72&phase=Fortenergyg2&defaults=0,1,2,3&args=0,1,0,13476,System_PR,System PR,15,%,1;0,1,0,13475,En_irradiation,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,13475,Energy_Module_Temp,Module Temperature,15,&deg;C,\'darkred\'&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '&title=Graph-2" border="0"></iframe></td></tr>';
    $count = 2;

  }
  else{
   $args = "0,1,0,13476,Forward_Active_Energy,Energy Meter Conzerv,1440,kWh,'green',1;0,1,0,13475,Irradiance,Irradiation,1440,Wh/mSQUA,'Gold',0";
   $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
   $count = 1;
  }
  }

else { 
    if ($park_no == 99) {
        $park_no = -1;
    }

    if (false && $park_no == 30 && $phase != "tag") {


        $args = '0,1,0,670,EA-,Outgoing%20Yield,1440,MWh,\'darkblue\',3;0,1,0,737,EA-,RMU-5%20Yield,1440,MWh,\'purple\',2;0,1,0,738,EA-,RMU-1%20Yield,1440,MWh,\'darkgreen\',2;0,0.001,0,678,E_Total,Block%201%20I1%20Master,1440,MWh,\'green\',1;0,0.001,0,713,E_Total,Block%201%20I1%20Slave,1440,MWh,\'green\',1;0,0.001,0,703,E_Total,Block%201%20I2%20Master,1440,MWh,\'green\',1;0,0.001,0,709,E_Total,Block%201%20I2%20Slave,1440,MWh,\'green\',1;0,0.001,0,651,E_Total,Block%202%20I3%20Master,1440,MWh,\'blue\',1;0,0.001,0,567,E_Total,Block%202%20I3%20Slave,1440,MWh,\'blue\',1;0,0.001,0,621,E_Total,Block%202%20I4%20Master,1440,MWh,\'blue\',1;0,0.001,0,616,E_Total,Block%202%20I4%20Slave,1440,MWh,\'blue\',1;0,0.001,0,516,E_Total,Block%203%20I5%20Master,1440,MWh,\'brown\',1;0,0.001,0,607,E_Total,Block%203%20I5%20Slave,1440,MWh,\'brown\',1;0,0.001,0,577,E_Total,Block%203%20I6%20Master,1440,MWh,\'brown\',1;0,0.001,0,479,E_Total,Block%203%20I6%20Slave,1440,MWh,\'brown\',1;0,0.001,0,660,E_Total,Block%204%20I7%20Master,1440,MWh,\'red\',1;0,0.001,0,592,E_Total,Block%204%20I7%20Slave,1440,MWh,\'red\',1;0,0.001,0,542,E_Total,Block%205%20I8%20Master,1440,MWh,\'orange\',1;0,0.001,0,529,E_Total,Block%205%20I8%20Slave,1440,MWh,\'orange\',1;0,0.001,0,570,E_Total,Block%205%20I9%20Master,1440,MWh,\'orange\',1;0,0.001,0,519,E_Total,Block%205%20I9%20Slave,1440,MWh,\'orange\',1';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp) . '&args=' . $args . '" border="0"></iframe></td></tr><br>';

        $args = '0,1,0,670,EA-,Outgoing%20Yield,1440,MWh,\'darkblue\',2;0,1,0,737,EA-,RMU-5%20Yield,1440,MWh,\'purple\',1;0,1,0,738,EA-,RMU-1%20Yield,1440,MWh,\'darkgreen\',1';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp) . '&args=' . $args . '" border="0"></iframe></td></tr><br>';

        $args = '0,1,0,670,EA-,Outgoing%20Yield,1440,MWh,\'darkblue\',1;';
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp) . '&args=' . $args . '" border="0"></iframe></td></tr><br>';


        $count = 3;
    } else if (false && $park_no == 20 && $phase != "tag") {

        $args = "0,1,0,15,E_Total,15.Total20Energy,1440,kWH,2,1;0,1,0,16,E_Total,16.Total%20Energy,1440,kWH,3,1;0,1,0,38,E_Total,38.Total%20Energy,1440,kWH,4,1;0,1,0,39,E_Total,39.Total%20Energy,1440,kWH,5,1;0,1,0,53,E_Total,53.Total%20Energy,1440,kWH,6,1;0,1,0,54,E_Total,54.Total%20Energy,1440,kWH,7,1;0,1,0,59,E_Total,59.Total%20Energy,1440,kWH,8,1;0,1,0,60,E_Total,60.Total%20Energy,1440,kWH,9,1;0,1,0,65,E_Total,65.Total%20Energy,1440,kWH,10,1;0,1,0,66,E_Total,66.Total%20Energy,1440,kWH,11,1;0,1,0,181,E_Total,Premier%202,1440,kWH,0,2;0,1,0,182,E_Total,Premier%201,1440,kWH,1,2;0,1,0,180,U1_R,Irradiation,1440,Wh/mSQUA,'yellow',0";
        $diagrammCode .= '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram8Compressed.php?phase=' . $phase . '&args=' . $args . '&hideClear=1&hideDelta=1&stamp=' . ($stamp) . '&endstamp=' . ($endstamp ) . '" border="0"></iframe></td></tr><br>';
        $count = 1;
    }  else {

        $query = "select bezeichnung, type from diagramm where (portal = '0' or portal = '" . $park_no . "') and subpark=-1 and area=-1 order by nr";
        $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

        $count = 0;
        $diagrammCode = "";

        while ($row_ds1 = mysql_fetch_array($ds1)) {
            $diagrammCode .= '<tr><td><iframe id="frame' . $count . '" width="99%" height="99%" SRC="diagram/mydiagram.php' . $row_ds1['type'] . '&title=' . $row_ds1['bezeichnung'] . '" border="0"></iframe></td></tr><br>';
            $count++;
        }
    }
}
$count*=100;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
    <HEAD>

        <TITLE>-- Solarportal iPLON --</TITLE>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="css/scroll.css" rel="stylesheet" type="text/css">
        <link href="css/text.css" rel="stylesheet" type="text/css">
        <link href="css/style_add.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            html,body,form {
                margin: 0;
                padding: 0;
                height: 100%;
                width: 100%;
                font-family: arial, sans-serif;
            }
        </style>


    </HEAD>

    <BODY style="font-family: arial, sans-serif;" bgcolor="#ffffff" leftmargin="20" topmargin="5"
          onLoad="if(parent.navigation.location != 'navigation.php?park_no=<?php echo $park_no; ?>&subpark=<?php echo $subpark_id; ?>&meter_existent=<?php echo $meter_existent; ?>')  parent.navigation.location.href='navigation.php?park_no=<?php echo $park_no; ?>&subpark=<?php echo $subpark_id; ?>&meter_existent=<?php echo $meter_existent; ?>';">

        <form name=ThisForm method="post" action="" target="_self">



            <?php
            echo '<!--[if IE]><TABLE height="' . ($count / 2) . '%" width="100%" cellpadding="0" cellspacing="0" border="0" align="left"><![endif]--><!--[if !IE]><TABLE height="' . ($count) . '%" width="100%" cellpadding="0" cellspacing="0" border="0" align="left"><![endif]-->';
            ?>
            <TR>
                <td style="height: 1px;" align="left" valign="top" class="grundtext" bgcolor="#ffffff">
                    <?php
                    if ($capacity_park > 0) {
                        echo "<span class=\"ueberschriftbraun\">" . $bezeichnung . " (" . $capacity_park . " kWp)</span>";
                    } else {
                        echo "<span class=\"ueberschriftbraun\">" . $bezeichnung . "</span>";
                    }
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
                            echo "<A HREF=\"einspeisung_park.php?diagtyp_sel=" . $diagtyp_sel . "&phase=jahr&jahr=" . $yy . "\"";
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

                                echo "<A HREF=\"einspeisung_park.php?diagtyp_sel=" . $diagtyp_sel . "&phase=mon&mon=" . $zahl . "&jahr=" . $jahr . "\"";
                                if ($zahl == $mon && ($phase == "mon" || $phase == "tag")) {
                                    if ($zahl == $monat_heute) {

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
                                    echo "<a href=\"einspeisung_park.php?diagtyp_sel=" . $diagtyp_sel . "&phase=tag&tag=" . $i_t . "&mon=" . $mon . "&jahr=" . $jahr . "\"";
                                    if ($i_t == $tag && ($phase == "tag")) {
                                        if ($i_t == $tag_heute) {

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
            <?php echo $diagrammCode; ?>


        </TABLE>
    </form>
</BODY>
</HTML>
