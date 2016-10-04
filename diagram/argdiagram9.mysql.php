<?php

if (!isset($delta)) {
    $delta = 0;
}

$sourcetable = "amplus_calculation";
if ($compressed) {
    $sourcetable = " _devicedatacompressedvalue ";
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
    $argString = '<tr><td><iframe id="frame1" width="99%" height="99%" SRC="diagram/argdiagram5.php?args=' . $args . '&defaults=' . $defaults . '&hideClear=1&hideDelta=1&stamp=' . $stamp . '&endstamp=' . $endstamp . '" border="0"></iframe></td></tr><br>';
    $count++;
}

$defaultNames = array();
if (is_null($defaults) || strlen($defaults) == 0) {
    $defaults = false;
} else {

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

function getdeviceData($startTime,$endTime,$deviceId,$field)
{
	$query="select ts,value from amplus_calculation where ts > $startTime and ts < $endTime and (device='".$deviceId."') and (field='".$field."')";
	$sql = mysql_query($query);
	if(mysql_num_rows($sql) > 0){
		$tsdata= array();
		$valuedata = array();
		$result = array();
		while($rlt = mysql_fetch_array($sql)){
			$tsdata[] = $rlt['ts'];
			$valuedata[] = $rlt['value'];	
		}
	}
	return array_combine($tsdata,$valuedata);
}
function getdeviceData1($startTime,$endTime,$deviceId,$field)
{
	$query="select ts,value from pune_calculation where ts > $startTime and ts < $endTime and (device='".$deviceId."') and (field='".$field."')";
	$sql = mysql_query($query);
	if(mysql_num_rows($sql) > 0){
		$tsdata= array();
		$valuedata = array();
		$result = array();
		while($rlt = mysql_fetch_array($sql)){
			$tsdata[] = $rlt['ts'];
			$valuedata[] = $rlt['value'];	
		}
	}
	return array_combine($tsdata,$valuedata);
}
function getSensorData($startTime,$endTime,$deviceId,$field)
{
	
	$query="select ts,value from pune_calculation where ts > $startTime and ts < $endTime and (device='".$deviceId."') and (field='".$field."')";
	$sql = mysql_query($query);
	if(mysql_num_rows($sql) > 0){
		$tsdata= array();
		$valuedata = array();
		$result = array();
		while($rlt = mysql_fetch_array($sql)){
			$tsdata[] = $rlt['ts'];
			$valuedata[] = $rlt['value'];	
		}
	}
	return array_combine($tsdata,$valuedata);
}

	if($phase == 'energy4' && $park_no == 36)
	{
		$Module_temp_600=getdeviceData($startTime,$endTime,7794,'AC_Module_Temp_600');

		foreach($Module_temp_600 as $moduleindex1=>$modulevalue){
		$ts= $moduleindex1;
		$mod_value = $modulevalue;
		$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM amplus_calculation where ts=$ts and (device='7792') and (field='Inv_AC_PR')");
		$numCount1 =  mysql_num_rows($qry_inverter1); 
		if($numCount1!=0)
		{
			$fetchdata1=mysql_fetch_array($qry_inverter1);
			$inv1ACpr = $fetchdata1['groupval'];
			$result1[] = '['.$mod_value.','.$inv1ACpr.']';
		}
		$qry_inverter2 = mysql_query("select  ROUND((value),3) as groupval,ts FROM amplus_calculation where ts=$ts and (device='7791') and (field='Inv_AC_PR')");
		$numCount2 =  mysql_num_rows($qry_inverter2); 
		if($numCount2!=0)
		{
			$fetchdata2=mysql_fetch_array($qry_inverter2);
			$inv2ACpr = $fetchdata2['groupval'];
			$result2[] = '['.$mod_value.','.$inv2ACpr.']';
		}	
		$qry_inverter3 = mysql_query("select  ROUND((value),3) as groupval,ts FROM amplus_calculation where ts=$ts and (device='7794') and (field='Inv_AC_PR')");
		$numCount3 =  mysql_num_rows($qry_inverter3); 
		if($numCount3!=0)
		{
			$fetchdata3=mysql_fetch_array($qry_inverter3);
			$inv3ACpr = $fetchdata3['groupval'];
			$result3[] = '['.$mod_value.','.$inv3ACpr.']';
		}	
		}	
		$inverPR1=str_replace('"',"",json_encode($result1));
		$inverPR2=str_replace('"',"",json_encode($result2));
		$inverPR3=str_replace('"',"",json_encode($result3));
	}
	
	if($phase == 'energy5' && $park_no == 36)
	{
		
		$Module_temp_600=getdeviceData($startTime,$endTime,7794,'AC_Module_Temp_600');

		foreach($Module_temp_600 as $moduleindex1=>$modulevalue){
		$ts= $moduleindex1;
		$mod_value = $modulevalue;
		$qry_inverterdc1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM amplus_calculation where ts=$ts and (device='7792') and (field='DC_Vol_Coeff')");
		$dcnumCount1 =  mysql_num_rows($qry_inverterdc1); 
		if($dcnumCount1!=0)
		{
			$dcfetchdata1=mysql_fetch_array($qry_inverterdc1);
			$inv1DCpr = $dcfetchdata1['groupval'];
			$dcresult1[] = '['.$mod_value.','.$inv1DCpr.']';
		}
		$qry_inverterdc2 = mysql_query("select  ROUND((value),3) as groupval,ts FROM amplus_calculation where ts=$ts and (device='7791') and (field='DC_Vol_Coeff')");
		$dcnumCount2 =  mysql_num_rows($qry_inverterdc2); 
		if($dcnumCount2!=0)
		{
			$dcfetchdata2=mysql_fetch_array($qry_inverterdc2);
			$inv2DCpr = $dcfetchdata2['groupval'];
			$dcresult2[] = '['.$mod_value.','.$inv2DCpr.']';
		}	
		$qry_inverterdc3 = mysql_query("select  ROUND((value),3) as groupval,ts FROM amplus_calculation where ts=$ts and (device='7794') and (field='DC_Vol_Coeff')");
		$dcnumCount3 =  mysql_num_rows($qry_inverterdc3); 
		if($dcnumCount3!=0)
		{
			$dcfetchdata3=mysql_fetch_array($qry_inverterdc3);
			$inv3DCpr = $dcfetchdata3['groupval'];
			$dcresult3[] = '['.$mod_value.','.$inv3DCpr.']';
		}	
		}	
		$inverPR1=str_replace('"',"",json_encode($result1));
		$inverPR2=str_replace('"',"",json_encode($result2));
		$inverPR3=str_replace('"',"",json_encode($result3));
		
		$inverDCPR1=str_replace('"',"",json_encode($dcresult1));
		$inverDCPR2=str_replace('"',"",json_encode($dcresult2));
		$inverDCPR3=str_replace('"',"",json_encode($dcresult3));
	}

	if($phase == 'graph4' && $park_no == 36)
	{
		$inver1Eff =getdeviceData($startTime,$endTime,7792,'Inv_Eff');
		$inver2Eff =getdeviceData($startTime,$endTime,7791,'Inv_Eff');
		$inver3Eff =getdeviceData($startTime,$endTime,7794,'Inv_Eff');
		
		if($inver1Eff!=''){
		foreach($inver1Eff as $inv1index1=>$inv1eff1){
					$qry_irrad1=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts=$inv1index1 and (device='7794') and (field='Solar_Radiation')");
					$numCount1 =  mysql_num_rows($qry_irrad1); 
					if($numCount1!=0)
					{
						  $fetchdata1=mysql_fetch_array($qry_irrad1);
						  $irradation1 =$fetchdata1['groupval'];
					}
					else {
							$irradation1 = 32.9;
					}
					$result1[] = '['.$irradation1.','.$inv1eff1.']';
		}
		$inverEff1=str_replace('"',"",json_encode($result1));
	}	
	if($inver2Eff!=''){
		foreach($inver2Eff as $inv2index2=>$inv2eff2){
					$qry_irrad2=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts=$inv2index2 and (device='7794') and (field='Solar_Radiation')");
					$numCount2=  mysql_num_rows($qry_irrad2); 
					if($numCount2!=0)
					{
						  $fetchdata2=mysql_fetch_array($qry_irrad2);
						  $irradation2 =$fetchdata2['groupval'];
					}
					else {
							$irradation2 = 32.9;
					}
					$result2[] = '['.$irradation2.','.$inv2eff2.']';
		}
		$inverEff2=str_replace('"',"",json_encode($result2));
	}	

	if($inver3Eff!=''){
		foreach($inver3Eff as $inv3index3=>$inv3eff3){
					$qry_irrad3=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts=$inv3index3 and (device='7794') and (field='Solar_Radiation')");
					$numCount3=  mysql_num_rows($qry_irrad3); 
					if($numCount3!=0)
					{
						  $fetchdata3=mysql_fetch_array($qry_irrad3);
						  $irradation3 =$fetchdata3['groupval'];
					}
					else {
							$irradation3 = 32.9;
					}
					$result3[] = '['.$irradation3.','.$inv3eff3.']';
		}
		$inverEff3=str_replace('"',"",json_encode($result3));
		}	

	}
	/* AMplus Pune start*/
	// All Refusol Inverter1 Efficiency
	if(($phase == 'INV1G5' && $park_no == 43) || ($phase == 'INV7G5' && $park_no == 43) || ($phase == 'INV9G5' && $park_no == 43) || ($phase == 'INV13G5' && $park_no == 43) 
	||($phase == 'INV2G5' && $park_no == 43) ||($phase == 'INV3G5' && $park_no == 43) ||($phase == 'INV4G5' && $park_no == 43) ||($phase == 'INV8G5' && $park_no == 43)
	||($phase == 'INV14G5' && $park_no == 43) || ($phase == 'INV15G5' && $park_no == 43) || ($phase == 'INV16G5' && $park_no == 43))
	{
		$argBack = $args;
		$args = split(";", $args);
			foreach($args as $input){
				$avgItems = split(",", $input);
				//echo $avgItems[3].$avgItems[4];
				$inv1Eff =getdeviceData1($startTime,$endTime,$avgItems[3],$avgItems[4]);
				//print_r($inv1Eff);
					if($inv1Eff!=''){
						foreach($inv1Eff as $inv1ts1=>$inv1effvalue){
							$inv1ts1a=$inv1ts1+40;
							$inv1ts1b=$inv1ts1-40;
								if($phase == 'INV1G5' || $phase == 'INV13G5' || $phase == 'INV3G5' || $phase == 'INV8G5' ){
									$qry_pirrad1=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts < $inv1ts1a and ts > $inv1ts1b and (device='8414') and (field='Solar_Radiation')");
								}else if($phase == 'INV9G5' || $phase == 'INV4G5' || $phase == 'INV15G5' || $phase == 'INV16G5'){
									$qry_pirrad1=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts < $inv1ts1a and ts > $inv1ts1b and (device='8412') and (field='Solar_Radiation')");
								}else if($phase == 'INV7G5' || $phase == 'INV2G5' || $phase == 'INV14G5'){
									//$qry_pirrad1=mysql_query("select  ROUND((sum(value)/2),3) as groupval FROM _devicedatavalue where ts=$inv1ts1 and (device IN ('8414','8412')) and (field='Solar_Radiation') group by ts");
									$qry_pirrad1=mysql_query("select  ROUND((sum(value)/2),3) as groupval FROM _devicedatavalue where ts < $inv1ts1a and ts > $inv1ts1b and (device IN ('8414','8412')) and (field='Solar_Radiation') group by ts");
								}
								$numCount1 =  mysql_num_rows($qry_pirrad1); 
								if($numCount1!=0){
									$fetchdata1=mysql_fetch_array($qry_pirrad1);
									$irradation2 =$fetchdata1['groupval'];
								}
								else {
									$irradation2 = 0;
								}
						$result1[] = '['.$irradation2.','.$inv1effvalue.']';
						}
					}
				//$inv1Eff =getdeviceData1($startTime,$endTime,8379,'Inv1_Eff');
				$inv1Eff1=str_replace('"',"",json_encode($result1));
				print_r($inv1Eff1);
        print ("<br>");
			}
	}	
	//INV5&6 Efficency
	
	if($phase == 'INV5G5' && $park_no == 43){
		$inver5Eff =getdeviceData1($startTime,$endTime,8458,'Inv5_Eff');
		$inver6Eff =getdeviceData1($startTime,$endTime,8456,'Inv6_Eff');
		if($inver5Eff!=''){
		foreach($inver5Eff as $inv5index=>$inv5eff){
					$inv5tsa=$inv5index+40;
					$inv5tsb=$inv5index-40;
					$qry_irrad5=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts < $inv5tsa and ts > $inv5tsb and (device='8412') and (field='Solar_Radiation')");
					$numCount5 =  mysql_num_rows($qry_irrad5); 
					if($numCount5!=0)
					{
						  $fetchdata5=mysql_fetch_array($qry_irrad5);
						  $irradation5 =$fetchdata5['groupval'];
					}
					else {
							$irradation5 = 0;
					}
					$result5[] = '['.$irradation5.','.$inv5eff.']';
		}
		$inverEff5=str_replace('"',"",json_encode($result5));
	}	
	if($inver6Eff!=''){
		foreach($inver6Eff as $inv6index=>$inv6eff){
							$inv6tsa=$inv6index+40;
							$inv6tsb=$inv6index-40;
					$qry_irrad6=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts < $inv6tsa and ts > $inv6tsb and (device='8412') and (field='Solar_Radiation')");
					$numCount6=  mysql_num_rows($qry_irrad6); 
					if($numCount6!=0)
					{
						  $fetchdata6=mysql_fetch_array($qry_irrad6);
						  $irradation6 =$fetchdata6['groupval'];
					}
					else {
							$irradation6 = 0;
					}
					$result6[] = '['.$irradation6.','.$inv6eff.']';
		}
		$inverEff6=str_replace('"',"",json_encode($result6));
	}	
		//print_r($inverEff6);
	}
	if($phase == 'INV10G5' && $park_no == 43){
		$inver10Eff =getdeviceData1($startTime,$endTime,8384,'Inv10_Eff');
		$inver11Eff =getdeviceData1($startTime,$endTime,8382,'Inv11_Eff');
		$inver12Eff =getdeviceData1($startTime,$endTime,8383,'Inv12_Eff');
		if($inver10Eff!=''){
		foreach($inver10Eff as $inv10index=>$inv10eff){
						$inv10tsa=$inv10index+40;
						$inv10tsb=$inv10index-40;
					$qry_irrad10=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts < $inv10tsa and ts > $inv10tsb and (device='8414') and (field='Solar_Radiation')");
					$numCount10 =  mysql_num_rows($qry_irrad10); 
					if($numCount10!=0)
					{
						  $fetchdata10=mysql_fetch_array($qry_irrad10);
						  $irradation10 =$fetchdata10['groupval'];
					}
					else {
							$irradation10 = 0;
					}
					$result10[] = '['.$irradation10.','.$inv10eff.']';
		}
		$inverEff10=str_replace('"',"",json_encode($result10));
	}	
	if($inver11Eff!=''){
		foreach($inver11Eff as $inv11index=>$inv11eff){
					$inv11tsa=$inv11index+40;
					$inv11tsb=$inv11index-40;
					$qry_irrad11=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts < $inv11tsa and ts > $inv11tsb and (device='8414') and (field='Solar_Radiation')");
					$numCount11=  mysql_num_rows($qry_irrad11); 
					if($numCount11!=0)
					{
						  $fetchdata11=mysql_fetch_array($qry_irrad11);
						  $irradation11 =$fetchdata11['groupval'];
					}
					else {
							$irradation11 = 0;
					}
					$result11[] = '['.$irradation11.','.$inv11eff.']';
		}
		$inverEff11=str_replace('"',"",json_encode($result11));
	}
	if($inver12Eff!=''){
		foreach($inver12Eff as $inv12index=>$inv12eff){
					$inv12tsa=$inv12index+40;
					$inv12tsb=$inv12index-40;
					$qry_irrad12=mysql_query("select  ROUND((value),3) as groupval FROM _devicedatavalue where ts < $inv12tsa and ts > $inv12tsb and (device='8414') and (field='Solar_Radiation')");
					$numCount12=  mysql_num_rows($qry_irrad12); 
					if($numCount12!=0)
					{
						  $fetchdata12=mysql_fetch_array($qry_irrad12);
						  $irradation12 =$fetchdata12['groupval'];
					}
					else {
							$irradation12 = 0;
					}
					$result12[] = '['.$irradation12.','.$inv12eff.']';
		}
		$inverEff12=str_replace('"',"",json_encode($result12));
	} 	
		//print_r($inverEff6);
	}
	
	if(($phase == 'INV1G6' && $park_no == 43) || ($phase == 'INV7G6' && $park_no == 43) || ($phase == 'INV9G6' && $park_no == 43) || ($phase == 'INV13G6' && $park_no == 43) 
	||($phase == 'INV2G6' && $park_no == 43) || ($phase == 'INV3G6' && $park_no == 43) || ($phase == 'INV4G6' && $park_no == 43) ||($phase == 'INV8G6' && $park_no == 43)
	||($phase == 'INV14G6' && $park_no == 43) || ($phase == 'INV15G6' && $park_no == 43) || ($phase == 'INV16G6' && $park_no == 43))
	{
		if($phase == 'INV1G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414','Inv1_AC_Module_Temp_600');
		}else if($phase == 'INV7G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414.8412','Inv7_AC_Module_Temp_600');
		}else if($phase == 'INV9G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8412','Inv9_AC_Module_Temp_600');
		}else if($phase == 'INV13G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414','Inv13_AC_Module_Temp_600');
		}else if($phase == 'INV2G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414.8412','Inv2_AC_Module_Temp_600');
		}else if($phase == 'INV3G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414','Inv3_AC_Module_Temp_600');
	    }else if($phase == 'INV4G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8412','Inv4_AC_Module_Temp_600');
	    }else if($phase == 'INV8G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414','Inv8_AC_Module_Temp_600');
		}else if($phase == 'INV14G6'){ 
			$Module_temp_600=getSensorData($startTime,$endTime,'8414.8412','Inv14_AC_Module_Temp_600');
		}else if($phase == 'INV15G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8412','Inv15_AC_Module_Temp_600');
		}else if($phase == 'INV16G6'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8412','Inv16_AC_Module_Temp_600');
		}
		//print_r($Module_temp_600);exit;
		
					foreach($Module_temp_600 as $moduleindex1=>$modulevalue){
						$ts= $moduleindex1;
						$mod_value = $modulevalue;
						if($phase == 'INV1G6'){
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8379) and (field='Inv1_AC_PR_600') group by ts");
						}else if($phase == 'INV7G6'){
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8381) and (field='Inv7_AC_PR_600') group by ts");
						}else if($phase == 'INV9G6'){
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8378) and (field='Inv9_AC_PR_600') group by ts");
						}else if($phase == 'INV13G6'){
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8380) and (field='Inv13_AC_PR_600') group by ts");
						}else if($phase == 'INV2G6'){
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8408) and (field='Inv2_AC_PR_600') group by ts");
						}else if($phase == 'INV3G6'){	
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8410) and (field='Inv3_AC_PR_600') group by ts");
						}else if($phase == 'INV4G6'){	
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8409) and (field='Inv4_AC_PR_600') group by ts");
						}else if($phase == 'INV8G6'){
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=12976) and (field='Inv8_AC_PR_600') group by ts");
						}else if($phase == 'INV14G6'){
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8386) and (field='Inv14_AC_PR_600') group by ts");
						}else if($phase == 'INV15G6'){
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8387) and (field='Inv15_AC_PR_600') group by ts");
						}else if($phase == 'INV16G6'){
							$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8388) and (field='Inv16_AC_PR_600') group by ts");
						}
							$numCount1 =  mysql_num_rows($qry_inverter1); 
								if($numCount1!=0)
								{
									$fetchdata1=mysql_fetch_array($qry_inverter1);
									$inv1ACpr = $fetchdata1['groupval'];
									$result1[] = '['.$mod_value.','.$inv1ACpr.']';
								}
					}	
				$inv1PR1=str_replace('"',"",json_encode($result1));	
				//print_R($inv1PR1);
	}
	
