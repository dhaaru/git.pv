<?php
header('Content-type: text/csv');
if ($endstamp-$stamp>40*24*3600){
  header('Content-Disposition: attachment; filename="Export-'.date("Y", $stamp) . '.xls"');
}else if($endstamp-$stamp>20*24*3600){
  header('Content-Disposition: attachment; filename="Export-'.date("Y-m", $stamp) . '.xls"');
}else {
  header('Content-Disposition: attachment; filename="Energy Meter-'.date("Y-m-d", $stamp) . '.xls"');
}


#####################################################################
# TODO  for  final use
#####################################################################
# - filename:   parameter not implemented yet
# - $interval:  caller must set parameter
# - Influx databases: use database <name> according to iGate
# - other aggregate functions than 'SUM' (Average), and when to apply
#   other aggregate: wenn minus statt unterstrich, dann Durchschnitt?
#####################################################################

#####################################################################
# arguments   in use
#####################################################################
# stamp         required  timestamp:  beginning of the desired range
# endstamp      required  timestamp:  end of the desired range
# args          required: semicolon-separated string of datapoint-descriptors
# 						  each datapoint-descriptor:
#							a comma-separated string of exactly 9 substrings
#						  "0,1,0,8240,Activepower_Total,Active%20Power,15,kW,4;"
# 						[0]=preOffset
#							[1]=factor
#							[2]=postOffset
#							[3]=device id
#							[4]=field
#							[5]=title
#							[6]
#							[7]=unit
#							[8]
# interval		optional  default: "5m"
# offset		optional  default: 19800  (Indian Standard Time)
# showQueries	optional  if True: debug output
#####################################################################
# arguments   NOT IN USE
#####################################################################
# jahr
# jahr_heute
# monat_heute
# tag_heute
# phase			was used to handle a specific plant (filename)
# delta			. not used .
# user_typ

require_once("../connections/queriesInflux2.php");
require_once ("../connections/queriesMysql2.php");
require_once ("../connections/influxResolver.php");

if (is_null($args)) {
  return;
}
date_default_timezone_set('UTC');
if( !isset($offset)){
  $offset = 19800; // Indian Standard Time
}
if( !isset($interval)){
  $interval = "5m"; // time interval, default: "5m"
}
if (! (isset($stamp) and isset($endstamp)) ){
  return;
}
$args = split(";", $args);

$datapoints = array();
########################################################################
#  $datapoints array:  build from given args[]
########################################################################
foreach ($args as $arg) {
  # each $arg is a line of comma-separated $words
  #		"0,1,0,8240,Activepower_Total,Active%20Power,15,kW,4;"
  # 		[0]=preOffset, [1]=factor, [2]=postOffset ,[3]=device id ,[4]=field ,[5]=title, [6], [7]=unit, [8]
  $words = split(',', $arg);
  if (sizeof($words) != 9) {
    continue;
  }
  $nextDatapoint = array(
    'pre'		=>$words[0],
    'fact'		=>$words[1],
    'post'		=>$words[2],
    'title'		=>$words[5],
    'unit'		=>str_replace("SQUA", "^2", str_replace("DEG", "DEG ", $words[7])),
    'f'    		=>str_replace("PLUS", "+",$words[4]),
    'device'	=>$words[3],
    'd'			=>'-1',
    'iid'		=>'-1',
    'aggregate'	=>""
  );

  // resolve 'device' to 'd' and 'iid' LATER.
  # ##################################################
  # aggregated datapoints     "1939_1946"
  # ##################################################
  // a `device` string like this "1939_1946"  means:
  //		several datapoints from `device`s
  //		$devs '1939' and '1946'
  // 		have to be aggretaged into one resulting column.
  $devs=explode("_",$nextDatapoint['device']);
  if(count($devs)>1) {
    $preAggregated  = $nextDatapoint['pre'];
    $factAggregated = $nextDatapoint['fact'];
    $postAggregated = $nextDatapoint['post'];
    $nextDatapoint['pre'] = 0;
    $nextDatapoint['fact'] = 1;
    $nextDatapoint['post'] = 0;
    $i = 0;
    foreach ($devs as $devid){
      $nextDatapoint['device'] = $devid;
      $i++;
      if ($i == count($devs)){
        # pre, factor, post:  apply only after aggregation!
        #                  (only after the last of several
        #					datapoints to aggregate)
        $nextDatapoint['pre'] = $preAggregated;
        $nextDatapoint['fact'] = $factAggregated;
        $nextDatapoint['post'] = $postAggregated;
      }
      $datapoints[] = $nextDatapoint;
      $nextDatapoint['aggregate'] = "SUM"; // aggregate function
    }
  } else {
    $datapoints[] = $nextDatapoint;
  }
}

