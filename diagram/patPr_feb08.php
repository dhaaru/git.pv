<?php

$park = "RREC";
$resolution = 300;

if ($park_no == 20) {
    $resolution = 900;
    $park = "Charanka";
} else if ($park_no == 10) {
    $resolution = 300;
    $park = "Anjar";
}
if ($print) {
    header('Content-Disposition: attachment; filename="' . $park . '-PR-' . $jahr . '-' . $mon . '-' . $tag . '.xls"');
}
require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);


date_default_timezone_set('UTC');
$stamp = mktime(1, 0, 0, $mon, $tag, $jahr);
$endstamp = mktime(14, 0, 0, $mon, $tag, $jahr);

set_time_limit(600);

$ln = "<br>";
if ($print) {

    echo "Timestamp\tMeter Energy total\tAverage Irradiation\r\n";
    echo "DD/MM/YYYY HH:MM\tkWh\t15 Min avg\r\n";
}
$installed = 1004.64;

$time = 0;
$irr = 0;
$deltaE = 0;

$meter = array();
$weather = array();

$curr = 0;

if ($park_no == 10) {
    $installed = 5000;
    //732,U1
    $query = "select max(value) as max from _devicedatavalue where `field`='EA-' and device = 670 where ts < $stamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $curr = $row_ds1[max];
    }


    $query = "select (ts+19800) as t, value*1000 as value from _devicedatavalue where value  > 0 and `field`='EA-' and device=670 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if (is_null($meter[floor($row_ds1[t] / $resolution)])) {
            $meter[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
        }
    }

    $query = "select (ts+19800) as t, value * 13 as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 732 and field = 'U1'";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
            $weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
        }
    }
}else if ($park_no == 25) {
    $query = "select max(e_total) as max from 2541_messdaten_s0 where ts < $stamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $curr = $row_ds1[max];
    }


    $query = "select (ts+19800) as t, e_total from 2541_messdaten_s0 where e_total > 0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if (is_null($meter[floor($row_ds1[t] / $resolution)])) {
            $meter[floor($row_ds1[t] / $resolution)] = $row_ds1[e_total];
        }
    }

    $query = "select (ts+19800) as t, value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 108 and field = 'SR2'";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
            $weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
        }
    }
} else {
    $installed = 20000;

    $query = "select max(e_total) as max from 2061_messdaten_s0 where sn = 'Premier_01' ts < $stamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $curr = $row_ds1[max];
    }

    $query = "select max(e_total) as max from 2061_messdaten_s0 where sn = 'Premier2_01' ts < $stamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $curr += $row_ds1[max];
    }


    $query = "select (ts+19800) as t, e_total from 2061_messdaten_s0 where sn='Premier_01' and e_total > 0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = $row_ds1[e_total];
    }

    $query = "select (ts+19800) as t, e_total from 2061_messdaten_s0 where sn='Premier2_01' and e_total > 0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][1] = $row_ds1[e_total];
    }


    $query = "select (ts+19800) as t, ((value-19.0476)*21) as value  from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 180 and field = 'U1_900'";
    if ($showQueries == 1) {
        if ($print) {

            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
        //echo floor($row_ds1[t] / 900)."=".$row_ds1[input_3]."<br>";
    }
}


$break = true;
while ($stamp < $endstamp) {
    $energy = 0;
    $weatherval = 0;
    if (sizeof($meter[floor(($stamp + 19800) / $resolution)]) > 0) {
        if (is_array($meter[floor(($stamp + 19800) / $resolution)])) {
            $energy = round(array_sum($meter[floor(($stamp + 19800) / $resolution)]));
        } else {
            $energy = round($meter[floor(($stamp + 19800) / $resolution)]);
        }
    }
    if (sizeof($weather[floor(($stamp + 19800) / $resolution)]) > 0) {
        $weatherval = round($weather[floor(($stamp + 19800) / $resolution)], 2);
    } else {
        
    }

    if ($energy == 0) {
        $energy = $curr;
    }
    if ($print) {

        echo date('d/m/Y H:i', $stamp + 19800) . "\t" . $energy . "\t" . $weatherval;
    }
    if ($curr < $energy && $weatherval >= 250) {
        if ($break) {
            $break = false;
            if ($print) {
                echo "\t+";
            }
        } else {
            $time++;
            $irr+=$weatherval;
            $deltaE+= $energy - $curr;
            if ($print) {
                echo "\t*";
            }
        }
    } else {
        $break = true;
    }
    $curr = max($energy, $curr);
    $stamp+=$resolution;
    if ($print) {

        echo "\r\n";
    }
}
$reference = 1000;
if ($print) {
    echo "\r\n\r\nKWh \t" . $deltaE . "\r\n";
    echo "Yf=\t" . $deltaE / $installed . "\r\n";
    echo "avg irr=\t" . round($irr / $time, 2);
    echo "\r\nhours= \t" . ($time / (3600 / $resolution));
    echo "\r\navg insolation= \t" . ($irr / (3600 / $resolution) / $reference);
    echo "\r\npr= \t" . round((100 * (($deltaE / $installed) / ($irr / (3600 / $resolution) / $reference))), 2) . "%";
}

$pr_value = round((100 * (($deltaE / $installed) / ($irr / (3600 / $resolution) / $reference))), 2);
?>