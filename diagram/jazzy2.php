<?php
$pic = $pic;
$diffs = split(";", $diffs);
$sums = split(";", $sums);
$avgs = split(";", $avgs);
$currs = split(";", $currs);
$links = split(";", $links);

$displayItems = array();
require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

date_default_timezone_set('UTC');
//nt $hour = date("H") [, int $minute = date("i") [, int $second = date("s") [, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") [, int $is_dst = -1 ]]]]]]] )
$endstamp = mktime(18, 30, 0);
$stamp = $endstamp - 24 * 3600;

foreach ($diffs as $avg) {
    $avgItems = split(",", $avg);
    if (sizeof($avgItems) != 16) {

        continue;
    }
    $currentValue = array();
    $erg = 0;
    $query = "select max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts < $stamp and ts > ($stamp-24*3600)";
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

    $query = "select floor(max(ts)/3600) as ts, max(((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ")) as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < ($endstamp)";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $topTs = 0;
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $topTs = date("H:i:s", ($row_ds2[ts] * 3600 + 19800));
        $erg += $row_ds2[value];
    }


    $values = 0;
    $avgValue = 0;

    $currentValue[ts] = $topTs;
    $currentValue[value] = $erg;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];
    $currentValue[x] = $avgItems[14];
    $currentValue[y] = $avgItems[15];


    $displayItems[$avgItems[6]][] = $currentValue;

    mysql_free_result($ds2);
}


foreach ($avgs as $avg) {
    $avgItems = split(",", $avg);
    if (sizeof($avgItems) != 16) {
        continue;
    }
    $currentValue = array();
    $query = "select floor(ts/3600) as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < $endstamp order by ts";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    $topTs = 0;
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $topTs = date("H:i:s", $row_ds2[ts] * 3600 + 19800);
        $currentValue[$row_ds2[ts]][] = $row_ds2[value];
    }

    $values = 0;
    $avgValue = 0;
    foreach ($currentValue as $value) {
        $avgValue+= array_sum($value) / sizeof($value);
        $values++;
    }

    $avgValue/=$values;

    $currentValue[ts] = $topTs;
    $currentValue[value] = $avgValue;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];
    $currentValue[x] = $avgItems[14];
    $currentValue[y] = $avgItems[15];

    $displayItems[$avgItems[6]][] = $currentValue;

    mysql_free_result($ds2);
}
//sums=;0,13,0 ,451,U3, null,6,Total%20Irradiation,yellow,2,W/m²
//&avgs=-22.68,1.15,0 ,451,U4, null,5,Avg.%20Temperature,red,2,°C;&sums=0,13,0,451,U3,null

foreach ($sums as $sum) {
    $avgItems = split(",", $sum);
    if (sizeof($avgItems) != 16) {

        continue;
    }
    $currentValue = array();
    $query = "select floor(ts/3600) as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp and ts < $endstamp order by ts";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }

    $topTs = 0;

    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds2 = mysql_fetch_array($ds2)) {
        $topTs = $row_ds2[ts];
        $currentValue[$row_ds2[ts]][] = $row_ds2[value];
    }

    $avgValue = 0;
    foreach ($currentValue as $value) {
        $avgValue+= array_sum($value) / sizeof($value);
    }

    mysql_free_result($ds2);

    $currentValue[ts] = date("H:i:s", $topTs * 3600 + 19800);

    $currentValue[value] = $avgValue;
    $currentValue[text] = $avgItems[7];
    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];
    $currentValue[x] = $avgItems[14];
    $currentValue[y] = $avgItems[15];

    $displayItems[$avgItems[6]][] = $currentValue;
}

