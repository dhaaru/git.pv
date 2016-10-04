<?php
$sourcetable = " _devicedatavalue ";
if ($compressed) {
    $sourcetable = " _devicedatacompressedvalue ";
}

set_time_limit(900);

require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

$anyArgs = false;
$now = mktime();

date_default_timezone_set('UTC');


$stamp = $stamp;
$endstamp = $endstamp;

$query = "select * from _leveldiagram";



if ($showQueries == 1) {
    echo $query . "<br>";
}
$ds2 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds1 = mysql_fetch_array($ds1)) {
    
    $diagram = $row_ds1["diagramid"];
    
    $url = $row_ds1["url"];
    
    $url = split($url, "?");
    
    $tmp = $url[1];
    $url = $url[0];
    
    $tmp = split($tmp, "&");
    foreach ($tmp as $word){
        echo $word;
    }
    return;
    if (is_null($defaults) || strlen($defaults) == 0) {
        $defaults = false;
    } else {

        $defaults = split(",", $defaults);
    }


    $argBack = $args;
    $args = split(";", $args);
    $infos = split(";;", $infos);

    $diffs = split(";", $diffs);
    $sums = split(";", $sums);
    $avgs = split(";", $avgs);
    $maxs = split(";", $maxs);


    $prTexts = array();
    $prValues = "[[0,0]";

    $prIndex = 0;
    if (isset($prs)) {
        $avgItems[0] = $prs;
        $query = 'insert into _diagram_pr values (' . $diagram . ', ' . $prs . ')';
        if ($showQueries == 1) {
            echo $query . "<br>";
        }
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    }
    $prValues.="]";

    $displayItems = array();


    $currentValue = array();
    $erg = 0;
    $tmpIndex = null;

    if (false) {
        foreach ($diffs as $avg) {
            $avgItems = split(",", $avg);
            if (sizeof($avgItems) != 14) {
                continue;
            }
            $query = "select max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts < $stamp-$offset and ts > ($stamp-18*3600-$offset)";
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

            $query = "select max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < ($endstamp)";
            if ($avgItems[5] != 'null') {
                $query.=" and value > $avgItems[5]";
            }

            if ($showQueries == 1) {
                echo $query . "<br>";
            }
            $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
            while ($row_ds2 = mysql_fetch_array($ds2)) {
                $erg += $row_ds2[value];
            }


            $values = 0;
            $avgValue = 0;


            $currentValue[value] = $erg;
            $currentValue[text] = $avgItems[7];
            $currentValue[color] = $avgItems[8];
            $currentValue[decimals] = $avgItems[9];
            $currentValue[unit] = $avgItems[10];
            $currentValue[size] = $avgItems[11];
            $currentValue[type] = $avgItems[12];
            $currentValue[font] = $avgItems[13];
            if (!isset($tmpIndex)) {
                $tmpIndex = $avgItems[6];
            }

            mysql_free_result($ds2);
        }
        if (isset($tmpIndex)) {
            $displayItems[$tmpIndex][] = $currentValue;
        }



        foreach ($maxs as $avg) {
            $avgItems = split(",", $avg);
            if (sizeof($avgItems) != 14) {

                continue;
            }
            $currentValue = array();
            $erg = 0;
            if ($phase == "tag") {
                $query = "select max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as maxvalue, min(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as minvalue from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp-$offset and ts < ($endstamp-$offset)";

                if ($showQueries == 1) {
                    echo $query . "<br>";
                }
                $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
                while ($row_ds2 = mysql_fetch_array($ds2)) {
                    $erg = $row_ds2['maxvalue'] - $row_ds2['minvalue'];
                }
            } else {
                $newTs = $stamp;
                while ($newTs < $endstamp) {
                    $query = "select max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as maxvalue, min(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as minvalue from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $newTs and ts < ($newTs+24*3600)";
                    if ($avgItems[5] != 'null') {
                        $query.=" and value > $avgItems[5]";
                    }

                    if ($showQueries == 1) {
                        echo $query . "<br>";
                    }
                    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
                    while ($row_ds2 = mysql_fetch_array($ds2)) {
                        $erg = $row_ds2['maxvalue'] - $row_ds2['minvalue'];
                    }
                    $newTs += 24 * 3600;
                }
            }
            mysql_free_result($ds2);

            $currentValue[value] = $erg;
            $currentValue[text] = $avgItems[7];
            $currentValue[color] = $avgItems[8];
            $currentValue[decimals] = $avgItems[9];
            $currentValue[unit] = $avgItems[10];
            $currentValue[size] = $avgItems[11];
            $currentValue[type] = $avgItems[12];
            $currentValue[font] = $avgItems[13];

            $displayItems[$avgItems[6]][] = $currentValue;

            mysql_free_result($ds2);
        }


        foreach ($avgs as $avg) {
            $avgItems = split(",", $avg);
            if (sizeof($avgItems) != 14) {
                continue;
            }
            $currentValue = array();
            $query = "select floor(ts) as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp-$offset and ts < $endstamp-$offset";
            if ($avgItems[5] != 'null') {
                $query.=" and value > $avgItems[5]";
            }

            if ($showQueries == 1) {
                echo $query . "<br>";
            }
            $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
            while ($row_ds2 = mysql_fetch_array($ds2)) {
                $currentValue[$row_ds2[ts]][] = $row_ds2[value];
            }

            $values = 0;
            $avgValue = 0;
            foreach ($currentValue as $value) {
                $avgValue+= array_sum($value) / sizeof($value);
                $values++;
            }

            $avgValue/=$values;

            $currentValue[value] = $avgValue;
            $currentValue[text] = $avgItems[7];
            $currentValue[color] = $avgItems[8];
            $currentValue[decimals] = $avgItems[9];
            $currentValue[unit] = $avgItems[10];
            $currentValue[size] = $avgItems[11];
            $currentValue[type] = $avgItems[12];
            $currentValue[font] = $avgItems[13];

            $displayItems[$avgItems[6]][] = $currentValue;

            mysql_free_result($ds2);
        }
//sums=;0,13,0 ,451,U3, null,6,Total%20Irradiation,yellow,2,W/m²
//&avgs=-22.68,1.15,0 ,451,U4, null,5,Avg.%20Temperature,red,2,°C;&sums=0,13,0,451,U3,null

        foreach ($sums as $sum) {
            $avgItems = split(",", $sum);
            if (sizeof($avgItems) == 14) {
                $avgItems[] = 0;
            }
            if (sizeof($avgItems) != 15) {

                continue;
            }
            $currentValue = array();
            $query = "select floor(ts/3600) as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp-$offset and ts < $endstamp-$offset";
            if ($avgItems[5] != 'null') {
                $query.=" and value > $avgItems[5]";
            }

            if ($showQueries == 1) {
                echo $query . "<br>";
            }
            $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
            while ($row_ds2 = mysql_fetch_array($ds2)) {
                $currentValue[$row_ds2[ts]][] = $row_ds2[value];
            }

            $avgValue = 0;
            foreach ($currentValue as $value) {
                if ($avgItems[14] == 1) {
                    $avgValue+= array_sum($value);
                } else {
                    $avgValue+= array_sum($value) / sizeof($value);
                }
            }

            mysql_free_result($ds2);
            if ($avgItems[14] == 1) {
                $avgValue = $avgValue / 4;
            }
            $currentValue[value] = $avgValue;
            $currentValue[text] = $avgItems[7];
            $currentValue[color] = $avgItems[8];
            $currentValue[decimals] = $avgItems[9];
            $currentValue[unit] = $avgItems[10];
            $currentValue[size] = $avgItems[11];
            $currentValue[type] = $avgItems[12];
            $currentValue[font] = $avgItems[13];

            $displayItems[$avgItems[6]][] = $currentValue;
        }

        ksort($displayItems);
    }

    $values = array();
    $axis = array();
    $devices = array();

    $digitals = 0;


    $argIndex = 0;


    foreach ($args as $arg) {
        $words = split(',', $arg);
        if (sizeof($words) != 9) {
            continue;
        }

        $color = 0;
        $colorName = "null";

        $default = 0;
        if (in_array($argIndex, $defaults)) {
            $default = 1;
        }

        if (is_numeric($words[8])) {
            $color = $words[8];
        } else {
            $color = "null";
            $colorName = "'$words[8]'";
        }

        $query = "insert into _diagramelement values ($diagram, $argIndex, $words[0], $words[1], $words[2], $words[3], '$words[4]', '$words[5]', $words[6], '$words[7]', $color, $colorName, 0, $default)";

        if ($showQueries == 1) {
            echo $query . "<br>";
        }
        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());

        $argIndex++;
    }
}
return;