########################################################################
#	insert influx tags  'd','iid'
#			to each $datapoint (associative array) in
#			$datapoints array
########################################################################
$datapoints =
//insert_InfluxTags($verbindung, $datapoints, $showQueries);
insert_InfluxTags($datapoints, $showQueries);
if ($datapoints == Null) {
  print ("Error: count datapoints:".count($datapoints)." count inflTagSets:".count($inflTagsets)."\n");
  print ("One or several device ids are unknown in mysql `_field`: \n" );
}

// ================================
//	$datapoints
//		 is now complete.
//  [
//		[
//		pre			Pre-Offset
//		fact		Factor
//		post		Post-Offset
//		title		output column title
//		unit		output unit string
//		f    		mysql `field`  and Influx "f" tag (tables: _devicedatavalue, _field) example: "EM_Accord_Act_Pow"
//		device	mysql site-unique `device` ID     (tables: _devicedatavalue, _field) example: 1945.
//					mysql 'device' column needs to be translated to both influx tags 'd' and 'iid'.
//		d			influx tag (device name) example: "B10_INV1_DC4"
//		iid			influx tag (igate id) example: '3094'
//		aggregate	if '' (empty or empty string), then this datapoint makes exactly one resulting table column.
//					if 'sum', then this datapoint must be aggregated  SUMmed with the previous datapoint to one column.
//		]
//	]
// ================================

# ####################################################################
# build  $inflTagsetsUnique
# ####################################################################
$inflTagsetsUnique = array();
foreach ($datapoints as $datapoint){
  # By using {datapoint} as key of an associative array,
  # this array will contain only unique tagsets.
  $inflTagsetsUnique[ sprintf('%04d',$datapoint['device']) . $datapoint['f'] ] =
  array(
    "iid"=>$datapoint['iid'],
    "d"  =>$datapoint['d'],
    "f"  =>$datapoint['f']
  );
}

# ####################################################################
# arrays overview
# ####################################################################
# $args
#		numeric indexed array of output columns
#		- a column can contain multiple datapoints ('combined column')!
#		- datapoints not unique
#		- arbitrary column order
#
# $datapoints
#		numeric indexed array of datapoints
#		- column order
#		- 'combined columns' from $args expanded to several datapoints
#		- not unique
#
# $inflTagsetsUnique
#		from $datapoints
# 		associative array of tag sets (for Influx)
#		- the sole purpose of explicit keys is to have unique values.
# 		- unique (omitted duplicate datapoints)
#
# $series
#		Influx Query result set
#		column orientated

if ($showQueries){
  echo ("<p>args:<br>\n");
  # $args=	"0,1,0,8240,Activepower_Total,Active%20Power,15,kW,4;"
  echo ("[0]=preOffset, [1]=factor, [2]=postOffset ,[3]=deviceid ,[4]=field ,[5]=title, [6], [7]=unit, [8]\n");
  print_r($args);
  echo "\n</p>\n";

  echo "<p>datapoints<br>\n";
  print_r($datapoints);
  echo "\n</p>\n";

  echo "<p>inflTagsetsUnique<br>\n";
  print_r($inflTagsetsUnique);
  echo "\n</p>\n";
}

# ###########################################################
# influx query  based on tagsets {d, iid, f}
#   from which Influx Database name?     --> getInfluxDBName()
#   assume that all desired igates share the same Influx database.
# ###########################################################
$firstiid = reset($inflTagsetsUnique);
$firstiid = $firstiid['iid'];
$tmpseries   =
inflQuery_tagset( $stamp,
$endstamp,
$inflTagsetsUnique,
"MEAN(value)",
$interval,
array('iid','d','f'),
null,
$showQueries,
getInfluxDBName($firstiid)
);

$series = array();

