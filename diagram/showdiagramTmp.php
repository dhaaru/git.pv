<?php
//ini_set("display_errors", 0);

include ('../functions/allg_functions.php');
set_time_limit(900);

function http_auth_get($url, $username, $password) {
    $cred = sprintf('Authorization: Basic %s', base64_encode("$username:$password"));
    $opts = array(
        'http' => array(
            'method' => 'GET',
            'header' => $cred)
    );
    $ctx = stream_context_create($opts);
    $handle = fopen($url, 'r', false, $ctx);
    $contents = stream_get_contents($handle);
    fclose($handle);
    return $contents;
}

function subcalc($device, $verbindung, $data, $showCalc) {

    if ($showCalc) {
        echo "-----------------subcalc: <br>";
        print_r($device);
        echo "<br>";
        //  echo "<br><br><br>";
    }

    $value = null;
    if ($device[sumtype] != 0) {
        if ($showCalc) {
            echo "+<br>";
        }
        $value = 0;
    } else {
        if ($showCalc) {
            echo "*<br>";
        }
        $value = 1;
    }

    $query = "select * from _scadapicitemelement where scada = $device[subscada] and item = $device[subitem]";
    if ($showQueries == 1) {
        echo $query . $ln;
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        $ndevice[x] = $row_ds1[x];
        $ndevice[y] = $row_ds1[y];
        $ndevice[font] = $row_ds1[font];
        $ndevice[device] = $row_ds1[device];
        $ndevice[dbfield] = $row_ds1[dbfield];
        $ndevice[sumtype] = $row_ds1[sumtype];
        $ndevice[elementpreoffset] = $row_ds1[elementpreoffset];
        $ndevice[elementpostoffset] = $row_ds1[elementpostoffset];
        $ndevice[elementfactor] = $row_ds1[elementfactor];

        //$ndevice[value] = $value;
        $ndevice[potenz] = $row_ds1[potenz];
        if (is_null($row_ds1[source])) {
            if ($showCalc) {
                echo "source = null <br>";
            }

            $ndevice[subsum] = $row_ds1[subsum];
            $ndevice[subscada] = $row_ds1[subscada];
            $ndevice[subitem] = $row_ds1[subitem];
            if ($device[sumtype] == 1) {
                $tmp = round(subcalc($ndevice, $verbindung, $data, $showCalc), 3);


                $value += $tmp;
                if ($showCalc) {
                    echo "sum: tmp=$tmp value=$value <br>";
                }
            } else {
                $tmp = round(subcalc($ndevice, $verbindung, $data, $showCalc), 3);

                $value *= $tmp;
                if ($showCalc) {
                    echo "mul: tmp=$tmp value=$value <br>";
                }
            }
        } else {
            if ($showCalc) {
                echo "source != null <br>";
            }
            if ($device[subsum] == 1) {
                if ($showCalc) {
                    echo "sumtype = 1 <br>";
                }
                if ($data[$row_ds1[source]][OK]) {
                    $tmp = round((($data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][value] + $row_ds1[elementpreoffset]) * $row_ds1[elementfactor] + $row_ds1[elementpostoffset]), 3);
                    if ($showCalc) {

                        echo "<br>source: $row_ds1[source] sn: $row_ds1[sn] field: $row_ds1[field] <br>";
                        echo "device.sumtype=1 " . $tmp . "<br><br>";
                    }
                    $value += $tmp;
                } else {
                    if ($showCalc) {
                        echo "SOURCE BAD!!";
                    }
                    $value = 0;
                    break;
                }
            } else if ($device[subsum] == 0) {
                if ($showCalc) {
                    echo "sumtype = 0 <br>";
                }
                if ($data[$row_ds1[source]][OK]) {
                    $tmp = round((($data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][value] + $row_ds1[elementpreoffset]) * $row_ds1[elementfactor] + $row_ds1[elementpostoffset]), 3);


                    if ($showCalc) {

                        echo "<br>source: $row_ds1[source] sn: $row_ds1[sn] field: $row_ds1[field] <br>";
                        echo "device.sumtype=0 " . $tmp . "<br><br>";
                    }
                    $value *= $tmp;
                } else {
                    if ($showCalc) {
                        echo "SOURCE BAD!!";
                    }
                    $value = 0;
                    break;
                }
            }
        }
    }
    if ($showCalc) {
        echo "<br>=$value";
        echo "<br>-------------<br>";
    }

    return $value;
}

