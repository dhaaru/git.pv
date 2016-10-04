<?php

$park = "RREC";
$resolution = 300;

if ($park_no == 20) {
    $resolution = 900;
    $park = "Charanka";
} else if ($park_no == 10) {
    $resolution = 300;
    $park = "Anjar";
} else if($park_no == 50){
    $resolution = 300;
    $park = "Neemuch";
}else if($park_no == 31){
	$resolution = 300;
    $park = "Maharashtra";
}else if($park_no == 32){
	$resolution = 300;
    $park = "Padayala";
}else if($park_no == 33){
	$resolution = 300;
    $park = "Karnataka";	
}else if($park_no == 34){
	$resolution = 300;
    $park = "Dindugal";
}else if($park_no == 36){
	$resolution = 300;
    $park = "Amplus";
}else if($park_no == 41){
	$resolution = 300;
    $park = "CWET";
}else if($park_no == 43){
	$resolution = 300;
    $park = "Amplus_Pune";	
 } else if($park_no == 45){
	$resolution = 300;
    $park = "GOA";	
 }  else if($park_no == 37){
	$resolution = 300;
    $park = "PUNJAB12";	
 }else if($park_no == 39){	
	$resolution = 300;
    $park = "Amplus_Mumbai";	
 } else if($park_no == 38)
 {
	 $resolution = 300;
    $park = "PUNJAB20";
 }else if($park_no == 51){
	 $resolution = 300;
    $park = "Kolkata";
 } else if($park_no == 46){
 	$resolution = 300;
    $park = "Amplus_Raisoni";
 }else if($park_no == 61){
 	$resolution = 300;
    $park = "Rajasthan";
 }else if($park_no == 52){
 	$resolution = 300;
    $park = "Amplus Dominos Nagpur";
 }else if($park_no == 54){
 	$resolution = 300;
    $park = "Amplus Indus Nagpur";
 }else if($park_no == 53){
 	$resolution = 300;
    $park = "Amplus Royal Heritage";
 }
 else if($park_no == 55){
 	$resolution = 300;
    $park = "Amplus Lalpur";
 }
 else if($park_no == 56){
 	$resolution = 300;
    $park = "Amplus Rudrapur";
 }
 else if($park_no == 62){
 	$resolution = 300;
    $park = "PUNJAB4";
 }
else if($park_no == 57){
 	$resolution = 300;
    $park = "Amplus Origami Bangaluru";
 }
 else if($park_no == 58){
 	$resolution = 300;
    $park = "Amplus Polymers Nagpur";
 }
 else if($park_no == 59){
 	$resolution = 300;
    $park = "Amplus Yamaha";
 }
else if($park_no == 21){
 	$resolution = 300;
    $park = "MAS SOLAR-IND";
 }

if ($print) {
    header('Content-Disposition: attachment; filename="' . $park . '-PR-' . $jahr . '-' . $mon . '-' . $tag . '.xls"');
}
require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);


date_default_timezone_set('UTC');
$stamp = mktime(0, 0, 0, $mon, $tag, $jahr);
$endstamp = mktime(14, 0, 0, $mon, $tag, $jahr);

set_time_limit(600);

$ln = "<br>";

if ($print) {
	$ln="\r\n";
	echo "Timestamp\tMeter Energy total\tAverage Irradiation\r\n";
	echo "DD/MM/YYYY HH:MM\tkWh\t15 Min avg\r\n";
}
$installed = 1004.64;

$time = 0;
$irr = 0;
$deltaE = 0;

$meter = array();
$weather = array();

$curr = 0;

