<?php
$field = $_POST ['select'];
if (is_null($field)) {
    $field = "e_total";
}
$now = time();

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


$_SESSION['phase_s'] = $phase;

if (!isset($jahr)) {
    if (!isset($_SESSION['jahr_s'])) {
        $jahr = $jahr_heute;
    } else {
        $jahr = $_SESSION['jahr_s'];
    }
}
$_SESSION['jahr_s'] = $jahr;

if (!isset($mon)) {
    if (!isset($_SESSION['mon_s'])) {
        $mon = $monat_heute;
    } else {
        $mon = $_SESSION['mon_s'];
    }
}
$_SESSION['mon_s'] = $mon;

if (!isset($tag)) {
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
    $endstamp = mktime(18, 30, 0, $mon, $tag, $jahr, 0);
} else if ($phase == "mon") {
    $stamp = mktime(18, 30, 0, $mon, 0, $jahr, 0);
    $endstamp = mktime(18, 30, 0, $mon + 1, 0, $jahr, 0);
} else {
    $stamp = mktime(18, 30, 0, 0, 0, $jahr, 0);
    $endstamp = mktime(18, 30, 0, 0, 0, $jahr + 1, 0);
}

//$stamp = 1341577800;
//$endstamp = 1341677800;

$dataSets = array();
$devices = array();
$deviceIds = "";

$fields = array();

$values = array();
$query = "select d.* from _diagramdevice as dd, _device as d where d.deviceid = dd.device and dd.diagram=" . $diagram;
if ($showQueries == 1) {
    echo $query . "<br>";
}
$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds1 = mysql_fetch_array($ds1)) {

    $query = "select distinct field from _devicedatavalue where device = " . $row_ds1[deviceid];
    if ($showQueries == 1) {
        echo $query . "<br>";
    }

    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        if (!in_array($row_ds2[field], $fields)) {
            $fields[] = $row_ds2[field];
        }
    }

    $devices[$row_ds1[deviceid]] = $row_ds1[igate] . "." . $row_ds1[sn];
    $deviceIds .= "," . $row_ds1[deviceid];
    // $values[$row_ds1[deviceid]][] = "values[$row_ds1[deviceid]]=[];";
}

$deviceIds[0] = "(";
$deviceIds .= ")";


$query = "select (ts+19800)*1000 as t, value, device from _devicedatavalue where device in " . $deviceIds . " and field = '$field' and ts>$stamp and ts < $endstamp order by device, ts";
if ($showQueries == 1) {
    echo $query . "<br>";
}
$ds2 = mysql_query($query, $verbindung) or die(mysql_error());

$oldTs = 0;
$oldValue = 0;
$dev = -1;
while ($row_ds2 = mysql_fetch_array($ds2)) {
    if (($row_ds2[t]-$oldTs)>900000){
            $values[$row_ds2[device]][$oldTs+1000][0]=null;
            $values[$row_ds2[device]][$oldTs+1000][1]=null;
        }
        
    if ($row_ds2[device] != $dev) {
        $dev = $row_ds2[device];
        $oldTs = 0;
        $oldValue = $row_ds2[value];
    }
    $values[$row_ds2[device]][$row_ds2[t]][0] = $row_ds2[value];
    
    if (!is_null($row_ds2[value])){
        $values[$row_ds2[device]][$row_ds2[t]][1] = 3600 * ($row_ds2[value] - $oldValue) / ($row_ds2[t] - $oldTs);
        $oldValue = $row_ds2[value];
        $oldTs = $row_ds2[t];
        }
    
    
}

if ($showTime == 1) {
    echo "<div>" . (time() - $now) . "</div>";
}



