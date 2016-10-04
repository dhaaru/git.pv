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

$jahr_heute = date('Y');
$monat_heute = date('n');
$tag_heute = date('d');

$startjahr = 2011;

if (!isset($yearI) || $yearI == "") {
    $yearI = $jahr_heute;
}

if (!isset($yearO) || $yearO == "") {
    $yearO = $jahr_heute;
}
if (!isset($monI) || $monI == "") {
    $monI = $monat_heute;
}

if (!isset($monO) || $monO == "") {
    $monO = $monat_heute;
}

if (!isset($dayI) || $dayI == "") {
    $dayI = $tag_heute;
}
if (!isset($dayO) || $dayO == "") {
    $dayO = $tag_heute;
}

if ($yearI > $yearO || (($yearI == $yearO) && ($monI > $monO)) || (($yearI == $yearO) && ($monI == $monO) && ($dayI > $dayO))) {
    $tmp = $yearI;
    $yearI = $yearO;
    $yearO = $tmp;

    $tmp = $monI;
    $monI = $monO;
    $monO = $tmp;

    $tmp = $dayI;
    $dayI = $dayO;
    $dayO = $tmp;
}

$stamp = mktime(18, 30, 0, $monI, $dayI - 1, $yearI, 0);
$endstamp = mktime(19, 30, 0, $monO, $dayO, $yearO, 0);


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


if (is_null($args)) {
    return;
}

$values = array();
$argBack = $args;
$args = split(";", $args);

$ergs = array();

$axis = array();
$devices = array();
$index =-1;
foreach ($args as $arg) {
    $index++;
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
        $ergs[floor($row_ds2[ts]/60)][$index] = $row_ds2[value];
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

    
                        
                        $(function () {
                            $("#frame").resize();
                            plotWithOptions();			
				
                        });
                    



    function plotWithOptions(){
    var devValues=new Array();

<?php



foreach ($ergs as $value){
    if (sizeof ($value)==2){
        echo "devValues.push([" . $value[1] . ", " . $value[2] . "]);\n";
        }
    }
?>
       
        $.plot($("#placeholder"), [devValues]);

    }
    plotWithOptions();
    window.onresize = plotWithOptions;

    
            </script>

        </div>

    </body>
</html>

