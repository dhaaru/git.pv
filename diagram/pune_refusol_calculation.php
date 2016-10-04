<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
set_time_limit(900);
$slmnth=date("m");
$crtyr=date("Y");
$sdd=date("d");

date_default_timezone_set('UTC');

if((isset($_REQUEST['date']))&&($_REQUEST['date']!=''))
{
 $sdd=stripslashes(trim($_REQUEST['date']));
}
if((isset($_REQUEST['month']))&&($_REQUEST['month']!=''))
{
 $slmnth=stripslashes(trim($_REQUEST['month']));
}
if((isset($_REQUEST['year']))&&($_REQUEST['year']!=''))
{
 $crtyr=stripslashes(trim($_REQUEST['year']));
}

$startTime = mktime(0, 0, 0, $slmnth, $sdd, $crtyr);
$endTime = mktime(0, 0, 0, $slmnth, $sdd+1, $crtyr);


$table="pune2_calculation";

function getdeviceData($startTime,$endTime,$deviceId,$field)
{
	 $query="select ts,value,device from _devicedatavalue where ts > $startTime and ts < $endTime and (device='".$deviceId."') and (field='".$field."')";
	//echo "<br>".$query."<br>";
	$sql = mysql_query($query);
	if(mysql_num_rows($sql) > 0){
		$tsdata= array();
		$valuedata = array();
		$result = array();
		while($rlt = mysql_fetch_array($sql)){
			$tsdata[] = $rlt['ts'];
			$valuedata[] = $rlt['value'].','.$rlt['device'];	
		}
	}
	return array_combine($tsdata,$valuedata);
}

function getdeviceData2($startTime,$endTime,$deviceId,$field1,$field2)
{
	
	if($field2!==''){
		$query="select ts,CASE WHEN value = 0 then 0 ELSE EXP(SUM(LOG(value)))/1000 end AS value,device from _devicedatavalue where ts > $startTime and ts < $endTime and (device = '".$deviceId."') and field in('".$field1."','".$field2."') group by ts";
		}
		else{
			if($field1=='Pac'){$div=1000;}else{$div=1;}
	 $query="select ts,value/$div as value,device from _devicedatavalue where ts > $startTime and ts < $endTime and device in ('".$deviceId."') and (field='".$field1."')";
		}
	//echo "<br>".$query."<br>";
	$sql = mysql_query($query);
	if(mysql_num_rows($sql) > 0){
		$tsdata= array();
		$valuedata = array();
		$result = array();
		while($rlt = mysql_fetch_array($sql)){
			$tsdata[] = $rlt['ts'];
			$valuedata[] = $rlt['value'].','.$rlt['device'];	
		}
	}
	return array_combine($tsdata,$valuedata);
}
$inverterACData =array();
$iverterDCData = array();
//device =array('8379','8408','8410','8409','8458','8456','8381','8457','8378','8384','8382','8383','8380','8386','8387','8388'); 
$device =array('8410','8409');
$device_ref =array('8379','8381','8378','8380'); //4 refusol inverter
$device_sma =array('8410','8409','8458','8456','8457','8384','8382','8383','8408','8386','8387','8388'); //12 SMA


	
	
for($i=0;$i<count($device);$i++){
	if(in_array($device[$i], $device_sma)) {
	$inverterACData[$i] =getdeviceData2($startTime,$endTime,$device[$i],'Pac','');
	$inverterDCData[$i] =getdeviceData2($startTime,$endTime,$device[$i],'Upv-Ist','Ipv');
	$inverterDCVolt[$i] =getdeviceData2($startTime,$endTime,$device[$i],'Upv-Ist','');
	//print_r($inverterDCData[$i]);echo "<br>";
		}
	else{
	$inverterACData[$i] =getdeviceData($startTime,$endTime,$device[$i],'AC_Power');
	$inverterDCData[$i] =getdeviceData($startTime,$endTime,$device[$i],'DC_Power');
	$inverterDCVolt[$i] =getdeviceData($startTime,$endTime,$device[$i],'DC_Voltage');
	//print_r($inverterDCData[$i]);echo "<br>";
	}
}

