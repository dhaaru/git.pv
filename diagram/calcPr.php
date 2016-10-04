<?php
#############################################################
# calculates performance ratio
#            writes into _devicedatacompressedvalue3
# -----------------------------------------------------------
# requires ./patPrDb.php (Influx version)
# -----------------------------------------------------------
# params:
# jahr    optional   year  [int]   default: current year
# mon     optional   month [1..12] default: current month
# tag     optional   day   [1-31]  default: 5 days before today
# days    optional   default:7 [int] calculate up to count of days
#############################################################

echo "start calcPr<br>";
require_once('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
set_time_limit(900);
if (!isset($jahr)) {
  $jahr = date("Y");
}
if (!isset($mon)) {
  $mon = date("n");
}
if (!isset($tag)) {
  $tag = date("j")-5;
}
if (!isset($days)) {
  $days = 7;
}
if (!isset($showQueries)) {
  $showQueries = False;
}
echo "$jahr-$mon-$tag<br>";


$tg_mon = 0;
if ($jahr % 400 == 0) {
  $tg_mon = array(1 => 31, 2 => 29, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
} elseif ($jahr % 4 == 0 && $jahr % 100 != 0) {
  $tg_mon = array(1 => 31, 2 => 29, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
} else {
  $tg_mon = array(1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
}
echo "$jahr-$mon-$tag<br>";

$query = "select * from _calcpr";
$ds_tmp = mysql_query($query, $verbindung) or die(mysql_error());

while ($jahr <= date('Y')) {
  print "jahr =  $jahr<br>";
  while ($mon <= date('n')) {
    print "mon =  $mon<br>";

    while ($tag <= $tg_mon[$mon]) {
      if ($days <= 0) return;
      $days--;
      print "tag =  $tag<br>";

      $stamp = mktime(1, 0, 0, $mon, $tag, $jahr);
      $endstamp = mktime(14, 0, 0, $mon, $tag, $jahr);
      //mktime
      echo "$jahr-$mon-$tag<br>";
      mysql_data_seek ( $ds_tmp , 0 );
      while ($row = mysql_fetch_array($ds_tmp)) {
        $park_no = $row["park"];
        
        include ('patPrDb.php');
        if (is_null($pr_value)) { 
          continue;
        }
        $query = "REPLACE INTO _devicedatacompressedvalue3 (ts, device, `field`, value) 
                  VALUES ($stamp, -1, '".$row["prfield"]."', $pr_value)";
        echo $query."<br>";
        mysql_query($query, $verbindung) or die(mysql_error());
      }



      if ($jahr == date('Y') && $mon == date('n') && $tag == date('j')) {

        return;
      }

      $tag++;
    }
    $tag = 1;
    $mon++;
  }
  $mon = 1;

  $jahr++;
}
mysql_query($query, $verbindung) or die(mysql_error());
?>