// Inverter 5&6 Ac Voltage PR
	
	if($phase == 'INV5G6' && $park_no == 43){
		$Module_temp5=getdeviceData1($startTime,$endTime,8412,'Inv5_AC_Module_Temp_600');
		
		foreach($Module_temp5 as $moduleindex5=>$modulevalue5){
		$ts= $moduleindex5;
		$mod_value = $modulevalue5;
		$qry_inverter5 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8458') and (field='Inv5_AC_PR_600')");
	    $numCount5 =  mysql_num_rows($qry_inverter5); 
		if($numCount5!=0)
		{
			$fetchdata5=mysql_fetch_array($qry_inverter5);
			$inv5ACpr = $fetchdata5['groupval'];
			$result5[] = '['.$mod_value.','.$inv5ACpr.']';
		}
		
		$qry_inverter6 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8456') and (field='Inv6_AC_PR_600')");
		$numCount6 =  mysql_num_rows($qry_inverter6); 
		if($numCount6!=0)
		{
			$fetchdata6=mysql_fetch_array($qry_inverter6);
			$inv6ACpr = $fetchdata6['groupval'];
			$result6[] = '['.$mod_value.','.$inv6ACpr.']';
		}
		
	}//print_r($result5);exit;
		$inverPR5=str_replace('"',"",json_encode($result5));
		$inverPR6=str_replace('"',"",json_encode($result6));	
		
	}
	
