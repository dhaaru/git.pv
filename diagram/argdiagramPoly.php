<?php
require_once('../connections/queriesMysql2.php');
require_once('../connections/queriesInflux2.php');

##################################################
# Influx clean version
# 'amplus_all_calculations' instead of 'amplus_calculation'
##################################################
# function combineIrradWithInverterEff()
# ------------------------------------------------
# replaces all loops containing queries to _devicedatavalue
# ------------------------------------------------
print ("<p><sub>argdiagramPoly influx clean version  'amplus_all_calculations' </sub></p>");
#################################################



if (!isset($delta)) {
  $delta = 0;
}

set_time_limit(900);
require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

$anyArgs = false;
$now = mktime();
date_default_timezone_set('UTC');

if (!isset($stamp)) {
  $endstamp = mktime(18, 30, 0);
  $stamp = $endstamp - 24 * 3600;
}

if (!isset($resolution)) {
  $resolution = 2;
}
$startTime = $stamp;
$endTime = $endstamp;

if (is_null($args)) {
    return;
}

$argString = "";
if ($echoArgs == 1) {
  $argString = '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=' . $args . '&defaults=' . $defaults . '&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br />';
  $count++;
}

$defaultNames = array();
if (is_null($defaults) || strlen($defaults) == 0) {
  $defaults = false;
}
else {
  $defaults = split(",", $defaults);
}

$hideClear = $hideClear;
if (is_null($hideClear)) {
  $hideClear = 0;
}
$hideDelta = $hideDelta;
if (is_null($hideDelta)) {
  $hideDelta = 0;
}

function getdeviceData($startTime, $endTime, $deviceId, $field)
{
  echo $query = "SELECT ts,value 
    FROM amplus_all_calculations 
    WHERE ts > $startTime and ts < $endTime 
    AND park_no=58
    AND (device='" . $deviceId . "') and (field='" . $field . "')";
  $sql = mysql_query($query);
  if (mysql_num_rows($sql) > 0) {
    $tsdata = array();
    $valuedata = array();
    $result = array();
    while ($rlt = mysql_fetch_array($sql)) {
      $tsdata[] = $rlt['ts'];
      $valuedata[] = $rlt['value'];
    }
  }
  return array_combine($tsdata, $valuedata);
}

