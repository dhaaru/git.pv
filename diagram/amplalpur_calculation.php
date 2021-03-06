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


$table="ampLalpur_calculation";

function getdeviceData($startTime,$endTime,$deviceId,$field)
{
	
	$query="select ts,(value) as value,device from _devicedatavalue where ts > $startTime and ts < $endTime and (device='".$deviceId."') and (field='".$field."') group by ts";	
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
$device =array('12669','12672','12670','12675');

for($i=0;$i<count($device);$i++){
	$inverterACData[$i] =getdeviceData($startTime,$endTime,$device[$i],'PAC_Total');
	$inverterDCData[$i] =getdeviceData($startTime,$endTime,$device[$i],'PDC_Total');
	$inverterDCVolt[$i] =getdeviceData($startTime,$endTime,$device[$i],'UAC_Mittelwert');
}

$inverterACData1 =getdeviceData($startTime,$endTime,12669,'PAC_Total');
$inverterDCData1 =getdeviceData($startTime,$endTime,12669,'PDC_Total');


$inverterACData2 =getdeviceData($startTime,$endTime,12672,'PAC_Total');
$inverterDCData2 =getdeviceData($startTime,$endTime,12672,'PDC_Total');

$inverterACData3 =getdeviceData($startTime,$endTime,12670,'PAC_Total');
$inverterDCData3 =getdeviceData($startTime,$endTime,12670,'PDC_Total');

$inverterACData4 =getdeviceData($startTime,$endTime,12675,'PAC_Total');
$inverterDCData4 =getdeviceData($startTime,$endTime,12675,'PDC_Total');

//print_r($inverterACData);

//DC voltage coefficient
foreach ($inverterDCVolt as $inverterDCVoltageData){
	foreach($inverterDCVoltageData as $dcindex1=>$dcvoltage){
	$dcVoltage = explode(',',$dcvoltage);
	$device = $dcVoltage[1];
	$stime= $dcindex1-90;
	$etime=$dcindex1+90;
	$qry_irrad1=mysql_query("select  ROUND((value),3) as groupval,ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and (device='10142') and (field='Solar_Radiation') and value > 600");
	//echo "<br>select  ROUND((value),3) as groupval,ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and (device='9316') and (field='Solar_Radiation') and value > 600";
	$numCount1 =  mysql_num_rows($qry_irrad1); 
			if($numCount1!=0)
				{	 
					$fetchdata1=mysql_fetch_array($qry_irrad1);
					$ts1 =$fetchdata1['ts'];
					
					if($ts1>=$stime || $ts1<=$etime){
						$DC_Vol_Coeff_fld= "DC_Vol_Coeff";
						$dcVoltage= $dcVoltage[0];
						$DC_Vol_Coeff_Val= round((($dcVoltage/(20*45.4))*100),2);
							
						$insq5="REPLACE ".$table." (ts,device,field,value) values('".$dcindex1."','".$device."','".$DC_Vol_Coeff_fld."','".$DC_Vol_Coeff_Val."')";
						//echo "<br>".$insq5."<br>";
						$iqry5= mysql_query($insq5) or die(mysql_error());
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
						
						//////above 250
						$stime= $acindex1-100;
						$etime= $acindex1+100;
						$qry_irrad1=mysql_query("select  ROUND((value),3) as groupval,ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and (device='10142') and (field='Solar_Radiation') and value > 250");
						$numCount1 =  mysql_num_rows($qry_irrad1); 
						if($numCount1!=0)
						{
							$fetchdata1=mysql_fetch_array($qry_irrad1);
							$ts1 =$fetchdata1['ts'];
							$irradation1 = $fetchdata1['groupval'];
							$qry_module=mysql_query("select  value FROM _devicedatavalue where ts=$ts1 and (device='10142') and (field='Module_Temperature')");
							$fetchdata3=mysql_fetch_array($qry_module);
							$module_tempVal1 =$fetchdata3['value'];
							$ACmodule_fld1="AC_Module_Temp_250";
							$pr_fld="Inv_PR";
							$inv_irr = "Inv_irrad_250";
							$expPower=($irradation1 * 840 *(1.9503) * (0.155))/1000;
							echo $actualPower = $acPower[0]/100;
							$pr= round((($actualPower/$expPower)*100),2);
							
							$insq1="REPLACE ".$table." (ts,device,field,value) values('".$acindex1."','".$device."','".$pr_fld."','".$pr."'),('".$acindex1."',10142,'".$inv_irr."','".$irradation1."'),('".$acindex1."',10142,'".$ACmodule_fld1."','".$module_tempVal1."')";
							echo "<br>".$insq1."<br>";
							$iqry1= mysql_query($insq1) or die(mysql_error());
						}
						
						//////above 600
						$qry_irrad2=mysql_query("select  ROUND((value),3) as groupval,ts FROM _devicedatavalue where ts BETWEEN $stime AND $etime and (device='10142') and (field='Solar_Radiation') and value > 600");
						$numCount2 =  mysql_num_rows($qry_irrad2); 
						if($numCount2!=0)
						{
							$fetchdata2=mysql_fetch_array($qry_irrad2);
							$ts2 =$fetchdata2['ts'];
							$irradation2 = $fetchdata2['groupval'];
							$qry_module=mysql_query("select  value FROM _devicedatavalue where ts=$ts2 and (device='10142') and (field='Module_Temperature')");
							$fetchdata3=mysql_fetch_array($qry_module);
							$module_tempVal2 =$fetchdata3['value'];
							$ACmodule_fld2="AC_Module_Temp_600";
							$pr_fld2="Inv_AC_PR";
							$inv_irr2 = "Inv_irrad_600";
							$expPower=($irradation2 * 840 * (1.9503) * (0.155))/1000;
							$actualPower = $acPower[0]/100;
							$pr2= round((($actualPower/$expPower)*100),2);
							//print_r($acPower[0]);echo '/'.$expPower.'<br>';
							
							$insq2="REPLACE ".$table." (ts,device,field,value) values('".$acindex1."','".$device."','".$pr_fld2."','".$pr2."'),('".$acindex1."',10142,'".$inv_irr2."','".$irradation2."'),('".$acindex1."',10142,'".$ACmodule_fld2."','".$module_tempVal2."')";
							//echo "<br>".$insq2."<br>";
							$iqry2= mysql_query($insq2) or die(mysql_error());
						}
						
						
					}
				}
			}
		}
	}		
}


	foreach($inverterACData1 as $acindex=>$acpower){
	foreach($inverterDCData1 as $dcindex=>$dcpower){
	if($acindex == $dcindex)
	{	
	$acPower = explode(',',$acpower);
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
	

//Energy meter system PR

  $qry_irrad4=mysql_query("select ts, ROUND((value),3) as groupval FROM _devicedatavalue where ts > $startTime and ts < $endTime and  (device='10142') and (field='Solar_Radiation') and value > 250");
   
   while($rlt = mysql_fetch_array($qry_irrad4)){
  	 $timestamp = $rlt ['ts'];
	  $stime= $rlt ['ts']-110;
	$etime= $rlt ['ts']+110;
  	 $En_irradation1 = $rlt['groupval'];

		  $qry_enacpower = mysql_query("select ts, ROUND((value),3) as groupval FROM _devicedatavalue where ts BETWEEN $stime AND $etime and (device='11588') and (field='Activepower_Total')");
		  $numCounten =  mysql_num_rows($qry_enacpower); 
		  if( $numCounten!=0){ 
				$fetchdata5=mysql_fetch_array($qry_enacpower);
				$En_ActPowValue =$fetchdata5['groupval']; 
				
			}
      $qry_temp =mysql_query("select ts, ROUND((value),3) as groupval FROM _devicedatavalue where ts=$timestamp and (device='10142') and (field='Module_Temperature')");
      $numCount2 =  mysql_num_rows($qry_temp); 
		if( $numCount2!=0){ 
			$fetchdata5=mysql_fetch_array($qry_temp);
			$En_ModuleValue =$fetchdata5['groupval']; 
			$En_Module_Temp="Energy_Module_Temp";
		}
		$Energy_irrad ="En_irradiation";
		$System_PR="System_PR";
		$En_ActPowFld="Energy_Actual_Pow";
		
		$expPower = ($En_irradation1*420*(1.9503)*(0.154))/1000;
		$SPR = round(($En_ActPowValue / $expPower)*100,2); 
        $insq4="REPLACE ".$table."(ts,device,field,value) values('".$timestamp."',11588,'".$System_PR."','".$SPR."'),('".$timestamp."',10142,'".$Energy_irrad."','".$En_irradation1."'),('".$timestamp."', 10142 ,'".$En_Module_Temp."','".$En_ModuleValue."'),('".$timestamp."', 11588 ,'".$En_ActPowFld."','".$En_ActPowValue."')";
		//echo "<br>".$insq4."<br>";
		$iqry4= mysql_query($insq4) or die(mysql_error());
   }


?>