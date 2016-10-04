<?php

#######################################################
# Amplus Calculations: Inverter Performance
#        Influx version
#######################################################
# parameters
# $time:		optional	range. 
#                     default: 3 [count of days]
#                     max: 22 (memory usage ($time=22): ~ 30 MB
#                              increases linear with $time,
#                              regardless of how many plants)
# $endtime:	optional	range until (including) count days before today 
#	     			          default: 0: until now
# $offset		optional	timezone shift in seconds from UTC. 
#	                		default: -19800 (Indian Standard Time)
# $plantname optional of all plant definition files in /plantdescription-amplus,
#                     select only that one whose plant name matches 
#                     part of $plantname, case-insensitive
# $testdb   optional  if 1 or true, then
#                     write to 'amplus_test_calculations' 
#                     else write to 'amplus_all_calculations'
# ###############################################################
# configuration
# $plantdesc_path = __DIR__ . "/../plantdescription-amplus/";
#################################################################

#######################################################
# remarks
#######################################################
# DC_VOL_Coeff:
#            This script assigns inverter's UDC to DC_VOL_Coeff unmodified.
#            I think, that's exactly what the diagram requires, 
#            but
#            the predecessor script 'amplus_calculation.php' multiplies it with
#            a constant factor (line 84):
# 					 $DC_Vol_Coeff_Val= round((($dcVoltage[0]/(21*45.5))*100),2);
#            see diagram: y-axis name is _not_ what is shown:
#            http://git.pv-india.net/diagram/argdiagram9.php?park_no=36&phase=energy5&defaults=0,1,2,3,4&args=0,1,0,7792,DC_Vol_Coeff,Inv%201(DC_Voltage),15,V,4;0,1,0,7791,DC_Vol_Coeff,Inv%202(DC_Voltage),15,V,5;0,1,0,7794,DC_Vol_Coeff,Inv%203(DC_Voltage),15,V,6;0,1,0,7794,Inv_irrad_600,Irradiation,15,W/m%C2%B2,%27Gold%27;0,1,0,7794,AC_Module_Temp_600,Module%20Temperature,15,%C2%B0C,%27darkred%27&hideClear=1&hideDelta=1&stamp=1467311400&endstamp=1469989800&title=Graph-7

#######################################################
# done for Hilton Pune (different tilt angles)
#
# - module_count_inv: plantwise default
# - module type:  inverter-wise overrides plantwise 
# - module eff:  inverter-wise overrides plantwise 
#
# - irradiations: if several, keep each and (default:) average
# - module_tempe: if several, keep each and (default:) average
# - inverter to which irrad+modTemp: optional weatherID overrides default weather Average

require_once('../connections/queriesMysql2.php');
require_once('../connections/queriesInflux2.php');
if (!isset($time)) 	      $time    = 3;
else                      $time = intval($time);
if ($time > 22)           $time = 22;
if (!isset($endtime))     $endtime = 0;      
else                      $endtime = intval($endtime);
if (!isset($offset))      $offset  = -19800;
if (!isset($plantname))   $plantname  = '//'; # empty regex pattern matches any string
else                      $plantname = '/'.$plantname.'/i';
if (!isset($showQueries)) $showQueries = False;
else                      $showQueries = intval($showQueries);
if (!isset($testdb))      $testdb    = 0;
$testdb = boolval(intval($testdb));

define("DAY_SECONDS",86400);

# round down $number to the nearest number dividable by $divisor
function alignInt($number,$divisor){
	return $number - fmod($number,$divisor);
}

if ($endtime > 0)  {
  $endts   = alignInt(time(), DAY_SECONDS) - ($endtime - 1) * DAY_SECONDS;
  $endts   += $offset;
} else {
  $endtime == 0;
  $endts   = time() ;
}
$startts = alignInt($endts-1, DAY_SECONDS) + DAY_SECONDS - intval($time) * DAY_SECONDS;
$startts += $offset; 

# returns true if string $haystack ends with string $needle
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || 
      ( 
        ($temp = strlen($haystack) - strlen($needle)) >= 0 && 
        strpos($haystack, $needle, $temp) !== false
      );
}
function boolval($v){
  # a function of this name has been introduced in php 5.5.0
  return ($v == true);
}

