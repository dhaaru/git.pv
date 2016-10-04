<?php

set_time_limit(600);
header('Content-Disposition: attachment; filename="Export-' . $jahr . '-' . $mon . '-' . $tag . '.xls"');
require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

date_default_timezone_set('UTC');

$stamp = 0;
$endstamp = 0;
if ($phase=="tag"){
$stamp = mktime(18, 30, 0, $mon, $tag - 1, $jahr, 0);
$endstamp = mktime(18, 30, 0, $mon, $tag, $jahr, 0);
}else if ($phase == "mon"){
$stamp = mktime(18, 30, 0, $mon,0, $jahr, 0);
$endstamp = mktime(18, 30, 0, $mon+1, 1, $jahr, 0);
    }else {
$stamp = mktime(18, 30, 0, 1,0, $jahr, 0);
$endstamp = mktime(18, 30, 0, 1, 1, $jahr+1, 0);
        
        }

set_time_limit(600);

if ($portal == "20") {

    $a1 = array();
    $a7 = array();
    $a61a = array();
    $a61b = array();

    $wrs = array();

    $tstamps = array();

    $query = "SELECT a1.ts as ts, a1.input_7 AS 1_module_surface, a1.input_2 AS 1_sensor_temp, a1.input_1 AS 1_irradiation FROM 2001_sensorik as a1 WHERE a1.ts > " . $stamp . " and a1.ts < $endstamp ORDER BY a1.ts";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $a1['' . round($row_ds1['ts'] / 900)] = "\t" . $row_ds1['1_irradiation'] . "\t" . $row_ds1['1_module_surface'] . "\t" . $row_ds1['1_sensor_temp'];
        $tstamps[] = '' . round($row_ds1['ts'] / 900);
    }

    $query = "SELECT a1.ts as ts, a1.input_7 AS 1_module_surface, a1.input_2 AS 1_sensor_temp, a1.input_1 AS 1_irradiation FROM 2007_sensorik as a1 WHERE a1.ts > " . $stamp . " and a1.ts < $endstamp ORDER BY a1.ts";

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $a7[round($row_ds1['ts'] / 900)] = "\t" . $row_ds1['1_irradiation'] . "\t" . $row_ds1['1_module_surface'] . "\t" . $row_ds1['1_sensor_temp'];
    }
    $query = "SELECT a1.ts as ts, a1.input_7 AS 1_module_surface, a1.input_2 AS 1_sensor_temp, a1.input_1 AS 1_irradiation FROM 2061_sensorik as a1 WHERE node_id = 107 and a1.ts > " . $stamp . " and a1.ts < $endstamp ORDER BY a1.ts";

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $a61a['' . round($row_ds1['ts'] / 900)] = "\t" . $row_ds1['1_irradiation'] . "\t" . $row_ds1['1_module_surface'] . "\t" . $row_ds1['1_sensor_temp'];
    }

    $query = "SELECT a1.ts as ts, a1.input_2 AS wind_speed, a1.input_1 AS wind_dir FROM 2061_sensorik as a1 WHERE node_id = 105 and a1.ts > " . $stamp . " and a1.ts < $endstamp ORDER BY a1.ts";

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $a61b['' . round($row_ds1['ts'] / 900)] = "\t" . $row_ds1['wind_speed'] . "\t" . $row_ds1['wind_dir'];
    }
    
    

    $wrIndex = 0;
    
    
    
    $query = "select distinct sn from 2061_messdaten_s0 where ts > " . $stamp . " and ts < $endstamp". " order by sn desc";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2061_messdaten_s0 where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " and ts < $endstamp order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }
    
    $query = "select distinct sn from 2031_messdaten_wr where ts > " . $stamp . " and ts < $endstamp". " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2031_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " and ts < $endstamp order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2041_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2041_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " and ts < $endstamp order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2032_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2032_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts > " . $stamp . " and ts < $endstamp order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2042_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2042_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {

            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2033_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2033_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {

            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2043_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2043_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {

            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2034_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2034_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {

            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2044_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2044_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2035_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2035_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2045_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2045_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2036_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2036_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2046_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2046_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2037_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2037_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2047_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2047_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2038_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2038_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2048_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2048_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2039_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2039_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2049_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2049_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2040_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2040_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 900), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 900);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 900)] = $row_ds2['e_total'];
        }
        $wrIndex++;
    }

    $query = "select distinct sn from 2050_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();

        $query = "select ts, e_total from 2050_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
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
    
    $blockNames = array();
    $blockNames[] = "A1";
    $blockNames[] = "A2";
    $blockNames[] = "A3";
    $blockNames[] = "A4";
    $blockNames[] = "A5";
    $blockNames[] = "A6";
    $blockNames[] = "A7";
    $blockNames[] = "B";
    $blockNames[] = "C1";
    $blockNames[] = "C2";
    
    while ($index < $wrIndex-2) {
        $blocks .= "\tBlock " . ($blockNames[floor($index/7)]);
        $types .= "\tInverter " . (($index)%7+1);
        $units.="\tkwh";
        $index++;
    }

    sort($tstamps);


    echo "Kiran 20MWp\tBlock A1\tBlock A1\tBlock A1\tBlock A7\tBlock A7\tBlock A7\tWeather station\tWeather station\tWeather station\tWeather station\tWeather station\tPlant Trafo1\tPlant Trafo2" . $blocks . "\r\n";
    echo "Timestamp\tIrradiation\tModule Surface\tSensor Temperature\tIrradiation\tModule Surface\tSensor Temperature\tIrradiation\tModule Surface\tAmbient Temperature\tWind speed\tWind direction\tEnergy Total\tEnergy Total" . $types . "\r\n";
    echo "DD/MM/YYYY HH:MM\tW/sq.m\t°C\t°C\tW/sq.m\t°C\t°C\tW/sq.m\t°C\t°C\tm/s\tdegree\tkwh\tkwh" . $units . "\r\n";

    $lastWrValues = array();

        for ($indexWr = 0; $indexWr < 72; $indexWr++) {
            $lastWrValues[$indexWr] = "\t=0";
        }
    
    $lastStamp = 0;
    foreach ($tstamps as $stamp) {
        if ($stamp == $lastStamp){
            continue;
        }
        $lastStamp = $stamp;
        echo "" . date('d-m-Y H:i', $stamp * 900 + 19800);

        if (array_key_exists($stamp, $a1)) {
            echo $a1[$stamp];
        } else {
            echo "\t=0\t=0\t=0";
        }

        if (array_key_exists($stamp, $a7)) {
            echo $a7[$stamp];
        } else {
            echo "\t=0\t=0\t=0";
        }

        if (array_key_exists($stamp, $a61a)) {
            echo $a61a[$stamp];
        } else {
            echo "\t=0\t=0\t=0";
        }

        if (array_key_exists($stamp, $a61b)) {
            echo $a61b[$stamp];
        } else {
            echo "\t=0\t=0";
        }
        

        $wrIndex = 0;
        foreach ($wrs as $wr) {
            if (array_key_exists($stamp, $wr[1]) && $wr[1][$stamp] != '' && $wr[1][$stamp] != 0) {
                echo "\t=" . $wr[1][$stamp];
                $lastWrValues[$wrIndex] = "\t=" . $wr[1][$stamp];
            } else {
                echo $lastWrValues[$wrIndex];
            }
            $wrIndex++;
        }


        echo "\r\n";
    }
} else if ($portal == "25") {

    $a1 = array();
    $energy = array();
    $wrs = array();

    $tstamps = array();

    $query = "SELECT a1.ts as ts, a1.input_1 AS temp, a1.input_3 AS irr1, a1.input_4 AS irr2, a1.input_5 as ws, a1.input_6 as wd FROM 2501_sensorik as a1 WHERE a1.ts < $endstamp and a1.ts > " . $stamp . " ORDER BY a1.ts";

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $a1['' . round($row_ds1['ts'] / 300)] = "=" . $row_ds1['temp'] . "\t=" . $row_ds1['irr1'] . "\t=" . $row_ds1['irr2'] . "\t=" . $row_ds1['ws'] . "\t=" . $row_ds1['wd'];
        $tstamps[] = '' . round($row_ds1['ts'] / 300);
    }

    $query = "select ts, e_total from 2541_messdaten_s0 where ts < $endstamp and ts > " . $stamp . " order by ts";

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        if (!in_array('' . round($row_ds1['ts'] / 300), $tstamps)) {
            $tstamps[] = '' . round($row_ds1['ts'] / 300);
        }
        $energy['' . round($row_ds1['ts'] / 300)] = "\t=" . $row_ds1['e_total'];
    }

    $query = "select distinct sn from 2531_messdaten_wr where ts < $endstamp and ts > " . $stamp. " order by sn";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    $wrIndex = 0;
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $wrs[$wrIndex] = array();
        $wrs[$wrIndex][0] = $row_ds1['sn'];
        $wrs[$wrIndex][1] = array();


        $query = "select ts, value from _devicedatavalue where field = 'UDC1' and device = " . (109 + $wrIndex) . "  and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 300), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 300);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 300)] = "\t=" . $row_ds2[value];
        }

        $query = "select ts, value from _devicedatavalue where field = 'IDC1' and device = " . (109 + $wrIndex) . "  and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 300), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 300);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 300)] .= "\t=" . $row_ds2[value];
        }


        $query = "select ts, e_total, u_ac_ph1, u_ac_ph2, u_ac_ph3, i_ac_ph1, i_ac_ph2, i_ac_ph3 from 2531_messdaten_wr where sn = '" . $row_ds1['sn'] . "' and ts < $endstamp and ts > " . $stamp . " order by ts";
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds2 = mysql_fetch_array($ds2)) {
            if (!in_array('' . round($row_ds2['ts'] / 300), $tstamps)) {
                $tstamps[] = '' . round($row_ds2['ts'] / 300);
            }
            $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 300)] = "" . $row_ds2[e_total] . $wrs[$wrIndex][1]['' . round($row_ds2['ts'] / 300)] . "\t=" . $row_ds2[u_ac_ph1] . "\t=" . $row_ds2[u_ac_ph2] . "\t=" . $row_ds2[u_ac_ph3] . "\t=" . $row_ds2[i_ac_ph1] . "\t=" . $row_ds2[i_ac_ph2] . "\t=" . $row_ds2[i_ac_ph3];
        }


        $wrIndex++;
    }



    echo "Timestamp\t Temperature\t Irradiation 1\t Irradiation 2\t Wind speed\t Wind direction\t Meter Energy total\t wr1 Energy Total\t UDC wr1\t IDC wr1\t UAC Ph1 wr1\t UAC Ph2 wr1\t UAC Ph3 wr1\t IAC Ph1 wr1\t IAC Ph2 wr1\t IAC Ph3 wr1\t wr2 Energy Total\t UDC wr2\t IDC wr2\t UAC Ph1 wr2\t UAC Ph2 wr2\t UAC Ph3 wr2\t IAC Ph1 wr2\t IAC Ph2 wr2\t IAC Ph3 wr2\t wr3 Energy Total\t UDC wr3\t IDC wr3\t UAC Ph1 wr3\t UAC Ph2 wr3\t UAC Ph3 wr3\t IAC Ph1 wr3\t IAC Ph2 wr3\t IAC Ph3 wr3\t wr4 Energy Total\t UDC wr4\t IDC wr4\t UAC Ph1 wr4\t UAC Ph2 wr4\t UAC Ph3 wr4\t IAC Ph1 wr4\t IAC Ph2 wr4\t IAC Ph3 wr4\r\n";
    echo "DD/MM/YYYY HH:MM\t °C\t W/sq.m\t W/sq.m\t m/s\t degree\t kwh\t kwh\t V\t A\t V\t V\t V\t A\t A\t A\t kwh\t V\t A\t V\t V\t V\t A\t A\t A\t kwh\t V\t A\t V\t V\t V\t A\t A\t A\t kwh\t V\t A\t V\t V\t V\t A\t A\t A\r\n";

    sort($tstamps);


    $lastWrValues = array();

    for ($indexWr = 0; $indexWr < 4; $indexWr++) {
        $lastWrValues[$indexWr] = "\t=0\t=0\t=0\t=0\t=0\t=0\t=0\t=0\t=0";
    }

    foreach ($tstamps as $stamp) {
        echo date('d-m-Y H:i', ($stamp * 300 + 19800)) . "\t";

        if (array_key_exists($stamp, $a1)) {
            echo $a1[$stamp];
        } else {
            echo "=0 \t=0 \t=0 \t=0 \t=0 ";
        }

        if (array_key_exists($stamp, $energy)) {
            echo $energy[$stamp];
        } else {
            echo "\t=0 ";
        }

        $wrIndex = 0;
        foreach ($wrs as $wr) {
            if (array_key_exists($stamp, $wr[1]) && $wr[1][$stamp] != '') {
                echo "\t=" . $wr[1][$stamp];
                $lastWrValues[$wrIndex] = "\t=" . $wr[1][$stamp];
            } else {
                echo $lastWrValues[$wrIndex];
            }
            $wrIndex++;
        }

        echo "\r\n";
    }
}
?>