if ($speed != 0) {
    echo "data: " . (mktime() - $now) . "<br>";
}



$deviceString = "(null ";
$firstDevice = false;
foreach ($devices as $key => $device) {
    if ($firstDevice) {
        $firstDevice = false;
        $deviceString.=$key;
    } else {
        $deviceString .=",$key";
    }
}
$deviceString .= ")";


$query = "select displayname, igate, sn, deviceid from _device where deviceid in $deviceString order by displayname";
if ($showQueries == 1) {
    echo $query . "<br>";
}
$index = 0;
$ds2 = mysql_query($query, $verbindung) or die(mysql_error());
$devId = null;
$first = true;
while ($row_ds2 = mysql_fetch_array($ds2)) {
    if (is_null($devId)) {

        $devId = $row_ds2[deviceid];
        $devices[$devId][displaydata] = "[";
    }
    $sn = $row_ds2[sn];
    $sn = substr($sn, 3);
    $query = "select distinct (floor((tstamp+ $offset)/(3600))*3600000) as tstamp, fehler_txt from alarm where igate_id = $row_ds2[igate] and seriennummer='$sn' and tstamp < $endstamp-$offset and tstamp > $stamp-$offset order by tstamp";
    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if ($first == true) {
            $first = false;
            $devices[$devId][displaydata].="[$row_ds1[tstamp], 0]";
        } else {
            $devices[$devId][displaydata].=", [$row_ds1[tstamp], 0]";
        }
        //$devices[$row_ds2[deviceid]][values][($row_ds1[tstamp] + 19800) * 1000][nr] = $row_ds1[fehler_nr];
        $devices[$devId][values][($row_ds1[tstamp])][txt] .= $row_ds2[displayname] . "." . $row_ds1[fehler_txt] . " ";
    }

    mysql_free_result($ds1);
}
mysql_free_result($ds2);
$devices[$devId][displaydata].="]";

