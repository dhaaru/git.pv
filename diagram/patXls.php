<?php

require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

session_start();

require_once 'Spreadsheet/Excel/Writer.php';
$workbook = new Spreadsheet_Excel_Writer();
$today = date("Y-m-d");
$filename = "pat_data_from_" . $jahr . "-" . $mon . "-" . $tag . "_to_" . $today . ".xls";
$workbook -> send($filename);
$sheet = &$workbook -> addWorksheet("Export");

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

		$a1['' . round($row_ds1['ts'] / 900)] = $row_ds1['1_irradiation'] . "," . $row_ds1['1_module_surface'] . "," . $row_ds1['1_sensor_temp'];
		$tstamps[] = '' . round($row_ds1['ts'] / 900);
	}

	$query = "SELECT a1.ts as ts, a1.input_7 AS 1_module_surface, a1.input_2 AS 1_sensor_temp, a1.input_1 AS 1_irradiation FROM 2007_sensorik as a1 WHERE a1.ts > " . $stamp . " ORDER BY a1.ts";

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {

		if (!in_array('' . round($row_ds1['ts'] / 900), $tstamps)) {
			$tstamps[] = '' . round($row_ds1['ts'] / 900);
		}
		$a7[round($row_ds1['ts'] / 900)] = $row_ds1['1_irradiation'] . "," . $row_ds1['1_module_surface'] . "," . $row_ds1['1_sensor_temp'];
	}
	$query = "SELECT a1.ts as ts, a1.input_7 AS 1_module_surface, a1.input_2 AS 1_sensor_temp, a1.input_1 AS 1_irradiation FROM 2061_sensorik as a1 WHERE node_id = 107 and a1.ts > " . $stamp . " ORDER BY a1.ts";

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (!in_array('' . round($row_ds1['ts'] / 900), $tstamps)) {
			$tstamps[] = '' . round($row_ds1['ts'] / 900);
		}
		$a61a['' . round($row_ds1['ts'] / 900)] = $row_ds1['1_irradiation'] . "," . $row_ds1['1_module_surface'] . "," . $row_ds1['1_sensor_temp'];
	}

	$query = "SELECT a1.ts as ts, a1.input_2 AS wind_speed, a1.input_1 AS wind_dir FROM 2061_sensorik as a1 WHERE node_id = 105 and a1.ts > " . $stamp . " ORDER BY a1.ts";

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (!in_array('' . round($row_ds1['ts'] / 900), $tstamps)) {
			$tstamps[] = '' . round($row_ds1['ts'] / 900);
		}
		$a61b['' . round($row_ds1['ts'] / 900)] = $row_ds1['wind_speed'] . "," . $row_ds1['wind_dir'];
	}

	//foreach ($a7 as $key => $value){
	//	echo $key."=".$value."<br>";
	//}
	//return;

	//echo "Kiran 20MWp, Block A1, Block A1, Block A1, Block A7, Block A7, Block A7, Weather station, Weather station, Weather station, Weather station, Weather station, Plant Trafo1, Plant Trafo2<br>";
	//echo "Timestamp, Irradiation, Module Surface, Sensor Temperature, Irradiation, Module Surface, Sensor Temperature, Irradiation, Module Surface, Ambient Temperature, Wind speed, Wind direction, Energy Total, Energy Total<br>";
	//echo "DD/MM/YYYY HH:MM, W/sq.m, °C, °C, W/sq.m, °C, °C, W/sq.m, °C, °C, m/s, degree, kwh, kwh<br>";

	sort($tstamps);

	$sheet -> write(0, 0, "Kiran 20MWp");
	$sheet -> write(0, 1, "Block A1");
	$sheet -> write(0, 2, "Block A1");
	$sheet -> write(0, 3, "Block A1");
	$sheet -> write(0, 4, "Block A7");
	$sheet -> write(0, 5, "Block A7");
	$sheet -> write(0, 6, "Block A7");
	$sheet -> write(0, 7, "Weather Station");
	$sheet -> write(0, 8, "Weather Station");
	$sheet -> write(0, 9, "Weather Station");
	$sheet -> write(0, 10, "Weather Station");
	$sheet -> write(0, 11, "Weather Station");
	$sheet -> write(0, 12, "Plant Trafo 1");
	$sheet -> write(0, 13, "Plant Trafo 2");

	$sheet -> write(1, 0, "Timestamp");
	$sheet -> write(1, 1, "Irradiation");
	$sheet -> write(1, 2, "Module Surface Temperature");
	$sheet -> write(1, 3, "Sensor Temperature");
	$sheet -> write(1, 4, "Irradation");
	$sheet -> write(1, 5, "Module Surface Temperature");
	$sheet -> write(1, 6, "Sensor Temperature");
	$sheet -> write(1, 7, "Irradiation");
	$sheet -> write(1, 8, "Module Surface Temperature");
	$sheet -> write(1, 9, "Ambient Temperature");
	$sheet -> write(1, 10, "Wind Speed");
	$sheet -> write(1, 11, "Wind Direction");
	$sheet -> write(1, 12, "Energy Total");
	$sheet -> write(1, 13, "Energy Total");

	$sheet -> write(2, 0, "DD/MM/YYYY HH:MM");
	$sheet -> write(2, 1, "W/sq.m");
	$sheet -> write(2, 2, "°C");
	$sheet -> write(2, 3, "°C");
	$sheet -> write(2, 4, "W/sq.m");
	$sheet -> write(2, 5, "°C");
	$sheet -> write(2, 6, "°C");
	$sheet -> write(2, 7, "W/sq.m");
	$sheet -> write(2, 8, "°C");
	$sheet -> write(2, 9, "°C");
	$sheet -> write(2, 10, "m/s");
	$sheet -> write(2, 11, "degree");
	$sheet -> write(2, 12, "kWh");
	$sheet -> write(2, 13, "kWh");

	$line = 2;

	foreach ($tstamps as $stamp) {
		$line++;
		$field = 0;
		$sheet -> write($line, $field++, date('d-m-Y H:i', ($stamp * 900 + 19800)));

		if (array_key_exists($stamp, $a1)) {

			foreach (split(",", $a1[$stamp]) as $word) {
				$sheet -> write($line, $field++, $word);
			}
		} else {
			foreach (split(",",", , , ") as $word) {
				$sheet -> write($line, $field++, $word);
			}
		}

		if (array_key_exists($stamp, $a7)) {

			foreach (split(",", $a7[$stamp]) as $word) {
				$sheet -> write($line, $field++, $word);
			}
		} else {
			foreach (split(",", ", , , ") as $word) {
				$sheet -> write($line, $field++, $word);
			}
		}

		if (array_key_exists($stamp, $a61a)) {
			foreach (split(",", $a61a[$stamp]) as $word) {
				$sheet -> write($line, $field++, $word);
			}

		} else {
			foreach (split(",", ", , , ") as $word) {
				$sheet -> write($line, $field++, $word);
			}
		}

		if (array_key_exists($stamp, $a61b)) {
			foreach (split(",", $a61b[$stamp]) as $word) {
				$sheet -> write($line, $field++, $word);
			}

		} else {
			foreach (split(",", ", , ") as $word) {
				$sheet -> write($line, $field++, $word);
			}
		}

	}

} else if ($portal == "25") {

	$a1 = array();
	$wrs = array();

	$energy = array();

	$tstamps = array();

	$query = "SELECT a1.ts as ts, a1.input_1 AS temp, a1.input_3 AS irr1, a1.input_4 AS irr2, a1.input_5 as ws, a1.input_6 as wd FROM 2501_sensorik as a1 WHERE a1.ts > " . $stamp . " ORDER BY a1.ts";
	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {

		$a1['' . round($row_ds1['ts'] / 300)] = $row_ds1['temp'] . "," . $row_ds1['irr1'] . "," . $row_ds1['irr2'] . "," . $row_ds1['ws'] . "," . $row_ds1['wd'];
		$tstamps[] = '' . round($row_ds1['ts'] / 300);

	}

	$query = "select ts, e_total from 2541_messdaten_s0 where ts > " . $stamp . " order by ts";

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {

		if (!in_array('' . round($row_ds1['ts'] / 300), $tstamps)) {
			$tstamps[] = '' . round($row_ds1['ts'] / 300);
		}
		$energy['' . round($row_ds1['ts'] / 300)] = $row_ds1['e_total'];
	}

	$query = "select distinct sn from 2531_messdaten_wr where ts > " . $stamp;
	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	$wrIndex = 0;
	while ($row_ds1 = mysql_fetch_array($ds1)) {

		$wrs[$wrIndex] = array();
		$wrs[$wrIndex][0] = $row_ds1['sn'];
		$wrs[$wrIndex][1] = array();

		$query = "select ts, e_total from 2531_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
		$ds2 = mysql_query($query, $verbindung) or die(mysql_error());
		while ($row_ds2 = mysql_fetch_array($ds2)) {
			if (!in_array('' . round($row_ds2['ts'] / 300), $tstamps)) {
				$tstamps[] = '' . round($row_ds2['ts'] / 300);
			}
			$wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 300)] = $row_ds2['e_total'];
		}
		$wrIndex++;
	}

	sort($tstamps);

	/*
	 foreach ($tstamps as $stamp){

	 echo $stamp."<br>";
	 }

	 echo "<br><br><br><br><br><br><br>";

	 foreach ($wrs as $wr){
	 echo "sn: '".$wr[0]."'<br>";
	 foreach ($wr[1] as $key=>$value){
	 echo $key."=".$value."<br>";
	 }
	 }
	 return;
	 */

	$sheet -> write(0, 0, "Timestamp");
	$sheet -> write(0, 1, "Temperature");
	$sheet -> write(0, 2, "Irradiation 1");
	$sheet -> write(0, 3, "Irradiation 2");
	$sheet -> write(0, 4, "Wind Speed");
	$sheet -> write(0, 5, "Wind Direction");
	$sheet -> write(0, 6, "Energy Total");

	$sheet -> write(1, 0, "DD/MM/YYYY HH:MM");
	$sheet -> write(1, 1, "°C");
	$sheet -> write(1, 2, "W/sq.m");
	$sheet -> write(1, 3, "W/sq.m");
	$sheet -> write(1, 4, "m/s");
	$sheet -> write(1, 5, "degree");
	$sheet -> write(1, 6, "kWh");
	for ($wrCounter = 0; $wrCounter < $wrIndex; $wrCounter++) {
		$sheet -> write(0, 7 + $wrCounter, $wrs[$wrCounter][0]);
		$sheet -> write(1, 7 + $wrCounter, "kWh");
	}

	$line = 1;

	foreach ($tstamps as $stamp) {
		$line++;
		$field = 0;
		$sheet -> write($line, $field++, date('d-m-Y H:i', ($stamp * 300 + 19800)));

		if (array_key_exists($stamp, $a1)) {
			foreach (split(",", $a1[$stamp]) as $word) {
				$sheet -> write($line, $field++, $word);
			}
		} else {
			foreach (split(",", ", , , , ") as $word) {
				$sheet -> write($line, $field++, $word);
			}
		}

		if (array_key_exists($stamp, $energy)) {
			foreach (split(",", $energy[$stamp]) as $word) {
				$sheet -> write($line, $field++, $word);
			}
		} else {
			foreach (split(",", ", ") as $word) {
				$sheet -> write($line, $field++, $word);
			}

		}

		foreach ($wrs as $wr) {
			if (array_key_exists($stamp, $wr[1])) {
				$sheet -> write($line, $field++, $wr[1][$stamp]);
			} else {
				$sheet -> write($line, $field++, ' ');
			}
		}
	}
}

$workbook -> close();
?>