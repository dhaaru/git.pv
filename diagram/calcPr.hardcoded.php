<?php
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

while ($jahr <= date('Y')) {
    print "jahr =  $jahr<br>";
    while ($mon <= date('n')) {
         print "mon =  $mon<br>";

        while ($tag <= $tg_mon[$mon]) {
             print "tag =  $tag<br>";

            //$park_no=10;
            //include ('patPr.php');
            $stamp = mktime(1, 0, 0, $mon, $tag, $jahr);
			$endstamp = mktime(14, 0, 0, $mon, $tag, $jahr);
            //mktime
            echo "$jahr-$mon-$tag<br>";

            $park_no = 10; 
           include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'anjar-pr', $pr_value)";
            echo $query."<br>";
            //return;
            mysql_query($query, $verbindung) or die(mysql_error());

            
            $park_no = 25;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'phagi-pr', $pr_value)";
            echo $query."<br>";
            //return;
            mysql_query($query, $verbindung) or die(mysql_error());

            $park_no = 20;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'charanka-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 50;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'neemuch-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 31;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'maharashtra-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 32;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'padayala-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 33;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'karnataka-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 34;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'dindugal-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 36;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'amplus-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 41;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'cwet-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
            
            $park_no = 43;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'amppune-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 45;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'goa-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
            
            $park_no = 37;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'punjab12-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
            
            $park_no = 38;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'punjab20-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 62;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'punjab4-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 39;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampmumbai-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
            
            $park_no = 51;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'kolkata-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 46;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampraisoni-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 61;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'rajasthan-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 55;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampLalpur-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 56;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampRudrapur-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			
			$park_no = 52;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampDominos-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 54;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampIndus-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 53;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampRoyal-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 57;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampOrigami-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 58;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampPolymers-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 21;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'massolar-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());
			
			$park_no = 59;
            include ('patPr.php');
            $query = "replace into _devicedatacompressedvalue3 (ts, device, `field`, value) values ($stamp, -1, 'ampYamaha-pr', $pr_value)";
            echo $query."<br>";
            mysql_query($query, $verbindung) or die(mysql_error());

            if ($jahr == date('Y') && $mon == date('n') && $tag == date('j')) {
				
                return;
            }
            
            $tag++;
        }
        $tag = 1;
        //return;
        $mon++;
    }
    $mon = 1;

    $jahr++;
}
mysql_query($query, $verbindung) or die(mysql_error());
?>
