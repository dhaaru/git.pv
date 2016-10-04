<?php
require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
include ('../locale/gettext_header.php');
include ('../functions/dgr_func_jpgraph.php');
include ('../functions/allg_functions.php');
include ('../functions/b_breite.php');
include ('exportHelper.php');

if ($park_no == "20") {

	$query = "select * from areas where status = 1 and subpark_id in ( select id from subparks where status = 1 and park_no = '$park_no' )";
	$total_nenn = 0;
	$inst_prod = 0;
	$day_prod = 0;
	$e_total = 0;
	$windSpeed = 0;
	$windDir = 0;
	$rad = 0;
	$tmp1 = 0;
	$tmp2 = 0;
	$yesterday = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
	$date = mktime();

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		//$query2 = "select distinct w.* from meters as w, virt_ez_transtab as t where w.anl_virt_id = 1 and w.status=1 and t.area_id = ".$row_ds1[id]." and w.igate_id = t.igate_id and w.sn = t.sn";
		$query2 = "select distinct w.* from wechselrichter as w, virt_wr_transtab as t where w.status=1 and w.watch = 1 and t.area_id = " . $row_ds1[id] . " and w.igate_id = t.igate_id and w.sn = t.sn";

		$ds2 = mysql_query($query2, $verbindung) or die(mysql_error());
		while ($row_ds2 = mysql_fetch_array($ds2)) {

			$total_nenn += $row_ds2[nennleistung];

			//$query3 = "select e_total, p_ac_ph1 from ".$row_ds2[igate_id]."_messdaten_s0 where sn='".$row_ds2[sn]."' and ts < $date and e_total>0 order by ts desc";
			$query3 = "select e_total, p_ac_ph1 from " . $row_ds2[igate_id] . "_messdaten_wr where sn='" . $row_ds2[sn] . "' and ts < $date and e_total>0 order by ts desc";

			$ds3 = mysql_query($query3, $verbindung) or die(mysql_error());

			$tmp_e = 0;
			while ($row_ds3 = mysql_fetch_array($ds3)) {
				//	echo " ".$row_ds2[sn].": p".$row_ds3[p_ac_ph1]." e_neu: ".$row_ds3[e_total];

				$inst_prod += $row_ds3[p_ac_ph1];

				$tmp_e = $row_ds3[e_total];
				$e_total += $tmp_e;
				break;
			}

			//$query3 = "select max(e_total) as e from ".$row_ds2[igate_id]."_messdaten_s0 where sn='".$row_ds2[sn]."' and ts < $yesterday";
			$query3 = "select max(e_total) as e from " . $row_ds2[igate_id] . "_messdaten_wr where sn='" . $row_ds2[sn] . "' and e_total>0 and ts < $yesterday";

			$ds3 = mysql_query($query3, $verbindung) or die(mysql_error());
			while ($row_ds3 = mysql_fetch_array($ds3)) {
				//	echo " ".$row_ds3[e]."<br>";
				$day_prod += ($tmp_e - $row_ds3[e]);
				break;
			}
		}
	}

	$query3 = "select input_2 as speed, input_1 as dir from 2061_sensorik where node_id='105' order by ts desc";
	$ds3 = mysql_query($query3, $verbindung) or die(mysql_error());
	while ($row_ds3 = mysql_fetch_array($ds3)) {
		//	echo " ".$row_ds3[e]."<br>";
		$windSpeed = $row_ds3[speed];
		$windDir = $row_ds3[dir];

		break;
	}

	$query3 = "select input_1 as rad, input_2 as tmp1, input_7 as tmp2 from 2061_sensorik where node_id='107' order by ts desc";
	$ds3 = mysql_query($query3, $verbindung) or die(mysql_error());
	while ($row_ds3 = mysql_fetch_array($ds3)) {
		//		echo " ".$row_ds3[e]."<br>";
		$rad = $row_ds3[rad];
		$tmp1 = $row_ds3[tmp1];
		$tmp2 = $row_ds3[tmp2];

		break;
	}

}

