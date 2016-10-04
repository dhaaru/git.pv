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
$startTime = mktime(0, 0, 0, $slmnth, $sdd, $crtyr);
$endTime = mktime(0, 0, 0, $slmnth, $sdd+1, $crtyr);


$table="pune_calculation";

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

// SMA 7,8,9 Data Fetching
$inverterACData =array();
$iverterDCData = array();
$inverterACData[] =getdeviceData($startTime,$endTime,8410,'Pac');
$device =array('8384','8382','8383');
for($i=0;$i<count($device);$i++){
	$smainvACpowerData[$i] =getdeviceData($startTime,$endTime,8410,'Pac');
	$smainvDCcurrentData[$i] =getdeviceData($startTime,$endTime,$device[$i],'Ipv');
	$smainvDCVolt[$i] =getdeviceData($startTime,$endTime,$device[$i],'Upv-Ist');
	
}

//SMA2 INV PR
	foreach ($inverterACData as $inverterACData){
		foreach($inverterACData as $acindex1=>$acpower1){
			$acpower = explode(',',$acpower1);
			$device = $acpower[1];
			$qry_irrad1=mysql_query("select  ROUND((value),3) as groupval,ts FROM _devicedatavalue where ts=$acindex1 and (device='8459') and (field='Solar_Radiation') and value > 250");
			$numCount1 =  mysql_num_rows($qry_irrad1); 
				if($numCount1!=0)
				{
					$fetchdata1=mysql_fetch_array($qry_irrad1);
					$ts1 =$fetchdata1['ts'];
					$irradiation =$fetchdata1['groupval'];
					if($ts1==$acindex1)
					{
						$SMA_prfield= "SMA2_PR";
						$expPower=$irradiation * 210 * (1956*941) * (0.155)/1000;
						$SMA_PR= round((($acpower[0]/$expPower)*100),2);
						$insq5="REPLACE ".$table." (ts,device,field,value) values('".$acindex1."','".$device."','".$SMA_prfield."','".$SMA_PR."')";
						echo "<br>".$insq5."<br>";
						$iqry5= mysql_query($insq5) or die(mysql_error());
					}
				}
		}
	}


//SMA 7,8,9 PR
	if(!empty($smainvACpowerData) && !empty($smainvDCcurrentData) && !empty($smainvDCVolt)){ 
		foreach ($smainvACpowerData as $smaACPowerData){
			foreach ($smainvDCcurrentData as $inverterDcCurrentData ){
				foreach($smainvDCVolt as $inverterDcData){
					foreach($smaACPowerData as $acindex1=>$acpower1){
						foreach($smainvDCcurrentData as $dccurtindex1=>$dccurrent1){
							foreach($smainvDCVolt as $dcvoltindex1=>$dcvolt1 ){echo $acindex1;echo $dccurtindex1;
										
											$irradation1=0;
											$acPower = explode(',',$acpower1);
											$dcCurrent = explode(',',$dccurrent1);
											$dcvolt1 = explode(',',$dcvolt1);
											echo $device = $acPower[1];
											echo $dcPower =$dcCurrent[0]*$dcvolt1[0];
											
										
								}
							}
						}
					}
				}
			}
		}	

//Energy meter system PR

   /*$qry_irrad4=mysql_query("select ts, ROUND((value),3) as groupval FROM _devicedatavalue where ts > $startTime and ts < $endTime and  (device='7794') and (field='Solar_Radiation') and value > 250");
   
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
 
*/
?>