if(($phase == 'PolyINV1ACPR' && $park_no == 58) || ($phase == 'PolyINV2ACPR' && $park_no == 58) || ($phase == 'PolyINV3ACPR' && $park_no == 58))
	{ 
      $Module_temp_600=getdeviceData($startTime,$endTime,12417,'AC_Module_Temp_600');

		foreach($Module_temp_600 as $moduleindex1=>$modulevalue){
		$ts= $moduleindex1;
		$mod_value = $modulevalue;
		$qry_inverter1 = mysql_query("SELECT  ROUND((value),3) as groupval,ts FROM amplus_all_calculations WHERE park_no=58 AND ts=$ts and (device='12660') and (field='Inv_AC_PR_600')");
		$numCount1 =  mysql_num_rows($qry_inverter1); 
		if($numCount1!=0)
		{
			$fetchdata1=mysql_fetch_array($qry_inverter1);
			$inv1ACpr = $fetchdata1['groupval'];
			$result1[] = '['.$mod_value.','.$inv1ACpr.']';
		}
		
		$qry_inverter2 = mysql_query("SELECT  ROUND((value),3) as groupval,ts FROM amplus_all_calculations WHERE park_no=58 AND ts=$ts and (device='12659') and (field='Inv_AC_PR_600')");
		$numCount2 =  mysql_num_rows($qry_inverter2); 
		if($numCount2!=0)
		{
			$fetchdata2=mysql_fetch_array($qry_inverter2);
			$inv2ACpr = $fetchdata2['groupval'];
			$result2[] = '['.$mod_value.','.$inv2ACpr.']';
		}	
		$qry_inverter3 = mysql_query("SELECT  ROUND((value),3) as groupval,ts FROM amplus_all_calculations WHERE park_no=58 AND ts=$ts and (device='12658') and (field='Inv_AC_PR_600')");
		$numCount3 =  mysql_num_rows($qry_inverter3); 
		if($numCount3!=0)
		{
			$fetchdata3=mysql_fetch_array($qry_inverter3);
			$inv3ACpr = $fetchdata3['groupval'];
			$result3[] = '['.$mod_value.','.$inv3ACpr.']';
		}
		}	
		
		$invACPR1=str_replace('"',"",json_encode($result1));
		$invACPR2=str_replace('"',"",json_encode($result2));
		$invACPR3=str_replace('"',"",json_encode($result3));
	}

	if(($phase == 'PolyINV1DCPR' && $park_no == 58) || ($phase == 'PolyINV2DCPR' && $park_no == 58) || ($phase == 'PolyINV3DCPR' && $park_no == 58))
	{
		
		$Module_temp_600=getdeviceData($startTime,$endTime,12417,'AC_Module_Temp_600');

		foreach($Module_temp_600 as $moduleindex1=>$modulevalue){
		$ts= $moduleindex1;
		$mod_value = $modulevalue;
		$qry_inverterdc1 = mysql_query("SELECT  ROUND((value),3) as groupval,ts FROM amplus_all_calculations WHERE park_no=58 AND ts=$ts and (device='12660') and (field='Inv_DC_Vol_Coeff')");
		$dcnumCount1 =  mysql_num_rows($qry_inverterdc1); 
		if($dcnumCount1!=0)
		{
			$dcfetchdata1=mysql_fetch_array($qry_inverterdc1);
			$inv1DCpr = $dcfetchdata1['groupval'];
			$dcresult1[] = '['.$mod_value.','.$inv1DCpr.']';
		}
		$qry_inverterdc2 = mysql_query("SELECT  ROUND((value),3) as groupval,ts FROM amplus_all_calculations WHERE park_no=58 AND ts=$ts and (device='12659') and (field='Inv_DC_Vol_Coeff')");
		$dcnumCount2 =  mysql_num_rows($qry_inverterdc2); 
		if($dcnumCount2!=0)
		{
			$dcfetchdata2=mysql_fetch_array($qry_inverterdc2);
			$inv2DCpr = $dcfetchdata2['groupval'];
			$dcresult2[] = '['.$mod_value.','.$inv2DCpr.']';
		}	
		$qry_inverterdc3 = mysql_query("SELECT  ROUND((value),3) as groupval,ts FROM amplus_all_calculations WHERE park_no=58 AND ts=$ts and (device='12658') and (field='Inv_DC_Vol_Coeff')");
		$dcnumCount3 =  mysql_num_rows($qry_inverterdc3); 
		if($dcnumCount3!=0)
		{
			$dcfetchdata3=mysql_fetch_array($qry_inverterdc3);
			$inv3DCpr = $dcfetchdata3['groupval'];
			$dcresult3[] = '['.$mod_value.','.$inv3DCpr.']';
		}	
		}	
		$inverDCPR1=str_replace('"',"",json_encode($dcresult1));
		$inverDCPR2=str_replace('"',"",json_encode($dcresult2));
		$inverDCPR3=str_replace('"',"",json_encode($dcresult3));
	}

	if(($phase == 'PolyINV1EFF' && $park_no == 58) || ($phase == 'PolyINV2EFF' && $park_no == 58) || ($phase == 'PolyINV3EFF' && $park_no == 58))
	{
		$inver1Eff =getdeviceData($startTime,$endTime,12660,'Inv_Eff');
		$inver2Eff =getdeviceData($startTime,$endTime,12659,'Inv_Eff');
		$inver3Eff =getdeviceData($startTime,$endTime,12658,'Inv_Eff');

		if ($inver1Eff) {
    # inver1Eff is such an array in 5 min interval:
    #    Array
    #    (
    #        [1468023301] => 0
    #        [1468023607] => 0
    #        [1468023901] => 88.89
    #        [1468024210] => 85
    #        [1468024509] => 91.67
    #       ...
    #        [1468068905] => 66.67
    #        [1468069205] => 60
    #        [1468069507] => 0
    #        [1468069807] => 0
    #        [1468070108] => 0
    #    )
    $inver1Eff = alignTimestamps($inver1Eff);
    $inverEff1 = combineIrradWithInverterEff($inver1Eff,12417,'Solar_Radiation');
  }
  if ($inver2Eff) {
    $inver2Eff = alignTimestamps($inver2Eff);
    $inverEff2 = combineIrradWithInverterEff($inver2Eff,12417,'Solar_Radiation');

  }
  if ($inver3Eff) {
    $inver3Eff = alignTimestamps($inver3Eff);
    $inverEff3 = combineIrradWithInverterEff($inver3Eff,12417,'Solar_Radiation'); 
  }		

  	}
	
