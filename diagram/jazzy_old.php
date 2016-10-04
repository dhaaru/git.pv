<?php
$pic = $pic;
$diffs = split(";", $diffs);
$sums = split(";", $sums);
$avgs = split(";", $avgs);
$currs = split(";", $currs);
$links = split(";", $links);

$displayItems = array();
require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

date_default_timezone_set('UTC');
//nt $hour = date("H") [, int $minute = date("i") [, int $second = date("s") [, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") [, int $is_dst = -1 ]]]]]]] )
$endstamp = mktime(18, 30, 0);
$stamp = $endstamp - 24 * 3600;


foreach ($diffs as $avg) {
    $avgItems = split(",", $avg);
    if (sizeof($avgItems) != 16) {

        continue;
    }
    $currentValue = array();
    $erg = 0;
    $query = "select max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts < $stamp and ts > ($stamp-24*3600)";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $erg -= $row_ds2[value];
    }

    mysql_free_result($ds2);

    $query = "select floor(max(ts)/3600) as ts, max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < ($endstamp)";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $topTs = 0;
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $topTs = date("H:i:s", ($row_ds2[ts] * 3600 + 19800));
        $erg += $row_ds2[value];
    }


    $values = 0;
    $avgValue = 0;

    $currentValue[ts] = $topTs;
    $currentValue[value] = $erg;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];
    $currentValue[x] = $avgItems[14];
    $currentValue[y] = $avgItems[15];


    $displayItems[$avgItems[6]][] = $currentValue;

    mysql_free_result($ds2);
}


foreach ($avgs as $avg) {
    $avgItems = split(",", $avg);
    if (sizeof($avgItems) != 16) {
        continue;
    }
    $currentValue = array();
    $query = "select floor(ts/3600) as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < $endstamp order by ts";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    $topTs = 0;
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $topTs = date("H:i:s", $row_ds2[ts] * 3600 + 19800);
        $currentValue[$row_ds2[ts]][] = $row_ds2[value];
    }

    $values = 0;
    $avgValue = 0;
    foreach ($currentValue as $value) {
        $avgValue+= array_sum($value) / sizeof($value);
        $values++;
    }

    $avgValue/=$values;

    $currentValue[ts] = $topTs;
    $currentValue[value] = $avgValue;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];
    $currentValue[x] = $avgItems[14];
    $currentValue[y] = $avgItems[15];

    $displayItems[$avgItems[6]][] = $currentValue;

    mysql_free_result($ds2);
}
//sums=;0,13,0 ,451,U3, null,6,Total%20Irradiation,yellow,2,W/m²
//&avgs=-22.68,1.15,0 ,451,U4, null,5,Avg.%20Temperature,red,2,°C;&sums=0,13,0,451,U3,null

foreach ($sums as $sum) {
    $avgItems = split(",", $sum);
    if (sizeof($avgItems) != 16) {

        continue;
    }
    $currentValue = array();
    $query = "select floor(ts/3600) as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < $endstamp order by ts";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }

    $topTs = 0;

    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $topTs = $row_ds2[ts];
        $currentValue[$row_ds2[ts]][] = $row_ds2[value];
    }

    $avgValue = 0;
    foreach ($currentValue as $value) {
        $avgValue+= array_sum($value) / sizeof($value);
    }

    mysql_free_result($ds2);

    $currentValue[ts] = date("H:i:s", $topTs * 3600 + 19800);

    $currentValue[value] = $avgValue;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];
    $currentValue[x] = $avgItems[14];
    $currentValue[y] = $avgItems[15];

    $displayItems[$avgItems[6]][] = $currentValue;
}

foreach ($currs as $sum) {
    $avgItems = split(",", $sum);
    if (sizeof($avgItems) != 16) {
        continue;
    }
    $currentValue = array();
    $query = "select ts as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp order by ts desc";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    $row_ds2 = mysql_fetch_array($ds2);



    mysql_free_result($ds2);
    $currentValue[value] = $row_ds2[value];

    $currentValue[text] = $avgItems[7];
    $currentValue[ts] = date("H:i:s", (floor($row_ds2[ts] / 3600) * 3600 + 19800));

    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];
    $currentValue[x] = $avgItems[14];
    $currentValue[y] = $avgItems[15];


    $displayItems[$avgItems[6]][] = $currentValue;
}



ksort($displayItems);
?>

<html>
    <head>
    </head>

    <BODY style="font-family: arial, sans-serif;" bgcolor="#ffffff" leftmargin="20" topmargin="15"
          onLoad="if(parent.navigation.location != '../navigation.php?park_no=<?php echo $park_no; ?>&subpark=<?php echo $subpark_id; ?>&meter_existent=<?php echo $meter_existent; ?>')  parent.navigation.location.href='../navigation.php?park_no=<?php echo $park_no; ?>&subpark=<?php echo $subpark_id; ?>&meter_existent=<?php echo $meter_existent; ?>';">

        <div style ="width: 100%; height: 100%">
            <canvas id="canvas" width="1884 "height="998" style="border:1px solid #c3c3c3;">
                Your browser does not support the canvas element.
            </canvas>

            <script type="text/javascript">
                var c=document.getElementById("canvas");
                var ctx=c.getContext("2d");
                var img=new Image();
                img.src="<?php echo $pic; ?>";
                img.onload = function(){
                    
                    ctx.drawImage(img,0,0);
                    ctx.textAlign="left";
                    
<?php
foreach ($displayItems as $myitem) {
    foreach ($myitem as $myelement) {
        //echo "ctx.font=$myelement[size]pt $myelement[font];";
        // echo 'ctx.font="' . $myelement[size] . "pt " . $myelement[font] . '";';
        if ($showTs != 0) {
            echo 'ctx.fillText("' . $myelement[text] . "(" . $myelement[ts] . ')=' . number_format($myelement[value], $myelement[decimals], '.', '') . $myelement[unit] . '",' . $myelement[x] . ',' . $myelement[y] . ');';
        } else {
            echo 'ctx.fillText("' . $myelement[text] . '=' . number_format($myelement[value], $myelement[decimals], '.', '') . $myelement[unit] . '",' . $myelement[x] . ',' . $myelement[y] . ');';
        }

        //             echo '<p style = "color:' . $myelement[color] . ';font-family:' . $myelement[font] . ';font-size:' . $myelement[size] . ' px">' . $myelement[text] . '<br>' . number_format($myelement[value], $myelement[decimals], '.', '') . $myelement[unit] . '</p>';
    }
}
?>
    }

<?php
if ($test == 1) {

    echo "document.onclick = function(e){";

    echo "var x = e.pageX;";
    echo "var y = e.pageY;";
    echo 'alert("User clicked at position (" + x + "," + y + ")")};';
}
?>
            </script>
        </div>
    </body>
</html>