//DC voltage coefficient
foreach ($inverterDCVolt as $inverterDCVoltageData){
	foreach($inverterDCVoltageData as $dcindex1=>$dcvoltage){
	$dcVoltage = explode(',',$dcvoltage);
	 $device = $dcVoltage[1];
	if($device == 8379){$n=1; $sensor=8414; $mod=11; $voc=85.6;}
elseif($device == 8408){$n=2; $sensor=8414.8412; $mod=7; $voc=85.6;}
elseif($device == 8410){$n=3; $sensor=8414; $mod=14; $voc=45.3;}
elseif($device == 8409){$n=4; $sensor=8412; $mod=14; $voc=45.3;}
elseif($device == 8458){$n=5; $sensor=8412; $mod=14; $voc=41.9;}
elseif($device == 8456){$n=6; $sensor=8412; $mod=14; $voc=41.9;}
elseif($device == 8381){$n=7; $sensor=8414.8412; $mod=21; $voc=41.9;}
elseif($device == 8457){$n=8; $sensor=8414; $mod=7; $voc=85.6;}
elseif($device == 8378){$n=9; $sensor=8412; $mod=21; $voc=45.3;}
elseif($device == 8384){$n=10; $sensor=8414; $mod=9; $voc=64.9;}
elseif($device == 8382){$n=11; $sensor=8414; $mod=9; $voc=64.9;}
elseif($device == 8383){$n=12; $sensor=8414; $mod=9; $voc=64.9;}
elseif($device == 8380){$n=13; $sensor=8414; $mod=21; $voc=41.9;}
elseif($device == 8386){$n=14; $sensor=8414.8412; $mod=9; $voc=64.9;}
elseif($device == 8387){$n=15; $sensor=8412; $mod=9; $voc=64.9;}
elseif($device == 8388){$n=16; $sensor=8412; $mod=14; $voc=45.3;}
//above 600
	$stime= $dcindex1-10;
	$etime=$dcindex1+10;
	
	if($sensor=="8414.8412"){
	$arrv2=explode(".",$sensor);
	 $devid=implode(",",$arrv2);
	 $qry1 = "select ROUND(sum(value)/2,2) as groupval, ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and device in (".$devid.") and (field='Solar_Radiation') group by ts";	} 
	else{
	 $qry1="select  ROUND((value),2) as groupval,ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and (device=$sensor) and (field='Solar_Radiation') and value > 600";	}
	 		//echo "<br>".$qry1."<br>";	
			$qry_irrad1= mysql_query($qry1);
			$numCount1 =  mysql_num_rows($qry_irrad1); 
			if($numCount1!=0)
				{
					$fetchdata1=mysql_fetch_array($qry_irrad1);
					if($fetchdata1['groupval']>=600){
					 $ts1 =$fetchdata1['ts'];
					
					//if($ts1==$dcindex1){
						if($ts1>=$stime || $ts1<=$etime){
						$DC_Vol_Coeff_fld= "Inv".$n."_DC_Vol_Coeff";
						$DC_Vol_Coeff_Val= round((($dcVoltage[0]/($mod*$voc))*100),2);
						//echo $device."-".$dcVoltage[0]."-".$mod."-".$voc."<br>";
							
							$insq5="REPLACE ".$table." (ts,device,field,value) values('".$dcindex1."','".$device."','".$DC_Vol_Coeff_fld."','".$DC_Vol_Coeff_Val."')";
							//echo "<br>".$insq5."<br>";
							$iqry5= mysql_query($insq5) or die(mysql_error());
					}		
					}
				}
			}
		}


