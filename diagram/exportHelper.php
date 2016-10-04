<?php

$ersatz = array ( '<BR>' => ' ', '&nbsp;' => ' ', '&uuml;'=>'ü', '&auml;'=>'ä', '&ouml;'=>'ö' );

function formatStunde ($ertrag, $einstrahlung, $temperatur, $tag, $monat, $jahr){

	$ausgabe = "";
	$a=4;
	for ($i = $a; $i < 22; $i++){
		$end2=mktime($i,30,0,$monat,$tag,$jahr); //EndTimeStamp
		$end = date('d-M-Y H:i', $end2);

		$ausgabe.=$end.",";
		//	$ausgabe.=$end2.",";
		$ausgabe.=$ertrag[$i-$a].",";
		$ein = $einstrahlung[$i-$a];
		if ($ein == ''){
			$ein = 0;
		}
		$ausgabe.=$ein.",";
		$tem = $temperatur[$i-$a];
		if($tem == ''){
			$tem = 0;
		}
		$ausgabe.=$tem."\n";
	}
	return $ausgabe;
}

function formatTag ($ertrag, $einstrahlung, $temperatur, $monat, $jahr, $tage){

	$ausgabe = "";

	for ($i = 0; $i < $tage; $i++){
		$end2=mktime(0,0,0,$monat,$i+1,$jahr); //EndTimeStamp
		$end = date('d-M-Y', $end2);
		$ausgabe.=$end.",";
		//	$ausgabe.=$end2.",";
		$ausgabe.=$ertrag[$i].",";
		//echo $ertrag[$i]."<br>";
		$ein = $einstrahlung[$i];
		if ($ein == ''){
			$ein = 0;
		}
		$ausgabe.=$ein.",";
		$tem = $temperatur[$i];
		if($tem == ''){
			$tem = 0;
		}
		$ausgabe.=$tem."\n";
	}


	return $ausgabe;
}

function formatMonat ($ertrag, $einstrahlung, $temperatur, $jahr){

	$ausgabe = "";

	for ($i = 0; $i < 12; $i++){
		$end2=mktime(0,0,0,$i+1,1,$jahr); //EndTimeStamp
		$end = date('M-Y', $end2);
			
		$ausgabe.=$end.",";
		//$ausgabe.=$end2.",";
		$ausgabe.=$ertrag[$i].",";
		$ein = $einstrahlung[$i];
		if ($ein == ''){
			$ein = 0;
		}
		$ausgabe.=$ein.",";
		$tem = $temperatur[$i];
		if($tem == ''){
			$tem = 0;
		}
		$ausgabe.=$tem."\n";
	}

	//echo $ausgabe; exit();
	return $ausgabe;
}

function formatKurven($typ,$plot_arr, $phase, $tag, $mon, $jahr, $tage){

	$ausgabe = "";

	$a=0;
	if($phase=='tag'){
		$a=3;
		$zeilen=22;
	}elseif($phase=='mon'){
		$zeilen=$tage;
	}elseif($phase=='jahr'){
		$zeilen=12;
	}

	$start=0;
	//echo "Plot: ".$plot_arr[0][1];
	if($typ=='areas'){$start=1;} //Area-Kurven
	
	for ($i=$a; $i < $zeilen; $i++){

		if($phase=='tag'){
			
			$end2=mktime($i,30,0,$mon,$tag,$jahr); //EndTimeStamp
			$end = date('d-M-Y H:i', $end2);
		}elseif($phase=='mon'){
			$end2=mktime(0,0,0,$mon,$i+1,$jahr); //EndTimeStamp
			$end = date('d-M-Y', $end2);
		}elseif($phase=='jahr'){
			$end2=mktime(0,0,0,$i+1,1,$jahr); //EndTimeStamp
			$end = date('M-Y', $end2);

		}
		$ausgabe.=$end.",";
		for ($plot=$start; $plot <=sizeof($plot_arr); $plot++){
			
			//$ausgabe.=$end2.",";
			$ausgabe.=$plot_arr[$plot][($i-$a)].",";
		
		}
		$ausgabe.="\n";
	}
	//echo $ausgabe; exit();
	return $ausgabe;

}


?>