// Inverter 10,11&12 Ac Voltage PR
	
	if($phase == 'INV10G6' && $park_no == 43){
		$Module_temp10=getdeviceData1($startTime,$endTime,8414,'Inv10_AC_Module_Temp_600');
		
		foreach($Module_temp10 as $moduleindex10=>$modulevalue10){
		$ts= $moduleindex10;
		$mod_value = $modulevalue10;
		$qry_inverter10 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8384') and (field='Inv10_AC_PR_600')");
	    $numCount10 =  mysql_num_rows($qry_inverter10); 
		if($numCount10!=0)
		{
			$fetchdata10=mysql_fetch_array($qry_inverter10);
			$inv10ACpr = $fetchdata10['groupval'];
			$result10[] = '['.$mod_value.','.$inv10ACpr.']';
		}
		
		$qry_inverter11 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8382') and (field='Inv11_AC_PR_600')");
		$numCount11 =  mysql_num_rows($qry_inverter11); 
		if($numCount11!=0)
		{
			$fetchdata11=mysql_fetch_array($qry_inverter11);
			$inv11ACpr = $fetchdata11['groupval'];
			$result11[] = '['.$mod_value.','.$inv11ACpr.']';
		}
		
		$qry_inverter12 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8383') and (field='Inv12_AC_PR_600')");
		$numCount12 =  mysql_num_rows($qry_inverter12); 
		if($numCount12!=0)
		{
			$fetchdata12=mysql_fetch_array($qry_inverter12);
			$inv12ACpr = $fetchdata12['groupval'];
			$result12[] = '['.$mod_value.','.$inv12ACpr.']';
		}
	}//print_r($result5);exit;
		$inverPR10=str_replace('"',"",json_encode($result10));
		$inverPR11=str_replace('"',"",json_encode($result11));	
		$inverPR12=str_replace('"',"",json_encode($result12));	
		
	}	
	
