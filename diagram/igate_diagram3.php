<?php

function startsWith($haystack, $needle, $case = true) {
    if ($case)
        return strpos($haystack, $needle, 0) === 0;

    return stripos($haystack, $needle, 0) === 0;
}

function endsWith($haystack, $needle, $case = true) {
    $expectedPosition = strlen($haystack) - strlen($needle);

    if ($case)
        return strrpos($haystack, $needle, 0) === $expectedPosition;

    return strripos($haystack, $needle, 0) === $expectedPosition;
}

$usr = $_SESSION['user'];
if (!($usr == "pvadminin")) {
    echo $usr;
    echo "you need to login before you can see the diagram";
    return;
}

$args = $args;
$argsArray = split(";", $args);
$selectedData = array();

$argIndex = 0;
if (strlen($defaluts) == 0) {
    foreach ($argsArray as $arg) {

        if (sizeof(split(",", $arg)) != 9) {

            continue;
        }
        if ($argIndex == 0) {
            $defaults = "0";
        } else {
            $defaults.="," . $argIndex;
        }
        $argIndex++;
    }
}

foreach ($argsArray as $arg) {
    $tmpArg = split(",", $arg);
    $selectedData[$tmpArg[3]][$tmpArg[4]] = 1;
}



date_default_timezone_set('UTC');
$ln = "<br>";

set_time_limit(900);

$igate = $igate;

if (!isset($showAll)) {
    $showAll = 0;
}
if (!isset($delta)) {
    $delta = 0;
}

if (!$_SESSION['id']) {
    session_start();
    $_SESSION['id'] = session_id();
}
$sourcefile = "igate_diagram3.php";


require_once('connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);


$typeId = 100000000;

$children = array();
$types = array();

$query = "SELECT distinct f.igate, f.sn, f.device, a.bezeichnung, d.type, s.bezeichnung as sub from _field as f, _device as d, subparks as s, areas as a, igates as g where f.igate = g.igate_id and g.area_id = a.area_id and d.deviceid = f.device and s.id = g.subpark_id and g.subpark_id = a.subpark_id order by f.igate, f.sn";

if ($showQueries == 1) {
    echo $query . $ln;
}

$currentType = "";
$currentGate = -1;

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());

$currentIsSelectedType = false;
$selectedDevices = array();

while ($row_ds1 = mysql_fetch_array($ds1)) {
    $subpark = $row_ds1[sub];
    $area = $row_ds1[bezeichnung];
    $igateId = $row_ds1[igate];
    $device = $row_ds1[device];
    $type = $row_ds1[type];
    $devName = $row_ds1[sn] . "(" . ($row_ds1[device]) . ")";

    if (!in_array("d.add($igateId, -1, '" . $subpark . " " . $area . " iGate $igateId');\n", $children)) {
        $children[] = "d.add($igateId, -1, '" . $subpark . " " . $area . " iGate $igateId');\n";
    }

    $typeExtra = "";

    if ($currentGate != $igateId || $currentType != $type) {
        $typeId++;
        $currentGate = $igateId;
        $currentType = $type;
        $currentIsSelectedType = false;
        if ($selectedType == $currentType && $selectedGate == $currentGate) {
            $currentIsSelectedType = true;
            $children[] = "d.add(" . ($typeId) . ", " . $igateId . ", '**" . $typeExtra . $type . $typeExtra . "**', 'igate_diagram3.php?selectedType=" . $type . "&selectedGate=" . $igateId . "&defaults=" . $defaults . "&args=" . $args . "&arg=" . $arg . "', '" . $type . "', '_self');\n";
        } else {

            $children[] = "d.add(" . ($typeId) . ", " . $igateId . ", '" . $type . "', 'igate_diagram3.php?selectedType=" . $type . "&selectedGate=" . $igateId . "&defaults=" . $defaults . "&args=" . $args . "&arg=" . $arg . "', '" . $type . "', '_self');\n";
        }
    }
    if ($currentIsSelectedType) {
        $selectedDevices[] = $device;
    }

    if (key_exists($device, $selectedData)) {
        $typeExtra = "+";
    }

    if ($selectedDevice == $device) {
        $children[] = "d.add(" . ($igateId + $device * 10000) . ", " . $typeId . ", '**" . $typeExtra . $devName . $typeExtra . "**', 'igate_diagram3.php?selectedDevice=" . $device . "&defaults=" . $defaults . "&args=" . $args . "&arg=" . $arg . "', '" . $type . "', '_self');\n";
    } else {
        $children[] = "d.add(" . ($igateId + $device * 10000) . ", " . $typeId . ", '" . $typeExtra . $devName . $typeExtra . "', 'igate_diagram3.php?selectedDevice=" . $device . "&defaults=" . $defaults . "&args=" . $args . "&arg=" . $arg . "', '" . $type . "', '_self');\n";
    }
}