if ($park_no == 10) {
    $installed = 5000;
    //732,U1
    $query = "select max(value) as max from _devicedatavalue where `field`='EA-' and device = 670 ts < $stamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $curr = $row_ds1[max];
    }


    $query = "select (ts+19800) as t, value*1000 as value from _devicedatavalue where value  > 0 and `field`='EA-' and device=670 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if (is_null($meter[floor($row_ds1[t] / $resolution)])) {
            $meter[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
        }
    }

    $query = "select (ts+19800) as t, value * 13 as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 732 and field = 'U1'";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
            $weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
        }
    }
}else if($park_no == 50){ //Neemuch

	$installed = 121;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='EM_Accord_Act_Energy_Exp' and device=1939 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
	$query = "select max(value) as max from _devicedatavalue where `field`='EM_Accord_Act_Energy_Exp' and device=1946 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	while ($row_ds1 = mysql_fetch_array($ds1)) {
		$curr += $row_ds1[max];
	}


    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EM_Accord_Act_Energy_Exp' and device=1946 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = $row_ds1[e_total];
    }

    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EM_Accord_Act_Energy_Exp' and device=1939 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][1] = $row_ds1[e_total];
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 7126 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if($park_no == 31){ // Maharashtra

	$installed = 20;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=6906 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
	$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=6905 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	while ($row_ds1 = mysql_fetch_array($ds1)) {
		$curr += $row_ds1[max];
	}


    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EAE' and device=6906 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = $row_ds1[e_total];
    }

    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EAE' and device=6905 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][1] = $row_ds1[e_total];
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 7100 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if($park_no == 32){ // PADAYALA

	$installed = 25;
	
	
		$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=6730 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
	$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=6896 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	while ($row_ds1 = mysql_fetch_array($ds1)) {
		$curr += $row_ds1[max];
	}


    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EAE' and device=6730 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = $row_ds1[e_total];
    }

    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EAE' and device=6896 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][1] = $row_ds1[e_total];
    }
	
	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 6335 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 25) {
    $query = "select max(e_total) as max from 2541_messdaten_s0 where ts < $stamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $curr = $row_ds1[max];
    }


    $query = "select (ts+19800) as t, e_total from 2541_messdaten_s0 where e_total > 0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if (is_null($meter[floor($row_ds1[t] / $resolution)])) {
            $meter[floor($row_ds1[t] / $resolution)] = $row_ds1[e_total];
        }
    }

    $query = "select (ts+19800) as t, value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 108 and field = 'SR2'";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
            $weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
        }
    }
}else if ($park_no == 34) { // DINDUGAL
    $installed = 1000;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=7252 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='EAE' and device=7252 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 7268 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 33) { // Karnataka
    $installed = 17;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=7285 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EAE' and device=7285 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 7747 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 36) { // Amplus
    $installed = 75600;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=7795 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=7795 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 7794 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 41) { // CWET
    $installed = 12.5;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Tot_Energy' and device=8148 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Tot_Energy' and device=8148 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 8149 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 43) { // Amplus Pune
    $installed = 246060;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=8240 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=8240 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, sum(value)/2 as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device IN (8414,8412) and field = 'Solar_Radiation' group by ts";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
		
}else if ($park_no == 45) { // Goa
    	$installed = 112;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Tot_Energy' and device=8724 and and value>0 ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Tot_Energy' and device=8722 and and value>0 ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	$query = "select max(value) as max from _devicedatavalue where `field`='Tot_Energy' and device=8727 and and value>0 ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	$query = "select max(value) as max from _devicedatavalue where `field`='Tot_Energy' and device=8726 and value>0 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	$query = "select max(value) as max from _devicedatavalue where `field`='Tot_Energy' and device=8723 and value>0 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	
	$query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Tot_Energy' and device=8724 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Tot_Energy' and device=8722 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][1] = round($row_ds1[e_total],2);
    }
	$query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Tot_Energy' and device=8727 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][2] = round($row_ds1[e_total],2);
    }
	$query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Tot_Energy' and device=8726 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][3] = round($row_ds1[e_total],2);
    }
	$query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Tot_Energy' and device=8723 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][4] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 8728 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 37) { // Punjab12 MW 
    $installed = 12;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=8860 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EAE' and device=8860 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 8857 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 38) { // Punjab20 MW 
    $installed = 20;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=9172 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EAE' and device=9172 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 9164 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 62) { // Punjab4 MW 
    $installed = 4;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=11751 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EAE' and device=11751 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 9164 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 39) { // Amplus Mumbai
    $installed = 201600;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=9317 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=9317 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 9316 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 51) { // Kolkata
    $installed = 10000;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='AC_Energy' and device=9902 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
	$query = "select max(value) as max from _devicedatavalue where `field`='AC_Energy' and device=9901 and and value>0 ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	$query = "select max(value) as max from _devicedatavalue where `field`='AC_Energy' and device=9903 and and value>0 ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	$query = "select max(value) as max from _devicedatavalue where `field`='AC_Energy' and device=9905 and value>0 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	
	$query = "select max(value) as max from _devicedatavalue where `field`='AC_Energy' and device=9904 and value>0 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='AC_Energy' and device=9902 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }
	
	 $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='AC_Energy' and device=9901 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][1] = round($row_ds1[e_total],2);
    }
	
	 $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='AC_Energy' and device=9903 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][2] = round($row_ds1[e_total],2);
    }
	
	 $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='AC_Energy' and device=9905 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][3] = round($row_ds1[e_total],2);
    }
	
	 $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='AC_Energy' and device=9904 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][4] = round($row_ds1[e_total],2);
    }
	

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 9301 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 46) { // Amplus Raisoni
    $installed = 75600;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=9958 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=9958 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 9957 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 61) { // Rajasthan
    $installed = 5;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='EAE' and device=9288 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value as e_total from _devicedatavalue where `field`='EAE' and device=9288 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 9279 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}