################################
# toTs_Array()
# merge a series array into a timestamp-values nested array
################################
# param1: required  [array] $series to merge into $tsvals 
#                           example Array
#                           (
#                              [0] => Array
#                                 (
#                                    [0] => 1463337000
#                                    [1] => 3.91
#                                 )
#                           )
# param2: required [str]    series name, 
#                           example: "irrad"
#                           as key in return array
#   
# param3: optional
#           either [array]  $tsvals array to merge $series into.
#                             if $tsvals is given, then
#                             don't create new timestamp keys in $tsvals.
#                           if $tsvals is null (not given), 
#                             then create new and insert  
#                             all values of $series into $tsvals.
#       
#            or    [str]    a function_name for a callback function.
#                           the function must take one [int] as parameter
#                           and return a boolean.
#                             if  function_name(value) evaluates to true,
#                               insert timestamp => value of $series 
#                             else don't create key timestamp in $series
#
# returns                   $tsvals
#                           Array
#                           (
#    initial                   [1463337000] => Array
#    execution                    ( 
#                                     [irrad] => 3.91
#                                 )
#                               [1463474400] => Array
#                                  (
#    after                             [irrad] => 766.9
#    repeated                          [Inv0_pac] => 15.76
#    execution                         [Inv0_pdc] => 16.12
#                                      [Inv0_udc] => 631.71
#                                      [Inv1_pac] => 15.72
#                                      [Inv1_pdc] => 16.11
#                                      [Inv1_udc] => 631.86
#                                      [Inv2_pac] => 15.89
#                                      [Inv2_pdc] => 16.27
#                                      [Inv2_udc] => 633.81
#                                  )
#                              )
#############################
function toTs_Array(&$series,$sname,$tsvals=null){
  if (is_array($tsvals)){ 
    $createTs = false;
  } else if (is_string($tsvals)){ 
    $callback_f = $tsvals; # name of a callback function($number)
    $createTs = true;
    $tsvals = array();
  } else if (is_null($tsvals)){ 
    $createTs = true;
    $tsvals = array();
    $callback_f = "isset";
  } else {
    return null;
  }
  if($createTs){
    # create timestamp key in $tsvals 
    #   if callback(value) evaluates to true 
    foreach($series as &$sentry){
      if (call_user_func($callback_f,$sentry[1])){
        $tsvals[$sentry[0]][$sname] = $sentry[1];
      }
    }
  } else {
    # add only if timestamp key exists already
    foreach($series as &$sentry){
      if (array_key_exists($sentry[0],$tsvals)){
        $tsvals[$sentry[0]][$sname] = $sentry[1];
      }
    }
  }
  return $tsvals;
}
   