$tree2 = array();
$fieldIndex = 0;

if (!is_null($selectedDevice) && !($selectedDevice == "")) {
    $query = "SELECT * from _field where device = $selectedDevice";

    if ($showQueries == 1) {
        echo $query . $ln;
    }


    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $fieldName = "";
        if ($row_ds1[name] == "") {
            $fieldName = $row_ds1[field];
        } else {
            $fieldName = $row_ds1[name];
        }
        $newArg = ";" . $row_ds1[preoffset] . "," . $row_ds1[factor] . "," . $row_ds1[offset] . "," . $row_ds1[device] . "," . $row_ds1[field] . "," . $fieldName . ",15," . $row_ds1[unit] . ",0";
        if (key_exists($row_ds1[field], $selectedData[$row_ds1[device]])) {
            $tmpArgs = split(";", $args);
            $newArgs = "";
            foreach ($tmpArgs as $tmpArg) {
                $tmpArgSplit = split(",", $tmpArg);
                if (sizeof($tmpArgSplit) != 9 || ( $tmpArgSplit[3] == $row_ds1[device] && $tmpArgSplit[4] == $row_ds1[field] )) {
                    
                } else {
                    $newArgs.= ";" . $tmpArg;
                }
            }


            $tree2[] = "d2.add(" . ($fieldIndex++) . ", -1, '+" . $fieldName . "+', 'igate_diagram3.php?args=" . $newArgs . "&arg=" . $arg . "&selectedDevice=" . $selectedDevice . "&defaults=" . $defaults . "," . $argIndex . "', '" . $row_ds1[field] . "', '_self');\n";
        } else {
            $tree2[] = "d2.add(" . ($fieldIndex++) . ", -1, '" . $fieldName . "', 'igate_diagram3.php?args=" . $args . $newArg . "&arg=" . $arg . "&selectedDevice=" . $selectedDevice . "&defaults=" . $defaults . "," . $argIndex . "', '" . $row_ds1[field] . "', '_self');\n";
        }
    }
} else if (!is_null($selectedType) && !($selectedType == "")) {
    if (startsWith($selectedType, "LDFI10_BTR") || startsWith($selectedType, "LRAS_BTR") || startsWith($selectedType, "LPV_BTR") || startsWith($selectedType, "LDM_BTR")) {
        
    } else {
        $query = "SELECT * from _typedefaultfield where type = '$selectedType'";

        if ($showQueries == 1) {
            echo $query . $ln;
        }


        $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
        while ($row_ds1 = mysql_fetch_array($ds1)) {
            $fieldName = "";
            if ($row_ds1[name] == "") {
                $fieldName = $row_ds1[field];
            } else {
                $fieldName = $row_ds1[name];
            }

            $newArg = "";
            $newDefaults = "";
            $colorIndex = 0;
            foreach ($selectedDevices as $oneDevice) {
                $newArg.=$row_ds1[preoffset] . "," . $row_ds1[factor] . "," . $row_ds1[offset] . "," . $oneDevice . "," . $row_ds1[field] . "," . $oneDevice . "." . $fieldName . ",15," . $row_ds1[unit] . "," . ($colorIndex++) . ";";
                $newDefaults.="," . ($argIndex++);
            }

            $tree2[] = "d2.add(" . ($fieldIndex++) . ", -1, '" . $fieldName . "', 'igate_diagram3.php?args=" . $args . $newArg . "&arg=" . $arg . "&selectedType=" . $selectedType . "&defaults=" . $defaults . $newDefaults . "', '" . $row_ds1[field] . "', '_self');\n";
        }
    }
}