else if ($park_no == 55) { // Amplus Lalpur
    $installed = 252000;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=11588 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=11588 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 10142 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}	

else if ($park_no == 56) { // Amplus Rudrapur
    $installed = 150000;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=7772 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=7772 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 12690 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}	
else if ($park_no == 52) { // Amplus Dominos Nagpur
    $installed = 126000;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=10070 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=10070 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 11623 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}	else if ($park_no == 54) { // Amplus Indus Nagpur
    $installed = 126000;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=10151 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=10151 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 10452 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}else if ($park_no == 53) { // Amplus Royal Heritage
    $installed = 201600;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=10071 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=10071 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 12692 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}

else if ($park_no == 57) { // Amplus Origami Bangaluru
    $installed = 151200;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=11615 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=11615 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 12029 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}
else if ($park_no == 58) { // Amplus Polymers Nagpur
    $installed = 198000;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=12037 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=12037 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 12417 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}
else if ($park_no == 21) { // MAS SOLAR-IND
    $installed = 66000;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=12908 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=12908 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 12907 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}
else if ($park_no == 59) { // Amplus Yamaha
    $installed = 476600;
  
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=12895 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr = $row_ds1[max];
	}
	
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=12899 and and value>0 ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=12897 and and value>0 ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=12898 and value>0 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	
	$query = "select max(value) as max from _devicedatavalue where `field`='Forward_Active_Energy' and device=12896 and value>0 and ts < $stamp";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}
	while ($row_ds1 = mysql_fetch_array($ds1)) {
        	$curr += $row_ds1[max];
	}
	
    $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=12895 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = round($row_ds1[e_total],2);
    }
	
	 $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=12899 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][1] = round($row_ds1[e_total],2);
    }
	
	 $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=12897 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][2] = round($row_ds1[e_total],2);
    }
	
	 $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=12898 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][3] = round($row_ds1[e_total],2);
    }
	
	 $query = "select (ts+19800) as t, value*1000 as e_total from _devicedatavalue where `field`='Forward_Active_Energy' and device=12896 and value>0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][4] = round($row_ds1[e_total],2);
    }
	

	$query = "select (ts+19800) as t, value as value from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 12209 and field = 'Solar_Radiation'";
	if ($showQueries == 1) {
		if ($print) {
			echo $query . "$ln";
		}
	}

	$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
	while ($row_ds1 = mysql_fetch_array($ds1)) {
		if (is_null($weather[floor($row_ds1[t] / $resolution)])) {
			$weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
		}
	}
}
else {
    $installed = 20000;

    $query = "select max(e_total) as max from 2061_messdaten_s0 where sn = 'Premier_01' ts < $stamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $curr = $row_ds1[max];
    }

    $query = "select max(e_total) as max from 2061_messdaten_s0 where sn = 'Premier2_01' ts < $stamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $curr += $row_ds1[max];
    }


    $query = "select (ts+19800) as t, e_total from 2061_messdaten_s0 where sn='Premier_01' and e_total > 0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][0] = $row_ds1[e_total];
    }

    $query = "select (ts+19800) as t, e_total from 2061_messdaten_s0 where sn='Premier2_01' and e_total > 0 and ts > $stamp and ts < $endstamp";
    if ($showQueries == 1) {
        if ($print) {
            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $meter[floor($row_ds1[t] / $resolution)][1] = $row_ds1[e_total];
    }


    $query = "select (ts+19800) as t, ((value-19.0476)*21) as value  from _devicedatavalue where ts > $stamp and ts < $endstamp and device = 180 and field = 'U1_900'";
    if ($showQueries == 1) {
        if ($print) {

            echo $query . "$ln";
        }
    }

    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $weather[floor($row_ds1[t] / $resolution)] = $row_ds1[value];
        //echo floor($row_ds1[t] / 900)."=".$row_ds1[input_3]."<br>";
    }
}


