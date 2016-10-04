<?php

require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

date_default_timezone_set('UTC');
$stamp = mktime(18, 30, 0, $mon, $tag - 1, $jahr, 0);

if ($portal == "20") {

	$a1 = array();
	$a7 = array();
	$a61a = array();
	$a61b = array();
	$tstamps = array();

	$query = "SELECT a1.ts as ts, a1.input_7 AS 1_module_surface, a1.input_2 AS 1_sensor_temp, a1.input_1 AS 1_irradiation FROM 2001_sensorik as a1 WHERE a1.ts > " . $stamp . " ORDER BY a1.ts";
	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {

		$a1['' . round($row_ds1['ts'] / 900)] = date('d-m-Y H:i', $row_ds1['ts'] + 19800) . ", " . $row_ds1['1_irradiation'] . ", " . $row_ds1['1_module_surface'] . ", " . $row_ds1['1_sensor_temp'];
		$tstamps[] = '' . round($row_ds1['ts'] / 900);
	}

	$query = "SELECT a1.ts as ts, a1.input_7 AS 1_module_surface, a1.input_2 AS 1_sensor_temp, a1.input_1 AS 1_irradiation FROM 2007_sensorik as a1 WHERE a1.ts > " . $stamp . " ORDER BY a1.ts";

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {

		$a7[round($row_ds1['ts'] / 900)] = ", " . $row_ds1['1_irradiation'] . ", " . $row_ds1['1_module_surface'] . ", " . $row_ds1['1_sensor_temp'];
	}
	$query = "SELECT a1.ts as ts, a1.input_7 AS 1_module_surface, a1.input_2 AS 1_sensor_temp, a1.input_1 AS 1_irradiation FROM 2061_sensorik as a1 WHERE node_id = 107 and a1.ts > " . $stamp . " ORDER BY a1.ts";

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		$a61a['' . round($row_ds1['ts'] / 900)] = ", " . $row_ds1['1_irradiation'] . ", " . $row_ds1['1_module_surface'] . ", " . $row_ds1['1_sensor_temp'];
	}

	$query = "SELECT a1.ts as ts, a1.input_2 AS wind_speed, a1.input_1 AS wind_dir FROM 2061_sensorik as a1 WHERE node_id = 105 and a1.ts > " . $stamp . " ORDER BY a1.ts";

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		$a61b['' . round($row_ds1['ts'] / 900)] = ", " . $row_ds1['wind_speed'] . ", " . $row_ds1['wind_dir'];
	}

	//foreach ($a7 as $key => $value){
	//	echo $key."=".$value."<br>";
	//}
	//return;

	echo "Kiran 20MWp, Block A1, Block A1, Block A1, Block A7, Block A7, Block A7, Weather station, Weather station, Weather station, Weather station, Weather station, Plant Trafo1, Plant Trafo2<br>";
	echo "Timestamp, Irradiation, Module Surface, Sensor Temperature, Irradiation, Module Surface, Sensor Temperature, Irradiation, Module Surface, Ambient Temperature, Wind speed, Wind direction, Energy Total, Energy Total<br>";
	echo "DD/MM/YYYY HH:MM, W/sq.m, °C, °C, W/sq.m, °C, °C, W/sq.m, °C, °C, m/s, degree, kwh, kwh<br>";

	foreach ($tstamps as $stamp) {
		echo $a1[$stamp];
		if (array_key_exists($stamp, $a7)) {
			echo $a7[$stamp];
		} else {
			echo ", , , ";
		}

		if (array_key_exists($stamp, $a61a)) {
			echo $a61a[$stamp];
		} else {
			echo ", , , ";
		}

		if (array_key_exists($stamp, $a61b)) {
			echo $a61b[$stamp];
		} else {
			echo ", , ";
		}
		echo "<br>";
	}
} else if ($portal == "25") {

	$a1 = array();
	$energy = array();

	$tstamps = array();

	$query = "SELECT a1.ts as ts, a1.input_1 AS temp, a1.input_3 AS irr1, a1.input_4 AS irr2, a1.input_5 as ws, a1.input_6 as wd FROM 2501_sensorik as a1 WHERE a1.ts > " . $stamp . " ORDER BY a1.ts";
	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {

		$a1['' . round($row_ds1['ts'] / 300)] = date('d-m-Y H:i', $row_ds1['ts'] + 19800) . ", " . $row_ds1['temp'] . ", " . $row_ds1['irr1'] . ", " . $row_ds1['irr2'] . ", " . $row_ds1['ws'] . ", " . $row_ds1['wd'];
		$tstamps[] = '' . round($row_ds1['ts'] / 300);

	}

	$query = "select ts, e_total from 2541_messdaten_s0 where ts > " . $stamp . " order by ts";

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		$energy['' . round($row_ds1['ts'] / 300)] = ", " . $row_ds1['e_total'];
	}

	echo "Timestamp, Temperature, Irradiation 1, Irradiation 2, Wind speed, Wind direction, Energy total<br>";
	echo "DD/MM/YYYY HH:MM, °C, W/sq.m, W/sq.m, m/s, degree, kwh<br>";

	foreach ($tstamps as $stamp) {
		echo $a1[$stamp];
		if (array_key_exists($stamp, $energy)) {
			echo $energy[$stamp];
		} else {
			echo ", ";
		}

		echo "<br>";
	}
}
?>