$argTree = array();
$argIndex = 0;


foreach ($argsArray as $arg) {
    $argLine = split(",", $arg);
    if (sizeof($argLine) != 9) {
        continue;
    }

    $argTree[] = "d3.add(" . ($argIndex) . ", -1, '" . $argLine[5] . "($argLine[3].$argLine[4])', 'igate_diagram3.php?selectedArgIndex=" . ($argIndex++) . "&selectedArg=" . $arg . "&args=" . $args . "&selectedType=" . $selectedType . "&selectedDevice=" . $selectedDevice . "&selectedGate=" . $selectedGate . "&defaults=" . $defaults . "', '" . $argLine[5] . "', '_self');\n";
}





ksort($children);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head runat="server">
        <title>Diagram Editor</title>

        <link rel="StyleSheet" href="dtree.css" type="text/css" />
        <script type="text/javascript" src="dtree.js"></script>
        <script type="text/javascript" src="diagram/jscolor/jscolor.js"></script>
        <script type="text/javascript">
            function resizeViewArea() {
                //Wrap your form contents in a div and get it's offset height
                var heightOfForm = document.getElementById('calendar1').offsetHeight;
                //Get Height of body (accounting for user installed toolbars)
                var heightOfBody = document.body.clientHeight;
                var buffer = 0; //Accounts for misc padding etc
                //Set the height of the Text Area Dynamically
                document.getElementById('area').style.height = 
                    (heightOfBody - heightOfForm) - buffer;
                //NOTE: For extra pinnache' add onresize="resizeTextArea()" to the body
            }
        </script>

        <link href="css/scroll.css" rel="stylesheet" type="text/css">
        <link href="css/text.css" rel="stylesheet" type="text/css">
        <link href="css/style_add.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            html,body,form {
                margin: 0;
                padding: 0;
                height: 100%;
                width: 100%;
                font-family: arial, sans-serif;
            }
        </style>
    </head>
    <body onload=" resizeViewArea()" onresize=" resizeViewArea()"> 
        <div style="height: 100%; width: 100%">

            <div style="height: 100%; width: 100%;" id="area">
                <div style="overflow: auto; float: left; height: 100%; width: 20%">


                    <div style="overflow: auto; height: 25%; width: 99%; border-width: 1px; border-style: solid" >
                        <div class="dtree" id ="parkDiv" style="overflow: auto; float: left; height: 100%; width: 100%" >
                            <p><a href="javascript: d.openAll();">open all</a> | <a href="javascript: d.closeAll();">close all</a></p>

                            <script type="text/javascript">  
                                        
                                    
                                d = new dTree('d');
<?php
foreach ($children as $child) {
    echo $child;
}
?>
    document.write(d);
    
                            </script>
                        </div>
                    </div>
                    <div style="overflow: auto; height: 24.5%; width: 99%; border-width: 1px; border-style: solid" >
                        <div class="dtree" id ="typeDiv" style="overflow: auto; float: left; height: 100%; width: 100%" >

                            <script type="text/javascript">  
                                        
                               
                                d2 = new dTree('d2');
                                    
<?php
foreach ($tree2 as $child) {
    echo $child;
}
?>
    document.write(d2);
                            </script>
                        </div>


                    </div>

                    <div style="overflow: auto; height: 24.5%; width: 99%; border-width: 1px; border-style: solid" >
                        <div class="dtree" id ="typeDiv" style="overflow: auto; float: left; height: 100%; width: 100%" >

                            <script type="text/javascript">  
                                        
                               
                                d3 = new dTree('d3');
                                    
