<?php
$selectedValue = $_POST['select'];

$args = $args;
$diffs = $diffs;
$sums = $sums;
$avgs = $avgs;
$hideClear = $hideClear;
$stamp = $stamp;
$endstamp = $endstamp;

//&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp .
    

$selectedOption=0;


$argsSplit = split(";;", $args);
$argNamesSplit = split(";", $argNames);
?>

<html>
    <head>

        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />

    </head>

    <body>
        <form method="post" style="padding: 0px">
            <select  style="padding: 0px" name="select" onchange="this.form.submit();">

                <?php
                for ($i = 0; $i < sizeof($argsSplit); $i++) {
                    if ($argNamesSplit[$i] == $selectedValue) {
                        echo "<option selected>";
                        $selectedOption=$i;
                    } else {
                        echo "<option>";
                    }
                    echo $argNamesSplit[$i];
                    echo "</option>";
                }
                ?>


            </select>
        </form>
        
        <!--0,1,0,478,PAC,EM%20AC%20Power,15,kW,12;0,1,0,478,IAC1,EM%20AC%20Current%201,15,A,4;0,1,0,478,IAC2,EM%20AC%20Current%202,15,A,5;0,1,0,478,IAC3,EM%20AC%20Current%203,15,A,6;0,1,0,478,UAC1,EM%20AC%20Voltage%201,15,V,11;0,1,0,478,UAC2,EM%20AC%20Voltage%202,15,V,12;0,1,0,478,UAC3,EM%20AC%20Voltage%203,15,V,13;0,1,0,477,PAC,Kaco%20AC%20Power,15,kW,10;0,1,0,477,IAC1,Kaco%20AC%20Current%201,15,A,19;0,1,0,477,IAC2,Kaco%20AC%20Current%202,15,A,0;0,1,0,477,IAC3,Kaco%20AC%20Current%203,15,A,18;0,1,0,477,UAC1,Kaco%20AC%20Voltage%201,15,V,12;0,1,0,477,UAC2,Kaco%20AC%20Voltage%202,15,V,13;0,1,0,477,UAC3,Kaco%20AC%20Voltage%203,15,V,14;
        $args = $args;
$diffs = $diffs;
$sums = $sums;
$avgs = $avgs;
$hideClear = $hideClear;
$stamp = $stamp;
$endstamp = $endstamp;
        -->
        
        <iframe id="framea"  style="padding: 0px" width="99%" height="90%" SRC="argdiagram5.php?args=<?php echo $argsSplit[$selectedOption]."&diffs=".$diffs."&sums=".$sums."&avgs=".$avgs."&hideDelta=".$hideDelta."&hideClear=".$hideClear."&stamp=".$stamp."&endstamp=".$endstamp?>" border="0"></iframe>
        
    </body>
</html>

