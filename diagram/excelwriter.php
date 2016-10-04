<?php
session_start();

require_once 'Spreadsheet/Excel/Writer.php';
//require_once 'Spreadsheet_Excel_Writer-0.9.2/Spreadsheet/Excel/Writer.php';
include('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
include('../functions/dgr_func_jpgraph.php');

// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

if($typ=='pr_gse'){

	$start_zeile=0;
	// sending HTTP headers


	$datum_arr=explode( ';',$_SESSION['xls_pr_gse']);
	if($datum_arr[0]=='tag'){
		$park_no_arr=array($datum_arr[1]);
		//print_r($park_no_arr);
		$xls_str=calc_pr_werte($park_no_arr,$datum_arr[2],$datum_arr[3],$datum_arr[4],0,'xls');
	}else{
		//Berechnete Werte aus DB
		$xls_str=get_pr_data_mon($datum_arr[0],$datum_arr[1],0,$datum_arr[3],$datum_arr[4],'xls');

	}


}else{

	$start_zeile=1;
	//$workbook->send('export.xls');

	if($typ=='balken'){
		$xls_str=$_SESSION['xls_diag_balken'];
	}elseif($typ=='balken_gse'){
		$xls_str=$_SESSION['xls_diag_balken_gse'];
	}elseif($typ=='balken_enel'){
		$xls_str=$_SESSION['xls_diag_balken_enel'];
	}elseif($typ=='linie'){
		$xls_str=$_SESSION['xls_diag_linie'];
	}elseif($typ=='s0_linie'){
		$xls_str=$_SESSION['xls_diag_s0_linie'];
	}elseif($typ=='area_linie'){
		$xls_str=$_SESSION['xls_area_linie'];
	}
}


$zeilen_arr=explode("\n",$xls_str);

$date_today=date("d-M-Y");
if($datum_arr[0]=='tag' || $phase=='tag'){

	$ueberschrift="Performance Ratio in the course of a day. Creation date: ".$date_today." for Park: ";
	$interval="Daily";
}elseif($datum_arr[0]=='mon' || $phase=='mon'){
	$ueberschrift="Performance Ratio in the course of a month. Creation date: ".$date_today." for Park: ";
	$interval="Monthly";
}elseif($datum_arr[0]=='jahr' || $phase=='jahr'){
	$interval="Yearly";
	$ueberschrift="Performance Ratio in the course of a year. Creation date: ".$date_today." for Park: ";

}

if($typ=='pr_gse' ){
	$filename = "SOL-PR-".$date_today."-".$zeilen_arr[0]."-".$interval.".xls";
	
	//$filename = "SOL-PR-".$zeilen_arr[0]."-".$date_today.".xls";
}else{
	$filename = "SOL-Data-".$date_today."-".$zeilen_arr[0].".xls";
}

$workbook->send($filename);


$sh_no=0;

for($z=0; $z<sizeof($zeilen_arr); $z++){

	$zeile=$zeilen_arr[$z];
	//echo $zeile."<br>";
	$spalten_arr=explode(",",$zeile);

	for($sp=0; $sp<sizeof($spalten_arr); $sp++){

		$sp_wert=$spalten_arr[$sp];
		if($sp==0 && sizeof($spalten_arr)==1 && $typ=='pr_gse'){ //Nur bei PR

			$sh_no++;
			$z_no=0;
			$titelzeile='';
			//echo $sp_wert."<br>";

			$worksheet[$sh_no] =& $workbook->addWorksheet($sp_wert);

			//1. Zeile
			$titelzeile.=$ueberschrift.$sp_wert;
			$worksheet[$sh_no]->write($z_no, $sp, $titelzeile);
			//echo "<br>Spaltenwert: ".$sp_wert."<br>";
			//echo "<br>Nummer1: ".$sh_no."<br>";
		}elseif($z==0){
			$sh_no=1;
			$z_no=0;
			//echo $sp_wert."<br>";
			if($sp==0){
				$worksheet[$sh_no] =& $workbook->addWorksheet('DataExport');
			}
			$worksheet[$sh_no]->write($z_no, $sp, $sp_wert);

		}else{
			// Creating a worksheet
			//echo "Nummer2: ".$sh_no."<br>";
			$worksheet[$sh_no]->write($z_no, $sp, $sp_wert);

		}
	}
	$z_no++;
}


// Let's send the file
$workbook->close();



?>