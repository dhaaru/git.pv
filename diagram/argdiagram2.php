<?php
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

$anyArgs = false;
$now = mktime();

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

$values = array();
$argBack = $args;
$args = split(";", $args);

$axis = array();
$devices = array();

foreach ($args as $arg) {

    $words = split(',', $arg);
    if (sizeof($words) != 9) {
        continue;
    }
    $anyArgs = true;
    if (!array_key_exists($words[3], $devices)) {
        $devices[$words[3]][name] = $words[5];
        $devices[$words[3]][color] = $words[8];
        $devices[$words[3]][values] = array();
    }
    if (!in_array($words[7], $axis)) {
        $axis[] = $words[7];
    }

    $translatedField = $words[4];
    $translatedField = str_replace("PLUS", "+", $translatedField);
    $query = "select ts+19800 as ts, (((value+$words[0])*$words[1])+$words[2]) as value from _devicedatavalue where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        echo $query . "<br>";
    }

    $ax = array_search($words[7], $axis);

    $values[$words[5]][axis] = $ax;
    $values[$words[5]][color] = $words[8];

    $lastTs = 0;
    $lastValuechangeTs = 0;
    $lastValue = 0;
    $first = true;

    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        if ($first) {
            $first = false;
            $lastTs = 0;
            $lastValue = $row_ds2[value];
        }

        if (($row_ds2[ts] - $lastTs) > (60 * ($words[6] + 1))) {
            $values[$words[5]][data][$lastTs + 1][value] = "null";
        }
        if ($delta == 0 || $words[7] == "DIGITAL") {
            $values[$words[5]][data][$row_ds2[ts]][value] = $row_ds2[value];
        } elseif ($row_ds2[value] != null && $row_ds2[value] != $lastValue) {
            if ($delta != 0) {
                $values[$words[5]][data][$row_ds2[ts]][value] = ($row_ds2[value] - $lastValue) / ($row_ds2[ts] - $lastValuechangeTs);
            }
            $lastValuechangeTs = $row_ds2[ts];
            $lastValue = $row_ds2[value];
        }
        $lastTs = $row_ds2[ts];
    }
    mysql_free_result($ds2);
}

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

