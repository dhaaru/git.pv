<?php

header('Content-type: text/csv');

if ($phase == "tag") {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '-' . $mon . '-' . $tag . '.xls"');
} else if ($phase == "mon") {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '-' . $mon . '.xls"');
} else {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '.xls"');
}


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


if (!isset($phase) || $phase == "") {
    if (!isset($_SESSION['phase_s'])) {
        $phase = "tag";
    } else {
        $phase = $_SESSION['phase_s'];
    }
}
$_SESSION['phase_s'] = $phase;


if (!isset($jahr) || $jahr == "") {
    if (!isset($_SESSION['jahr_s'])) {
        $jahr = $jahr_heute;
    } else {
        $jahr = $_SESSION['jahr_s'];
    }
}
$_SESSION['jahr_s'] = $jahr;

if (!isset($mon) || $mon == "") {
    if (!isset($_SESSION['mon_s'])) {
        $mon = $monat_heute;
    } else {
        $mon = $_SESSION['mon_s'];
    }
}
$_SESSION['mon_s'] = $mon;

if (!isset($tag) || $tag == "") {
    if (!isset($_SESSION['tag_s'])) {
        $tag = $tag_heute;
    } else {
        $tag = $_SESSION['tag_s'];
    }
}
$_SESSION['tag_s'] = $tag;

$anz_tage = $_SESSION['anz_tage_s'];

$diagram = $diagram;

date_default_timezone_set('UTC');

$stamp = 0;
$endstamp = 0;


if ($phase == "tag") {
    $stamp = mktime(18, 30, 0, $mon, $tag - 1, $jahr, 0);
    $endstamp = mktime(19, 30, 0, $mon, $tag, $jahr, 0);
} else if ($phase == "mon") {
    $stamp = mktime(18, 30, 0, $mon, -1, $jahr, 0);
    $endstamp = mktime(19, 30, 0, $mon + 1, 0, $jahr, 0);
} else {
    $stamp = mktime(18, 30, 0, 0, -1, $jahr, 0);
    $endstamp = mktime(19, 30, 0, 0, 0, $jahr + 1, 0);
}

if (is_null($args)) {
    return;
}

set_time_limit(600);

$header = "Timestamp";
$header2 = "\r\nDD/MM/YY HH:mm";

$offset = 19800;

$args = split(";", $args);
foreach ($args as $arg) {

    $words = split(',', $arg);
    if (sizeof($words) != 9) {
        continue;
    }

    $translatedField = $words[4];
    $translatedField = str_replace("PLUS", "+", $translatedField);
    $query = "select ts+$offset as ts, (((value+$words[0])*$words[1])+$words[2]) as value from _devicedatavalue where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
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
        if (is_null($data[floor($row_ds1[ts] / 900)][$words[5]])) {
            $data[floor($row_ds1[ts] / 900)][$words[5]] = $row_ds1[value];
        }
    }
}

echo $header;
echo $header2;

ksort($data);

foreach ($data as $key => $values) {
    if ($useTs == 0) {
        echo "\r\n" . date('d-m-Y H:i', ($key * 900));
    } else {
        echo "\r\n" . ($key * (900));
    }

    foreach ($values as $value) {
        echo "\t" . $value;
    }
}


return;
?>