foreach ($currs as $sum) {
    $avgItems = split(",", $sum);
    if (sizeof($avgItems) != 16) {
        continue;
    }
    $currentValue = array();
    $query = "select ts as ts, ((value+" . $avgItems[0] . ")*" . $avgItems[1] . "+" . $avgItems[2] . ") as value from _devicedatavalue where device = $avgItems[3] and field='$avgItems[4]' and ts > $stamp order by ts desc";
    if ($avgItems[5] != 'null') {
        $query.=" and value > $avgItems[5]";
    }

    if ($showQueries == 1) {
        echo $query . "<br>";
    }
    $ds2 = mysql_query($query, $verbindung) or die(mysql_error());
    $row_ds2 = mysql_fetch_array($ds2);



    mysql_free_result($ds2);
    $currentValue[value] = $row_ds2[value];

    $currentValue[text] = $avgItems[7];
    $currentValue[ts] = date("H:i:s", (floor($row_ds2[ts] / 3600) * 3600 + 19800));

    $currentValue[color] = $avgItems[8];
    $currentValue[decimals] = $avgItems[9];
    $currentValue[unit] = $avgItems[10];
    $currentValue[size] = $avgItems[11];
    $currentValue[type] = $avgItems[12];
    $currentValue[font] = $avgItems[13];
    $currentValue[x] = $avgItems[14];
    $currentValue[y] = $avgItems[15];


    $displayItems[$avgItems[6]][] = $currentValue;
}



ksort($displayItems);
?>


<!DOCTYPE html>

<!-- This page shows how to add multiple links to <canvas> (by Yakovenko Max) -->

<html>
    <head>
        <title>Canvas Links Example</title>

        <script>  
            function OnLoad(){
                // Get canvas
                var canvas = document.getElementById("myCanvas");

                // 2d context 
                var ctx = canvas.getContext("2d");
                //               ctx.translate(0, 0.5); // * Move the canvas by 0.5px to fix blurring

 
                // Photo
                var img = new Image();
                img.src="<?php echo $pic; ?>";
                img.onload = function(){
                    ctx.drawImage(img, 0, 0); // Use -0.5px on photos to prevent blurring caused by * fix
                    
                                    <?php
foreach ($displayItems as $myitem) {
    foreach ($myitem as $myelement) {
        //echo "ctx.font=$myelement[size]pt $myelement[font];";
         echo 'ctx.font="' . $myelement[size] . "pt " . $myelement[font] . '";';
         echo 'ctx.fillStyle="'.$myelement[color].'";';
        if ($showTs != 0) {
            echo 'ctx.fillText("' . $myelement[text] . "(" . $myelement[ts] . ')=' . number_format($myelement[value], $myelement[decimals], '.', '') . $myelement[unit] . '",' . $myelement[x] . ',' . $myelement[y] . ');';
        } else {
            echo 'ctx.fillText("' . $myelement[text] . '=' . number_format($myelement[value], $myelement[decimals], '.', '') . $myelement[unit] . '",' . $myelement[x] . ',' . $myelement[y] . ');';
        }

        //             echo '<p style = "color:' . $myelement[color] . ';font-family:' . $myelement[font] . ';font-size:' . $myelement[size] . ' px">' . $myelement[text] . '<br>' . number_format($myelement[value], $myelement[decimals], '.', '') . $myelement[unit] . '</p>';
    }
}
?>
                    
                }

                // Text
                ctx.fillStyle = "#000000";
                ctx.font = "15px Tahoma";
                ctx.textBaseline = "top"; 
                ctx.fillText("Username", 95, 65);

                // ***** Magic starts here *****

                // Links
                var Links = new Array(); // Links information
                var hoverLink = ""; // Href of the link which cursor points at
                ctx.fillStyle = "#0000ff"; // Default blue link color
                ctx.font = "15px Courier New"; // Monospace font for links
                ctx.textBaseline = "top"; // Makes left top point a start point for rendering text

                // Draw the link
                function drawLink(x,y,href,title){
                    var linkTitle = title,
                    linkX = x,
                    linkY = y,
                    linkWidth = ctx.measureText(linkTitle).width,
                    linkHeight = parseInt(ctx.font); // Get lineheight out of fontsize

                    // Draw the link
                    ctx.fillText(linkTitle, linkX, linkY);

                    // Underline the link (you can delete this block)
                    ctx.beginPath();
                    ctx.moveTo(linkX, linkY + linkHeight);
                    ctx.lineTo(linkX + linkWidth, linkY + linkHeight);
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = "#0000ff";
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
                        window.open(hoverLink); // Use this to open in new tab
                        //window.location = hoverLink; // Use this to open in current window
                    }
                }
                

            }
            
            
            // Ready for use ! You are welcome !
                  
   
        </script> 
    </head>
    <body onload="OnLoad();">
        <canvas id="myCanvas" width="1884 "height="998" style="border:1px solid #eee;">  
            Canvas is not supported in your browser ! :(  
        </canvas>
    </body>
</html>