$query = "select igate, sn, deviceid from _device where deviceid in $deviceString";
if ($showQueries == 1) {
    echo $query . "<br>";
}
$ds2 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds2 = mysql_fetch_array($ds2)) {
    $sn = $row_ds2[sn];
    $sn = substr($sn, 3);
    $query = "select * from alarm where igate_id = $row_ds2[igate] and seriennummer='$sn' and tstamp < $endstamp and tstamp > $stamp order by tstamp";
    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    $first = true;
    $devices[$row_ds2[deviceid]][displaydata] = "[";
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if ($first == true) {
            $first = false;
            $devices[$row_ds2[deviceid]][displaydata].="[($row_ds1[tstamp]+19800)*1000, 0]";
        } else {
            $devices[$row_ds2[deviceid]][displaydata].=", [($row_ds1[tstamp]+19800)*1000, 0]";
        }
        //$devices[$row_ds2[deviceid]][values][($row_ds1[tstamp] + 19800) * 1000][nr] = $row_ds1[fehler_nr];
        $devices[$row_ds2[deviceid]][values][($row_ds1[tstamp] + 19800) * 1000][txt] = $row_ds1[fehler_txt];
    }
    $devices[$row_ds2[deviceid]][displaydata].="]";
    mysql_free_result($ds1);
}
mysql_free_result($ds2);


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
        src="../functions/flot/jquery.flot.selection.min.js"></script>
    </head>
    <body>

        <div style="height: 99.5%; width: 100%">
            <div style="float: left; height: 99%; width: 85%; padding-top: 7px">
                <form name=ThisForm method="post" action="" target="_self">

                    <div>
                        <p>
                            <?php
                            echo $title;
                            ?>
                        </p>
                    </div>
                    <div>
                        <div id="placeholder" style="font-size: 95%; width: 99%; height: 98%"></div>
                    </div>
                </form>
            </div>
            <div style="float: left; height: 99.5%; width: 14%; text-align: center">
                <div
                    style="padding: 3px; background-color: BlanchedAlmond; font-size: 85%; width: 99%; height: 77.5%; overflow: auto;"
                    id="legend">

                </div>

                <div style="height: 22%; background-color: beige; width: 99%; overflow: auto;"
                     id="buttons">

                    <input title="Reset the zoom"    style="flow: left;" id="resetZoom" onclick="resetZoom()" type="image"    src="../imgs/lupe_grey.png" disabled>

                    <?php
                    $phpArg = "?phase=$phase&mon=$mon&tag=$tag&jahr=$jahr";
                    $deltaNew = 0;
                    if ($delta == 0) {
                        $deltaNew = "&delta=1";
                    } else {
                        $deltaNew = "&delta=0";
                    }


                    if ($anyArgs) {

                        echo '<a href="../selectableDiagram.php'.$phpArg.'" target="_parent">';
                        echo'                        <img title="Reset Diagram" src="clear.png"/>';
                        echo '                    </a>';

                        echo '                    <a href="export.php'.$phpArg.'&args='.$argBack.'&delta='.$delta.'" target="_parent">';
                        echo '                  <img title="Export selection as Excel file" src="xls.png"/>';
                        echo'         </a>';
                    } else {
                        echo '<img title="Reset Diagram" src="clear0.png"/>';
                        echo '<img title="Export selection as Excel file" src="xls0.png"/>';
                    }

                    if ($delta == 1) {
                        echo ' <a href="../selectableDiagram.php' . $phpArg . '&args=' . $argBack . $deltaNew . '" target="_parent">';
                        echo '                        <img title="Toggle absolute values and d/dt" src="ddt1.png"/>';
                        echo '                   </a>';
                    } else {

                        echo ' <a href="../selectableDiagram.php' . $phpArg . '&args=' . $argBack . $deltaNew . '" target="_parent">';
                        echo '                        <img title="Toggle absolute values and d/dt" src="ddt0.png"/>';
                        echo '                   </a>';
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                var alarmTexts = new Array();
<?php
echo "var start = ($stamp+19800)*1000;\n";
echo "var min = start;\n";
echo "var end = ($endstamp+16200)*1000;\n";
echo "var max = end;\n";


foreach ($devices as $device) {
    foreach ($device[values] as $ts => $data) {
        echo "alarmTexts[$ts]='$data[txt]';\n";
    }
}
?>
    
    
                        var miny=null;
                        var maxy=null;
                        
                        $(function () {
                            $("#frame").resize();
                            plotWithOptions();			
				
                        });
                    
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
                                    var  y = item.datapoint[1].toFixed(2);

                                    var xmin = (x.getUTCMinutes());
                                    if (xmin<10){
                                        xmin="0"+xmin;
                                    }
                                    var xh = x.getUTCHours();
                                    if (xh<10){
                                        xh="0"+xh;
                                    }
                                    showTooltip(item.pageX, item.pageY,
                                    item.series.label +" ( "+xh+":"+xmin + " ) = " + y, item.datapoint[0]);
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
            ,lines: {show: true}
            ,points: {show: true}
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
    $ax2 = str_replace("SQUA", "&sup2;", $ax2);
    if ($ax2 != "DIGITAL" && $delta == 1) {
        $ax2 = "&#916;(" . $ax2 . ")/s";
    }
    if ($ax2 == "DIGITAL") {

        echo "{ticks: [[0, 'LOW'], [1, 'HIGH']]}";
    } else {
        echo "{tickFormatter: function(v, axis){return v.toFixed(axis.tickDecimals) +' $ax2'}}";
    }
}
?>
            ]
            ,selection: { mode: "x"}
            
            ,legend: {container: $("#legend"),
                labelFormatter: function (label, series) {
                    //                    var cb = '<input class="color"> ' + label+'</input>';
                    return label;
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
    }

    echo "{ points: {show: false}, yaxis: " . ($value[axis] + 1) . ", color: $value[color], label: '" . $key . $axisWord . "', data: devValues['" . ($index++) . "']}\n";
}

$first = true;
foreach ($devices as $key => $device) {

    echo ", { points: {show: true}, lines: {show: false}, color: $device[color], data: $device[displaydata] }\n";
}
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