$maintenance = array();
$maintenance[str] = "[[0,0]";
$maintenance[val] = array();

if (isset($maint)) {
    $query = "select unix_timestamp(datum) as date, bearbeiter, hardware, software, bemerkungen from manager where igate_id in ( $maint ) and datum = FROM_UNIXTIME($stamp+$offset)";
    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $maintenance[val][(($row_ds1[date] + 36000) * 1000)][] = $row_ds1[bearbeiter] . " " . $row_ds1[hardware] . " " . $row_ds1[software];
        $maintenance[str].=", [" . (($row_ds1[date] + 36000) * 1000) . ", 0]";
    }
}
$maintenance[str].="]";

if ($speed != 0) {
    echo "alarms: " . (mktime() - $now) . "<br>";
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
        <link rel="stylesheet" href="style.css" type="text/css" />
        <!--[if lte IE 9]><script type="text/javascript" src="../functions/flot/excanvas.js"></script><![endif]-->
        <script type="text/javascript" src="../functions/flot/jquery-1.7.min.js"></script>
        <script type="text/javascript" src="jscolor/jscolor.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.stack.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.selection.min.js"></script>
    </head>
    <body>

        <div style="height: 100%; width: 100%">
            <div style="float: left; height: 99.5%; width: 85%; padding-top: 2px">
                <form name=ThisForm method="post" action="" target="_self">
                    <?php
                    if (isset($title)) {
                        echo "<div>";
                        echo "<p>";

                        echo $title;

                        echo "</p>";
                        echo "</div>";
                    }
                    ?>
                    <div>
                        <div id="placeholder" style="font-size: 95%; width: 100%; height: 97%"></div>
                    </div>
                </form>
            </div>
            <div style="float: left; height: 97%; width: 14%; text-align: center">
                <div
                    style="background-color: BlanchedAlmond; font-size: 90%; width: 99%; height: <?php
                    if (isset($height)) {
                        echo $height . "%";
                    } else {
                        echo "69%";
                    }
                    ?>; overflow: auto;"
                    id="legend">

                </div>

                <div style="height: <?php
                    if (isset($height)) {
                        echo (100 - 1 - $height) . "%";
                    } else {
                        echo "30%";
                    }
                    ?>; background-color: beige; width: 99%; overflow: auto;"
                     id="buttons">
                         <?php
                         foreach ($displayItems as $myitem) {
                             foreach ($myitem as $myelement) {
                                 echo '<p style = "color:' . $myelement[color] . ';font-family:' . $myelement[font] . ';font-size:' . $myelement[size] . 'px">' . $myelement[text] . '<br>' . number_format($myelement[value], $myelement[decimals], '.', '') . $myelement[unit] . '</p>';
                             }
                         }

                         if (!is_null($exception)) {
                             $query = "select * from _exception where id = $exception and number = -1";

                             if ($showQueries == 1) {
                                 echo $query . "<br>";
                             }
                             $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
                             while ($row_ds2 = mysql_fetch_array($ds2)) {

                                 $park_no = 25;
                                 $stampBak = $stamp;
                                 $endstampBak = $endstamp;
                                 $resBack = $resolution;
                                 include ('patPr.php');
                                 echo '<p style = "font-size: 90%">Performance Ratio<br>' . number_format($pr_value, 2, ".", "") . ' %</p>';
                                 $resolution = $resBack;

                                 $stamp = $stampBak;
                                 $endstamp = $endstampBak;
                             }
                         }
                         ?>
                    <input title="Toggle between a stacked diagram showing the total yields and an easily comparable line diagram" id="stackbtn" onclick="toggleStack()" type="image" src="../imgs/linien.png">
                    <input title="Reset the zoom"    style="flow: left;" id="resetZoom" onclick="resetZoom()" type="image"    src="../imgs/lupe_grey.png" disabled>

                    <?php
                    $phpArg = "?stamp=" . $stamp . "&endstamp=" . $endstamp;
                    $deltaNew = 0;
                    if ($delta == 0) {
                        $deltaNew = "&delta=1";
                    } else {
                        $deltaNew = "&delta=0";
                    }
                    $endString = "&yearO=$yearO&monO=$monO&dayO=$dayO";
                    $startString = "&yearI=$yearI&monI=$monI&dayI=$dayI";

                    $phaseWord = "&showQueries=$showQueries&showAll=$showAll" . $endString . $startString . "&phase=$phase&tag=$tag&mon=$mon&jahr=$jahr&offset=$offset";

                    if ($anyArgs) {
                        if ($hideClear == 0) {
                            echo '<a href="../' . $sourcefile . $phpArg . $phaseWord . '&igate=' . $igate . '" target="_parent">';
                            echo '<img title="Reset Diagram" src="clear.png">';
                            echo '</a>';
                        }

                        echo '<a href="export2.php' . $phpArg . '&args=' . $argBack . $phaseWord . '&delta=' . $delta . '" target="_parent">';
                        echo '<img title="Export selection as Excel file" src="xls.png">';
                        echo '</a>';
                    } else {
                        if ($hideClear == 0) {
                            echo '<img title="Reset Diagram" src="clear0.png">';
                        }
                        echo '<img title="Export selection as Excel file" src="xls0.png">';
                    }

                    if (!is_null($exception)) {
                        $query = "select * from _exception where id = $exception and icon is not null order by number";

                        if ($showQueries == 1) {
                            echo $query . "<br>";
                        }
                        $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
                        while ($row_ds2 = mysql_fetch_array($ds2)) {
                            echo '<a href="' . $row_ds2['url'] . '&phase=' . $phase . '&tag=' . $tag . '&mon=' . $mon . '&jahr=' . $jahr . '&stamp=' . $stamp . '&endstamp=' . $endstamp . '" target="_parent">';
                            echo '<img title="' . $row_ds2['title'] . '" src="' . $row_ds2['icon'] . '">';
                            echo '</a>';
                        }
                    }

                    if ($hideDelta == 0) {
                        if ($delta == 1) {
                            echo '<a href="../' . $sourcefile . $phpArg . '&args=' . $argBack . $phaseWord . $deltaNew . '&igate=' . $igate . '" target="_parent">';
                            echo '<img title="Toggle absolute values and d/dt" src="ddt1.png">';
                            echo '</a>';
                        } else {

                            echo '<a href="../' . $sourcefile . $phpArg . '&args=' . $argBack . $phaseWord . $deltaNew . '&igate=' . $igate . '" target="_parent">';
                            echo '<img title="Toggle absolute values and d/dt" src="ddt0.png">';
                            echo '</a>';
                        }
                    }
                    if ($echoArgs == 1) {
                        ?>

                        <input width ="99%" type="text" name="unit" class="textfeld" 
                               value='$diagrammCode .= <?php echo $argString; ?>'>


                        <?php
                    }
                    ?>
                </div>


            </div>
            <script type="text/javascript">
                var zeigeKurve = {};
                var alarmTexts = new Array();
                var maintTexts = new Array();
                var prTexts = new Array();
<?php
echo "var start = ($stamp)*1000;\n";
echo "var min = start;\n";
echo "var end = ($endstamp)*1000;\n";
echo "var max = end;\n";

$device = $devices[$devId];
foreach ($device[values] as $ts => $data) {
    echo "alarmTexts[$ts]='$data[txt]';\n";
}
foreach ($prTexts as $ts => $data) {
    echo "prTexts[$ts]='$data';\n";
}

foreach ($maintenance[val] as $ts => $blob) {
    foreach ($blob as $data) {
        ?>
                    if (maintTexts[<?php echo $ts; ?>] === undefined){            
        <?php echo "maintTexts[$ts]='" . str_replace("\n", " ", str_replace("\r", "", $data)) . "\\n';\n"; ?>
                    }else{
        <?php echo "maintTexts[$ts]+='" . str_replace("\n", " ", str_replace("\r", "", $data)) . "\\n';\n"; ?>
                    }
        <?php
    }
}
?>
    
    var stack=false;
    var miny=null;
    var maxy=null;
    var resolution = <?php echo $resolution; ?>;
                        
    $(function () {
        $("#frame").resize();
        plotWithOptions();			
				
    });
    
    function toggleKurve(label){
        zeigeKurve[label] = !zeigeKurve[label];
        plotWithOptions();
    }
    
    function toggleStack(){
        if (stack==false){
            document.getElementById("stackbtn").src="../imgs/balken.png";
            stack = true;
        }else {
            document.getElementById("stackbtn").src="../imgs/linien.png";
            
            stack = false;	
        }
        plotWithOptions();
    }
                    
    function resetZoom(){
        min = start;
        max = end;
        miny=null;
        maxy=null;
        document.getElementById("resetZoom").disabled=true;
        document.getElementById("resetZoom").src="../imgs/lupe_grey.png";
        plotWithOptions();
    }

    function showTooltip(x, y, contents, ts) {
        var content = contents;
        if (contents.indexOf("undefined") == 0){
            content = alarmTexts[ts];
            
        }
        $('<div id="tooltip">' + content + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }
    
    var previousPoint = null;
    $("#placeholder").bind("plothover", function (event, pos, item) {

        $("#x").text(pos.x);
        $("#y").text(pos.y);

        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;
                    
                $("#tooltip").remove();
                var x = new Date(item.datapoint[0]);
                var  y = item.datapoint[1].toFixed(resolution);

                var xmin = (x.getUTCMinutes());
                if (xmin<10){
                    xmin="0"+xmin;
                }
                var xh = x.getUTCHours();
                if (xh<10){
                    xh="0"+xh;
                }
                
                var label = item.series.label;
                if (label==undefined){
                    if (item.series.color=="green"){
                        showTooltip(pos.pageX, pos.pageY, maintTexts[item.datapoint[0]], item.datapoint[0]);
                    }else if (item.series.color=="purple"){
                        showTooltip(pos.pageX, pos.pageY, prTexts[item.datapoint[0]], item.datapoint[0]);
                    }else {
                        showTooltip(pos.pageX, pos.pageY, alarmTexts[item.datapoint[0]], item.datapoint[0]);
                    }
                    return;
                }
                var unit = "";
                if (item.series.label.indexOf("DIGITAL")!=-1){
                    if (y%2==0){
                        y="LOW";
                    }else {
                        y="HIGH";
                    }
                }else {
                    
                    label = item.series.label.substring(0, item.series.label.indexOf("("));
                    
                    unit = " "+item.series.label.substring(item.series.label.indexOf("(")+1, item.series.label.indexOf(")"));
                }
                
                
                showTooltip(item.pageX, item.pageY,
                label +" ( "+xh+":"+xmin + " ) = " + y+unit, item.datapoint[0]);
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;            
        }
    });

    $("#placeholder").bind("plotselected", function(event, ranges)
    {
        document.getElementById("resetZoom").src="../imgs/lupe.png";
        document.getElementById("resetZoom").disabled=false;
        min = ranges.xaxis.from;
        max = ranges.xaxis.to;
        
<?php
echo "max = Math.max(max, min+3600000*2);";
?>
        plotWithOptions();	
    }
);
    var devValues=new Array();

<?php
$index = 0;
foreach ($values as $devkey => $device) {
    echo "devValues['$index']=new Array();\n";
    if ($defaults == false) {

        echo "zeigeKurve['$devkey']=true;\n";
    } else {
        if (in_array($devkey, $defaultNames)) {
            echo "zeigeKurve['$devkey']=true;\n";
        } else {
            echo "zeigeKurve['$devkey']=false;\n";
        }
    }

    $lastValue = -9999999999999999999;

    $justChanged = true;

    foreach ($device[data] as $tskey => $value) {

        echo "devValues['" . $index . "'].push([" . ($tskey * 1000) . ", " . $value[value] . "]);\n";
    }
    $index++;
}
?>

    function plotWithOptions(){

        var options = 
            { 
            xaxis: 
                { 
                mode: "time" , 
                min: min, 
                max: max
            }
            ,grid: { hoverable: true, autoHighlight: true } 
            ,lines: {show: true, lineWidth: 1}
            ,points: {show: true}
            ,shadowSite: 0
            ,yaxes: [ 
<?php
$first = true;
foreach ($axis as $ax) {
    if ($first) {
        $first = false;
    } else {
        echo ",";
    }
    $ax2 = str_replace("DEG", "&deg;", $ax);
    $ax2 = str_replace("%B0", "&deg;", $ax2);


    $ax2 = str_replace("SQUA", "&sup2;", $ax2);
    if ($ax2 != "DIGITAL" && $delta == 1) {
        $ax2 = "&#916;(" . $ax2 . ")/h";
    }
    if ($ax2 == "DIGITAL") {
        echo "{ticks: [";

        for ($index = 0; $index < $digitals; $index++) {
            if ($index > 0) {
                echo ",";
            }
            echo "[" . ($index * 2 + 0.15) . ", 'LOW'], [" . ($index * 2 + 0.85) . ", 'HIGH']";
        }
        echo "]}";
    } else {
        echo "{tickFormatter: function(v, axis){return v.toFixed(axis.tickDecimals) +' $ax2'}}";
    }
}
?>
            ]
            ,selection: { mode: "x"}
            
            ,legend: {container: $("#legend"),
                labelFormatter: function (label, series) {
                    var cutLabel = label.substr(0, label.indexOf(" ("));
                    			
                    var zeige = ""
                    if (zeigeKurve[cutLabel]){
                        zeige = 'checked="checked"';

                    }
				                
                    var cb = '<input type="checkbox" name="' + cutLabel + '" '+zeige+' id="id' + cutLabel + '" onClick="toggleKurve(\''+cutLabel+'\');"> ' + label+'</input>';
                    return cb;
                }
            }
        };
        $.plot($("#placeholder"), [
<?php
$first = true;

$index = 0;
foreach ($values as $key => $value) {
    if (!$first) {
        echo ",";
    } else {
        $first = false;
    }
    $axisWord = "";
    if (isset($axis[$value[axis]])) {
        $axisWord = " (" . $axis[$value[axis]] . ")";
        $axisWord = str_replace("DEG", "&deg;", $axisWord);
        $axisWord = str_replace("SQUA", "&sup2;", $axisWord);
        $axisWord.="'";
        if ($axis[$value[axis]] == "DIGITAL") {
            $axisWord.=", lines: {show: zeigeKurve['" . $key . "'], steps: true}";
        }
    }


    echo "zeigeKurve['" . $key . "']?{ stack: (stack?" . $value[stack] . ":null), lines: {show: zeigeKurve['" . $key . "']}, points: {show: false}, yaxis: " . ($value[axis] + 1) . ", color: $value[color], label: '" . $key . $axisWord . ", data: devValues['" . ($index++) . "']}:{label: '" . $key . $axisWord . ", data:  [[0, null]], color: $value[color]}\n";
}

$device = $devices[$devId];
echo ", { points: {show: true}, lines: {show: false}, color: \"red\", data: $device[displaydata] }";
if (isset($maint)) {

    echo ", { points: {show: true}, lines: {show: false}, color: \"green\", data: $maintenance[str] }";
}

if (isset($prs)) {
    echo ", { points: {show: true}, lines: {show: false}, color: \"purple\", data: $prValues }";
}
echo " \n";
?>
        ],options);

    }
    
    window.onresize = plotWithOptions;
<?php
if ($speed != 0) {
    echo "alert('total: " . (mktime() - $now) . "');";
}
?>
            </script>
        </div>
    </body>
</html>