?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
        <link rel="stylesheet" href="style.css" type="text/css" />
        <!--[if lte IE 9]><script type="text/javascript" src="../functions/flot/excanvas.js"></script><![endif]-->
        <script type="text/javascript" src="../functions/flot/jquery-1.7.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.crosshair.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.selection.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.stack.min.js"></script>
    </head>
    <body>

        <div style="height: 98%; width: 100%">
            <div style="float: left; height: 97%; width: 82%; padding-top: 7px">
                <form name=ThisForm method="post" action="" target="_self">

                    <div>
                        <p>
                            <?php
                            echo $title;
                            ?>
                        </p>
                        <?php
                        if (sizeof($fields) > 0) {
                            echo '<select id="select" name="select" id="" onchange="this.form.submit();">';
                            foreach ($fields as $cfield) {
                                if ($cfield == $field) {
                                    echo "<option selected='selected'>" . $cfield . "</option> ";
                                } else {
                                    echo "<option>" . $cfield . "</option> ";
                                }
                            }
                            echo "</select>";
                        }
                        ?>
                    </div>
                    <div>
                        <div id="placeholder" style="font-size: 95%; width: 97%; height: 85%"></div>
                    </div>
                </form>
            </div>
            <div style="float: left; height: 99.5%; width: 16%; text-align: center">
                <div
                    style="padding: 3px; background-color: BlanchedAlmond; font-size: 85%; height: 37.5%; overflow: auto;"
                    id="legend">
                    <br>
                </div>
                <div
                    style="padding: 3px; background-color: Gainsboro; font-size: 85%; height: 37.5%; overflow: auto;"
                    id="infoText">
                        <?php
                        if ($einAvg != null) {
                            echo '<p style = "font-size: 90%">Average Irradiation<br>' . $einAvg . '</p>';
                        }
                        if ($tmpAvg != null) {
                            echo '<p style = "color: red;font-size: 90%">Average Temperature<br>' . $tmpAvg . '</p>';
                        }
                        if ($totalProduction > 0) {
                            $totalProduction = number_format($totalProduction, 2, ".", "");
                            echo '<p style = "font-size: 90%">Total Production<br>' . $totalProduction . ' kWh</p>';
                        }

                        if (false && $pr_anzeige != null) {
                            echo '<p style = "font-size: 90%">Performance Ratio<br>' . number_format($pr_anzeige, 1, ".", "") . ' %</p>';
                        }
                        ?>
                </div>
                <div style="height: 22%; background-color: beige; overflow: auto;"
                     id="buttons">
                         <?php
                         if (($type == "wetter")) {
                             echo '<img title="Export diagram as .xls file" src="../imgs/xls_grey.png">';
                         } else {

                             echo '<a href="excelwriter.php?typ=balken" style="flow: left;" id="xls"><img title="Export diagram as .xls file" src="../imgs/xls.png"></a>';
                         }
                         ?>
                         <?php
                         if (($type == "wetter" || $styp != 1)) {
                             echo '<img title="Export diagram as .xls file" src="../imgs/xls_pr_grey.png">';
                         } else {

                             echo '<a href="excelwriter.php?typ=pr_gse" style="flow: left;" id="xls_pr"><img title="Export performance ratio values as .xls file" src="../imgs/xls_pr.png"></a>';
                         }
                         ?>
                         <?php
                         if (($type == "wetter" || $styp != 1)) {
                             echo '<img
title="Export performance ratio values as .pdf file" src="../imgs/pdf_pr_grey.png">';
                         } else {

                             echo '<a href="pdfwriter.php?typ=pr_gse" style="flow: left;" id="pdf_pr"><img
title="Export performance ratio values as .pdf file" src="../imgs/pdf_pr.png"></a>';
                         }
                         ?>
                    <br>

                    <input title="Toggle between a stacked diagram showing the total yields and an easily comparable line diagram"    id="stackbtn" onclick="toggleStack()" type="image"    src="../imgs/balken.png">
                    <input title="Reset the zoom"    style="flow: left;" id="resetZoom" onclick="resetZoom()" type="image"    src="../imgs/lupe_grey.png" disabled>
                    <input title="Toggle between absolute values and delta(value)/delta(time)" style="flow: left;"    id="toggleDelta" onclick="toggleDelta()" type="image"    src="ddt.png">
                    <br>
                    <a href="pat.php?jahr=<?php echo $jahr; ?>&mon=<?php echo $mon; ?>&tag=<?php echo $tag; ?>&portal=<?php echo $park_no; ?>"> <img title="Export Diadem-data as .csv file" src="../imgs/xls_grey.png"> </a>
                    <a href="patXls.php?jahr=<?php echo $jahr; ?>&mon=<?php echo $mon; ?>&tag=<?php echo $tag; ?>&portal=<?php echo $park_no; ?>" style="flow:left"> <img title="Export Diadem-data as .xls file" src="../imgs/xls.png"> </a>

                </div>
            </div>
            <script type="text/javascript">
<?php
echo "var start = ($stamp + 19800)*1000;\n";
echo "var min = start;\n";
echo "var end = ($endstamp + 19800)*1000;\n";
echo "var max = end;\n";
?>
var isDelta = false;
                        
$(function () {
    $("#frame").resize();
    plotWithOptions();			
				
});
                    
function resetZoom(){
    min = start;
    max = end;
    document.getElementById("resetZoom").disabled=true;
    document.getElementById("resetZoom").src="../imgs/lupe_grey.png";
    plotWithOptions();
}
    
function toggleDelta(){
    isDelta = !isDelta;
    if (isDelta){
        devs = devDeltas;
    }else {
        devs = devValues;    
    }
    plotWithOptions();
}

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
var devDeltas=new Array();

<?php
foreach ($values as $devkey => $device) {
    echo "devValues[$devkey]=new Array();\n";
    echo "devDeltas[$devkey]=new Array();\n";
    foreach ($device as $tskey => $value) {
        if (is_null($value)) {
            $value = 0;
        }else {
            echo "devDeltas[" . $devkey . "].push([" . $tskey . ", " . $value[1] . "]);\n";
        }
        echo "devValues[" . $devkey . "].push([" . $tskey . ", " . $value[0] . "]);\n";
        
    }
}
?>
var devs = devValues;

function plotWithOptions(){

    var options = 
        { 
        xaxis: 
            { 
            mode: "time" , 
            min: min, 
            max: max
        }
        ,lines: {show: true}
        ,points: {show: true}
        ,selection: { mode: "x"}
        ,legend: {container: $("#legend") }
    };
    $.plot($("#placeholder"), [
<?php
$first = true;


foreach ($values as $key => $value) {
    if (!$first) {
        echo ",";
    } else {

        $first = false;
    }
    echo "{ label: '" . $devices[$key] . "', data: devs[" . $key . "]}";
}
?>
    ],options);

}
    
window.onresize = plotWithOptions;

            </script>

        </div>

    </body>
</html>