$diagram = $diagram;

$monthvalue = 0;
$yearvalue = 0;


$data = array();
$pic = "";
$name = "";

$urls = array();


//$ln = "\r\n";
$ln = "<br>";




require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

if ($diagram != 11 && is_null(get_user())) {
    return;
}
if (!$diagram == 11 && !(get_user() == "pvadmin")) {
    $query = "select * from _userscada where pic = $diagram and user= " . get_user_id();
    if ($showQueries == 1) {
        echo $query . $ln;
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    if (mysql_num_rows($ds1) == 0) {
        return;
    }
}

$alarmtexts = array();
if ($diagram == 11) {
//    int mktime ([ int $hour = date("H") [, int $minute = date("i") [, int $second = date("s") [, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") [, int $is_dst = -1 ]]]]]]] )

    date_default_timezone_set('UTC');
    $yesterdate = mktime(18, 30, 0, date("n"), date("j") - 1);
    $monthdate = mktime(18, 30, 0, date("n"), 0);
    $yeardate = mktime(18, 30, 0, 1, 0);

    $query = "select fehler_nr, fehler_txt from wr_spez_fehler where wr_hersteller='KACO'";
    if ($showQueries == 1) {
        echo $query . $ln;
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $alarmtexts[$row_ds1[fehler_nr]] = $row_ds1[fehler_txt];
    }


    $query = "select value, ts from _devicedatavalue where device=477 and field = 'E_Total' and ts < " . $yesterdate . " and ts > " . ($yesterdate - 3600 * 24) . " order by ts desc";
    if ($showQueries == 1) {
        echo $query . $ln;
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    $row_ds1 = mysql_fetch_array($ds1);
    $yestervalue = $row_ds1[value];

    $query = "select value, ts from _devicedatavalue where device=477 and field = 'E_Total' and ts < " . $monthdate . " and ts > " . ($monthdate - 3600 * 24) . " order by ts desc";
    if ($showQueries == 1) {
        echo $query . $ln;
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    if ($row_ds1 = mysql_fetch_array($ds1)) {
        $monthvalue = $row_ds1[value];
    } else {
        $monthvalue = 0;
    }

    $query = "select value, ts from _devicedatavalue where device=477 and field = 'E_Total' and ts < " . $yeardate . " and ts > " . ($yeardate - 3600 * 24) . " order by ts desc";
    if ($showQueries == 1) {
        echo $query . $ln;
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    if ($row_ds1 = mysql_fetch_array($ds1)) {
        $yearvalue = $row_ds1[value];
    } else {
        $yearvalue = 0;
    }
}

$query = "select s.name, s.filename from _scadapic as s where s.id = $diagram";
if ($showQueries == 1) {
    echo $query . $ln;
}
$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds1 = mysql_fetch_array($ds1)) {
    $pic = $row_ds1[filename];
    $name = $row_ds1[name];
}

$query = "select  si.* from _scadapicitem as si where si.scada= $diagram";
if ($showQueries == 1) {
    echo $query . $ln;
}

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds1 = mysql_fetch_array($ds1)) {
    if (!in_array($row_ds1[source], $urls)) {
        $urls[] = $row_ds1[source];
    }

    $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][potenz] = $row_ds1[potenz];
    $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][offset] = $row_ds1[offset];
    $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][factor] = $row_ds1[factor];
    $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][x] = $row_ds1[x];
    $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][y] = $row_ds1[y];
    $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][font] = $row_ds1[font];
    if (isset($row_ds1[device])) {
        $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][device] = $row_ds1[device];
    } else {
        $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][device] = null;
    }
    if (isset($row_ds1[dbfield])) {
        $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][dbfield] = $row_ds1[dbfield];
    } else {
        $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][dbfield] = null;
    }

    $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][value] = 0;
    $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][ts] = 0;
}