$id = 0;
foreach ($tmpseries as $serie){
  $found=false;
  for ($i = 0; $i < sizeof($series); $i++){
    if ($series[$i]["tags"]["d"] == $serie["tags"]["d"]&&$series[$i]["tags"]["iid"] == $serie["tags"]["iid"] && $series[$i]["tags"]["f"] == $serie["tags"]["f"]){
      $found = true;
      foreach ( $serie["values"] as $value ){
        if ($value[1]!=""){
          $series[$i]["values"][]=$value;
        }
      }
    }
  }
  if (!$found){
    $tmp = array();
    $tmp["name"]=$serie["name"];
    $tmp["tags"]["d"] = $serie["tags"]["d"];
    $tmp["tags"]["iid"] = $serie["tags"]["iid"];
    $tmp["tags"]["f"] = $serie["tags"]["f"];
    $tmp["columns"][] = "time";
    $tmp["columns"][] = "mean";
    foreach ( $serie["values"] as $value ){
      if ($value[1]!=""){
        $tmp["values"][]=$value;
      }
    }
    $series[]=$tmp;

  }
}
//	from  ../connection/queriesInflux2.php

# ######################################################
# $result : arrays for lines of the result table
# ######################################################
# for each datapoint of $datapoints:
# 	  pull values from $series (influx query result as columns)
# 	  store values in 2-dimensional array (line-based arrays)
# 				      $resultlines[time][column]
# ######################################################
$resulttitles = array("Timestamp");
$resultunits = array("DD-MM-YYYY HH:mm");
$resultlines = array();
$col = 1;
foreach ($datapoints as $datapoint) {
  //	get_seriesIndex()  from  ../connection/queriesInflux2.php
  $sindex = get_seriesIndex($series, array('iid' => $datapoint['iid'],
  'd'   => $datapoint['d'],
  'f'   => $datapoint['f']
));
if ( empty($datapoint['aggregate']) ){
  $resulttitles[] = $datapoint['title'];
  $resultunits[]  = $datapoint['unit'];
  if ($sindex < 0) {
    # empty column! no result from InfluxDB
    #               solution: implode_keepEmptyCol()
  } else {
    foreach($series[$sindex]['values'] as $tsvalue){
      # apply pre, factor, post
      #     note: On the first one of several aggregated datapoints,
      #	        pre,post,factor have been set to 0,1,0
      #					(section 'aggregated datapoints'),
      #			so that pre,post,factor will only be applied
      #			after datapoints have been aggregated.
      $resultlines[$tsvalue[0]][$col] =
      (	$tsvalue[1]
      + $datapoint['pre']  )
      * $datapoint['fact']
      + $datapoint['post'];
    }
  }
} else {
  $col--;
  if ($sindex < 0) {
    # leave value unchanged
  } else if($datapoint['aggregate'] == 'SUM'){
    foreach($series[$sindex]['values'] as $tsvalue){
      $resultlines[$tsvalue[0]][$col] += $tsvalue[1];
      # apply pre, factor, post
      #			only after datapoints have been aggregated.
      $resultlines[$tsvalue[0]][$col] =
      (	$resultlines[$tsvalue[0]][$col]
      + $datapoint['pre']  )
      * $datapoint['fact']
      + $datapoint['post'];
    }
  } else {
    # other aggregate functions than 'SUM'
  }

}
$col++;
}

if ($showQueries){
  echo "resulttitles:\n";
  print_r($resulttitles);

  echo "\nresultunits:\n";
  print_r($resultunits);

  echo "\nresultlines:\n";
  print_r($resultlines);
}

function implode_keepEmptyCol($glue,$a){
  # like implode, but
  # adds additional glue for missing index key
  #
  # assume $a is sorted
  # assume $a has numeric keys
  # assume $a first index should be 1
  $keyPrev = 0;
  foreach ($a as $key => $val){
    while ( ++$keyPrev < $key){
      # missing index $key; insert $key=>val
      $a[$keyPrev] = "";
    }
  }
  ksort($a);
  return implode($glue,$a);
}

# ######################################################
# output  		as tab separated text
#         		an array >>  a line of the table
#				apply timezone offset
# ######################################################
print (implode_keepEmptyCol("\t",$resulttitles) . "\r\n");
print (implode_keepEmptyCol("\t",$resultunits) . "\r\n");
ksort($resultlines);
foreach($resultlines as $ts => $resultline){
  // $offset:  Indian Standard Time
  #$resultline[0] = date('d-m-Y H:i', $ts + $offset);
  print (date('d-m-Y H:i',$ts + $offset) . "\t" .
  implode_keepEmptyCol("\t",$resultline) . "\r\n");
}

return;
?>