?>


<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
		<link rel="stylesheet" href="style.css" type="text/css" />
		<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/excanvas.min.js"></script><![endif]-->
		<script language="javascript" type="text/javascript" src="../js/jquery.js"></script>
		<script language="javascript" type="text/javascript" src="../js/jquery.flot.js"></script>
		<script type="text/javascript" src="jscolor/jscolor.js"></script>
		<script type="text/javascript" src="../functions/flot/jquery.flot.selection.min.js"></script>
		<script language="javascript" type="text/javascript" src="../js/jquery.flot.symbol.js"></script>
		<script language="javascript" type="text/javascript" src="../js/jquery.flot.axislabels.js"></script>
		<!-- The styles -->
		<link href="../css/examples.css" rel="stylesheet" type="text/css">
	</head>
    <body>

        <div style="height: 98%; width: 100%">
            <div style="float: left; height: 99%; width: 100%; padding-top: 2px">
                <form name=ThisForm method="post" action="" target="_self">

                    <div>
                       <div style="font-family:Verdana, Geneva, sans-serif; font-size:13px; color:darkblue; font-weight:bold; padding-bottom:2px">
                            <?php
                            echo $title;
                            ?>
                        </div>
                    </div>
                    <div>
                        <!--<div id="placeholder" style="font-size: 95%; width: 99%; height: 98%"></div>-->
						
						<div class="demo-container">
			<div id="placeholder" class="demo-placeholder" style="text-align:center;  margin:0 auto; width:921px;"></div>
			
			<span id="hoverdata"></span>
			
             </div>
                    </div>
                </form>

