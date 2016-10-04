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


if (!isset($phase) || $phase == "") {
    if (!isset($_SESSION['phase_s'])) {
        $phase = "tag";
    } else {
        $phase = $_SESSION['phase_s'];
    }
}
$_SESSION['phase_s'] = $phase;


if (!isset($jahr) || $jahr=="") {
    if (!isset($_SESSION['jahr_s'])) {
        $jahr = $jahr_heute;
    } else {
        $jahr = $_SESSION['jahr_s'];
    }
}
$_SESSION['jahr_s'] = $jahr;

if (!isset($mon) || $mon=="") {
    if (!isset($_SESSION['mon_s'])) {
        $mon = $monat_heute;
    } else {
        $mon = $_SESSION['mon_s'];
    }
}
$_SESSION['mon_s'] = $mon;

if (!isset($tag) || $tag=="") {
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

$values = array();
$args = split(";", $args);

$axis=array();


foreach ($args as $arg) {

    $words = split(',', $arg);
    if (sizeof($words) != 9) {
        continue;
    }

    $translatedField = $words[4];
    $translatedField = str_replace("PLUS", "+", $translatedField);
    $query = "select ts+19800 as ts, (((value+$words[0])*$words[1])+$words[2]) as value from _devicedatavalue where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        echo $query . "<br>";
    }

    
    if (!in_array($words[7], $axis)){
        $axis[]=$words[7];
        }
    $ax=array_search($words[7], $axis);    
    
    $values[$words[5]][axis] = $ax;
    $values[$words[5]][color] = $words[8];

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
            $values[$words[5]][data][$lastTs + 1][value] = "null";
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
    mysql_free_result($ds2);
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
                    </div>
                    <div>
                        <div id="placeholder" style="font-size: 95%; width: 97%; height: 97%"></div>
                    </div>
                </form>
            </div>
            <div style="float: left; height: 99.5%; width: 16%; text-align: center">
                <div
                    style="padding: 3px; background-color: BlanchedAlmond; font-size: 85%; height: 77.5%; overflow: auto;"
                    id="legend">

                </div>

                <div style="height: 22%; background-color: beige; overflow: auto;"
                     id="buttons">

                    <input title="Reset the zoom"    style="flow: left;" id="resetZoom" onclick="resetZoom()" type="image"    src="../imgs/lupe_grey.png" disabled>
                    
                    <?php 
                    $phpArg = "?phase=$phase&mon=$mon&tag=$tag&jahr=$jahr";
                    ?>
                    
                    <a href="../selectableDiagram.php<?php echo $phpArg;?>" target="_parent">Clear</a>


                </div>
            </div>
            <script type="text/javascript">
<?php
echo "var start = ($stamp+19800)*1000;\n";
echo "var min = start;\n";
echo "var end = ($endstamp+16200)*1000;\n";
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
            ,yaxes: [ 
        <?php
        $first = true;
        foreach ($axis as $ax){
            if ($first){
                    $first=false;
                }else {
                    echo ",";
                }
            $ax2 = str_replace("DEG", "&deg;", $ax );
            $ax2 = str_replace("SQUA", "&sup2;", $ax );
            echo "{tickFormatter: function(v, axis){return v.toFixed(axis.tickDecimals) +'$ax2'}}";
            }
        ?>
        ]
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
    echo "{ points: {show: false}, yaxis: ".($value[axis]+1).", color: $value[color], label: '" . $key . "', data: devValues['" . ($index++) . "']}";
}
?>
        ],options);

    }
    
    window.onresize = plotWithOptions;

            </script>

        </div>

    </body>
</html>