if(($phase == 'INV1G7' && $park_no == 43) || ($phase == 'INV7G7' && $park_no == 43) || ($phase == 'INV9G7' && $park_no == 43) || ($phase == 'INV13G7' && $park_no == 43)
||($phase == 'INV2G7' && $park_no == 43) || ($phase == 'INV3G7' && $park_no == 43) || ($phase == 'INV4G7' && $park_no == 43) ||($phase == 'INV8G7' && $park_no == 43)
||($phase == 'INV14G7' && $park_no == 43) || ($phase == 'INV15G7' && $park_no == 43) || ($phase == 'INV16G7' && $park_no == 43)  )
{ 
		if($phase == 'INV1G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414','Inv1_AC_Module_Temp_600');
		}else if($phase == 'INV7G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414.8412','Inv7_AC_Module_Temp_600');
		}else if($phase == 'INV9G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8412','Inv9_AC_Module_Temp_600');
		}else if($phase == 'INV13G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414','Inv13_AC_Module_Temp_600');
		}else if($phase == 'INV2G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414.8412','Inv2_AC_Module_Temp_600');
	    }else if($phase == 'INV3G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414','Inv3_AC_Module_Temp_600');
		}else if($phase == 'INV4G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8412','Inv4_AC_Module_Temp_600');
		}else if($phase == 'INV8G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414','Inv8_AC_Module_Temp_600');
		}else if($phase == 'INV14G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8414.8412','Inv14_AC_Module_Temp_600');
		}else if($phase == 'INV15G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8412','Inv15_AC_Module_Temp_600');
		}else if($phase == 'INV16G7'){
			$Module_temp_600=getSensorData($startTime,$endTime,'8412','Inv16_AC_Module_Temp_600');
		}
	    	

		foreach($Module_temp_600 as $moduleindex1=>$modulevalue){
			$ts= $moduleindex1;
			$mod_value = $modulevalue;
			if($phase == 'INV1G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8379) and (field='Inv1_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV7G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8381) and (field='Inv7_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV9G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8378) and (field='Inv9_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV13G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8380) and (field='Inv13_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV2G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8408) and (field='Inv2_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV3G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8410) and (field='Inv3_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV4G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8409) and (field='Inv4_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV8G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=12976) and (field='Inv8_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV14G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8386) and (field='Inv14_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV15G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8387) and (field='Inv15_DC_Vol_Coeff') group by ts");
			}else if($phase == 'INV16G7'){
				$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device=8388) and (field='Inv16_DC_Vol_Coeff') group by ts");
			}		
			//$qry_inverter1 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8379') and (field='Inv1_DC_Vol_Coeff') group by ts");
			$numCount1 =  mysql_num_rows($qry_inverter1); 
			if($numCount1!=0)
			{
				$fetchdata1=mysql_fetch_array($qry_inverter1);
				$inv1DCpr = $fetchdata1['groupval'];
				$result1[] = '['.$mod_value.','.$inv1DCpr.']';
			}
		}	
		$inv1DCPR=str_replace('"',"",json_encode($result1));	
		
	}
