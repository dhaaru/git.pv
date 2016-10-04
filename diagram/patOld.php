<?php

require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

date_default_timezone_set('UTC');
$stamp = mktime(18, 30, 0, $mon, $tag - 1, $jahr, 0);

    set_time_limit(600);

if ($portal == "20") {

	$a1 = array();
	$a7 = array();
	$a61a = array();
	$a61b = array();
    
    $wrs = array();
    
	$tstamps = array();

	$query = "SELECT a1.ts as ts, a1.input_7 AS 1_module_surface, a1.input_2 AS 1_sensor_temp, a1.input_1 AS 1_irradiation FROM 2001_sensorik as a1 WHERE a1.ts > " . $stamp . " ORDER BY a1.ts";
	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {

		$a1['' . round($row_ds1['ts'] / 900)] =  ", " . $row_ds1['1_irradiation'] . ", " . $row_ds1['1_module_surface'] . ", " . $row_ds1['1_sensor_temp'];
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

    $wrIndex = 0;
	$query = "select distinct sn from 2031_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2031_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2041_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2041_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2032_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2032_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2042_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2042_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2033_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2033_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2043_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2043_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {

            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2034_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2034_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {

            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2044_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2044_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2035_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2035_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] /900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2045_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2045_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2036_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2036_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2046_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2046_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
        $query = "select distinct sn from 2037_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2037_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2047_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2047_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2038_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2038_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2048_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2048_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2039_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2039_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2049_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2049_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2040_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2040_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2050_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2050_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $index = 0;
    $blocks = "";
    $types = "";
    $units = "";
    while ($index < $wrIndex){
        $blocks .= ", inverter".$index;
        $types .= ", inverter".$index;
        $units.=", kwh";
        $index++;
    }
    
    sort($tstamps);
    
    
	echo "Kiran 20MWp, Block A1, Block A1, Block A1, Block A7, Block A7, Block A7, Weather station, Weather station, Weather station, Weather station, Weather station, Plant Trafo1, Plant Trafo2".$blocks."<br>";
	echo "Timestamp, Irradiation, Module Surface, Sensor Temperature, Irradiation, Module Surface, Sensor Temperature, Irradiation, Module Surface, Ambient Temperature, Wind speed, Wind direction, Energy Total, Energy Total".$types."<br>";
	echo "DD/MM/YYYY HH:MM, W/sq.m, °C, °C, W/sq.m, °C, °C, W/sq.m, °C, °C, m/s, degree, kwh, kwh".$units."<br>";

	foreach ($tstamps as $stamp) {
	    
        echo "".date('d-m-Y H:i', $stamp*900 + 19800);
        
        if (array_key_exists($stamp, $a1)) {
            echo $a1[$stamp];
        } else {
            echo ", , , ";
        }
        
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
        echo ", , ";
        
        $wrIndex = 0;
        foreach ($wrs as $wr){
            if (array_key_exists($stamp, $wr[1])&& $wr[1][$stamp]!='') {
                echo ", ".$wr[1][$stamp];
                $lastWrValues[$wrIndex]=", ".$wr[1][$stamp];
            } else {
                echo $lastWrValues[$wrIndex];
            }
            $wrIndex++;
        }
        
        
		echo "<br>";
	}
} else if ($portal == "25") {

	$a1 = array();
	$energy = array();
    $wrs = array();

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
		
		if (!in_array('' . round($row_ds1['ts'] / 300), $tstamps)){
			$tstamps[]='' . round($row_ds1['ts'] / 300);
		}
		$energy['' . round($row_ds1['ts'] / 300)] = "," . $row_ds1['e_total'];
	}
    
    $query = "select distinct sn from 2531_messdaten_wr where ts > " . $stamp;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    $wrIndex = 0;
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total, u_ac_ph1, u_ac_ph2, u_ac_ph3, i_ac_ph1, i_ac_ph2, i_ac_ph3 from 2531_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 300), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 300);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 300)] = $row_ds2[e_total].", ".$row_ds2[u_ac_ph1].", ".$row_ds2[u_ac_ph2].", ".$row_ds2[u_ac_ph3].", ".$row_ds2[i_ac_ph1].", ".$row_ds2[i_ac_ph2].", ".$row_ds2[i_ac_ph3];
        }
        $wrIndex++;
    }

	echo "Timestamp, Temperature, Irradiation 1, Irradiation 2, Wind speed, Wind direction, Meter Energy total, wr1 Energy Total, UAC Ph1 wr1, UAC Ph2 wr1, UAC Ph3 wr1, IAC Ph1 wr1, IAC Ph2 wr1, IAC Ph3 wr1, wr2 Energy Total, UAC Ph1 wr2, UAC Ph2 wr2, UAC Ph3 wr2, IAC Ph1 wr2, IAC Ph2 wr2, IAC Ph3 wr2, wr3 Energy Total, UAC Ph1 wr3, UAC Ph2 wr3, UAC Ph3 wr3, IAC Ph1 wr3, IAC Ph2 wr3, IAC Ph3 wr3, wr4 Energy Total, UAC Ph1 wr4, UAC Ph2 wr4, UAC Ph3 wr4, IAC Ph1 wr4, IAC Ph2 wr4, IAC Ph3 wr4<br>";
	echo "DD/MM/YYYY HH:MM, °C, W/sq.m, W/sq.m, m/s, degree, kwh, V, V, V, A, A, A, kwh, V, V, V, A, A, A, kwh, V, V, V, A, A, A, kwh, V, V, V, A, A, A, kwh, V, V, V, A, A, A<br>";

	sort($tstamps);
	
    
    $lastWrValues=array();
    $lastWrValues[0]=-1;
    $lastWrValues[1]=-1;
    $lastWrValues[2]=-1;
    $lastWrValues[3]=-1;
	foreach ($tstamps as $stamp) {
		echo date('d-m-Y H:i', ($stamp*300+19800)).", ";
			
		if (array_key_exists($stamp, $a1)) {
			echo $a1[$stamp];
		} else {
			echo ", , , , ";
		}	
			
		if (array_key_exists($stamp, $energy)) {
			echo $energy[$stamp];
		} else {
			echo ", ";
		}
        
        $wrIndex = 0;
        foreach ($wrs as $wr){
            if (array_key_exists($stamp, $wr[1])&& $wr[1][$stamp]!='') {
                echo ",".$wr[1][$stamp];
                $lastWrValues[$wrIndex]=",".$wr[1][$stamp];
            } else {
                echo $lastWrValues[$wrIndex];
            }
            $wrIndex++;
        }

		echo "<br>";
	}
}
?>