$query = "select distinct source from _scadapicitemelement where scada = $diagram";
if ($showQueries == 1) {
    echo $query . $ln;
}

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds1 = mysql_fetch_array($ds1)) {
    if (!in_array($row_ds1[source], $urls)) {
        $urls[] = $row_ds1[source];
    }
}

$query = "select distinct source from _scadapictextitem where scada = $diagram";
if ($showQueries == 1) {
    echo $query . $ln;
}

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds1 = mysql_fetch_array($ds1)) {
    if (!in_array($row_ds1[source], $urls)) {
        $urls[] = $row_ds1[source];
    }
}



foreach ($urls as $url) {
    $username = "arm9";
    $password = "arm9Solar";
    $value = http_auth_get($url, $username, $password);
    $lines = explode("\r\n", $value);
    if (sizeof($lines) == 1) {
        $data[$url][OK] = false;
        continue;
    } else {
        $data[$url][OK] = true;
    }

    for ($index = 1; $index < sizeof($lines); $index++) {

        $line = explode(",", $lines[$index]);

        $dev = $data[$url][devices][$line[0]][$line[1]][device];

        $field = $data[$url][devices][$line[0]][$line[1]][dbfield];

        $newTs = $data[$url][devices][$line[0]][$line[1]][ts] = $line[3];


        $newValue = $data[$url][devices][$line[0]][$line[1]][value] = round($line[2], 3);

        if (!is_null($dev) && !is_null($field)) {
            $query = "replace _devicedatavalue (ts, device, field, value) values ($newTs, $dev, '$field', $newValue)";
            if ($showQueries == 1) {
                echo $query . $ln;
            }

            mysql_query($query, $verbindung) or die(mysql_error());
        }
    }
}



$calcitems = array();
$itemkeys = array();

$query = "select s.name, s.filename, si.*, se.* from _scadapic as s, _scadapiccalcitem as si, _scadapicitemelement as se where s.id = $diagram and si.scada= $diagram and se.scada = $diagram and si.item = se.item";
if ($showQueries == 1) {
    echo $query . $ln;
}

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());

while ($row_ds1 = mysql_fetch_array($ds1)) {

    $nullvalue = null;
    if ($row_ds1[sumtype] != 0) {
        $nullvalue = 0;
    } else {
        $nullvalue = 1;
    }
    $calcitems[$row_ds1[item]][x] = $row_ds1[x];
    $calcitems[$row_ds1[item]][y] = $row_ds1[y];
    $calcitems[$row_ds1[item]][font] = $row_ds1[font];
    $calcitems[$row_ds1[item]][device] = $row_ds1[device];
    $calcitems[$row_ds1[item]][dbfield] = $row_ds1[dbfield];
    $calcitems[$row_ds1[item]][sumtype] = $row_ds1[sumtype];
    $calcitems[$row_ds1[item]][factor] = $row_ds1[factor];
    $calcitems[$row_ds1[item]][offset] = $row_ds1[offset];
    $calcitems[$row_ds1[item]][preoffset] = $row_ds1[preoffset];
    $calcitems[$row_ds1[item]][value] = $nullvalue;
    $calcitems[$row_ds1[item]][potenz] = $row_ds1[potenz];
    if ($row_ds1[source] == null) {
        $calcitems[$row_ds1[item]][subsum] = $row_ds1[sumtype];
        $calcitems[$row_ds1[item]][subscada] = $row_ds1[scada];
        $calcitems[$row_ds1[item]][subitem] = $row_ds1[item];
    } else {
        if ($data[$row_ds1[source]][OK]) {
            $calcitems[$row_ds1[item]][values][] = ($data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][value] + $row_ds1[elementpreoffset]) * $row_ds1[elementfactor] + $row_ds1[elementpostoffset];
        } else {
            $calcitems[$row_ds1[item]][values][] = 0;
        }
    }if (!in_array($row_ds1[item], $itemkeys)) {
        $itemkeys[] = $row_ds1[item];
    }
}