if ($park_no == "25") {

	$query = "select * from areas where status = 1 and subpark_id in ( select id from subparks where status = 1 and park_no = '$park_no' )";
	$total_nenn = 0;
	$inst_prod = 0;
	$day_prod = 0;
	$e_total = 0;
	$windSpeed = 0;
	$windDir = 0;
	$rad2 = 0;
	$rad1 = 0;
	$tmp = 0;
	$yesterday = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
	$date = mktime();

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		//$query2 = "select distinct w.* from meters as w, virt_ez_transtab as t where w.anl_virt_id = 1 and w.status=1 and t.area_id = ".$row_ds1[id]." and w.igate_id = t.igate_id and w.sn = t.sn";
		$query2 = "select distinct w.* from wechselrichter as w, virt_wr_transtab as t where w.status=1 and w.watch = 1 and t.area_id = " . $row_ds1[id] . " and w.igate_id = t.igate_id and w.sn = t.sn";

		$ds2 = mysql_query($query2, $verbindung) or die(mysql_error());
		while ($row_ds2 = mysql_fetch_array($ds2)) {

			$total_nenn += $row_ds2[nennleistung];

			//$query3 = "select e_total, p_ac_ph1 from ".$row_ds2[igate_id]."_messdaten_s0 where sn='".$row_ds2[sn]."' and ts < $date and e_total>0 order by ts desc";
			$query3 = "select e_total, p_ac_ph1 from " . $row_ds2[igate_id] . "_messdaten_wr where sn='" . $row_ds2[sn] . "' and ts < $date and e_total>0 order by ts desc";

			$ds3 = mysql_query($query3, $verbindung) or die(mysql_error());

			$tmp_e = 0;
			while ($row_ds3 = mysql_fetch_array($ds3)) {
				//	echo " ".$row_ds2[sn].": p".$row_ds3[p_ac_ph1]." e_neu: ".$row_ds3[e_total];

				$inst_prod += $row_ds3[p_ac_ph1];

				$tmp_e = $row_ds3[e_total];
				$e_total += $tmp_e;
				break;
			}

			//$query3 = "select max(e_total) as e from ".$row_ds2[igate_id]."_messdaten_s0 where sn='".$row_ds2[sn]."' and ts < $yesterday";
			$query3 = "select max(e_total) as e from " . $row_ds2[igate_id] . "_messdaten_wr where sn='" . $row_ds2[sn] . "' and e_total>0 and ts < $yesterday";

			$ds3 = mysql_query($query3, $verbindung) or die(mysql_error());
			while ($row_ds3 = mysql_fetch_array($ds3)) {
				//	echo " ".$row_ds3[e]."<br>";
				$day_prod += ($tmp_e - $row_ds3[e]);
				break;
			}
		}
	}

	$query3 = "select Input_5 as speed, Input_6 as dir from 2501_sensorik where node_id='101' order by ts desc";
	$ds3 = mysql_query($query3, $verbindung) or die(mysql_error());
	while ($row_ds3 = mysql_fetch_array($ds3)) {
		//	echo " ".$row_ds3[e]."<br>";
		$windSpeed = $row_ds3[speed];
		$windDir = $row_ds3[dir];

		break;
	}

	// Zwei Einstrahlungssensoren und eine  Umgebungstemperatur
	$query3 = "select input_3 as rad, Input_4 as rad2, Input_1 as tmp from 2501_sensorik where node_id='101' order by ts desc";
	$ds3 = mysql_query($query3, $verbindung) or die(mysql_error());
	while ($row_ds3 = mysql_fetch_array($ds3)) {
		//		echo " ".$row_ds3[e]."<br>";
		$rad1 = $row_ds3[rad];
		$rad2 = $row_ds3[rad2];
		$tmp = $row_ds3[tmp];

		break;
	}

}
?>

<HTML>
	<HEAD>
		<TITLE>-- Solar park India --</TITLE>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="css/scroll.css" rel="stylesheet" type="text/css">
		<link href="css/text.css" rel="stylesheet" type="text/css">
		<style type="text/css">
			html, body, form {
				margin: 0;
				padding: 0;
				height: 100%;
				width: 100%;
				font-family: arial, sans-serif;
			}
			.grau {
				width: 180;
				background-color: #DFDFE2;
				text-align: center;
				valign: middle;
				font-family: Verdana;
				font-size: 16pt;
				font-weight: bold;
				border: 2px solid darkgrey;
			}
			.name-td {
				width: 380;
				valign: middle;
				font-family: Verdana;
				font-size: 16pt;
				font-weight: bold;
			}
		</style>
	</head>
	<body
	style="background: #F7EE35; position: absolute; left: 100px; top: 100px">
		<table class="ScadaOverview" border="0" cellpadding="0" cellspacing="30">
			<tbody>
				<tr>
					<td class="name-td">Actual produced Power</td>
					<td class="grau" valign="middle"><?php echo $inst_prod;?></td>
					<td class="name-td">kW</td>
				</tr>
				<tr>
					<td class="name-td">Energy acc. for the day</td>
					<td class="grau"><?php echo $day_prod;?></td>
					<td class="name-td">kWh</td>
				</tr>
				<tr>
					<td class="name-td">Total produced Energy</td>
					<td class="grau"><?php echo $e_total;?></td>
					<td class="name-td">kWh</td>
				</tr>
				<tr>
					<td class="name-td">Total saved CO<sub>2</sub></td>
					<td class="grau"><?php echo round($e_total * 0.55, 0);?></td>
					<td class="name-td">kg</td>
				</tr>
				
				<?php 
				
				if ($park_no=="20"){
				echo '<tr>';
				echo'<td class="name-td">Actual Irradiation</td>';
				echo'<td class="grau">'.round($rad).'</td>';
				echo'<td class="name-td">W/sq.m</td>';
				echo				'</tr>';
				echo '<tr>';
				echo					'<td class="name-td">Ambient Temperature</td>';
				echo					'<td class="grau">'.round($tmp1).'</td>';
				echo					'<td class="name-td">&deg;C</td>';
				echo				'</tr>';
				echo				'<tr>';
				echo					'<td class="name-td">Module Surface Temperature</td>';
				echo					'<td class="grau">'.round($tmp2).'</td>';
				echo					'<td class="name-td">&deg;C</td>';
				echo				'</tr>';
				}else if ($park_no=="25"){
				echo '<tr>';
				echo'<td class="name-td">Irradiation 1</td>';
				echo'<td class="grau">'.round($rad1).'</td>';
				echo'<td class="name-td">W/sq.m</td>';
				echo				'</tr>';
				echo '<tr>';
				echo					'<td class="name-td">Irradiation 2</td>';
				echo					'<td class="grau">'.round($rad2).'</td>';
				echo					'<td class="name-td">W/sq.m</td>';
				echo				'</tr>';
				echo				'<tr>';
				echo					'<td class="name-td">Temperature</td>';
				echo					'<td class="grau">'.round($tmp).'</td>';
				echo					'<td class="name-td">&deg;C</td>';
				echo				'</tr>';
					
				}
				?>
				
				<tr>
					<td class="name-td">Actual Wind speed</td>
					<td class="grau"><?php echo round($windSpeed);?></td>
					<td class="name-td">m/h</td>
				</tr>
				<tr>
					<td class="name-td">Actual Wind Direction</td>
					<td class="grau"><?php echo round($windDir);?></td>
					<td class="name-td">&deg;deg</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>