if(!empty($inverterACData) && !empty($inverterDCData)){
	foreach ($inverterACData as $inverterACPowerData){
		foreach ($inverterDCData as $inverterDCPowerData ){
			foreach($inverterACPowerData as $acindex1=>$acpower1){
				foreach($inverterDCPowerData as $dcindex1=>$dcpower1){
				if($acindex1 == $dcindex1)
					{
						$irradation1=0;
						$acPower = explode(',',$acpower1);
						$dcPower = explode(',',$dcpower1);
						$device = $acPower[1];
							
						if($device == 8379){$n=1; $sensor=8414; $totMod=111; $eff=0.201; $area=2.067*1.046;}
					elseif($device == 8408){$n=2; $sensor=8414.8412; $totMod=111; $eff=0.201; $area=2.067*1.046;}
					elseif($device == 8410){$n=3; $sensor=8414; $totMod=210; $eff=0.155; $area=1.956*0.941;}
					elseif($device == 8409){$n=4; $sensor=8412; $totMod=210; $eff=0.155; $area=1.956*0.941;}
					elseif($device == 8458){$n=5; $sensor=8412; $totMod=252; $eff=0.155; $area=1.956*0.992;}
					elseif($device == 8456){$n=6; $sensor=8412; $totMod=252; $eff=0.155; $area=1.956*0.992;}
					elseif($device == 8381){$n=7; $sensor=8414.8412; $totMod=252; $eff=0.155; $area=1.956*0.992;}
					elseif($device == 8457){$n=8; $sensor=8414; $totMod=111; $eff=0.201; $area=2.067*1.046;}
					elseif($device == 8378){$n=9; $sensor=8412; $totMod=210; $eff=0.155; $area=1.956*0.941;}
					elseif($device == 8384){$n=10; $sensor=8414; $totMod=180; $eff=0.204; $area=1.559*1.046;}
					elseif($device == 8382){$n=11; $sensor=8414; $totMod=180; $eff=0.204; $area=1.559*1.046;}
					elseif($device == 8383){$n=12; $sensor=8414; $totMod=180; $eff=0.204; $area=1.559*1.046;}
					elseif($device == 8380){$n=13; $sensor=8414; $totMod=252; $eff=0.155; $area=1.956*0.992;}
					elseif($device == 8386){$n=14; $sensor=8414.8412; $totMod=180; $eff=0.204; $area=1.559*1.046;}
					elseif($device == 8387){$n=15; $sensor=8412; $totMod=180; $eff=0.204; $area=1.559*1.046;}
					elseif($device == 8388){$n=16; $sensor=8412; $totMod=210; $eff=0.155; $area=1.956*0.941;}
					
						$eff_fld="Inv".$n."_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acpower1/$dcpower1)*100),2);
						
						$insq3="REPLACE ".$table." (ts,device,field,value) values('".$acindex1."','".$device."','".$eff_fld."','".$eff."')";
					//	echo "<br>".$insq3."<br>";
						$iqry3= mysql_query($insq3) or die(mysql_error());
						}
					
					
					$stime= $acindex1-10;
					$etime= $acindex1+10;
						//////above 250
						if($sensor=="8414.8412"){
						$arrv2=explode(".",$sensor);
						$devid=implode(",",$arrv2);								
			$qry2 = "select ROUND(sum(value)/2,2) as groupval,ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and device in (".$devid.") and (field='Solar_Radiation') group by ts";
  }
			else{
			$qry2="select  ROUND((value),2) as groupval,ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and (device=$sensor) and (field='Solar_Radiation') and value >= 250";
	}				//echo "<br>".$qry2."<br>";	
						$qry_irrad1= mysql_query($qry2);
						$numCount1 =  mysql_num_rows($qry_irrad1); 
						if($numCount1!=0)
						{
							$fetchdata1=mysql_fetch_array($qry_irrad1);
							if($fetchdata1['groupval']>=250){
							$ts1 =$fetchdata1['ts'];
							$irradation1 = $fetchdata1['groupval'];
							$stime= $ts1-10;
							$etime= $ts1+10;
							if($sensor=="8414.8412"){
							$qry_module=mysql_query("select sum(value)/2 as value FROM _devicedatavalue where ts BETWEEN $stime and $etime and device in(".$devid.") and (field='Module_Temperature') group by ts");
							}else{
								$qry_module=mysql_query("select  value FROM _devicedatavalue where ts BETWEEN $stime and $etime and (device=$sensor) and (field='Module_Temperature')");
								}							
							$fetchdata3=mysql_fetch_array($qry_module);
							$module_tempVal1 =$fetchdata3['value'];
							$ACmodule_fld1="Inv".$n."_AC_Module_Temp_250";
							$pr_fld="Inv".$n."_PR";
							$inv_irr = "Inv".$n."_irrad_250";
							//$expPower=$irradation1 * 111 * (2.067*1.046) * (0.201)/1000;
							$expPower=$irradation1 * $totMod * ($area) * ($eff)/1000;
							//echo "(".$acPower[0].")/".$irradation1 ."*". $totMod ."*". ($area) ."*". ($eff)."<br>";
							$pr= round((($acPower[0]/$expPower)*100),2);
							
							$insq1="REPLACE ".$table." (ts,device,field,value) values('".$acindex1."','".$device."','".$pr_fld."','".$pr."'),('".$acindex1."','".$sensor."','".$inv_irr."','".$irradation1."'),('".$acindex1."','".$sensor."','".$ACmodule_fld1."','".$module_tempVal1."')";
							//echo "<br>".$insq1."<br>";
							$iqry1= mysql_query($insq1) or die(mysql_error());
						}}
						
						//////above 600
						$stime= $acindex1-10;
						$etime= $acindex1+10;
						if($sensor=="8414.8412"){
						$arrv2=explode(".",$sensor);
						$devid=implode(",",$arrv2);								
$qry3 = "select ROUND(sum(value),2)/2 as groupval,ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and device in (".$devid.") and (field='Solar_Radiation') group by ts";
  }
else{
$qry3="select  ROUND((value),3) as groupval,ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and (device=$sensor) and (field='Solar_Radiation') and value >= 600";
						}		//echo "<br>".$qry3."<br>";	
						$qry_irrad2= mysql_query($qry3);
						$numCount2 =  mysql_num_rows($qry_irrad2); 
						if($numCount2!=0)
						{
							$fetchdata2=mysql_fetch_array($qry_irrad2);
							if($fetchdata1['groupval']>=600){
							$ts2 =$fetchdata2['ts'];
							$irradation2 = $fetchdata2['groupval'];
							$stime= $ts2-10;
							$etime= $ts2+10;
							if($sensor=="8414.8412"){
							$qry_module=mysql_query("select sum(value)/2 as value FROM _devicedatavalue where ts BETWEEN $stime and $etime and device in(".$devid.") and (field='Module_Temperature') group by ts");
							}else{
								$qry_module=mysql_query("select  value FROM _devicedatavalue where ts BETWEEN $stime and $etime and (device=$sensor) and (field='Module_Temperature')");
								}	
							$fetchdata3=mysql_fetch_array($qry_module);
							$module_tempVal2 =$fetchdata3['value'];
							$ACmodule_fld2="Inv".$n."_AC_Module_Temp_600";
							$pr_fld2="Inv".$n."_AC_PR_600";
							$inv_irr2 = "Inv".$n."_irrad_600";
							$expPower=$irradation2 * $totMod * ($area) * ($eff)/1000;
							//echo $irradation1 ."*". $totMod ."*". ($area) ."*". ($eff)."<br>";
							$pr2= round((($acPower[0]/$expPower)*100),2);
							
							$insq2="REPLACE ".$table." (ts,device,field,value) values('".$acindex1."','".$device."','".$pr_fld2."','".$pr2."'),('".$acindex1."','".$sensor."','".$inv_irr2."','".$irradation2."'),('".$acindex1."','".$sensor."','".$ACmodule_fld2."','".$module_tempVal2."')";
							//echo "<br>".$insq2."<br>";
							$iqry2= mysql_query($insq2) or die(mysql_error());
						}}
						
						//inverter efficient
						
					}
				}
			}
		}
	}		
}

	$inverterACData1 =getdeviceData($startTime,$endTime,8379,'AC_Power');
	$inverterDCData1 =getdeviceData($startTime,$endTime,8379,'DC_Power');
	
	$inverterACData2 =getdeviceData2($startTime,$endTime,8408,'Pac','');
	$inverterDCData2 =getdeviceData2($startTime,$endTime,8408,'Upv-Ist','Ipv');
	
	$inverterACData3 =getdeviceData2($startTime,$endTime,8410,'Pac','');
	$inverterDCData3 =getdeviceData2($startTime,$endTime,8410,'Upv-Ist','Ipv');

	$inverterACData4 =getdeviceData2($startTime,$endTime,8409,'Pac','');
	$inverterDCData4 =getdeviceData2($startTime,$endTime,8409,'Upv-Ist','Ipv');

	$inverterACData5 =getdeviceData2($startTime,$endTime,8458,'Pac','');
	$inverterDCData5 =getdeviceData2($startTime,$endTime,8458,'Upv-Ist','Ipv');

	$inverterACData6 =getdeviceData2($startTime,$endTime,8456,'Pac','');
	$inverterDCData6 =getdeviceData2($startTime,$endTime,8456,'Upv-Ist','Ipv');

	$inverterACData7 =getdeviceData($startTime,$endTime,8381,'AC_Power');
	$inverterDCData7 =getdeviceData($startTime,$endTime,8381,'DC_Power');
	
	$inverterACData8 =getdeviceData2($startTime,$endTime,8457,'Pac','');
	$inverterDCData8 =getdeviceData2($startTime,$endTime,8457,'Upv-Ist','Ipv');

	$inverterACData9 =getdeviceData($startTime,$endTime,8378,'AC_Power');
	$inverterDCData9 =getdeviceData($startTime,$endTime,8378,'DC_Power');
	
	$inverterACData10 =getdeviceData2($startTime,$endTime,8384,'Pac','');
	$inverterDCData10 =getdeviceData2($startTime,$endTime,8384,'Upv-Ist','Ipv');
	
	$inverterACData11 =getdeviceData2($startTime,$endTime,8382,'Pac','');
	$inverterDCData11 =getdeviceData2($startTime,$endTime,8382,'Upv-Ist','Ipv');
	
	$inverterACData12 =getdeviceData2($startTime,$endTime,8383,'Pac','');
	$inverterDCData12 =getdeviceData2($startTime,$endTime,8383,'Upv-Ist','Ipv');
	
	$inverterACData13 =getdeviceData($startTime,$endTime,8380,'AC_Power');
	$inverterDCData13 =getdeviceData($startTime,$endTime,8380,'DC_Power');
	
	$inverterACData14 =getdeviceData2($startTime,$endTime,8386,'Pac','');
	$inverterDCData14 =getdeviceData2($startTime,$endTime,8386,'Upv-Ist','Ipv');
	
	$inverterACData15 =getdeviceData2($startTime,$endTime,8387,'Pac','');
	$inverterDCData15 =getdeviceData2($startTime,$endTime,8387,'Upv-Ist','Ipv');
	
	$inverterACData16 =getdeviceData2($startTime,$endTime,8388,'Pac','');
	$inverterDCData16 =getdeviceData2($startTime,$endTime,8388,'Upv-Ist','Ipv');
	
	
			foreach($inverterACData1 as $acindex=>$acpower){
				foreach($inverterDCData1 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv1_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}

			foreach($inverterACData2 as $acindex=>$acpower){
				foreach($inverterDCData2 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv2_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						//echo $acPower[0]."/".$dcPower[0];
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
					}}}}
			
			foreach($inverterACData3 as $acindex=>$acpower){
				foreach($inverterDCData3 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv3_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}					
			foreach($inverterACData4 as $acindex=>$acpower){
				foreach($inverterDCData4 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv4_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}
			foreach($inverterACData5 as $acindex=>$acpower){
				foreach($inverterDCData5 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv5_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}
						
			foreach($inverterACData6 as $acindex=>$acpower){
				foreach($inverterDCData6 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv6_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}

			foreach($inverterACData7 as $acindex=>$acpower){
				foreach($inverterDCData7 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv7_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
					}}}}
			
			foreach($inverterACData8 as $acindex=>$acpower){
				foreach($inverterDCData8 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv8_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}					
			foreach($inverterACData9 as $acindex=>$acpower){
				foreach($inverterDCData9 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv9_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}
			foreach($inverterACData10 as $acindex=>$acpower){
				foreach($inverterDCData10 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv10_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}
			foreach($inverterACData11 as $acindex=>$acpower){
				foreach($inverterDCData11 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv11_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}

			foreach($inverterACData12 as $acindex=>$acpower){
				foreach($inverterDCData12 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv12_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
					}}}}
			
			foreach($inverterACData13 as $acindex=>$acpower){
				foreach($inverterDCData13 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv13_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}					
			foreach($inverterACData14 as $acindex=>$acpower){
				foreach($inverterDCData14 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv14_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}
			foreach($inverterACData15 as $acindex=>$acpower){
				foreach($inverterDCData15 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv15_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}
						
			foreach($inverterACData16 as $acindex=>$acpower){
				foreach($inverterDCData16 as $dcindex=>$dcpower){
				if($acindex == $dcindex)
					{	$acPower = explode(',',$acpower);
						$dcPower = explode(',',$dcpower);
						$device = $acPower[1];
						$eff_fld="Inv16_Eff";
						if( $dcPower[0]>0){
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						mysql_query("REPLACE ".$table." (ts,device,field,value) values('".$acindex."','".$device."','".$eff_fld."','".$eff."')");
						}}}}
						
	echo"<br>". "finished";																
//Energy meter system PR
 //  $qry_irrad4=mysql_query("select ts, ROUND((value),2) as groupval FROM _devicedatavalue where ts > $startTime and ts < $endTime and  (device='7794') and (field='Solar_Radiation') and value > 250");
  /* 
  $qry_irrad4=mysql_query(" select ts, ROUND(SUM(value)/2,2) as groupval FROM _devicedatavalue where ts > $startTime and ts < $endTime and  ((device='8412') or (device='8414')) and (field='Solar_Radiation') group by ts");
   
   while($rlt = mysql_fetch_array($qry_irrad4)){
  	 
  	 $En_irradation1 = $rlt['groupval'];
	if($En_irradation1 >= 250) {
		$timestamp = $rlt ['ts'];
		  $qry_enacpower = mysql_query("select ts, ROUND((value),2) as groupval FROM _devicedatavalue where ts=$timestamp and (device='8240') and (field='Activepower_Total')");
		  $numCounten =  mysql_num_rows($qry_enacpower); 
		  if( $numCounten!=0){ 
				$fetchdata5=mysql_fetch_array($qry_enacpower);
				$En_ActPowValue =$fetchdata5['groupval']; 
				$En_ActPowFld="Energy_Actual_Pow";
			}
      $qry_temp =mysql_query("select ts, ROUND(SUM(value)/2,2) as groupval FROM _devicedatavalue where ts=$timestamp and ((device='8412') or (device='8414')) and (field='Module_Temperature') group by ts");
      $numCount2 =  mysql_num_rows($qry_temp); 
		if( $numCount2!=0){ 
			$fetchdata5=mysql_fetch_array($qry_temp);
			$En_ModuleValue =$fetchdata5['groupval']; 
			$En_Module_Temp="Energy_Module_Temp";
		}
		$Energy_irrad ="En_irradiation";
		$System_PR="System_PR";
		$SPR = round(($En_ActPowValue)/(($En_irradation1*111*(2.067*1.046)* 2.01)/1000)*100,2); 
        $insq4="REPLACE ".$table."(ts,device,field,value) values('".$timestamp."',8240,'".$System_PR."','".$SPR."'),('".$timestamp."',8412,'".$Energy_irrad."','".$En_irradation1."'),('".$timestamp."', 8412 ,'".$En_Module_Temp."','".$En_ModuleValue."'),('".$timestamp."', 8240 ,'".$En_ActPowFld."','".$En_ActPowValue."')";
		echo "<br>".$insq4."<br>";
		$iqry4= mysql_query($insq4) or die(mysql_error());
   }
   }
*/
?>