# read plant description files
$plantdesc_path = __DIR__ . "/../plantdescription-amplus/";
print ("<h2>scanning '$plantdesc_path' for *.json plantdescription files</h2>\n");
if ($handle = opendir($plantdesc_path)) {
  while (false !== ($entry = readdir($handle))) {
    if (endsWith($entry,".json")){
      print ("<h2>found plantdescription file: ".$entry."</h2>\n");
      $jsonstring = file_get_contents($plantdesc_path . $entry);
      if($jsonstring !== False){
        $plantdesc = json_decode($jsonstring);
        print_r($plantdesc); /////////////////////////////////////
      }
      if (!preg_match($plantname,$plantdesc->plantname)){
        print ("<h2>ignored plantname: '".$plantdesc->plantname."'</h2>\n");
        continue;
      }
      
      ###############################################
      # foreach plant
      ###############################################
      
      ###############################################
      # plantdesc
      ###############################################
      # fully populate $plantdesc structure with defaults: 
      # plant properties and each inverter's properties
      ###############################################
      
      # $plantdesc: plant defaults:
      ###############################################
      
      $reporting_interval = (isset($plantdesc->reporting_interval)) 
                            ? $plantdesc->reporting_interval 
                            : "5m";

      # to numeric array: 
      # energymeters
      if (isset($plantdesc->energymeter) ){
        $plantdesc->energymeters = &$plantdesc->energymeter;
        unset($plantdesc->energymeter); 
      }
      if (!is_array($plantdesc->energymeters)){
        $plantdesc->energymeters = array($plantdesc->energymeters);
      }
      # energymeters_pac_unit; default: 'kW'
      if (!isset($plantdesc->energymeters_pac_unit) OR $plantdesc->energymeters_pac_unit != 'W'){
        $plantdesc->energymeters_pac_unit = "kW"; # em values are in [kW]
        $plantdesc->energymeters_pac_factor = 1000;
      } else {
        $plantdesc->energymeters_pac_unit = "W"; # em values are in [W]
        $plantdesc->energymeters_pac_factor = 1;
      }
      # to associative array: 
      # irradiation_sensors
      if (isset($plantdesc->irradiation_sensor) ){
        $plantdesc->irradiation_sensors = $plantdesc->irradiation_sensor;
        unset($plantdesc->irradiation_sensor);
      }
      if (!is_array($plantdesc->irradiation_sensors)){
        $plantdesc->irradiation_sensors = array($plantdesc->irradiation_sensors);
      }
      $c=0;
      foreach($plantdesc->irradiation_sensors as &$sensor){
        $plantdesc->irradiation_sensors[$sensor->mysqldeviceid] = $sensor;
        unset($plantdesc->irradiation_sensors[$c]);        
        $c++;
      }
      
      # to numeric and associative array: 
      # module_temp_sensors
      if (isset($plantdesc->module_temp_sensor) ){
        $plantdesc->module_temp_sensors = &$plantdesc->module_temp_sensor;
        unset($plantdesc->module_temp_sensor);
      }
      if (!is_array($plantdesc->module_temp_sensors)){
        $plantdesc->module_temp_sensors = array($plantdesc->module_temp_sensors);
      }
      $c=0;
      foreach($plantdesc->module_temp_sensors as &$sensor){
        $plantdesc->module_temp_sensors[$sensor->mysqldeviceid] = $sensor;
        unset($plantdesc->module_temp_sensors[$c]);
        $c++;
      }
      
      # $plantdesc: inverter defaults:
      ###############################################
      foreach($plantdesc->inverters as &$inverter){
        # module_area
        if (!isset($inverter->module_area) ){
          $inverter->module_area = &$plantdesc->module_area;
        }
        # module_eff
        if (!isset($inverter->module_eff) ){
          $inverter->module_eff = &$plantdesc->module_eff;
        }
        # module_count
        if (!isset($inverter->module_count) ){
          $inverter->module_count = &$plantdesc->module_count_inv;
        }
        # irradiation_sensor_mysqldeviceid
        if (!isset($inverter->irradiation_sensor_mysqldeviceid) ){
          $inverter->irradiation_sensor_mysqldeviceid = "Average";
        }
        # module_temp_sensor_mysqldeviceid
        if (!isset($inverter->module_temp_sensor_mysqldeviceid) ){
          $inverter->module_temp_sensor_mysqldeviceid = "Average";
        }
        # f_pac_unit; default: 'kW'
        if (!isset($inverter->f_pac_unit) OR $inverter->f_pac_unit != 'W'){
          $inverter->f_pac_unit = "kW"; # inverter values are in [kW]
          $inverter->f_pac_factor = 1000;
        } else {
          $inverter->f_pac_unit = "W"; # inverter values are in [W]
          $inverter->f_pac_factor = 1;
        }
        # f_pdc, 
        #     if array, then set  f_pdc_operator:'ADD'(default), 'MUL'
        if (is_array($inverter->f_pdc)){
          if (!isset($inverter->f_pdc_operator) OR $inverter->f_pdc_operator != "MUL"){
            $inverter->f_pdc_operator = "ADD";
          }
        } else {
          $inverter->f_pdc = array($inverter->f_pdc);
        }
        # f_pdc_unit; default: 'kW'
        if (!isset($inverter->f_pdc_unit) OR $inverter->f_pdc_unit != 'W'){
          $inverter->f_pdc_unit = "kW"; # inverter values are in [kW]
          $inverter->f_pdc_factor = 1000;
        } else {
          $inverter->f_pdc_unit = "W"; # inverter values are in [W]
          $inverter->f_pdc_factor = 1;
        }
        # f_udc, 
        #     if array, then set  f_udc_operator:'AVG'(default)
        if (is_array($inverter->f_udc)){
          if (!isset($inverter->f_udc_operator) OR $inverter->f_udc_operator != "other"){
            $inverter->f_pdc_operator = "AVG";
          }
        } else {
          $inverter->f_udc = array($inverter->f_udc);
        }
      }
      
      ############# irrad_inverters ######################
      #   inverters to each irradiation sensor
      #    note: I recognized the need for this array list
      #          only when I was nearly finished and was
      #          doing the section "write to database" near the bottom.
      #          So, TODO use of this list  at some prior points
      #          would improve the script logic.
      $irrad_inverters = array();
      $irrad_inverters["Average"] = array(); //$irrad_inverters[""] = array();
      if (count($plantdesc->irradiation_sensors) > 1){
        foreach($plantdesc->irradiation_sensors as $mysqldeviceid => &$sensor){
          $irrad_inverters[$mysqldeviceid] = array();
        }
      }
      foreach($plantdesc->inverters as $inv_count => &$inverter){
        $irrad_inverters[$inverter->irradiation_sensor_mysqldeviceid][] = $inv_count;
      }
      $irrad_inverters[""] = &$irrad_inverters["Average"];
      unset($irrad_inverters["Average"]);
      #       ===== irrad_inverters ======================
      #       Array
      #       (
      #           [] => Array
      #               (
      #                   [0] => 1
      #                   [1] => 13
      #               )
      #           [8412] => Array
      #               (
      #                   [0] => 3
      #                   [1] => 4
      #                   [2] => 5
      #                   [3] => 6
      #                   [4] => 8
      #                   [5] => 14
      #                   [6] => 15
      #               )
      #           [8414] => Array
      #               (
      #                   [0] => 0
      #                   [1] => 2
      #                   [2] => 7
      #                   [3] => 9
      #                   [4] => 10
      #                   [5] => 11
      #                   [6] => 12
      #               )
      #       )
      ##################################################

      //print("===== plantdesc ============================\n");///////////////////////
      //print_r($plantdesc); /////////////////////////////////////
      //print("===== irrad_inverters ======================\n");///////////////////////
      //print_r($irrad_inverters);/////////////////////////////////////////////////////////
      //print("============================================\n");///////////////////////

      ###############################################
      # plantdescription file read, parsed and fully populated 
      # $plantdesc is such an object:
      ###############################################
      #    plantdesc -> stdClass Object
      #    (
      #        [plantname] => Amplus Raisoni1 Nagapur
      #        [mysqlplanttable] => amplus_calculation2
      #        [influxdbname] => amplus
      #        [reporting_interval] => 5m
      #        [energymeters] => Array
      #            (
      #                [0] => stdClass Object
      #                    (
      #                        [mysqldeviceid] => 7795
      #                        [f] => Activepower_Total
      #                        [iid] => 4031
      #                        [d] => EM_CONZERV
      #                    )
      #    
      #            )
      #    
      #        [irradiation_sensors] => Array
      #            (
      #                [7794] => stdClass Object
      #                    (
      #                        [mysqldeviceid] => 7794
      #                        [f] => Solar_Radiation
      #                        [iid] => 4031
      #                        [d] => INV_Ref_03
      #                    )
      #    
      #            )
      #    
      #        [module_temp_sensors] => Array
      #            (
      #                [7794] => stdClass Object
      #                    (
      #                        [mysqldeviceid] => 7794
      #                        [f] => Module_Temperature
      #                        [iid] => 4031
      #                        [d] => INV_Ref_03
      #                    )
      #    
      #            )
      #    
      #        [module_area] => 1.940352
      #        [module_eff] => 0.155
      #        [inverters] => Array
      #            (
      #                [0] => stdClass Object
      #                    (
      #                        [type] => Refusol
      #                        [mysqldeviceid] => 7791
      #                        [iid] => 4031
      #                        [d] => INV_Ref_02
      #                        [f_pac] => AC_Power
      #                        [f_pdc] => DC_Power
      #                        [f_udc] => DC_Voltage
      #                        [module_count] => 84
      #                        [module_area] => 1.940352
      #                        [module_eff] => 0.155
      #                        [irradiation_sensor_mysqldeviceid] => Average
      #                        [module_temp_sensor_mysqldeviceid] => Average
      #                    )
      #    
      #                [1] => stdClass Object
      #                    (
      #                        [type] => Refusol
      #                        [mysqldeviceid] => 7794
      #                        [iid] => 4031
      #                        [d] => INV_Ref_03
      #                        [f_pac] => AC_Power
      #                        [f_pdc] => DC_Power
      #                        [f_udc] => DC_Voltage
      #                        [module_count] => 84
      #                        [module_area] => 1.940352
      #                        [module_eff] => 0.155
      #                        [irradiation_sensor_mysqldeviceid] => Average
      #                        [module_temp_sensor_mysqldeviceid] => Average
      #                    )
      #    
      #            )
      #    
      #    )
      ################################################

      ################################################
      ### foreach plant: get data ####################
      ################################################
      print("<h2>start plant: ".$plantdesc->plantname ."</h2>\n");
      print("<p>".
        date(DATE_RFC2822,$startts)." until ".
        date(DATE_RFC2822,$endts)." ($startts .. $endts)</p>\n");
      
      ### foreach plant: get data -> Avg(irradiation) #####
      ###                            where Avg(irradiation) > 0
      $series = inflQuery_tagset(  
                            $startts, 
                            $endts, 
                            $plantdesc->irradiation_sensors, 
                            "MEAN(value)",
                            $reporting_interval , 
                            array('iid','d','f'),
                            Null,
                            $showQueries,
                            $plantdesc->influxdbname,
                            0,
                            'a');
      $tsdata =  toTs_Array($series[0]['values'],"irrad","boolval");
      
      ### foreach plant: get data -> Each irradiation #####
      if( count($plantdesc->irradiation_sensors) > 1 ){
        $series = inflQuery_tagset(  
                              $startts, 
                              $endts, 
                              $plantdesc->irradiation_sensors, 
                              "MEAN(value)",
                              $reporting_interval , 
                              array('iid','d','f'),
                              Null,
                              $showQueries,
                              $plantdesc->influxdbname,
                              0,
                              'd');
        foreach($plantdesc->irradiation_sensors as $mysqldeviceid => &$sensor){
          $sindex = get_seriesIndex($series, get_object_vars($sensor));
          $tsdata =  toTs_Array($series[$sindex]['values'],"irrad".$mysqldeviceid,$tsdata);
        }
      }

      ### foreach plant: get data -> Avg(module temperature) #####
      $series = inflQuery_tagset(  
                            $startts, 
                            $endts, 
                            $plantdesc->module_temp_sensors, 
                            "MEAN(value)",
                            $reporting_interval , 
                            array('iid','d','f'),
                            Null,
                            $showQueries,
                            $plantdesc->influxdbname,
                            0,
                            'a');
      $tsdata =  toTs_Array($series[0]['values'],"module_temp",$tsdata);
      
      ### foreach plant: get data -> Each module temperature #####
      if( count($plantdesc->module_temp_sensors) > 1 ){
        $series = inflQuery_tagset(  
                              $startts, 
                              $endts, 
                              $plantdesc->module_temp_sensors, 
                              "MEAN(value)",
                              $reporting_interval , 
                              array('iid','d','f'),
                              Null,
                              $showQueries,
                              $plantdesc->influxdbname,
                              0,
                              'd');
        foreach($plantdesc->module_temp_sensors as $mysqldeviceid => &$sensor){
          $sindex = get_seriesIndex($series, get_object_vars($sensor));
          $tsdata =  toTs_Array($series[$sindex]['values'],"module_temp".$mysqldeviceid,$tsdata);
        }
      }

      ### foreach plant: get data -> energy meters SUM 
      $ems = $plantdesc->energymeters;
      $tagsets = array();
      foreach($ems as &$em){
        $tagsets[] = get_object_vars($em);
      }
      $series = inflQuery_tagset(  
                            $startts, 
                            $endts, 
                            $tagsets, 
                            "SUM(value) * ".$plantdesc->energymeters_pac_factor,
                            $reporting_interval , 
                            array('iid','d','f'),
                            Null,
                            $showQueries,
                            $plantdesc->influxdbname,
                            0,
                            'a');
      $tsdata =  toTs_Array($series[0]['values'],"em_pac",$tsdata);

      # pac: of all inverters
      $series = inflQuery_tagset(  
                            $startts, 
                            $endts, 
                            $plantdesc->inverters, 
                            "MEAN(value)",
                            $reporting_interval , 
                            array('iid','d','f_pac'=>'f'),
                            Null,
                            $showQueries,
                            $plantdesc->influxdbname,
                            0);        
      foreach($plantdesc->inverters as $inv_no => &$inverter){
        $sindex = get_seriesIndex($series, get_object_vars($inverter));
        $tsdata =  toTs_Array($series[$sindex]['values'],"Inv".$inv_no."_pac",$tsdata);
        foreach($tsdata as $ts => &$tsvals){
          $tsvals["Inv".$inv_no."_pac"] *= $inverter->f_pac_factor;
        }
      }
      
      # pdc: per inverter
      foreach($plantdesc->inverters as $inv_no => &$inverter){
        $tagsets = array();
        foreach($inverter->f_pdc as $f_pdc){
          $tagsets[] = array('iid'=>($inverter->iid), 'd'=>($inverter->d), 'f'=>$f_pdc);
        }
        $series = inflQuery_tagset(  
                              $startts, 
                              $endts, 
                              $tagsets, 
                              "MEAN(value)",
                              $reporting_interval , 
                              array('iid','d','f'),
                              Null,
                              $showQueries,
                              $plantdesc->influxdbname,
                              0);  
        $s0key = "Inv".$inv_no."_pdc";
        $tsdata =  toTs_Array($series[0]['values'],$s0key,$tsdata);
        if (count($inverter->f_pdc) > 1){
          $series_no = 1;
          while($series_no < count($inverter->f_pdc)){
            $skey = "Inv".$inv_no."_pdc".$series_no;
            $tsdata =  toTs_Array($series[$series_no]['values'],$skey,$tsdata);
            if ($inverter->f_pdc_operator == "MUL"){
              foreach($tsdata as $ts => &$tsvals){
                $tsvals[$s0key] *= $tsvals[$skey];
              }
            } else { 
              foreach($tsdata as $ts => &$tsvals){
                $tsvals[$s0key] += $tsvals[$skey];
              }
            }
            $series_no++;
          }
        }
        foreach($tsdata as $ts => &$tsvals){
          $tsvals["Inv".$inv_no."_pdc"] *= $inverter->f_pdc_factor;
        }
      }
        
      # udc: per inverter, AVG !
      foreach($plantdesc->inverters as $inv_no => &$inverter){
        $tagsets = array();
        foreach($inverter->f_udc as &$f_udc){
          $tagset = get_object_vars($inverter);
          $tagset['f'] = $f_udc;
          $tagsets[] = $tagset;
        } 
        $series = inflQuery_tagset(  
                              $startts, 
                              $endts, 
                              $tagsets, 
                              "MEAN(value)",
                              $reporting_interval , 
                              array('iid','d','f'),
                              Null,
                              $showQueries,
                              $plantdesc->influxdbname,
                              0);
        $tsdata =  toTs_Array($series[0]['values'],"Inv".$inv_no."_udc",$tsdata);
      }
        
      print("<p>memory_get_usage: ".(memory_get_usage()/1024)." KB</p>\n");/////
      print("<h3>start plant calculations: ".$plantdesc->plantname ."</h3>\n");///
        
      ####################################################
      ### foreach plant: calculations ####################
      ####################################################
      $inv_names = array();
      foreach($plantdesc->inverters as $inv_no => &$inverter){
        $inv_names[] = "Inv".$inv_no;
      }
      
      ### calculations -> (A) Inv_Eff Inverter Efficiency
      foreach ($tsdata as $ts => &$values){
        foreach ($inv_names as $inv_name){
          #  PHP Warning:  Division by zero    occurs if data is missing
          $values[$inv_name."_Eff"] = 100 
            * $values[$inv_name."_pac"] 
            / $values[$inv_name."_pdc"];
        }
      }

      # calculations -> epf: expected power factor  (per inverter + total SUM)
      $epf = array();
      foreach($plantdesc->inverters as &$inv){
        $inv->epf = $inv->module_area * $inv->module_eff * $inv->module_count;
        $epf[] = $inv->epf;
        //print("inv->type:".$inv->type." inv->id:".$inv->mysqldeviceid." inv->epf (Expected Power Factor):".$inv->epf."<br>\n");/////////////////////////////////
      }
      $epf = array_sum($epf);
      //print("sum epf (Expected Power Factor):".$epf."<br>\n");//////////////////////////////////////////////////

      ### calculations -> (B) System PR ####
      foreach ($tsdata as $ts => &$values){
        if($values['irrad']>=250){          
          $values['Sys_PR'] = 100 * $values['em_pac']  
            / ($epf * $values['irrad']);
        }
      }
          
      ### calculations -> (C) Inverter PR 
      foreach($tsdata as $ts => &$values){
          foreach ($inv_names as $i => $inv_name){
            # which irradiation value for this inverter?
            if($plantdesc->inverters[$i]->irradiation_sensor_mysqldeviceid=="Average"){
              $virrad = $values['irrad'];
            } else {
              $virrad = $values['irrad'.$plantdesc->inverters[$i]->irradiation_sensor_mysqldeviceid];
            }
            if($virrad>=250){    
              $values[$inv_name."_PR"] = 100 * $values[$inv_name.'_pac'] 
                / ($plantdesc->inverters[$i]->epf * $virrad);
            }
          }
      }
      //print("=========================================\n");///////////////////////
      //print_r($tsdata);/////////////////////////////////////////////////////////
      //print("=========================================\n");///////////////////////
         
      if($showQueries>1){
        print("======= plantdesc ===============:\n"); ////////////////////////////////////////////////////////
        print_r($plantdesc);/////////////////////////////////////////////////////////
        print("======= tsdata ==================:\n"); ////////////////////////////////////////////////////////
        print_r($tsdata); //////////////////////////////////////////////////////////
        print("<p>memory_get_usage: ".(memory_get_usage()/1024)." KB</p>\n");///////
      }      
      ########################################################
      # $tsdata
      ########################################################
      #   Array
      #   (
      #     ------------------------ where irradiation > 0:
      #     [1463534700] => Array
      #       (
      #           [irrad] => 90.2
      #           [module_temp] => 37.1
      #           [em_pac] => 5.22
      #           [Inv0_pac] => 1.82
      #           [Inv0_pdc] => 1.87
      #           [Inv0_udc] => 681.65
      #           [Inv1_pac] => 1.63
      #           [Inv1_pdc] => 1.67
      #           [Inv1_udc] => 700.72
      #           [Inv2_pac] => 1.83
      #           [Inv2_pdc] => 1.86
      #           [Inv2_udc] => 687.39
      #           [Inv0_Eff] => 97.326203208556
      #           [Inv1_Eff] => 97.604790419162
      #           [Inv2_Eff] => 98.387096774194
      #       )
      #     
      #   ------------------------- where irradiation >= 250:
      #   [1463466300] => Array
      #       (
      #           [irrad] => 885.2
      #           [module_temp] => 63.6
      #           [em_pac] => 53.12
      #           [Inv0_pac] => 17.62
      #           [Inv0_pdc] => 17.97
      #           [Inv0_udc] => 618.51
      #           [Inv1_pac] => 17.6
      #           [Inv1_pdc] => 18.03
      #           [Inv1_udc] => 624.53
      #           [Inv2_pac] => 17.65
      #           [Inv2_pdc] => 18.04
      #           [Inv2_udc] => 622.35
      #           [Inv0_Eff] => 98.052309404563
      #           [Inv1_Eff] => 97.615085967831
      #           [Inv2_Eff] => 97.838137472284
      #           [Sys_PR] => 79.17788551997
      #           [Inv0_PR] => 78.790343158615
      #           [Inv1_PR] => 78.700910305995
      #           [Inv2_PR] => 78.924492437546
      #       )
      #   )
      ########################################################
      
      ########################################################
      ### foreach plant: write to db #########################
      ########################################################
      print("<h3>start write to database</h3>\n");
      
      $query = "SELECT park_no FROM subparks
                WHERE bezeichnung = '".$plantdesc->plantname."'";
      $park_no = mysql_query_execute($query);
      $park_no = $park_no[0]['park_no'];

      $insert_points = array();
      foreach($tsdata as $ts => $tsvals){
        # Inv_Eff
        foreach($plantdesc->inverters as $inv_count => &$inverter){
          $key = "Inv".$inv_count."_Eff";
          if ($tsvals[$key]){ 
            $insert_points[] = strval($ts) . "," .
              $inverter->mysqldeviceid . "," .
              "'Inv_Eff'" . "," .
              $park_no . "," . 
              $tsvals[$key];
          }
        }
        foreach ($irrad_inverters as $irrad_mysqldeviceid => &$inverter_nos){
          ## foreach irradiation sensor:   list of inverter_index numbers 
          $irrad_key = "irrad".$irrad_mysqldeviceid;
          $module_temp_key = "module_temp".$irrad_mysqldeviceid; 
          if($tsvals[$irrad_key]>=250){  
            foreach($inverter_nos as $inv_no){
              # AC_Module_Temp_250 
              if ($tsvals[$module_temp_key]){ 
                $insert_points[] = strval($ts) . "," .
                  $plantdesc->inverters[$inv_no]->mysqldeviceid . "," .
                  "'AC_Module_Temp_250'" . "," .
                  $park_no . "," . 
                  $tsvals[$module_temp_key];
                # Energy_Module_Temp  (duplicate value as AC_Module_Temp_250)
                #                     for Raisoni Nagpur diagram
                $insert_points[] = strval($ts) . "," .
                  $plantdesc->inverters[$inv_no]->mysqldeviceid . "," .
                  "'Energy_Module_Temp'" . "," .
                  $park_no . "," . 
                  $tsvals[$module_temp_key];
              }
              # En_irradiation       (duplicate value as Inv_irrad_250)
              #                       for Raisoni Nagpur diagram
              $insert_points[] = strval($ts) . "," .
                $plantdesc->inverters[$inv_no]->mysqldeviceid . "," .
                "'En_irradiation'" . "," .
                $park_no . "," . 
                $tsvals[$irrad_key];
              # Inv_irrad_250      
              $insert_points[] = strval($ts) . "," .
                $plantdesc->inverters[$inv_no]->mysqldeviceid . "," .
                "'Inv_irrad_250'" . "," .
                $park_no . "," . 
                $tsvals[$irrad_key];
              # Inv_PR         
              $key = "Inv".$inv_no."_PR";
              if ($tsvals[$key]){ 
                $insert_points[] = strval($ts) . "," .
                  $plantdesc->inverters[$inv_no]->mysqldeviceid . "," .
                  "'Inv_PR'" . "," .
                  $park_no . "," . 
                  $tsvals[$key];
              }
              if (!$irrad_mysqldeviceid){ 
                #       only if $irrad_inverters is ("" => "Average")
                # Energy_Actual_Pow  
                if ($tsvals["em_pac"]){ 
                  $insert_points[] = strval($ts) . "," .
                    $plantdesc->energymeters[0]->mysqldeviceid . "," .
                    "'Energy_Actual_Pow'" . "," .
                    $park_no . "," . 
                    $tsvals["em_pac"] / 1000; # value: W, write out: kW
                }
                  # System_PR         
                if ($tsvals["Sys_PR"]){ 
                  $insert_points[] = strval($ts) . "," .
                    $plantdesc->energymeters[0]->mysqldeviceid . "," .
                    "'System_PR'" . "," .
                    $park_no . "," . 
                    $tsvals["Sys_PR"];
                }
              }
              if($tsvals[$irrad_key]>=600){
                # AC_Module_Temp_600    
                if ($tsvals[$module_temp_key]){ 
                  $insert_points[] = strval($ts) . "," .
                    $plantdesc->inverters[$inv_no]->mysqldeviceid . "," .
                    "'AC_Module_Temp_600'" . "," .
                    $park_no . "," . 
                    $tsvals[$module_temp_key];
                }
                # Inv_irrad_600         
                $insert_points[] = strval($ts) . "," .
                  $plantdesc->inverters[$inv_no]->mysqldeviceid . "," .
                  "'Inv_irrad_600'" . "," .
                  $park_no . "," . 
                  $tsvals[$irrad_key];
                # Inv_AC_PR_600 (changed from: Inv_AC_PR)!                    
                foreach($inverter_nos as $inv_no){
                  $key = "Inv".$inv_no."_PR";
                  if ($tsvals[$key]){ 
                    $insert_points[] = strval($ts) . "," .
                      $plantdesc->inverters[$inv_no]->mysqldeviceid . "," .
                      "'Inv_AC_PR_600'" . "," .
                      $park_no . "," . 
                      $tsvals[$key];
                  }
                }
                # Inv_DC_Vol_Coeff  (changed from: DC_Vol_Coeff )!       
                foreach($inverter_nos as $inv_no){
                  $key = "Inv".$inv_no."_udc";
                  if ($tsvals[$key]){ 
                    $insert_points[] = strval($ts) . "," .
                      $plantdesc->inverters[$inv_no]->mysqldeviceid . "," .
                      "'Inv_DC_Vol_Coeff'" . "," .
                      $park_no . "," . 
                      $tsvals[$key];
                  }
                }
              }
            }
          }
          //$insert_points[] = "---------" . "," ;/////////////////////////////
        }
      }
      $count_points = count($insert_points);
      //print_r($insert_points);////////////////////////////////////////////

      $insert_points_len = 5500;
      $offs = 0;
      $dbname = ($testdb) 
        ? 'amplus_test_calculations'
        : 'amplus_all_calculations';
      while($offs < count($insert_points)){
        $insert_points_slice = array_slice($insert_points, $offs, $insert_points_len);
        $insert_points_slice = implode("),\n\t(",$insert_points_slice);
        $insert_query = 
          "REPLACE INTO " . $dbname . "
          (ts, device, `field`, park_no, `value`)
          VALUES
          (" . $insert_points_slice . ");";  //print_r($insert_query);
        if ($showQueries) {
          print_r($insert_query);////////////////////////////////////////////
          print("\n");
        }
        $success = mysql_query_execute($insert_query);
        if ($success) {
          print("<p>written to '$dbname' ".
            "a slice starting at ".$offs." out of $count_points points</p>\n");
        } else {
          print("\n<p>ERROR writing up to $insert_points_len of $count_points points starting at ".$offs.
            " to '$dbname'</p>\n");
        }
        $offs += $insert_points_len;
      }
      print("<p>memory_get_usage: ".(memory_get_usage()/1024)." KB</p>\n");/////
      print("<h2>finished plant: ".$plantdesc->plantname ."</h2>\n");
      
    }
  }
}