<?php
foreach ($argTree as $child) {
    echo $child;
}
?>
    document.write(d3);
                            </script>
                        </div>


                    </div>


                    <div style="overflow: auto; height: 24.5%; width: 99%; border-width: 1px; border-style: solid" >
                        <form action="updateDiagramArg.php" target="_self" method="get">
                            <?php
                            $selectedArgSplit = split(",", $selectedArg);
                            $defaultSplit = split(",", $defaults);
//
                            ?> 
                            <input type="hidden" name="args" value="<?php echo $args;?>">
                            <input type="hidden" name="arg" value="<?php echo $arg;?>">
                            <input type="hidden" name="selType" value="<?php echo $selectedType;?>">
                            <input type="hidden" name="selGate" value="<?php echo $selectedGate;?>">
                            <input type="hidden" name="selDev" value="<?php echo $selectedDevice;?>">
                            <input type="hidden" name="selArg" value="<?php echo $selectedArg;?>">
                            <input type="hidden" name="selArgIndex" value="<?php echo $selectedArgIndex;?>">
                            <input type="hidden" name="default" value="<?php echo $defaults;?>">
                            
                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="pre Offset">
                            <input type="text" name="preOffset" size="20" class="textfeld" 
                                   value="<?php echo $selectedArgSplit[0]; ?>">

                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="factor">
                            <input type="text" name="factor" size="20" class="textfeld" 
                                   value="<?php echo $selectedArgSplit[1]; ?>">

                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="post Offset">
                            <input type="text" name="postOffset" size="20" class="textfeld" 
                                   value="<?php echo $selectedArgSplit[2]; ?>">

                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="device">
                            <input type="text" name="device" size="20" class="textfeld" readonly style="background-color: #AAAAAA"
                                   value="<?php echo $selectedArgSplit[3]; ?>">

                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="field">
                            <input type="text" name="field" size="20" class="textfeld" readonly style="background-color: #AAAAAA"
                                   value="<?php echo $selectedArgSplit[4]; ?>">

                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="name">
                            <input type="text" name="name" size="20" class="textfeld" 
                                   value="<?php echo $selectedArgSplit[5]; ?>">

                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="resolution">
                            <input type="text" name="resolution" size="20" class="textfeld" 
                                   value="<?php echo $selectedArgSplit[6]; ?>">

                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="unit">
                            <input type="text" name="unit" size="20" class="textfeld" 
                                   value="<?php echo $selectedArgSplit[7]; ?>">

                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="color">
                            <input style="float: left" type="text" name="colorField" size="20" class="textfeld" 
                                   value="<?php echo $selectedArgSplit[8]; ?>">
                            
                            <input style="float: left; background-color: #AAAAAA" type="text" size="15" class="textfeld" readonly
                                   value="default">
                            <input type="text" name="defaultValue" size="20" class="textfeld" 
                                   value="<?php echo in_array($selectedArgIndex, $defaultSplit); ?>">
                            <input style="float: left" type="submit" name="graphUpdate" value="Update">

                        </form>
                    </div>

                </div>
                <div style="float: left; height: 100%; width: 79%; overflow: auto">
                    <?php
                    $stamp = mktime(18, 30, 0, date('n'), date('d') - 3, date('Y'), 0);
                    $endstamp = mktime(19, 30, 0, date('n'), date('d'), date('Y'), 0);


                    $diag = "argdiagram5.php?args=$args&stamp=$stamp&endstamp=$endstamp" . "&defaults=" . $defaults . "&sourcefile=" . $sourcefile . "&echoArgs=1";
                    $diag = str_replace("°", "DEG", $diag);
                    ?>
                    <iframe name ="diagramframe" src='diagram/<?php echo $diag; ?>' style="width: 99%; height: 99%"></iframe>
                </div>
            </div>
        </div>
    </body>
</html>
