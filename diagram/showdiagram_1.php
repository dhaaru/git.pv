<?php
//ini_set("display_errors", 0);

include ('../functions/allg_functions.php');

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

function subcalc($device, $verbindung, $data) {

    $value = null;
    if ($device[sumtype] != 0) {
        $value = 0;
    } else {
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
        //$ndevice[value] = $value;
        $ndevice[potenz] = $row_ds1[potenz];
        if ($row_ds1[source] == null) {
            $ndevice[subsum] = $row_ds1[subsum];
            $ndevice[subscada] = $row_ds1[subscada];
            $ndevice[subitem] = $row_ds1[subitem];
            if ($device[sumtype] != 0) {
                $value += subcalc($ndevice, $verbindung, $data);
            } else {
                $value *= subcalc($ndevice, $verbindung, $data);
            }
        } else {
            if ($device[sumtype] != 0) {
                $value += $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][value];
            } else {
                $value *= $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][value];
            }
        }
    }

    return $value;
}

$diagram = $diagram;


$data = array();
$pic = "";
$name = "";

$urls = array();


//$ln = "\r\n";
$ln = "<br>";

require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

if (is_null(get_user())) {
    return;
}
if (!(get_user() == "pvadmin")) {
    $query = "select * from _userscada where pic = $diagram and user= " . get_user_id();
    if ($showQueries == 1) {
        echo $query . $ln;
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    if (mysql_num_rows($ds1) == 0) {
        return;
    }
}

$query = "select s.name, s.filename, si.* from _scadapic as s, _scadapicitem as si where s.id = $diagram and si.scada= $diagram";
if ($showQueries == 1) {
    echo $query . $ln;
}

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds1 = mysql_fetch_array($ds1)) {
    $pic = $row_ds1[filename];
    $name = $row_ds1[name];

    if (!in_array($row_ds1[source], $urls)) {
        $urls[] = $row_ds1[source];
    }

    $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][potenz] = $row_ds1[potenz];
    $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][x] = $row_ds1[x];
    $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][y] = $row_ds1[y];
    $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][font] = $row_ds1[font];
    if (isset($row_ds1[device])) {
        $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][device] = $row_ds1[device];
    } else {
        $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][device] = null;
    }
    if (isset($row_ds1[dbfield])) {
        $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][dbfield] = $row_ds1[dbfield];
    } else {
        $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][dbfield] = null;
    }

    $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][value] = 0;
    $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][ts] = 0;
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

    for ($index = 1; $index < sizeof($lines); $index++) {

        $line = explode(",", $lines[$index]);

        $dev = $data[$url][$line[0]][$line[1]][device];

        $field = $data[$url][$line[0]][$line[1]][dbfield];

        $newTs = $data[$url][$line[0]][$line[1]][ts] = $line[3];
        $newValue = $data[$url][$line[0]][$line[1]][value] = $line[2];

        if (!is_null($dev) && !is_null($field)) {
            $query = "replace _devicedatavalue (ts, device, field, value) values ($newTs, $dev, '$field', $newValue)";
            if ($showQueries == 1) {
                echo $query . $ln;
            }

            mysql_query($query, $verbindung) or die(mysql_error());
        }
    }
}

$query = "select s.name, s.filename, si.*, se.* from _scadapic as s, _scadapiccalcitem as si, _scadapicitemelement as se where s.id = $diagram and si.scada= $diagram and se.scada = $diagram and si.item = se.item";
if ($showQueries == 1) {
    echo $query . $ln;
}

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());

$calcitems = array();
$itemkeys = array();

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
    $calcitems[$row_ds1[item]][value] = $nullvalue;
    $calcitems[$row_ds1[item]][potenz] = $row_ds1[potenz];
    if ($row_ds1[source] == null) {
        $calcitems[$row_ds1[item]][subsum] = $row_ds1[sumtype];
        $calcitems[$row_ds1[item]][subscada] = $row_ds1[scada];
        $calcitems[$row_ds1[item]][subitem] = $row_ds1[item];
    } else {
        $calcitems[$row_ds1[item]][values][] = $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][value];
    }if (!in_array($row_ds1[item], $itemkeys)) {
        $itemkeys[] = $row_ds1[item];
    }
}


for ($index = 0; $index < sizeof($calcitems); $index++) {
    if ($calcitems[$itemkeys[$index]][subsum] != null) {
        $calcitems[$itemkeys[$index]][value] = subcalc($calcitems[$itemkeys[$index]], $verbindung, $data);
    } elseif ($calcitems[$itemkeys[$index]][sumtype] != 0) {
        for ($index2 = 0; $index2 < sizeof($calcitems[$itemkeys[$index]][values]); $index2++) {
            $calcitems[$itemkeys[$index]][value] += $calcitems[$itemkeys[$index]][values][$index2];
        }
    } else {
        for ($index2 = 0; $index2 < sizeof($calcitems[$itemkeys[$index]][values]); $index2++) {
            $calcitems[$itemkeys[$index]][value] *= $calcitems[$itemkeys[$index]][values][$index2];
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
    
    $val = $data[$row_ds1[source]][$row_ds1[sn]][$row_ds1[field]][value];
  //  echo ">$row_ds1[item]=$item$val<<br>";
    $textitems[$row_ds1[item]][value] = $val;
    
}





?>

<!DOCTYPE html>
<html>
    <head>
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
foreach ($data as $devices) {
    foreach ($devices as $fields) {
        foreach ($fields as $field) {

            if (isset($field[x])) {
                echo 'ctx.font="' . $field[font] . '";';


                echo 'ctx.fillText("' . number_format($field[value] * pow(10, $field[potenz]), 2, ".", "") . '",' . $field[x] . ',' . $field[y] . ');';
            }
        }
    }
}

foreach ($calcitems as $device) {
    echo 'ctx.font="' . $device[font] . '";';
    echo 'ctx.fillText("' . number_format($device[value] * pow(10, $device[potenz]), 2, ".", "") . '",' . $device[x] . ',' . $device[y] . ');';
}

foreach ($textitems as $device) {
    echo 'ctx.font="' . $device[font] . '";';
    //echo ">".$device[value]."<";
    echo 'ctx.fillText(\''.$device[value].'\',' . $device[x] . ',' . $device[y] . ');';
    
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