for ($index = 0; $index < sizeof($calcitems); $index++) {
    if (!is_null($calcitems[$itemkeys[$index]][subsum])) {
        if ($showCalc) {
            echo "<br><br>********************************************<br><br>";
            print_r($calcitems[$itemkeys[$index]]);
            echo "<br> subsum = " . $calcitems[$itemkeys[$index]][subsum] . "<br>";
        }
        $calcitems[$itemkeys[$index]][value] = subcalc($calcitems[$itemkeys[$index]], $verbindung, $data, $showCalc);
    } elseif ($calcitems[$itemkeys[$index]][sumtype] != 0) {
        if ($showCalc) {

            echo "<br><br>********************************************<br><br>";
            print_r($calcitems[$itemkeys[$index]]);
            echo "<br> sumtype != 0<br>";
        }
        $calcitems[$itemkeys[$index]][value] = 0;
        for ($index2 = 0; $index2 < sizeof($calcitems[$itemkeys[$index]][values]); $index2++) {
            if ($showCalc) {
                echo "+" . $calcitems[$itemkeys[$index]][values][$index2] . "<br>";
            } $calcitems[$itemkeys[$index]][value] += $calcitems[$itemkeys[$index]][values][$index2];
        }
        if ($showCalc) {
            echo "=" . $calcitems[$itemkeys[$index]][value] . "<br><br>";
        }
    } else {
        if ($showCalc) {
            echo "<br><br>********************************************<br><br>";
            print_r($calcitems[$itemkeys[$index]]);
            echo "<br> sumtype = 0<br>";
        }
        $calcitems[$itemkeys[$index]][value] = 1;
        for ($index2 = 0; $index2 < sizeof($calcitems[$itemkeys[$index]][values]); $index2++) {
            if ($showCalc) {
                echo "*" . $calcitems[$itemkeys[$index]][values][$index2] . "<br>";
            } $calcitems[$itemkeys[$index]][value] *= $calcitems[$itemkeys[$index]][values][$index2];
        }if ($showCalc) {

            echo "=" . $calcitems[$itemkeys[$index]][value] . "<br><br>";
        }
    }
}


////

$query = "select s.name, s.filename, si.* from _scadapic as s, _scadapictextitem as si where s.id = $diagram and si.scada= $diagram";
if ($showQueries == 1) {
    echo $query . $ln;
}

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());

$textitems = array();

