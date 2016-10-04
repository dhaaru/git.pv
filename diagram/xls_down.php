<?php
session_start();

// Wir werden eine PDF Datei ausgeben
header('Content-type: text/cvs');
header('Content-Disposition: attachment; filename="export.csv"');
//header("Content-type: application/vnd-ms-excel"); 
//header("Content-Disposition: attachment; filename=export.xls"); 
// Die originale PDF Datei heißt original.pdf
//readfile('original.pdf');
//include ('exportHelper.php');
include('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

include('../functions/dgr_func_jpgraph.php');

if($typ=='balken'){
	if($semikolon==';'){
		$_SESSION['xls_diag_balken']= str_replace ( ',', ';', $_SESSION['xls_diag_balken'] );
	}
	echo $_SESSION['xls_diag_balken'];
}elseif($typ=='balken_gse'){
	echo $_SESSION['xls_diag_balken_gse'];
}elseif($typ=='balken_enel'){
	echo $_SESSION['xls_diag_balken_enel'];
}elseif($typ=='linie'){
	echo $_SESSION['xls_diag_linie'];
}elseif($typ=='area_linie'){
	echo $_SESSION['xls_area_linie'];
}elseif($typ=='pr_gse'){ //inaktiv, da dieser Fall über excelwriter.php läuft.
	//PR-Ratio Daten erst bilden? da sonst Diagramm aufbau v.a. bei Tagesansicht zu lange dauert.

	$datum_arr=explode( ';',$_SESSION['xls_pr_gse']);
	//print_r($datum_arr);


	//Phase, Tag, Mon, Jahr, anz_tage
	if($datum_arr[0]=='tag'){

		$park_no_arr=array($datum_arr[1]);
		//print_r($park_no_arr);
		$xls_str_pr=calc_pr_werte($park_no_arr,$datum_arr[2],$datum_arr[3],$datum_arr[4],0,'xls');
	}else{
		//Berechnete Werte aus DB
		$xls_str_pr=get_pr_data_mon($datum_arr[0],$datum_arr[1],0,$datum_arr[3],$datum_arr[4],'xls');

	}

	if($semikolon==';'){
		$xls_str_pr= str_replace ( ',', ';', $xls_str_pr );
	}
	echo $xls_str_pr;
	//echo $xls_str_pr;


}

?>