<!-- Excel sheet link start -->
				<div style="position:absolute; right:10px; top:10px;" id="buttons">
                         <?php 
                         foreach ($displayItems as $myitem) {
                             foreach ($myitem as $myelement) {
                                 echo '<p style = "color:' . $myelement[color] . ';font-family:' . $myelement[font] . ';font-size:' . $myelement[size] . 'px">' . $myelement[text] . '<br>' . number_format($myelement[value], $myelement[decimals], '.', '') . $myelement[unit] . '</p>';
                             }
                         }
                         ?>
                   <!-- <input title="Reset the zoom"    style="flow: left;" id="resetZoom" onClick="resetZoom()" type="image"    src="../imgs/lupe_grey.png" disabled>-->

                    <?php 

                    $phpArg = "?stamp=" . $stamp . "&endstamp=" . $endstamp;
                    $deltaNew = 0;
                    if ($delta == 0) {
                        $deltaNew = "&delta=1";
                    } else {
                        $deltaNew = "&delta=0";
                    }
                    $endString = "&yearO=$yearO&monO=$monO&dayO=$dayO";
                    $startString = "&yearI=$yearI&monI=$monI&dayI=$dayI";

                    $phaseWord = "&showQueries=$showQueries&showAll=$showAll" . $endString . $startString;
					
					
					///export access permission
					$user_name = $_SESSION['user'];
					$query_ds = "SELECT export FROM users WHERE user = '$user_name' and admin_id= 8";
					$ds = mysql_query($query_ds, $verbindung) or die(mysql_error());
					 $row_ds = mysql_fetch_assoc($ds);
					 $row_ds[0];
					
					
					$query_ds = "SELECT export FROM users WHERE user = '$user_name' and export=1";
					$ds = mysql_query($query_ds, $verbindung) or die(mysql_error());
					$row_export = mysql_num_rows($ds);
					
					
					if($row_export==1){
                    if ($exception != 25) {
                        if ($anyArgs) {
                            if ($hideClear == 0) {
                                echo '<a href="../' . $sourcefile . $phpArg . $phaseWord . '&igate=' . $igate . '" target="_parent">';
                                echo '<img title="Reset Diagram" src="clear.png">';
                                echo '</a>';
                            }
							             
                        } else {
							
							if($phase == "PolyINV1EFF" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12660,Inv_Eff,Inverter1,15,%,8;0,1,0,12417,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							if($phase == "PolyINV2EFF" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12659,Inv_Eff,Inverter2,15,%,8;0,1,0,12417,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							if($phase == "PolyINV3EFF" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12658,Inv_Eff,Inverter3,15,%,8;0,1,0,12417,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}							
							
							 if($phase == "PolyINV1ACPR" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12660,Inv_AC_PR_600,Inv1%20AC%20PR,15,%,4;0,1,0,12417,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}  
							if($phase == "PolyINV2ACPR" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12659,Inv_AC_PR_600,Inv2%20AC%20PR,15,%,4;0,1,0,12417,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							} 
							if($phase == "PolyINV3ACPR" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12658,Inv_AC_PR_600,Inv3%20AC%20PR,15,%,4;0,1,0,12417,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							} 
							
							if($phase == "PolyINV1DCPR" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12660,Inv_DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,12417,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							if($phase == "PolyINV2DCPR" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12659,Inv_DC_Vol_Coeff,Inv 2(DC_Voltage),15,V,4;0,1,0,12417,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							if($phase == "PolyINV3DCPR" && $park_no=="58"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,12658,Inv_DC_Vol_Coeff,Inv 3(DC_Voltage),15,V,4;0,1,0,12417,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,12417,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
                        }
                    } else {

                        if (!isset($park_no)) {
                            if (!isset($_SESSION ['park_no_s'])) {
                                $park_no = 0;
                            } else {
                                $park_no = $_SESSION ['park_no_s'];
                            }
                        }
                        $_SESSION ['park_no_s'] = $park_no;

                        if (!isset($subpark_id)) {
                            if (!isset($_SESSION ['subpark_s'])) {
                                $subpark_id = 0;
                            } else {
                                $subpark_id = $_SESSION ['subpark_s'];
                            }
                        }
                        $_SESSION ['subpark_s'] = $subpark_id;

                        if (!isset($area_id)) {
                            if (!isset($_SESSION ['area_s'])) {
                                $area_id = 0;
                            } else {
                                $area_id = $_SESSION ['area_s'];
                            }
                        }
                        $_SESSION ['area_s'] = $area_id;

                        if (!isset($phase)) {
                            if (!isset($_SESSION ['phase_s'])) {
                                $phase = "tag";
                            } else {
                                $phase = $_SESSION ['phase_s'];
                            }
                        }
                        $_SESSION ['phase_s'] = $phase;

                        if (!isset($jahr)) {
                            if (!isset($_SESSION ['jahr_s'])) {
                                $jahr = $jahr_heute;
                            } else {
                                $jahr = $_SESSION ['jahr_s'];
                            }
                        }
                        $_SESSION ['jahr_s'] = $jahr;

                        if (!isset($mon)) {
                            if (!isset($_SESSION ['mon_s'])) {
                                $mon = $monat_heute;
                            } else {
                                $mon = $_SESSION ['mon_s'];
                            }
                        }
                        $_SESSION ['mon_s'] = $mon;

                        if (!isset($tag)) {
                            if (!isset($_SESSION ['tag_s'])) {
                                $tag = $tag_heute;
                            } else {
                                $tag = $_SESSION ['tag_s'];
                            }
                        }
                        $_SESSION ['tag_s'] = $tag;

                        if ($phase == "tag") {
                            echo '<a href="pat.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&portal=' . $park_no . '&phase=' . $phase . '">';

                            echo '     <img title="Export diagram as .csv file" src="../imgs/xls.png">';
                            echo '</a>';

                            echo '<a href="patPyr.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&name=WeatherStation&device=108&offset=19800&useTs=0&showQueries=0">';
                            echo '    <img title="Export Pyranometer as .csv file" src="../imgs/sun.png">';
                            echo '</a>';

                            echo '<a href="patPr.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&print=true&park_no=' . $park_no . '">';
                            echo '    <img title="Export Performance Ratio as .csv file" src="../imgs/xls_pr.png">';
                            echo '</a>';
                        }
                        
                    }
					}
                    if ($hideDelta == 0) {
                        if ($delta == 1) {
                            echo '<a href="../' . $sourcefile . $phpArg . '&args=' . $argBack . $phaseWord . $deltaNew . '&igate=' . $igate . '" target="_parent">';
                            echo '<img title="Toggle absolute values and d/dt" src="ddt1.png">';
                            echo '</a>';
                        } else {

                            echo '<a href="../' . $sourcefile . $phpArg . '&args=' . $argBack . $phaseWord . $deltaNew . '&igate=' . $igate . '" target="_parent">';
                           // echo '<img title="Toggle absolute values and d/dt" src="ddt0.png">';
                            echo '</a>';
                        }
                    }
                    if ($echoArgs == 1) {
                        ?>

                        <input width ="99%" type="text" name="unit" class="textfeld" 
                               value='$diagrammCode .= <?php echo $argString; ?>'>


                        <?php
                    }
                    ?>
                </div>
<!-- Excel sheet link end -->

            </div>
            <div style="float: left; height: 99%; display:none; width: 16%; text-align: center">
                <div
                    style="background-color: BlanchedAlmond; font-size: 85%; width: 99%; height: 60%; overflow: auto;"
                    id="legend"> <!-- displayheight-->
<p id="choices" style="float:right;   margin-right: 3px; height: 60%;
    overflow: auto;
    width: 99%;font: 13px/1.5em proxima-nova;"></p>
                </div>

                

            </div>
			<script type="text/javascript">
<?php if(($phase == 'PolyINV1EFF' && $park_no == 58) || ($phase == 'PolyINV2EFF' && $park_no == 58)|| ($phase == 'PolyINV3EFF' && $park_no == 58)){?>
	$(function() {

			var datasets = {
			"Inv Efficency": {
				
				<?php if($phase == 'PolyINV1EFF'){?>
				label: "Inv1 Efficency", points: { symbol: "circle" },
				data: <?php echo $inverEff1; ?>
				<?php } else if($phase == 'PolyINV2EFF'){?>
				label: "Inv2 Efficency", points: { symbol: "circle" },
				data: <?php echo $inverEff2; ?>
				<?php } else if($phase == 'PolyINV3EFF'){?>
				label: "Inv3 Efficency", points: { symbol: "circle" },
				data: <?php echo $inverEff3; ?>
				<?php }?>
			}
		};
							
				
		// hard-code color indices to prevent them from shifting as
		// countries are turned on/off

		var i = 2;
		$.each(datasets, function(key, val) {
			val.color = i;
			++i;
		});

		// insert checkboxes 
		var choiceContainer = $("#choices");
		$.each(datasets, function(key, val) {
			choiceContainer.append("<br/><input type='checkbox' name='" + key +
				"' checked='checked' id='id" + key + "'></input>" +
				"<label for='id" + key + "'>"
				+ val.label + "</label>");
		});
		//show tooltips
		
		$("<div id='tooltip'></div>").css({
			position: "absolute",
			display: "none",
			border: "1px solid #fdd",
			padding: "2px",
			"background-color": "#fee",
			opacity: 0.80
		}).appendTo("body");
		
		choiceContainer.find("input").click(plotAccordingToChoices);
		$("#placeholder").bind("plothover", function (event, pos, item) {
		if (item) {
					var x = item.datapoint[0].toFixed(2),
						y = item.datapoint[1].toFixed(2);

					$("#tooltip").html(item.series.label + " of " + x + " = " + y)
						.css({top: item.pageY+5, left: item.pageX+5})
						.fadeIn(200);
				} else {
					$("#tooltip").hide();
				}
		});
		
		function plotAccordingToChoices() {

			var data = [];

			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("name");
				if (key && datasets[key]) {
					data.push(datasets[key]);
				}
			});

			if (data.length > 0) {
				$.plot("#placeholder", data, {
					yaxis: {
						min: 0,
						axisLabel: 'Inverter Eff %',
						axisLabelUseCanvas: true,
						axisLabelFontSizePixels: 15,
						axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
						axisLabelPadding: 5

						
					},
					xaxis: {
						tickDecimals: 0,
						axisLabel: 'Solar Radiation(W/m²)',
						axisLabelUseCanvas: true,
						axisLabelFontSizePixels: 18,
						axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
						axisLabelPadding: 5
					}
				});
				
			}
		}

		plotAccordingToChoices();

		// Add the Flot version string to the footer

		//$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
	});
	
	<?php }elseif(($phase == 'PolyINV1ACPR' && $park_no == 58) || ($phase == 'PolyINV2ACPR' && $park_no == 58) || ($phase == 'PolyINV3ACPR' && $park_no == 58)){?>
	$(function() {

		var data = [
			<?php if($phase == 'PolyINV1ACPR'){?>
			{ data: <?php echo $invACPR1; ?>, points: { symbol: "diamond" }, label: "INV1 AC PR (%)" }
			<?php } else if($phase == 'PolyINV2ACPR'){?>
			{ data: <?php echo $invACPR2; ?>, points: { symbol: "diamond" }, label: "INV2 AC PR (%)" }
			<?php } else if($phase == 'PolyINV3ACPR'){?>
			{ data: <?php echo $invACPR3; ?>, points: { symbol: "diamond" }, label: "INV3 AC PR (%)" }
			<?php }?>
		];
	
		$.plot("#placeholder", data, {
			series: {
				points: {
					show: true,
					radius: 3
				}
			},
			grid: {
				hoverable: true
			},
			xaxis: {
				axisLabel: 'Module Temperature (°C)',
				axisLabelUseCanvas: true,
				axisLabelFontSizePixels: 18,
				axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
				axisLabelPadding: 10

			},
			yaxis: {
				axisLabel: 'Inv PR(%)',
				axisLabelUseCanvas: true,
				axisLabelFontSizePixels: 15,
				axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
				axisLabelPadding: 20

			},
		});	
		$("<div id='tooltip'></div>").css({
			position: "absolute",
			display: "none",
			border: "1px solid #fdd",
			padding: "2px",
            "font-size": "13px",
            "font-weight": "690",
			"background-color": "#fee",
			opacity: 0.80
		}).appendTo("body");

		$("#placeholder").bind("plothover", function (event, pos, item) {

				var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
				//$("#hoverdata").text(str);

				if (item) {
					var x = item.datapoint[0].toFixed(2),
						y = item.datapoint[1].toFixed(2);
						$("#tooltip").html(item.series.label + " at " + x + " Deg C = " + y)
						.css({top: item.pageY+5, left: item.pageX+5})
						.fadeIn(200);
				}
			});

		$("#placeholder").bind("plotclick", function (event, pos, item) {
			if (item) {
				$("#clickdata").text(" - click point " + item.dataIndex + " in " + item.series.label);
				plot.highlight(item.series, item.datapoint);
			}
		});
	});	
	<?php }elseif(($phase == 'PolyINV1DCPR' && $park_no == 58) || ($phase == 'PolyINV2DCPR' && $park_no == 58) || ($phase == 'PolyINV3DCPR' && $park_no == 58)){?>
	$(function() {

		var data = [
			<?php if($phase == 'PolyINV1DCPR'){?>
			{ data: <?php echo $inverDCPR1; ?>, points: { symbol: "circle" }, label: "INV1 DC Voltage(V)" }
			<?php } else if($phase == 'PolyINV2DCPR'){?>
			{ data: <?php echo $inverDCPR2; ?>, points: { symbol: "circle" }, label: "INV2 DC Voltage(V)" }
			<?php } else if($phase == 'PolyINV3DCPR'){?>
			{ data: <?php echo $inverDCPR3; ?>, points: { symbol: "circle" }, label: "INV3 DC Voltage(V)" }
			<?php }?>
			
		
		];
	
		$.plot("#placeholder", data, {
			series: {
				points: {
					show: true,
					radius: 3
				}
			},
			grid: {
				hoverable: true
			},
			xaxis: {
				axisLabel: 'Module Temperature (°C)',
				axisLabelUseCanvas: true,
				axisLabelFontSizePixels: 18,
				axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
				axisLabelPadding: 10

			},
			yaxis: {
				axisLabel: 'Inv DC Volt(V)',
				axisLabelUseCanvas: true,
				axisLabelFontSizePixels: 15,
				axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
				axisLabelPadding: 20

			},
		});	
		$("<div id='tooltip'></div>").css({
			position: "absolute",
			display: "none",
			border: "1px solid #fdd",
			padding: "2px",
            "font-size": "13px",
            "font-weight": "690",
			"background-color": "#fee",
			opacity: 0.80
		}).appendTo("body");

		$("#placeholder").bind("plothover", function (event, pos, item) {

				var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
				//$("#hoverdata").text(str);

				if (item) {
					var x = item.datapoint[0].toFixed(2),
						y = item.datapoint[1].toFixed(2);

						$("#tooltip").html(item.series.label + " at " + x + " = " + y)
				   
						.css({top: item.pageY+5, left: item.pageX+5})
						.fadeIn(200);
				}
			});

		$("#placeholder").bind("plotclick", function (event, pos, item) {
			if (item) {
				$("#clickdata").text(" - click point " + item.dataIndex + " in " + item.series.label);
				plot.highlight(item.series, item.datapoint);
			}
		});
		

		// Add the Flot version string to the footer

		$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
	});
	

		
	<?php }?>

		
	</script>
			
			
          
        </div>
    </body>
</html>


