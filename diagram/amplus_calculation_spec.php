<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
set_time_limit(900);
$slmnth=3;
$crtyr=2015;
$sdd=24;

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


$table="amplus_calculation";

function getdeviceData($startTime,$endTime,$deviceId,$field)
{
	$query="select ts,value,device from _devicedatavalue where ts > $startTime and ts < $endTime and (device='".$deviceId."') and (field='".$field."')";
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
$device =array('7791','7792','7794');
for($i=0;$i<count($device);$i++){
	$inverterACData[$i] =getdeviceData($startTime,$endTime,$device[$i],'AC_Power');
	$inverterDCData[$i] =getdeviceData($startTime,$endTime,$device[$i],'DC_Power');
	$inverterDCVolt[$i] =getdeviceData($startTime,$endTime,$device[$i],'DC_Voltage');
}

//DC voltage coefficient
foreach ($inverterDCVolt as $inverterDCVoltageData){
	foreach($inverterDCVoltageData as $dcindex1=>$dcvoltage){
	$dcVoltage = explode(',',$dcvoltage);
	$device = $dcVoltage[1];
	
	$qry_irrad1=mysql_query("select  ROUND((value),3) as groupval,ts FROM _devicedatavalue where ts=$dcindex1 and (device='7794') and (field='Solar_Radiation') and value > 600");
			$numCount1 =  mysql_num_rows($qry_irrad1); 
			if($numCount1!=0)
				{
					$fetchdata1=mysql_fetch_array($qry_irrad1);
					$ts1 =$fetchdata1['ts'];
					
					if($ts1==$dcindex1){
						$DC_Vol_Coeff_fld= "DC_Vol_Coeff";
						$DC_Vol_Coeff_Val= round((($dcVoltage[0]/(21*45.5))*100),2);
							
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
						$qry_irrad1=mysql_query("select  ROUND((value),3) as groupval,ts FROM _devicedatavalue where ts=$acindex1 and (device='7794') and (field='Solar_Radiation') and value > 250");
						$numCount1 =  mysql_num_rows($qry_irrad1); 
						if($numCount1!=0)
						{
							$fetchdata1=mysql_fetch_array($qry_irrad1);
							$ts1 =$fetchdata1['ts'];
							$irradation1 = $fetchdata1['groupval'];
							$qry_module=mysql_query("select  value FROM _devicedatavalue where ts=$ts1 and (device='7794') and (field='Module_Temperature')");
							$fetchdata3=mysql_fetch_array($qry_module);
							$module_tempVal1 =$fetchdata3['value'];
							$ACmodule_fld1="AC_Module_Temp_250";
							$pr_fld="Inv_PR";
							$inv_irr = "Inv_irrad_250";
							$expPower=$irradation1 * 84 * (1.956*0.992) * (0.155)/1000;
							$pr= round((($acPower[0]/$expPower)*100),2);
							
							$insq1="REPLACE ".$table." (ts,device,field,value) values('".$acindex1."','".$device."','".$pr_fld."','".$pr."'),('".$acindex1."',7794,'".$inv_irr."','".$irradation1."'),('".$acindex1."',7794,'".$ACmodule_fld1."','".$module_tempVal1."')";
							//echo "<br>".$insq1."<br>";
							$iqry1= mysql_query($insq1) or die(mysql_error());
						}
						
						//////above 600
						$qry_irrad2=mysql_query("select  ROUND((value),3) as groupval,ts FROM _devicedatavalue where ts=$acindex1 and (device='7794') and (field='Solar_Radiation') and value > 600");
						$numCount2 =  mysql_num_rows($qry_irrad2); 
						if($numCount2!=0)
						{
							$fetchdata2=mysql_fetch_array($qry_irrad2);
							$ts2 =$fetchdata2['ts'];
							$irradation2 = $fetchdata2['groupval'];
							$qry_module=mysql_query("select  value FROM _devicedatavalue where ts=$ts2 and (device='7794') and (field='Module_Temperature')");
							$fetchdata3=mysql_fetch_array($qry_module);
							$module_tempVal2 =$fetchdata3['value'];
							$ACmodule_fld2="AC_Module_Temp_600";
							$pr_fld2="Inv_AC_PR";
							$inv_irr2 = "Inv_irrad_600";
							$expPower=$irradation2 * 84 * (1.956*0.992) * (0.155)/1000;
							$pr2= round((($acPower[0]/$expPower)*100),2);
							
							
							//$DC_Vol_Coeff_fld= "DC_Vol_Coeff";
							//$DC_Vol_Coeff_Val= round((($dcPower[0]/(21*45.5))*100),2);
							
							$insq2="REPLACE ".$table." (ts,device,field,value) values('".$acindex1."','".$device."','".$pr_fld2."','".$pr2."'),('".$acindex1."',7794,'".$inv_irr2."','".$irradation2."'),('".$acindex1."',7794,'".$ACmodule_fld2."','".$module_tempVal2."')";
							//echo "<br>".$insq2."<br>";
							$iqry2= mysql_query($insq2) or die(mysql_error());
						}
						
						//inverter efficient
						$eff_fld="Inv_Eff";
						$eff = round((($acPower[0]/$dcPower[0])*100),2);
						
						$insq3="REPLACE ".$table." (ts,device,field,value) values('".$acindex1."','".$device."','".$eff_fld."','".$eff."')";
						//echo "<br>".$insq."<br>";
						$iqry3= mysql_query($insq3) or die(mysql_error());
					}
				}
			}
		}
	}		
}

//Energy meter system PR

   $qry_irrad4=mysql_query("select ts, ROUND((value),3) as groupval FROM _devicedatavalue where ts > $startTime and ts < $endTime and  (device='7794') and (field='Solar_Radiation') and value > 250");
   
   while($rlt = mysql_fetch_array($qry_irrad4)){
  	 $timestamp = $rlt ['ts'];
  	 $En_irradation1 = $rlt['groupval'];

		  $qry_enacpower = mysql_query("select ts, ROUND((value),3) as groupval FROM _devicedatavalue where ts=$timestamp and (device='7795') and (field='Activepower_Total')");
		  $numCounten =  mysql_num_rows($qry_enacpower); 
		  if( $numCounten!=0){ 
				$fetchdata5=mysql_fetch_array($qry_enacpower);
				$En_ActPowValue =$fetchdata5['groupval']; 
				$En_ActPowFld="Energy_Actual_Pow";
			}
      $qry_temp =mysql_query("select ts, ROUND((value),3) as groupval FROM _devicedatavalue where ts=$timestamp and (device='7794') and (field='Module_Temperature')");
      $numCount2 =  mysql_num_rows($qry_temp); 
		if( $numCount2!=0){ 
			$fetchdata5=mysql_fetch_array($qry_temp);
			$En_ModuleValue =$fetchdata5['groupval']; 
			$En_Module_Temp="Energy_Module_Temp";
		}
		$Energy_irrad ="En_irradiation";
		$System_PR="System_PR";
		$SPR = round(($En_ActPowValue)/(($En_irradation1*252*(1.956*0.992)* 0.155)/1000)*100,2); 
        $insq4="REPLACE ".$table."(ts,device,field,value) values('".$timestamp."',7795,'".$System_PR."','".$SPR."'),('".$timestamp."',7794,'".$Energy_irrad."','".$En_irradation1."'),('".$timestamp."', 7794 ,'".$En_Module_Temp."','".$En_ModuleValue."'),('".$timestamp."', 7795 ,'".$En_ActPowFld."','".$En_ActPowValue."')";
		echo "<br>".$insq4."<br>";
		$iqry4= mysql_query($insq4) or die(mysql_error());
   }
 

?>
