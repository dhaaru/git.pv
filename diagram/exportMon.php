<?php

header('Content-type: text/csv');

if ($phase == "tag") {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '-' . $mon . '-' . $tag . '.xls"');
} else if ($phase == "mon") {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '-' . $mon . '.xls"');
} else {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '.xls"');
}

$compress = $compress;


if (!isset($delta)) {
    $delta = 0;
}

require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
include ('../locale/gettext_header.php');
include ('../functions/dgr_func_jpgraph.php');
include ('../functions/allg_functions.php');
include ('../functions/b_breite.php');
include ('exportHelper.php');

$user_typ = get_user_attribut('usertyp');

$jahr_heute = date('Y');
$monat_heute = date('n');
$tag_heute = date('d');


date_default_timezone_set('UTC');


if (is_null($args)) {
    return;
}

set_time_limit(600);

$header = "Timestamp";
$header2 = "\r\nDD/MM/YY HH:mm";

$offset = 19800;

$headerData = array();

$args = split(";", $args);
foreach ($args as $arg) {

    $words = split(',', $arg);
    if (sizeof($words) != 10) {
        continue;
    }
    $headerData[] = $words[5];
    $translatedField = $words[4];
    $translatedField = str_replace("PLUS", "+", $translatedField);

    $query = "select ts+$offset+400 as ts, (((value+$words[0])*$words[1])+$words[2]) as value from _devicedatacompressedvalue3 where value is not null and device = $words[3] and field = '$translatedField' and ts >= $stamp and ts < $endstamp";
    $header.="\t" . $words[5];

    $unit = $words[7];

    $unit = str_replace("DEG", "DEG ", $unit);
    $unit = str_replace("SQUA", "^2", $unit);

    $header2.="\t" . $unit;
    if ($showQueries == 1) {
        echo $query . "\r\n";
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    $values = array();
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        //  echo ($row_ds1[ts])."=".$row_ds1[value]."<br>";

        if (!(isset($data[floor($row_ds1[ts] / 900)][$words[5]][difference]))) {
            //           echo "new ".round($row_ds1[ts] / 900) ."=".($row_ds1[ts] / 900);
            //  echo "<br>------------------------<br>";
            //  echo "new Value for ".(900*round($row_ds1[ts]/900))."<br><br>";
            $data[floor($row_ds1[ts] / 900)][$words[5]][value] = $row_ds1[value];
            $data[floor($row_ds1[ts] / 900)][$words[5]][difference] = abs(($row_ds1[ts] / 900) - floor($row_ds1[ts] / 900));
        } else if ($data[floor($row_ds1[ts] / 900)][$words[5]][difference] > abs(($row_ds1[ts] / 900) - floor($row_ds1[ts] / 900))) {
            //echo "bigger ".($row_ds1[ts] / 900);
            //     echo "closer to ".(900*round($row_ds1[ts]/900))."<br><br>";

            $data[floor($row_ds1[ts] / 900)][$words[5]][value] = $row_ds1[value];
            $data[floor($row_ds1[ts] / 900)][$words[5]][difference] = abs(($row_ds1[ts] / 900) - floor($row_ds1[ts] / 900));

            //echo "<br>";
        } else {
            //         echo "smaller ".($row_ds1[ts] / 900);
            //echo "<br>";
        }
    }
}

echo $header;
echo $header2;

ksort($data);

foreach ($data as $key => $values) {
    if ($useTs == 0) {
        if ($compress) {
            echo "\r\n" . date('d-m-Y', ($key * 900));
        } else {
            echo "\r\n" . date('d-m-Y H:i', ($key * 900));
        }
    } else {
        echo "\r\n" . ($key * (900));
    }

    foreach ($headerData as $currentheader) {

        //echo $currentheader;


        if (!is_null($values[$currentheader])) {
            echo "\t" . $values[$currentheader][value];
        } else {

            echo "\t";
        }
    }
}


return;
?>
