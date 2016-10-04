<?php
session_start();

//require_once 'Spreadsheet/Excel/Writer.php';
require_once 'Spreadsheet_Excel_Writer-0.9.2/Spreadsheet/Excel/Writer.php';

include('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
include('../functions/dgr_func_jpgraph.php');

// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

// sending HTTP headers
$workbook->send('pr_export.xls');

$datum_arr=explode( ';',$_SESSION['xls_pr_gse']);
if($datum_arr[0]=='tag'){
	$park_no_arr=array($datum_arr[1]);
	//print_r($park_no_arr);
	$xls_str_pr=calc_pr_werte($park_no_arr,$datum_arr[2],$datum_arr[3],$datum_arr[4],0,'xls');
}else{
		//Berechnete Werte aus DB
	$xls_str_pr=get_pr_data_mon($datum_arr[0],$datum_arr[1],0,$datum_arr[3],$datum_arr[4],'xls');

}

$zeilen_arr=explode("\n",$xls_str_pr);

$sh_no=0;

for($z=0; $z<sizeof($zeilen_arr); $z++){

	$zeile=$zeilen_arr[$z];
	//echo $zeile."<br>";
	$spalten_arr=explode(",",$zeile);

	for($sp=0; $sp<sizeof($spalten_arr); $sp++){

		$sp_wert=$spalten_arr[$sp];
		if($sp==0 && sizeof($spalten_arr)==1){
			$sh_no++;
			$z_no=-1;
			//echo $sp_wert."<br>";
			
			$worksheet[$sh_no] =& $workbook->addWorksheet($sp_wert);
		//	echo "<br>Spaltenwert: ".$sp_wert."<br>";	
		//	echo "<br>Nummer1: ".$sh_no."<br>";	
		}else{
			// Creating a worksheet		
			//	echo "Nummer2: ".$sh_no."<br>";				
			$worksheet[$sh_no]->write($z_no, $sp, $sp_wert);
			
		}
	}
$z_no++;
}
// Let's send the file
$workbook->close();



?>