$break = true;
while ($stamp < $endstamp) {
    $energy = 0;
    $weatherval = 0;
    if(sizeof($meter[floor(($stamp + 19800) / $resolution)])!=5 && $park_no == 45 ){
		$energy = $curr;
	}else {
		if (sizeof($meter[floor(($stamp + 19800) / $resolution)]) > 0) {
			if (is_array($meter[floor(($stamp + 19800) / $resolution)])) {
				$energy = round(array_sum($meter[floor(($stamp + 19800) / $resolution)]));
			} else {
				$energy = round($meter[floor(($stamp + 19800) / $resolution)]);
			}
		}
	}
    if (sizeof($weather[floor(($stamp + 19800) / $resolution)]) > 0) {
        $weatherval = round($weather[floor(($stamp + 19800) / $resolution)], 2);
    } else {
        
    }

    if ($energy == 0) {
        $energy = $curr;
    }
    if ($print) {

        echo date('d/m/Y H:i', $stamp + 19800) . "\t" . $energy . "\t" . $weatherval;
    }
	if($park_no == 50 || $park_no == 31 ||$park_no == 32 || $park_no == 33 || $park_no == 41 || $park_no == 43 || $park_no == 45 || $park_no == 37 || $park_no == 38 || $park_no == 51 
	|| $park_no == 61 || $park_no == 55 || $park_no == 52 || $park_no == 54 || $park_no == 53 || $park_no == 62 || $park_no == 57  || $park_no == 58 || $park_no == 21 || $park_no == 59 || $park_no == 56){
		if ($weatherval != 0 && $energy!=0) {
			if ($break) {
				$break = false;
				if ($print) {
					echo "\t+";
				}
			} else {
			
			  $time++;
			  $irr+=$weatherval;		
			$deltaE+= $energy - $curr;
				if ($print) {
					echo "\t*";
				}
			}
		}
		else {
			$break = true;
		}
	}else if($park_no == 81){
		$break = false;
	}
	
	else {
		if ($curr < $energy && $weatherval >= 250) {
			if ($break) {
				$break = false;
				if ($print) {
					echo "\t+";
				}
			} else {
			
			  $time++;
			  $irr+=$weatherval;		
			  $deltaE+= $energy - $curr;
				if ($print) {
					echo "\t*";
				}
			}
		}
		else {
			$break = true;
		}
	}
   $curr = max($energy, $curr);
    $stamp+=$resolution;
    if ($print) {

        echo "\r\n";
    }
	
}
$reference = 1000;
if ($print) {
    echo "\r\n\r\nKWh \t" . $deltaE . "\r\n";
    echo "Yf=\t" . $deltaE / $installed . "\r\n";
    echo "avg irr=\t" . round($irr / $time, 2);
    echo "\r\nhours= \t" . ($time / (3600 / $resolution));
    echo "\r\navg insolation= \t" . ($irr / (3600 / $resolution) / $reference);
    echo "\r\npr= \t" . round((100 * (($deltaE / $installed) / ($irr / (3600 / $resolution) / $reference))), 2) . "%";
    //echo "\r\nkolkata pr= \t" .round(($deltaE / (($irr)*0.024336*0.154)),2);
}
if($park_no == 51){
	
	echo $energy = ($deltaE/1000). "\r\n";
	echo $irradiation = ($irr / (3600 / $resolution) / $reference). "\r\n";
	echo $pr_value = round(($energy / (($irradiation)*584*0.155)),2)*100;
	
}
else{
	$pr_value = round((100 * (($deltaE / $installed) / ($irr / (3600 / $resolution) / $reference))), 2);
}
?>
