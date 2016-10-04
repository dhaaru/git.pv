<?php
header('Content-type: text/csv');

// Es wird downloaded.pdf benannt
header('Content-Disposition: attachment; filename="'.$name.'-'.$jahr.'-'.$mon.'-'.$tag.'.xls"');

require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

date_default_timezone_set('UTC');


$stamp = mktime(18, 30, 0, $mon, $tag - 1, $jahr, 0);
//$endstamp = mktime(18, 30, 0, $mon, $tag, $jahr, 0);

set_time_limit(600);

$header = array();
//$header[] = "ts";
$query = "select distinct field from _devicedatavalue where device = $device and ts > $stamp order by field";

if ($showQueries == 1) {

    echo $query . "\r\n";
}

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
$values = array();
while ($row_ds1 = mysql_fetch_array($ds1)) {

    $header[] = $row_ds1[field];
}

$query = "select ts+$offset as ts, field, value from _devicedatavalue where device = $device and ts>$stamp";
if ($showQueries == 1) {

    echo $query . "\r\n";
}
$ds2 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds2 = mysql_fetch_array($ds2)) {
    $ts = $row_ds2[ts];
    
    $val = $row_ds2[value];
    if (is_null($val)){
        $val="null";
    }
    $val= "\t".$val;

        if (array_key_exists($ts, $values)) {
            $values[$ts][$row_ds2[field]] = $val;
        } else {
            if ($useTs == 0) {
                $time = date('d-m-Y H:i', $ts);
                $values[$ts][$row_ds2[field]] = $time.$val;
            } else {
                $values[$ts][$row_ds2[field]] = $ts.$val;
            }
        }
    
}


if ($showQueries == 1) {

    echo "\r\n" . "\r\n" . "\r\n";
}

$first = true;
foreach ($header as $word) {
    if ($first) {
        echo "ts";
        $first = false;
    } 
    echo "\t" . $word;
}

echo "\r\n";
foreach ($values as $value) {
    foreach ($header as $word) {
        if (key_exists($word, $value)){
            echo $value[$word];
        }else {
            echo "\tnull";
        }
    }
    echo "\r\n";
}
?>