while ($row_ds1 = mysql_fetch_array($ds1)) {

    $textitems[$row_ds1[item]][x] = $row_ds1[x];
    $textitems[$row_ds1[item]][y] = $row_ds1[y];
    $textitems[$row_ds1[item]][font] = $row_ds1[font];
    $textitems[$row_ds1[item]][device] = $row_ds1[device];
    $textitems[$row_ds1[item]][sumtype] = $row_ds1[sumtype];

    $val = $data[$row_ds1[source]][devices][$row_ds1[sn]][$row_ds1[field]][value];
    //  echo ">$row_ds1[item]=$item$val<<br>";
    $textitems[$row_ds1[item]][value] = $val;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <!--[if lte IE 9]><script type="text/javascript" src="../functions/flot/excanvas.js"></script><![endif]-->
    </head>

    <BODY style="font-family: arial, sans-serif;" bgcolor="#ffffff" leftmargin="20" topmargin="15"
          onLoad="if(parent.navigation.location != '../navigation.php?park_no=<?php echo $park_no; ?>&subpark=<?php echo $subpark_id; ?>&meter_existent=<?php echo $meter_existent; ?>')  parent.navigation.location.href='../navigation.php?park_no=<?php echo $park_no; ?>&subpark=<?php echo $subpark_id; ?>&meter_existent=<?php echo $meter_existent; ?>';">

        <div style ="width: 100%; height: 100%">
            <canvas id="canvas" width="1884" height="998" style="border:1px solid #c3c3c3;">
                Your browser does not support the canvas element.
            </canvas>

            <script type="text/javascript">
                var c=document.getElementById("canvas");
                var ctx=c.getContext("2d");
                var img=new Image();
                img.onload = function(){
                    ctx.drawImage(img,0,0);
                    ctx.textAlign="right";
                    ctx.font="14pt Arial";
                    //   ctx.canvas.width  = 1884;
                    //   ctx.canvas.height = 998;

<?php
$tmpETotal = 0;
$tmpIScr = 0;
$tmpISc = 0;
foreach ($data as $devices) {

    foreach ($devices[devices] as $fields) {
        foreach ($fields as $key => $field) {

            //echo "alert(".$key.");";
            if ($diagram == 11 && $key == '"E_Total_G"') {
                $tmpETotal = $field[value];
            }if ($diagram == 11 && $key == '"Iscr_1"') {
                $tmpIScr = round($field[value], 3);
                //echo $tmpIScr."<br>";
            }
            if ($diagram == 11 && $key == '"Isc_1"') {
                $tmpISc = round($field[value], 3);
                //echo $tmpISc."<br>";
            }
            if (isset($field[x])) {
                echo 'ctx.font="' . $field[font] . '";';

                if (!$devices[OK]) {
                    echo 'ctx.font="9pt Arial";';
                    echo 'ctx.fillText("' . "No connection" . '",' . $field[x] . ',' . $field[y] . ');';
                } else {
                    if ($diagram == 11 && $key == '"Status"') {

                        if (!is_null($alarmtexts[$field[value]])) {
                            echo 'ctx.fillText("' . $alarmtexts[$field[value]] . '",' . $field[x] . ',' . $field[y] . ');';
                        } else {
                            echo 'ctx.fillText("Status ' . $field[value] . '",' . $field[x] . ',' . $field[y] . ');';
                        }
                    } else if ($diagram == 11 && $key == '"Isc_1"') {
                        echo 'ctx.fillText("' . number_format(($field[value] - $field[offset]) * $field[factor] * pow(10, $field[potenz]), 3, ".", "") . '",' . $field[x] . ',' . $field[y] . ');';
                    } else if ($diagram == 11 && $key == '"Iscr_1"') {
                        echo 'ctx.fillText("' . number_format(($field[value] - $field[offset]) * $field[factor] * pow(10, $field[potenz]), 3, ".", "") . '",' . $field[x] . ',' . $field[y] . ');';
                    } else {
                        echo 'ctx.fillText("' . number_format(($field[value] - $field[offset]) * $field[factor] * pow(10, $field[potenz]), 1, ".", "") . '",' . $field[x] . ',' . $field[y] . ');';
                    }
                }
            }
        }
    }
}


$tmpFont = "";

foreach ($textitems as $key => $device) {

    echo 'ctx.font="' . $device[font] . '";';
    $tmpFont = 'ctx.font="' . $device[font] . '";';
    //echo ">".$device[value]."<";
    echo 'ctx.fillText(\'' . $device[value] . '\',' . $device[x] . ',' . $device[y] . ');';
}


$e_total = 0;
foreach ($calcitems as $key => $device) {
    if ($diagram == 7 && $key == 42) {
        $e_total = $device[value];
        //echo "YEY".$e_total;
        continue;
    } else if ($diagram == 11 && $key == 2) {


        if ($tmpIScr != 0) {
            $e_total = 100 * ($tmpIScr - $tmpISc) / $tmpIScr;
        } else {
            $e_total = 0;
        }

        if ($test != 0) {
            echo "alert(" . $tmpIScr . ");";
            echo "alert(" . $tmpISc . ");";
            echo "alert(" . $e_total . ");";
        }

        echo 'ctx.font="' . $device[font] . '";';
        echo 'ctx.strokeStyle = "#000000";';
        echo 'ctx.fillText("' . number_format($e_total, 3, ".", "") . '",' . $device[x] . ',' . $device[y] . ');';
    } else {
        echo 'ctx.font="' . $device[font] . '";';
        echo 'ctx.strokeStyle = "#000000";';
        echo 'ctx.fillText("' . number_format((($device[value] + $device[preoffset]) * $device[factor] + $device[offset]) * pow(10, $device[potenz]), 1, ".", "") . '",' . $device[x] . ',' . $device[y] . ');';
    }
}

if ($diagram == 7) {

    $time = mktime(18, 30, 0, date("n"), date("j") - 1, date("Y"));
    $sum = 0;

    $query = "select device, max(value) as value from _devicedatavalue where ts < $time and ts > " . ($time - 86400) . " and field ='e_total' and device in (182, 181) group by device";
    if ($showQueries == 1) {
        echo $query . "\r\n";
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $sum+=$row_ds1[value];
    }

    $query = "select device, field, value from _devicedatavalue where ts > $time and field in ('PAC', 'PDC1', 'PDC2', 'PDC3') and device in (70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173) order by ts desc";
    if ($showQueries == 1) {
        echo $query . "\r\n";
    }

    $hits = 0;
    $dc = 0;
    $ac = 0;
    $values = array();

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {

        //echo "$row_ds1[device] $row_ds1[field] = $row_ds1[value]\r\n";
        if (is_null($values[$row_ds1[device]][$row_ds1[field]])) {
            // echo "=null";
            $values[$row_ds1[device]][$row_ds1[field]] = $row_ds1[value];
            $hits++;
            if ($row_ds1[field] == "PAC") {
                //     echo "pac";
                $ac+=$row_ds1[value];
            } else {
                $dc+=$row_ds1[value];
            }
            //   echo "\r\nac=$ac\r\ndc=$dc\r\n".$hits."\r\n\r\n";
            if ($hits == 280) {
                break;
            }
        }
    }

    echo $tmpFont;
    if ($e_total > 1) {
        echo 'ctx.fillText(\'' . number_format(($e_total - $sum) / 1, 1, ".", "") . '\',' . 928 . ',' . 158 . ');';
    } else {
        echo 'ctx.fillText(\'' . "No connection" . '\',' . 928 . ',' . 158 . ');';
    }

    echo 'ctx.fillText(\'' . number_format($ac, 1, ".", "") . '\',' . 930 . ',' . 485 . ');';
    echo 'ctx.fillText(\'' . number_format($dc, 1, ".", "") . '\',' . 214 . ',' . 495 . ');';
    if ($dc != 0) {
        echo 'ctx.fillText(\'' . number_format(100 * $ac / ($dc), 1, ".", "") . '\',' . 1230 . ',' . 156 . ');';
    } else {
        echo 'ctx.fillText(\'' . "-", '\',' . 1230 . ',' . 156 . ');';
    }
} else if ($diagram == 11) {

    echo 'ctx.fillText(\'' . number_format(($yestervalue - $monthvalue + $tmpETotal), 1, ".", ""), '\',' . 360 . ',' . 345 . ');';
    echo 'ctx.fillText(\'' . number_format(($yestervalue - $yearvalue + $tmpETotal), 1, ".", ""), '\',' . 360 . ',' . 415 . ');';

    if ($e_total > 2) {
        echo 'ctx.fillText(\'' . "Panels to be cleaned", '\',' . 830 . ',' . 410 . ');';
    } else {
        echo 'ctx.fillText(\'' . "-", '\',' . 830 . ',' . 410 . ');';
    }
}
?>
        


    };
    
    img.src="<?php echo $pic; ?>";
    /*
          oldCanvas = c.toDataURL("image/png");
                c.width  = window.innerWidth;
                c.height = window.innerHeight;
                var img2 = new Image();
                img2.src = oldCanvas;
                img2.onload = function(){
                    c.getContext('2d').drawImage(img2, 0, 0, 1884, 998);
                }
     */
    
    var Links = new Array(); // Links information
    var hoverLink = ""; // Href of the link which cursor points at
    ctx.fillStyle = "#000000"; // Default blue link color
    //ctx.textBaseline = "top"; // Makes left top point a start point for rendering text

    // Draw the link
    function drawLink(x,y,href,title, size){
        var linkTitle = title,
        linkX = x,
        linkY = y;
        ctx.font = ""+size+"px Courier New";
        ctx.textAlign="left";
                    
        var linkWidth = ctx.measureText(linkTitle).width,
            
            
        linkHeight = parseInt(ctx.font); // Get lineheight out of fontsize
        
        ctx.fillStyle = 
<?php
if ($diagram == 11) {
    echo '"#CC5855";';
} else {
    echo '"white";';
}
?>
        
        // Draw the link
        ctx.fillRect(linkX, linkY, linkWidth, linkHeight)
            
        ctx.fillText(linkTitle, linkX, linkY);
                    
        // Underline the link (you can delete this block)
        ctx.beginPath();
        ctx.moveTo(linkX, linkY + linkHeight);
        ctx.lineTo(linkX + linkWidth, linkY + linkHeight);
        ctx.lineWidth = 1;
        ctx.strokeStyle = "#000000";
        ctx.stroke();

        // Add mouse listeners
        canvas.addEventListener("mousemove", on_mousemove, false);
        canvas.addEventListener("click", on_click, false);

        // Add link params to array
        Links.push(x + ";" + y + ";" + linkWidth + ";" + linkHeight + ";" + href);
    }

    // Link hover
    function on_mousemove (ev) {
        var x, y;

        // Get the mouse position relative to the canvas element
        if (ev.layerX || ev.layerX == 0) { // For Firefox
            x = ev.layerX;
            y = ev.layerY;
        }

        // Link hover
        for (var i = Links.length - 1; i >= 0; i--) {
            var params = new Array();

            // Get link params back from array
            params = Links[i].split(";");

            var linkX = parseInt(params[0]),
            linkY = parseInt(params[1]),
            linkWidth = parseInt(params[2]),
            linkHeight = parseInt(params[3]),
            linkHref = params[4];

            // Check if cursor is in the link area
            if (x >= linkX && x <= (linkX + linkWidth) && y >= linkY && y <= (linkY + linkHeight)){
                document.body.style.cursor = "pointer";
                hoverLink = linkHref;
                break;
            }
            else {
                document.body.style.cursor = "";
                hoverLink = "";
            }
        };
    }

    // Link click
    function on_click(e) {
        if (hoverLink){
            window.open(hoverLink, "_self"); // Use this to open in new tab
            //window.location = hoverLink; // Use this to open in current window
        }
    }
    
<?
$query = "select * from _scadapiclink where scada = $diagram";

if ($showQueries == 1) {
    echo $query . "<br>";
}
$ds2 = mysql_query($query, $verbindung) or die(mysql_error());

while ($row_ds2 = mysql_fetch_array($ds2)) {
    echo 'drawLink(' . $row_ds2[x] . ',' . $row_ds2[y] . ',"' . $row_ds2[url] . '","' . $row_ds2[text] . '",' . $row_ds2[size] . ');';
}
mysql_free_result($ds2);


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

