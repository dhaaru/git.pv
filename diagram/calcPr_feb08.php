<?php

require_once('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
set_time_limit(900);
if (!isset($jahr)) {
    $jahr = date("Y");
}
if (!isset($mon)) {
    $mon = date("n");
}
if (!isset($tag)) {
    $tag = date("j")-10;
}

$tg_mon = 0;
if ($jahr % 400 == 0) {
    $tg_mon = array(1 => 31, 2 => 29, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
} elseif ($jahr % 4 == 0 && $jahr % 100 != 0) {
    $tg_mon = array(1 => 31, 2 => 29, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
} else {
    $tg_mon = array(1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
}

while ($jahr < 2014) {
    while ($mon < 13) {
        while ($tag <= $tg_mon[$mon]) {

            //$park_no=10;
            //include ('patPr.php');
            $stamp = mktime(1, 0, 0, $mon, $tag, $jahr);
            //mktime
            echo "$jahr-$mon-$tag<br>";

            $park_no = 10; 
           include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'anjar-pr', $pr_value)";
            echo $query."<br>";
            //return;
            mysql_query($query, $verbindung) or die(mysql_error());

            
            $park_no = 25;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'phagi-pr', $pr_value)";
            echo $query."<br>";
            //return;
            mysql_query($query, $verbindung) or die(mysql_error());

            $park_no = 20;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'charanka-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());

            if ($jahr == date('Y') && $mon == date('n') && $tag == date('j')) {
                return;
            }
            
            $tag++;
        }
        $tag = 1;
        //return;
        $mon++;
    }
    $mon = 1;

    $jahr++;
}
mysql_query($query, $verbindung) or die(mysql_error());
?>