//Inverter 5&6 DC Voltage PR%
if(($phase == 'INV5G7' && $park_no == 43)){
	$Module_temp5=getdeviceData1($startTime,$endTime,8412,'Inv5_AC_Module_Temp_600');
		
		foreach($Module_temp5 as $moduleindex5=>$modulevalue5){
		$ts= $moduleindex5;
		$mod_value = $modulevalue5;
		$qry_inverter5 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8458') and (field='Inv5_DC_Vol_Coeff') group by ts");
	    $numCount5 =  mysql_num_rows($qry_inverter5); 
		if($numCount5!=0)
		{
			$fetchdata5=mysql_fetch_array($qry_inverter5);
			$inv5DCpr = $fetchdata5['groupval'];
			$result5[] = '['.$mod_value.','.$inv5DCpr.']';
		}
		
		$qry_inverter6 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8456') and (field='Inv6_DC_Vol_Coeff') group by ts");
		$numCount6 =  mysql_num_rows($qry_inverter6); 
		if($numCount6!=0)
		{
			$fetchdata6=mysql_fetch_array($qry_inverter6);
			$inv6DCpr = $fetchdata6['groupval'];
			$result6[] = '['.$mod_value.','.$inv6DCpr.']';
		}
		
	}//print_r($result5);exit;
		$inv5DCPR=str_replace('"',"",json_encode($result5));
		$inv6DCPR=str_replace('"',"",json_encode($result6));	
}	
//Inverter 10,11&12 DC Voltage PR%
if(($phase == 'INV10G7' && $park_no == 43)){
	$Module_temp10=getdeviceData1($startTime,$endTime,8414,'Inv10_AC_Module_Temp_600');
		
		foreach($Module_temp10 as $moduleindex10=>$modulevalue10){
		$ts= $moduleindex10;
		$mod_value = $modulevalue10;
		$qry_inverter10 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8384') and (field='Inv10_DC_Vol_Coeff') group by ts");
	    $numCount10 =  mysql_num_rows($qry_inverter10); 
		if($numCount10!=0)
		{
			$fetchdata10=mysql_fetch_array($qry_inverter10);
			$inv10DCpr = $fetchdata10['groupval'];
			$result10[] = '['.$mod_value.','.$inv10DCpr.']';
		}
		
		$qry_inverter11 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8382') and (field='Inv11_DC_Vol_Coeff') group by ts");
		$numCount11 =  mysql_num_rows($qry_inverter11); 
		if($numCount11!=0)
		{
			$fetchdata11=mysql_fetch_array($qry_inverter11);
			$inv11DCpr = $fetchdata11['groupval'];
			$result11[] = '['.$mod_value.','.$inv11DCpr.']';
		}
		$qry_inverter12 = mysql_query("select  ROUND((value),3) as groupval,ts FROM pune_calculation where ts=$ts and (device='8383') and (field='Inv12_DC_Vol_Coeff') group by ts");
		$numCount12 =  mysql_num_rows($qry_inverter12); 
		if($numCount12!=0)
		{
			$fetchdata12=mysql_fetch_array($qry_inverter12);
			$inv12DCpr = $fetchdata12['groupval'];
			$result12[] = '['.$mod_value.','.$inv12DCpr.']';
		}
		
	}//print_r($result5);exit;
		$inv10DCPR=str_replace('"',"",json_encode($result10));
		$inv11DCPR=str_replace('"',"",json_encode($result11));
		$inv12DCPR=str_replace('"',"",json_encode($result12));	
}		
/* Amplus Pune End*/
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
					$query_ds = "select export FROM users WHERE user = '$user_name' and admin_id= 8";
					$ds = mysql_query($query_ds, $verbindung) or die(mysql_error());
					 $row_ds = mysql_fetch_assoc($ds);
					 $row_ds[0];
					
                    if ($exception != 25) {
                        if ($anyArgs) {
                            if ($hideClear == 0) {
                                echo '<a href="../' . $sourcefile . $phpArg . $phaseWord . '&igate=' . $igate . '" target="_parent">';
                                echo '<img title="Reset Diagram" src="clear.png">';
                                echo '</a>';
                            }
							else if($phase == "graph4" && $park_no=="36"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&phase=' . $phase .'&args=0,1,0,7792,INV_EFF,Inverter 1,15,%,8;0,1,0,7791,INV_EFF,Inverter 2,15,%,9;0,1,0,7794,INV_EFF,Inverter 3,15,%,6;0,1,0,7794,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else {
								echo '<a href="export2.php' . $phpArg . '&args=' . $argBack . $phaseWord . '&delta=' . $delta . '" target="_parent">';
								echo '<img title="Export selection as Excel file" src="xls.png">';
								echo '</a>';
							}                  
                        } else {
                            if($phase == "graph4" && $park_no=="36"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,7792,INV_EFF,Inverter 1,15,%,8;0,1,0,7791,INV_EFF,Inverter 2,15,%,9;0,1,0,7794,INV_EFF,Inverter 3,15,%,6;0,1,0,7794,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							if($phase == "energy4" && $park_no=="36"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3,4&args=0,1,0,7792,Inv_AC_PR,Inverter1%20PR,5,%,4;0,1,0,7791,Inv_AC_PR,Inverter2%20PR,5,%,5;0,1,0,7794,Inv_AC_PR,Inverter3%20PR,5,%,6;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							if($phase == "energy5" && $park_no=="36"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3,4&args=0,1,0,7792,DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,7791,DC_Vol_Coeff,Inv 2(DC_Voltage),15,V,5;0,1,0,7794,DC_Vol_Coeff,Inv 3(DC_Voltage),15,V,6;0,1,0,7794,Inv_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,7794,AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							/* AMPLUS Pune Export Start*/
							else if($phase == "INV1G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8379,Inv1_Eff,Inv1(REFSU1) EFF,15,%,8;0,1,0,8414,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV1G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8379,Inv1_AC_PR_600,Inv1%20AC%20PR,15,%,4;0,1,0,8414,Inv1_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv1_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV1G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8379,Inv1_DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,8414,Inv1_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv1_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV2G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8408,Inv2_Eff,Inv2(SMA1) Eff,15,%,8;0,1,0,8414,Solar_Radiation,20°%20Irradiation,5,W/m&sup2;,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV2G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8408,Inv2_AC_PR_600,Inv2%20AC%20PR,15,%,4;0,1,0,8414.8412,Inv2_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414.8412,Inv2_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV2G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8408,Inv2_DC_Vol_Coeff,Inv2(DC_Voltage),15,V,4;0,1,0,8414.8412,Inv2_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414.8412,Inv2_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV3G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8410,Inv3_Eff,Inv2(SMA2) Eff,15,%,8;0,1,0,8414,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV3G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8410,Inv3_AC_PR_600,Inv3%20AC%20PR,15,%,4;0,1,0,8414,Inv3_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv3_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV3G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3,4&args=0,1,0,8410,Inv3_DC_Vol_Coeff,Inv3(DC_Voltage),15,V,4;0,1,0,8414,Inv3_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv3_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV4G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8409,Inv4_Eff,Inv4(SMA3) Eff,15,%,8;0,1,0,8412,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV4G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8409,Inv4_AC_PR_600,Inv4%20AC%20PR,15,%,4;0,1,0,8412,Inv4_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv4_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV4G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,2,3,4&args=0,1,0,8409,Inv4_DC_Vol_Coeff,Inv4(DC_Voltage),15,V,4;0,1,0,8412,Inv4_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv3_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV5G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8458,Inv5_Eff,Inv5(SMA4) Eff,15,%,8;0,1,0,8456,Inv6_Eff,Inv6(SMA5) Eff,15,%,8;0,1,0,8412,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV5G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8458,Inv5_AC_PR_600,Inv5%20AC%20PR,15,%,4;0,1,0,8456,Inv6_AC_PR_600,Inv6%20AC%20PR,15,%,4;0,1,0,8412,Inv5_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv5_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV5G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8458,Inv5_DC_Vol_Coeff,Inv 5(DC_Voltage),15,V,4;0,1,0,8456,Inv6_DC_Vol_Coeff,Inv 6(DC_Voltage),15,V,4;0,1,0,8412,Inv5_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv5_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV7G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8381,Inv7_Eff,Inv7(REFSU2) EFF,15,%,8;0,1,0,8414,Solar_Radiation,20°%20Irradiation,5,W/m&sup2;,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV7G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8381,Inv7_AC_PR_600,Inv7(Refu2)%20AC%20PR,15,%,4;0,1,0,8414.8412,Inv7_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414.8412,Inv7_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV7G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8381,Inv7_DC_Vol_Coeff,Inv7(DC_Voltage),15,V,4;0,1,0,8414.8412,Inv7_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414.8412,Inv7_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV8G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12976,Inv8_Eff,Inv8(SMA6)%20Eff,15,%,8;0,1,0,7794,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV8G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12976,Inv8_AC_PR_600,Inv8%20AC%20PR,15,%,4;0,1,0,8414,Inv8_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv8_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV8G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,12976,Inv8_DC_Vol_Coeff,Inv8(DC_Voltage),15,V,4;0,1,0,8414,Inv8_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv8_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV9G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8378,Inv9_Eff,Inv9(REFSU3) EFF,15,%,8;0,1,0,8412,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV9G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8378,Inv9_AC_PR_600,Inv9(Refu3)%20AC%20PR,15,%,4;0,1,0,8412,Inv9_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv9_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV9G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8378,Inv9_DC_Vol_Coeff,Inv9 (DC_Voltage),15,V,4;0,1,0,8412,Inv9_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv9_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV10G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8384,Inv10_Eff,Inv10(SMA7)%20Eff,15,%,8;0,1,0,8382,Inv11_Eff,Inv11(SMA8)%20Eff,15,%,8;0,1,0,8383,Inv12_Eff,Inv12(SMA9)%20Eff,15,%,8;">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV10G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8384,Inv10_AC_PR_600,Inv10%20AC%20PR,15,%,4;0,1,0,8382,Inv11_AC_PR_600,Inverter11%20PR,15,%,4;0,1,0,8383,Inv12_AC_PR_600,Inverter12%20PR,15,%,4;0,1,0,8414,Inv8_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv8_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV10G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8384,Inv10_DC_Vol_Coeff,Inv10(DC_Voltage),15,V,4;0,1,0,8382,Inv11_DC_Vol_Coeff,Inv11(DC_Voltage),15,V,4;0,1,0,8383,Inv12_DC_Vol_Coeff,Inv12(DC_Voltage),15,V,4;0,1,0,8414,Inv8_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv8_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV13G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8380,Inv13_Eff,Inv13 Eff,15,%,8;0,1,0,8414,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV13G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8380,Inv13_AC_PR_600,Inv13%20AC%20PR,15,%,4;0,1,0,8414,Inv13_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv13_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV13G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8380,Inv13_DC_Vol_Coeff,Inv 1(DC_Voltage),15,V,4;0,1,0,8414,Inv13_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414,Inv13_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV14G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8386,Inv14_Eff,Inv14(SMA10)%20Eff,15,%,8;0,1,0,8414,Solar_Radiation,20°%20Irradiation,5,W/m&sup2;,\'Gold\';0,1,0,8412,Solar_Radiation,10°%20Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV14G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8386,Inv14_AC_PR_600,Inv14%20AC%20PR,15,%,4;0,1,0,8414.8412,Inv14_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414.8412,Inv14_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV14G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8386,Inv14_DC_Vol_Coeff,Inv14(DC_Voltage),15,V,4;0,1,0,8414.8412,Inv14_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8414.8412,Inv14_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV15G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8387,Inv15_Eff,Inv15(SMA11)%20Eff,15,%,8;0,1,0,8412,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV15G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8387,Inv15_AC_PR_600,Inv15%20AC%20PR,15,%,4;0,1,0,8412,Inv15_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv15_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV15G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8387,Inv15_DC_Vol_Coeff,Inv15(DC_Voltage),15,V,4;0,1,0,8412,Inv15_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv15_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV16G5" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8388,Inv16_Eff,Inv16(SMA12)%20Eff,15,%,8;0,1,0,8412,Solar_Radiation,Irradiation,5,W/m&sup2;,\'Gold\';">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							else if($phase == "INV16G6" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8388,Inv16_AC_PR_600,Inv16%20AC%20PR,15,%,4;0,1,0,8412,Inv16_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv16_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							else if($phase == "INV16G7" && $park_no=="43"){
								echo '<a href="amplusExport.php?stamp='.$stamp.'&endstamp='.$endstamp.'&park_no='.$park_no.'&phase=' . $phase .'&args=0,1,0,8388,Inv16_DC_Vol_Coeff,Inv16(DC_Voltage),15,V,4;0,1,0,8412,Inv16_irrad_600,Irradiation,15,W/m&sup2;,\'Gold\';0,1,0,8412,Inv16_AC_Module_Temp_600,Module Temperature,15,&deg;C,\'darkred\'">';
								echo '    <img title="Export Meter data as .csv file" src="../imgs/xls.png">';
								echo '</a>';
							}
							
							/* Refusol End*/
							
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
<?php if(($phase == 'graph4' && $park_no == 36)|| ($phase == 'INV5G5' && $park_no == 43) || ($phase == 'INV10G5' && $park_no == 43))
{ ?>
	$(function() {
	
	<?php if($phase == 'INV5G5' && $park_no == 43){?>
	var datasets = {
			"Inv5 Efficency": {
				label: "Inv5 Efficency", points: { symbol: "circle" },
				data: <?php echo $inverEff5; ?>
			},        
			"Inv6 Efficency": {
				label: "Inv6 Efficency", points: { symbol: "circle" }, 
			data:<?php echo $inverEff6; ?> 
			}
		};
	
	<?php }else if($phase == 'graph4' && $park_no == 36) {?>
var datasets = {
			"Inv1 Efficency": {
				label: "Inv1 Efficency", points: { symbol: "circle" },
				data: <?php echo $inverEff1; ?>
			},        
			"Inv2 Efficency": {
				label: "Inv2 Efficency", points: { symbol: "circle" }, 
			data:<?php echo $inverEff2; ?> 
			},
			"Inv3 Efficency": {
				label: "Inv3 Efficency", points: { symbol: "circle" },
			data: <?php echo $inverEff3; ?>
			}
		};
<?php } else if($phase == 'INV10G5' && $park_no == 43){?>
var datasets = {
			"Inv10Efficency": {
				label: "Inv10 Efficency", points: { symbol: "circle" },
				data: <?php echo $inverEff10; ?>
			},        
			"Inv11 Efficency": {
				label: "Inv11 Efficency", points: { symbol: "circle" }, 
			data:<?php echo $inverEff11; ?> 
			},
			"Inv12 Efficency": {
				label: "Inv12 Efficency", points: { symbol: "circle" },
			data: <?php echo $inverEff12; ?>
			}
		};
<?php }?>
		// hard-code color indices to prevent them from shifting as
		// countries are turned on/off

		var i = 0;
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
<?php } else if(($phase == 'INV1G5' && $park_no == 43) || ($phase == 'INV7G5' && $park_no == 43) || ($phase == 'INV9G5' && $park_no == 43) || ($phase == 'INV13G5' && $park_no == 43) ||($phase == 'INV2G5' && $park_no == 43)
|| ($phase == 'INV3G5' && $park_no == 43) || ($phase == 'INV4G5' && $park_no == 43) || ($phase == 'INV8G5' && $park_no == 43) ||($phase == 'INV14G5' && $park_no == 43) || ($phase == 'INV15G5' && $park_no == 43) || ($phase == 'INV16G5' && $park_no == 43)){?>
	$(function() {
	
	
var datasets = {
			"Inv1 Efficency": {
				<?php if($phase == 'INV1G5'){?>
				label: "Inv1 Efficency", points: { symbol: "circle" },
				<?php }else if($phase == 'INV7G5'){?>
				label: "Inv7 Efficency", points: { symbol: "circle" },
				<?php }else if($phase == 'INV9G5'){?>
				label: "Inv9 Efficency", points: { symbol: "circle" },
				<?php }elseif($phase == 'INV13G5') {?>
				label: "Inv13 Efficency", points: { symbol: "circle" },
				<?php }else if($phase == 'INV2G5'){?>
				label: "Inv2 Efficency", points: { symbol: "circle" },
				<?php }else if($phase == 'INV3G5'){?>
				label: "Inv3 Efficency", points: { symbol: "circle" },
				<?php }else if($phase == 'INV4G5'){?>
				label: "Inv4 Efficency", points: { symbol: "circle" },
				<?php }else if($phase == 'INV8G5'){?>
				label: "Inv8 Efficency", points: { symbol: "circle" },
				<?php }else if($phase == 'INV14G5'){?>
                label: "Inv14 Efficency", points: { symbol: "circle" },
                <?php } else if($phase == 'INV15G5'){?>
                label: "Inv15 Efficency", points: { symbol: "circle" },
                <?php }else if($phase == 'INV16G5'){?>
                label: "Inv16 Efficency", points: { symbol: "circle" },
                <?php }?>
				data: <?php echo $inv1Eff1; ?>
			}
		};

		// hard-code color indices to prevent them from shifting as
		// countries are turned on/off

		var i = 0;
		$.each(datasets, function(key, val) {
			val.color = 2;
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
						<?php if($phase == 'INV1G5'){?>
						axisLabel: 'Inv1 Eff %',
						<?php }else if($phase == 'INV7G5'){?>axisLabel: 'Inv7 Eff %',
						<?php }else if($phase == 'INV9G5'){?>axisLabel: 'Inv9 Eff %',
						<?php }else if($phase == 'INV13G5'){?> axisLabel: 'Inv13 Eff %',
						<?php }else if($phase == 'INV2G5'){?>axisLabel: 'Inv2 Eff %',
                        <?php }else if($phase == 'INV3G5'){?>axisLabel: 'Inv3 Eff %',
						<?php }else if($phase == 'INV4G5'){?>axisLabel: 'Inv4 Eff %',
						<?php }else if($phase == 'INV8G5'){?>axisLabel: 'Inv8 Eff %',
						<?php }?>
						axisLabelUseCanvas: true,
						axisLabelFontSizePixels: 15,
						axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
						axisLabelPadding: 5

						
					},
					xaxis: {
						tickDecimals: 0,
						axisLabel: 'Solar Radiation (W/m²)',
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
	
<?php }

else
{ ?>

$(function() {
	
		<?php if(($phase == 'energy4' && $park_no == 36) || ($phase == 'INV5G6' && $park_no == 43) || ($phase == 'INV10G6' && $park_no == 43) ) {?>
		<?php if($phase == 'energy4' && $park_no == 36){?>
		var data = [
			{ data: <?php echo $inverPR1; ?>, points: { symbol: "circle" }, label: "INV1 AC PR (%)" },
			{ data: <?php echo $inverPR2; ?>, points: { symbol: "square" }, label: "INV2 AC PR (%)" },
			{ data: <?php echo $inverPR3; ?>, points: { symbol: "diamond" }, label: "INV3 AC PR (%)" }
			
		];
		<?php }else if($phase == 'INV5G6' && $park_no == 43){?>
		var data = [
			{ data: <?php echo $inverPR5; ?>, points: { symbol: "circle" }, label: "INV5 AC PR (%)" },
			{ data: <?php echo $inverPR6; ?>, points: { symbol: "square" }, label: "INV6 AC PR (%)" }			
		];
		<?php }else if($phase == 'INV10G6' && $park_no == 43){?>
		var data = [
			{ data: <?php echo $inverPR10; ?>, points: { symbol: "circle" }, label: "INV10 AC PR (%)" },
			{ data: <?php echo $inverPR11; ?>, points: { symbol: "square" }, label: "INV11 AC PR (%)" },
			{ data: <?php echo $inverPR12; ?>, points: { symbol: "square" }, label: "INV12 AC PR (%)" }			
		];
		<?php }?>
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
				axisLabel: 'Inverter AC PR%',
				axisLabelUseCanvas: true,
				axisLabelFontSizePixels: 15,
				axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
				axisLabelPadding: 20

			},
		});
		<?php } else if(($phase == 'INV1G6' && $park_no == 43) || ($phase == 'INV7G6' && $park_no == 43) || ($phase == 'INV9G6' && $park_no == 43) || ($phase == 'INV13G6' && $park_no == 43)
		|| ($phase == 'INV2G6' && $park_no == 43) || ($phase == 'INV3G6' && $park_no == 43) || ($phase == 'INV4G6' && $park_no == 43) ||($phase == 'INV8G6' && $park_no == 43)
		||($phase == 'INV14G6' && $park_no == 43) || ($phase == 'INV15G6' && $park_no == 43) || ($phase == 'INV16G6' && $park_no == 43)){?>
		var data = [
			{ data: <?php echo $inv1PR1; ?>, points: { symbol: "circle" },
						<?php if($phase == 'INV1G6'){?>
						label: "INV1 AC PR (%)" }
						<?php }else if($phase == 'INV7G6'){?>label: "INV7 AC PR (%)" }
						<?php }elseif($phase == 'INV9G6'){?>label: "INV9 AC PR (%)" }
						<?php }else if($phase == 'INV13G6') {?>label: "INV13 AC PR (%)" }
						<?php }else if($phase == 'INV2G6'){?>label: "INV2 AC PR (%)" }
						<?php } else if($phase == 'INV3G6'){?>label: "INV3 AC PR (%)" }
						<?php } else if($phase == 'INV4G6'){?>label: "INV4 AC PR (%))" }
						<?php }else if($phase == 'INV8G6'){?>label: "INV8 AC PR (%)" }
						<?php }else if($phase == 'INV14G6'){?>label: "INV14 AC PR (%)" }
						<?php }else if($phase == 'INV15G6'){?>label: "INV15 AC PR (%)" }
						<?php }else if($phase == 'INV16G6'){?>label: "INV16 AC PR (%)" }
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
						<?php if($phase == 'INV1G6'){?>axisLabel: 'Inv1 AC PR (%)',
						<?php }else if($phase == 'INV7G6'){?>axisLabel: 'Inv7 AC PR (%)',
						<?php }elseif($phase == 'INV9G6'){?>axisLabel: 'Inv9 AC PR (%)',
						<?php }else if($phase == 'INV13G6') {?>axisLabel: 'Inv13 AC PR (%)',
						<?php }else if($phase == 'INV2G6'){?>axisLabel: 'Inv2 AC PR (%)',
						<?php }else if($phase == 'INV3G6'){?>axisLabel: 'Inv3 AC PR (%)',
                        <?php }else if($phase == 'INV4G6'){?>axisLabel: 'Inv4 AC PR (%)',
						<?php }else if($phase == 'INV8G6'){?>axisLabel: 'Inv8 AC PR (%)',
						<?php }else if($phase == 'INV14G6'){?>axisLabel: 'Inv14 AC PR (%)',
						<?php }else if($phase == 'INV15G6'){?>axisLabel: 'Inv15 AC PR (%)',
						<?php }else if($phase == 'INV16G6'){?>axisLabel: 'Inv16 AC PR (%)',
						<?php }?>
				axisLabelUseCanvas: true,
				axisLabelFontSizePixels: 15,
				axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
				axisLabelPadding: 20

			},
		});
		
		<?php } else if(($phase == 'INV1G7' && $park_no == 43) || ($phase == 'INV7G7' && $park_no == 43) || ($phase == 'INV9G7' && $park_no == 43) || ($phase == 'INV13G7' && $park_no == 43)
		||($phase == 'INV2G7' && $park_no == 43) || ($phase == 'INV3G7' && $park_no == 43) || ($phase == 'INV4G7' && $park_no == 43) ||($phase == 'INV8G7' && $park_no == 43)
		||($phase == 'INV14G7' && $park_no == 43) || ($phase == 'INV15G7' && $park_no == 43) || ($phase == 'INV16G7' && $park_no == 43)){?>
		
		var data = [
			{ data: <?php echo $inv1DCPR; ?>, points: { symbol: "square" }, 
			
						<?php if($phase == 'INV1G7'){?>label: "INV1 Voltage(V)" }
						<?php }else if($phase == 'INV7G7'){?>label: "INV7 Voltage(V)" }
						<?php }elseif($phase == 'INV9G7'){?>label: "INV9 Voltage(V)" }
						<?php }else if($phase == 'INV13G7') {?>label: "INV13 Voltage(V)" }
						<?php }else if($phase == 'INV2G7'){?>label: "INV2 Voltage(V)" }
						<?php }else if($phase == 'INV3G7'){?>label: "INV3 Voltage(V)" }
                        <?php }else if($phase == 'INV4G7'){?>label: "INV4 Voltage(V)" }
						<?php }else if($phase == 'INV8G7'){?>label: "INV8 Voltage(V)" }
						<?php }else if($phase == 'INV14G7'){?>label: "INV14 Voltage(V)" }
						<?php }else if($phase == 'INV15G7'){?>label: "INV15 Voltage(V)" }
						<?php }else if($phase == 'INV16G7'){?>label: "INV16 Voltage(V)" }
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
					axisLabelPadding: 5,
					labelAngle: -90

                 },
			yaxis: {
						<?php if($phase == 'INV1G7'){?>axisLabel: 'Inv1 DC Volt (V)',
						<?php }else if($phase == 'INV7G7'){?>axisLabel: 'Inv7 DC Volt (V)',
						<?php }else if($phase == 'INV9G7'){?>axisLabel: 'Inv9 DC Volt (V)',
						<?php }else if($phase == 'INV13G7') {?>axisLabel: 'Inv13 DC Volt (V)',
						<?php }else if($phase == 'INV2G7') {?>axisLabel: 'Inv2 DC Volt (V)',
						<?php }else if($phase == 'INV3G7') {?>axisLabel: 'Inv3 DC Volt (V)',
                        <?php }else if($phase == 'INV4G7') {?>axisLabel: 'Inv4 DC Volt (V)',
						<?php }else if($phase == 'INV8G7'){?>axisLabel: 'Inv8 DC Volt (V)',
						<?php }else if($phase == 'INV14G7'){?>axisLabel: 'Inv14 DC Volt (V)',
						<?php }else if($phase == 'INV15G7'){?>axisLabel: 'Inv15 DC Volt (V)',
						<?php }else if($phase == 'INV16G7'){?>axisLabel: 'Inv16 DC Volt (V)',
						<?php }?>
			axisLabelUseCanvas: true,
			axisLabelFontSizePixels: 15,
			axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
			axisLabelPadding: 5

               },
		});
		
		<?php }else if(($phase == 'energy5' && $park_no == 36) || ($phase == 'INV5G7' && $park_no == 43) || ($phase == 'INV10G7' && $park_no == 43)) {?>
		<?php if($phase == 'energy5' && $park_no == 36){?>
		var data = [
			{ data: <?php echo $inverDCPR1; ?>, points: { symbol: "circle" }, label: "INV1 Voltage(V)" },
			{ data: <?php echo $inverDCPR2; ?>, points: { symbol: "square" }, label: "INV2 Voltage(V)" },
			{ data: <?php echo $inverDCPR3; ?>, points: { symbol: "diamond" }, label: "INV3 Voltage(V)" }
			
		];
		<?php } else if($phase == 'INV5G7' && $park_no == 43){?>
		var data = [
			{ data: <?php echo $inv5DCPR; ?>, points: { symbol: "circle" }, label: "INV5 Voltage(V)" },
			{ data: <?php echo $inv6DCPR; ?>, points: { symbol: "square" }, label: "INV6 Voltage(V)" }	
		];
		<?php } else if($phase == 'INV10G7' && $park_no == 43){?>
		var data = [
			{ data: <?php echo $inv10DCPR; ?>, points: { symbol: "circle" }, label: "INV10 Voltage(V)" },
			{ data: <?php echo $inv11DCPR; ?>, points: { symbol: "square" }, label: "INV11 Voltage(V)" },
			{ data: <?php echo $inv12DCPR; ?>, points: { symbol: "diamond" }, label: "INV12 Voltage(V)" }
			
		];
		<?php }?>
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
                     axisLabel: 'Module Temperature(°C)',
                      axisLabelUseCanvas: true,
                      axisLabelFontSizePixels: 18,
					axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
					axisLabelPadding: 5,
					labelAngle: -90

                 },
            yaxis: {
		axisLabel: 'Inv DC Volt(V)',
        axisLabelUseCanvas: true,
        axisLabelFontSizePixels: 15,
        axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
        axisLabelPadding: 5

               },
		});
		<?php }?>
		
		

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

						<?php if($phase == 'energy4' && $park_no == 36) {?>
							$("#tooltip").html(item.series.label + " at " + x + " Deg C = " + y)
						<?php } else if(($phase == 'INV1G6' && $park_no == 43) || ($phase == 'INV7G6' && $park_no == 43) || ($phase == 'INV9G6' && $park_no == 43) || ($phase == 'INV13G6' && $park_no == 43)
						|| ($phase == 'INV2G6' && $park_no == 43) || ($phase == 'INV3G6' && $park_no == 43) || ($phase == 'INV4G6' && $park_no == 43) || ($phase == 'INV5G6' && $park_no == 43) || ($phase == 'INV10G6' && $park_no == 43)
						||($phase == 'INV8G6' && $park_no == 43)||($phase == 'INV14G6' && $park_no == 43)||($phase == 'INV15G6' && $park_no == 43)||($phase == 'INV16G6' && $park_no == 43)){?>
							$("#tooltip").html(item.series.label + " at " + x + " Deg C = " + y)
						<?php }else{ ?>
							$("#tooltip").html(item.series.label + " at " + x + " = " + y)
						<?php } ?>
				   
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
<?php } ?>
	</script>
			
			
          
        </div>
    </body>
</html>

