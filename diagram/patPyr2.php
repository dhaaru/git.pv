<?php


require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

date_default_timezone_set('UTC');



$stamp = mktime(18, 30, 0, $mon, $tag - 1, $jahr, 0);

set_time_limit(600);
$report = $report;

$query = "select r.name as repname, r.resolution as res, e.element as el, de.type as type, de.name as name, de.source as source, de.unit as unit, des.device as device, des.field as field  from _report as r, _reportelement as e, _displayelement as de, _displayelementsource as des where r.id = $report and e.report = $report and e.element = de.id and e.element = des.element order by de.order, de.id";
if ($showQueries == 1) {
    echo $query . "$ln";
}

$reportName = "";
//$reportResolution = 5;

$elements = array();

$ids = array();
$sources = "";
$names = "Timestamp";
$units = "YYYY-MM-DD HH:mm";

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds1 = mysql_fetch_array($ds1)) {
    $reportName = $row_ds1[repname];
    $reportResolution = $row_ds1[res];
    $elements[$row_ds1[el]][type] = $row_ds1[type];

    $sources.= "\t".$row_ds1[source];
    $names.= "\t".$row_ds1[name];
    $units.= "\t".$row_ds1[unit];

    $elements[$row_ds1[el]][id] = "$row_ds1[device].$row_ds1[field]";
    $ids[] = "$row_ds1[device].$row_ds1[field]";

    $elements[$row_ds1[el]][devices][] = $row_ds1[device];
    $elements[$row_ds1[el]][fields][] = $row_ds1[field];
}

$ln="<br>";
if ($exportExcel==1){
    header('Content-type: text/csv');

    header('Content-Disposition: attachment; filename="'.$reportName.'-'.$jahr.'-'.$mon.'-'.$tag.'.xls"');
    $ln="\r\n";
}


$sources.=$ln;
$names.=$ln;
$units.=$ln;


$values = array();

$reportResolution*=60;


$maxTs = 0;
foreach ($elements as $element) {
    $index = 0;
    $elementString = "";
    $anyElements = 0;
    $moreThanOne = 0;

    while ($index < sizeof($element[devices])) {
        if ($anyElements > 0) {
            $elementString.=" or ";
            $moreThanOne = 1;
        }
        $anyElements = 1;
        $elementString.="(device = " . $element[devices][$index] . " and field = '" . $element[fields][$index] . "' )";
        $index++;
    }

    $query = "select ts+$offset as ts, value from _devicedatavalue where ts>$stamp and ( $elementString )";
    if ($showQueries == 1) {
        echo $query . "$ln";
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        
        $currentTs=$row_ds1[ts];
        $element[values][$currentTs][] = $row_ds1[value];
        $maxTs=max((int)floor($currentTs/$reportResolution), $maxTs);
    }

    if ($element[type]==0) {
        foreach ($element[values] as $ts => $eltvalues) {
            $values[(int)floor($ts/$reportResolution)][$element[id]][] = array_sum($eltvalues);
        }
    } else {
        foreach ($element[values] as $ts => $eltvalues) {
            $values[(int)floor($ts/$reportResolution)][$element[id]][] = array_sum($eltvalues) / sizeof($eltvalues);
        }
    }
}

echo $reportName;

echo $sources;
echo $names;
echo $units;

//$values = array();
//ksort($values);

$loc = localeconv();
$decimalPoint = $loc[decimal_point];


$ts = (int) floor(($stamp+$offset)/$reportResolution);

$prevs=array();

while ($ts<=$maxTs){
//foreach ($values as $ts => $tsvalues) {
    $tsvalues = array();
    if (key_exists($ts, $values)){
        $tsvalues=$values[$ts];
    }
    
    $stamp =  $ts * $reportResolution;

    if ($useTs>0){
       echo $stamp; 
    }else{
       echo  date('d-m-Y H:i', $stamp);
    }

    foreach ($ids as $id) {
        if ($tsvalues[$id] == null) {
            if ($showNulls==0){
                echo $prevs[$id];
            }else {
                echo "\tnull";
            }
            
        } else {
            $val = "\t" . number_format(array_sum($tsvalues[$id]) / sizeof($tsvalues[$id]), 2, $decimalPoint, "");
            echo $val;
            $prevs[$id]=$val;
        }
        
        
    }
    
    $ts++;

    echo "$ln";
}
?>