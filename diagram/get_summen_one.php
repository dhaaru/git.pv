<?php

include('../connections/verbindung.php');
include('harv_functions/es_sum_functions.php');
include('../functions/datum_formate.php');
include('../functions/dgr_func_jpgraph.php');
include('../betreiber/betr_functions/betr_functions.php');

mysql_select_db($database_verbindung, $verbindung);
//////////////////////



$query_ds1 = "SELECT igate_id FROM igates where id=$id_extern and status='1' ORDER BY igate_id ASC";
//echo $query_ds1;
$ds1 = mysql_query($query_ds1, $verbindung) or die(mysql_error());
$sum1 = mysql_num_rows($ds1);

//echo $sum1;

//Summen f�r alle Anlagen mit Status = 1

while($row_ds1 = mysql_fetch_assoc($ds1)){//Anlage


	$igate_id=$row_ds1['igate_id'];
	//$max_leistung=$row_ds1['max_leistung'];

	$messdaten_table=$igate_id."_sensorik";

	//echo $messdaten_table.":<br>";
	//echo "Anlage ".$igate_id."<br>";

	######### ES-Tagesdurchschnitt-Tabelle f�r jede Anlage Wenn nicht Vorhanden anlegen

	$t_estag=$igate_id."_estag"; //Tagesummen ES Tabellenname

	$q_r="CREATE TABLE `".$t_estag."` (
`datum` date NOT NULL DEFAULT '0000-00-00',
  `tstamp` bigint(255) NOT NULL DEFAULT '0',
  `node_id` int(11) NOT NULL DEFAULT '0',
  `input_1` float DEFAULT NULL,
  `input_2` float DEFAULT NULL,
  `input_3` float DEFAULT NULL,
  `input_4` float DEFAULT NULL,
  `input_5` float DEFAULT NULL,
  `input_6` float DEFAULT NULL,
  `input_7` float DEFAULT NULL,
  `input_8` float DEFAULT NULL,
  PRIMARY KEY (`datum`),
  KEY `TS` (`tstamp`)
)";



	if(!table_exists($t_estag)){
		cr_table($q_r);
	}

	################# Tagessummen pro Monat ausrechnen ##############
	$tag=date('j');
	$monat=date('m');
	$jahr=date('Y');


	//Stamp  0 Uhr des aktuellen Tages
	$stamp_0=mktime(0,0,0,$monat,$tag,$jahr);

	for($i=1; $i<=$tage_extern; $i++){  //Anzahl Tage, die zur�ckgerechnet wird

		$tag=date('j', $stamp_0);
		$monat=date('m', $stamp_0);
		$jahr=date('Y', $stamp_0);

		$datum=date("Y-m-d", $stamp_0);

		$query_ds2 = "SELECT distinct node_id FROM io_modules where igate_id=$igate_id and status=1 ORDER BY node_id ASC";
		//echo $query_ds2;
		$ds2 = mysql_query($query_ds2, $verbindung) or die(mysql_error());

		while($row_ds2 = mysql_fetch_assoc($ds2)){ //IO-Module

			$node_id=$row_ds2['node_id'];

			$query_del="DELETE FROM $t_estag WHERE datum='$datum' and node_id='$node_id'";
			mysql_query($query_del, $verbindung);

			//// Wieder einf�gen
			$query_ins="INSERT INTO $t_estag SET datum='$datum', tstamp='$stamp_0', node_id='$node_id'";
			mysql_query($query_ins, $verbindung);
			echo $query_ins."<BR>";
			$query_ds3 = "SELECT * FROM io_modules where igate_id=$igate_id and node_id=$node_id and status=1  ORDER BY ip_id ASC";
			//echo $query_ds2;
			$ds3 = mysql_query($query_ds3, $verbindung) or die(mysql_error());

			while($row_ds3 = mysql_fetch_assoc($ds3)){  //alle Eing�nge mit status 1

				$ip_pos=(int)$row_ds3['ip_id'];
				
                                //if ($igate_id==2061){
                                //    if ($ip_pos>4){
                                //        $ip_pos -= 4;
                                //        }else {
                                //            $ip_pos += 4;
                                            
                                //            }
                                    
                                //    }
				$sensor_art=$row_ds3['sensor_art'];

				$spalte="input_".$ip_pos;

				$arr_stdschnitt= sensorik_stundenwerte_tabelle($tag, $monat, $jahr,$igate_id,$node_id, $ip_pos, $sensor_art);
			//	echo "Tageswert: ".$arr_stdschnitt."<br>";
				//exit();
				if(array_sum($arr_stdschnitt) > 0){

					if($sensor_art=='Radiation'){
						$tages_wert=array_sum($arr_stdschnitt);
					}else{ // Temp. und Feuchte
						$tages_wert=array_sum($arr_stdschnitt)/sizeof($arr_stdschnitt);
					}
					//echo "Tageswert: ".$tages_wert."<br>";
				}else{
					$tages_wert=0;
				}

				$tages_wert=round($tages_wert,2);

				//
				$query_upd="update $t_estag SET $spalte='$tages_wert' where node_id='$node_id' and tstamp='$stamp_0'";
				echo $query_upd."<br>";
				mysql_query($query_upd, $verbindung);
			} //for eing�nge
		}
		$stamp_0=$stamp_0-86400;  // 4x einen Tag zur�ck rechnen, falls da noch nicht alle Dateien ber�cksichtigt wurden!
	} //for Tage
} //iGATE


mysql_free_result($ds1);

//}//for each owner id


?>