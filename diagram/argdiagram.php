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
$offset = 19800;

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

if (is_null($args)) {
    return;
}

$values = array();
$args = split(";;;", $args);
$axis = split(";;", $args[0]);
$args = $args[1];

foreach ($args as $arg) {
    $kurven = split(";;", $arg);

    
    foreach ($kurven as $kurve) {
        $kurvenDetails = split(";", $kurve);
        $info = split(",", $kurvenDetais[0]);

        $values[$info[0]][color] = $info[1];
        $values[$info[0]][resolution] = $info[2];
        $values[$info[0]][axis] = $info[3];

        $values[$info[0]][bubbles] = $info[4];
        $values[$info[0]][steps] = $info[5];
        $values[$info[0]][lines] = $info[6];
        $values[$info[0]][fillable] = $info[7];

        $kurvenDetails = split(",,", $kurvenDetails[1]);
        
        foreach ($kurvenDetails as $part) {
            $parts = split(",", $part);

            $query = "select (ts + offset) as ts, (((value+$parts[0])*$parts[1])+$parts[$index][2]) as value from _devicedatavalue where value is not null and device = $parts[$index][3] and field = '$parts[$index][4]' and ts > $stamp and ts < $endstamp";

            if ($showQueries == 1) {
                echo $query . "<br>";
            }

            $ds2 = mysql_query($query, $verbindung) or die(mysql_error());

            while ($row_ds2 = mysql_fetch_array($ds2)) {
                $values[$info[0]][data][floor($row_ds2[ts]/$info[3])]=$row_ds2[value];
            }
        }
    }
}

/*
  $calcer = false;
  $words = split(';;', $arg);
  foreach ($words as $word){
  $subwords = split(";", $word);
  $graphInfo = split(",", $subwords[0]);



  for ($index = 1; $index<sizeof($subwords); $index++){
  $query = "select ts, (((value+$subwords[$index][0])*$subwords[$index][1])+$subwords[$index][2]) as value from _devicedatavalue where value is not null and device = $subwords[$index][3] and field = '$subwords[$index][4]' and ts > $stamp and ts < $endstamp";
  if ($showQueries == 1) {
  echo $query . "<br>";
  }

  $ds2 = mysql_query($query, $verbindung) or die(mysql_error());

  $lastTs = 0;
  $lastValue = 0;
  $first = true;


  while ($row_ds2 = mysql_fetch_array($ds2)) {
  if ($first) {
  $first = false;
  $lastTs = 0;
  $lastValue = $row_ds2[value];
  }

  if (($row_ds2[ts] - $lastTs) > (60 * $words[6])) {
  $values[$graphInfo[0]][data][floor($lastTs/$graphInfo[2]) + 1][value] = "null";
  //$values[$words[5]][data][$lastTs + 1][delta] = null;
  }

  $values[$words[5]][data][$row_ds2[ts]][value] = $row_ds2[value];
  if ($row_ds2[value] != null) {
  // $values[$words[5]][data][$row_ds2[ts]][delta] = ($row_ds2[value] - $lastValue) / ($row_ds2[ts] - $lastTs);
  $lastTs = $row_ds2[ts];
  $lastValue = $row_ds2[value];
  } else {
  //   $values[$words[5]][data][$row_ds2[ts]][delta] = null;
  //    $first = true;
  }
  }
  }


  }

  }
 * 
 */
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

                        if ($pr_anzeige != null) {
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
$index = 0;
foreach ($values as $devkey => $device) {
    echo "devValues['$index']=new Array();\n";
    foreach ($device[data] as $tskey => $value) {

        echo "devValues['" . $index . "'].push([" . ($tskey * 1000) . ", " . $value[value] . "]);\n";
        //  echo "devDeltas['" . $devkey . "'].push([" . ($tskey*1000) . ", " . $value[delta] . "]);\n";
    }
    $index++;
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

$index = 0;
foreach ($values as $key => $value) {
    if (!$first) {
        echo ",";
    } else {

        $first = false;
    }
    echo "{ points: {show: false}, yaxis: $value[axis], color: $value[color], label: '" . $key . "', data: devValues['" . ($index++) . "']}";
}
?>
        ],options);

    }
    
    window.onresize = plotWithOptions;

            </script>

        </div>

    </body>
</html>

