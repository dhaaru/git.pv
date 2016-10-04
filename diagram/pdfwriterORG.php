<?php
session_start();

require_once 'fpdf.php';
//require_once 'Spreadsheet_Excel_Writer-0.9.2/Spreadsheet/Excel/Writer.php';
include('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
include('../functions/dgr_func_jpgraph.php');
include("../functions/allg_functions.php");

// Creating a workbook


class PDF extends FPDF {
	function __construct($filename, $park, $date_today, $capacity)
	{
		parent::__construct();
		$this->filename = $filename;
		$this->park = $park;
		$this->date_today = $date_today;
		$this->capacity = $capacity;
	}

	function Footer()
	{
		//Position 1,5 cm von unten
		$this->SetY(-15);
		//Arial kursiv 8
		$this->SetFont('Arial','I',8);
		//Seitenzahl
		$this->Cell(0,10,$this->filename,0,0,'L');
		$this->Cell(0,10,$this->PageNo(),0,0,'R');
	}

	function setCurrentPark($park){
		$this->park = $park;

	}

	function setCurrentCapacity($cap){
		$this->capacity= $cap;
	}

	function Header(){

		if ($this->park=="Legend"){
			return;
		}else {
			if ($this->PageNo()==1){
				$this->Ln(18);
			}
			$this->SetFont('Arial','B',10);



			$this->Cell(124, 12, $this->park, 1, 0, 'L');
			$this->Cell(44, 12, "Module capacity (kWp)", 1, 0, 'L');
			$this->Cell(22, 12, $this->capacity, 1, 0, 'C');
			$this->Ln();
			if($this->PageNo()==1){

				$this->SetFont('Arial','B',9);
				$this->Cell(53, 6, "Author:", 1, 0, 'L');
				$this->Cell(71, 6, get_user_attribut('name'), 1, 0, 'C');

				$this->Cell(44, 6, "Creation Date:", 1, 0, 'C');
				$this->Cell(22, 6, $this->date_today, 1, 0, 'C');

				$this->Ln();
				$this->SetFillColor(192, 192, 192);
				$datum_arr=explode( ';',$_SESSION['xls_pr_gse']);

				if($datum_arr[0]=='tag'){
					$this->Cell(190, 10, "Performance Ratio Determination, $datum_arr[2].$datum_arr[3].$datum_arr[4]", 1, 0, 'C', 1);
				}elseif($datum_arr[0]=='mon'){
					$mon = "";
					switch ($datum_arr[3]){
						case 1:
							$mon = "January";
							break;
						case 2:
							$mon = "February";
							break;
						case 3:
							$mon = "March";
							break;
						case 4:
							$mon = "April";
							break;
						case 5:
							$mon = "May";
							break;
						case 6:
							$mon = "June";
							break;
						case 7:
							$mon = "July";
							break;
						case 8:
							$mon = "August";
							break;
						case 9:
							$mon = "September";
							break;
						case 10:
							$mon = "October";
							break;
						case 11:
							$mon = "November";
							break;
						case 12:
							$mon = "December";
							break;
					}

					$this->Cell(190, 10, "Performance Ratio Determination, $mon, $datum_arr[4]", 1, 0, 'C', 1);
				}else{
					$this->Cell(190, 10, "Performance Ratio Determination, $datum_arr[4]", 1, 0, 'C', 1);
				}
				$this->Ln();
			}
		}

		$this->SetFont('Arial','',9);
		$this->SetFillColor(255, 204, 0);

		$zeile=' , , , , , , , , , , , ,PR';
		$spalten_arr=explode(",",$zeile);
		$spalte_aus=array(1,3,5,6,7,8,10,12);
		$size=38;
		
		for($sp=0; $sp<sizeof($spalten_arr); $sp++){

				
			if(in_array($sp, $spalte_aus )){
				//	if($sp==1 || $sp == 3 || $sp == 5 || $sp == 8 ||  $sp == 10 ||  $sp == 12 || $sp == 7){
				continue;
			}elseif ($sp==0){

				//	}elseif ($sp==8){
				//	$size =27;
			}elseif ($sp==2){

			}


			$sp_wert=$spalten_arr[$sp];

			$this->Cell($size,4,$sp_wert,'TLR', 0, 'C', 1);


		}
		$this->Ln();


		$zeile='Date, ,EGSE,Radiation,Radiation, ,Temperature,Module,Temperature, , E*GSE,Performance,with';
		$spalten_arr=explode(",",$zeile);
		for($sp=0; $sp<sizeof($spalten_arr); $sp++){


			if(in_array($sp, $spalte_aus )){
				continue;
			}elseif ($sp==0){
					
			}elseif ($sp==2){

			}

			$sp_wert=$spalten_arr[$sp];

			$this->Cell($size,4,$sp_wert,'LR', 0, 'C', 1);


		}
		$this->Ln();

		$zeile=' , E total,> 100 W/qm,total,> 100 W/qm,Temperature,> 100 W/qm,capacity,correction,Eref,[kWh],Ratio,temperature';
		$spalten_arr=explode(",",$zeile);
		for($sp=0; $sp<sizeof($spalten_arr); $sp++){

				
			if(in_array($sp, $spalte_aus )){
				continue;
			}elseif ($sp==0){

				//}elseif ($sp==8){
				//$size =27;
			}elseif ($sp==2){

			}


			$sp_wert=$spalten_arr[$sp];

			$this->Cell($size,4,$sp_wert,'LR', 0, 'C', 1);


		}
		$this->Ln();

		$zeile=',,,[Wh/qm],[Wh/qm],total [�C],[�C],[kWp],factor,,,[%],[%]';
		$spalten_arr=explode(",",$zeile);
		for($sp=0; $sp<sizeof($spalten_arr); $sp++){


			if(in_array($sp, $spalte_aus )){
				continue;
			}elseif ($sp==0){

				//}elseif ($sp==8){
				//$size =27;
			}elseif ($sp==2){
					
			}


			$sp_wert=$spalten_arr[$sp];

			$this->Cell($size,4,$sp_wert,'BLR', 0, 'C', 1);
		}

		$this->Ln();
		if ($this->PageNo()==1){

			$this->Image("logo.jpg", 168, 10, 30, 15);
			$this->Image("egse.png", 58, 57, 20, 14);
			$this->Image("eref.png", 135, 57, 20, 14);
		}else {
			$this->Image("egse.png", 58, 23, 20, 14);
			$this->Image("eref.png", 135, 23, 20, 14);
		}

	}
}



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

	if($typ=='balken'){
		$xls_str=$_SESSION['xls_diag_balken'];
	}elseif($typ=='balken_gse'){
		$xls_str=$_SESSION['xls_diag_balken_gse'];
	}elseif($typ=='balken_enel'){
		$xls_str=$_SESSION['xls_diag_balken_enel'];
	}elseif($typ=='linie'){
		$xls_str=$_SESSION['xls_diag_linie'];
	}elseif($typ=='area_linie'){
		$xls_str=$_SESSION['xls_area_linie'];
	}
}

$zeilen_arr=explode("\n",$xls_str);


// echo "hallo";


// foreach ($zeilen_arr as $zeile){
// 	echo $zeile;
// 	echo "<br>";
	
// 	foreach ($zeile as $word){
// 		echo $word." ";
// 	}
// }
// return;



$date_today=date("d.m.Y");

if($datum_arr[0]=='tag'){

	//$ueberschrift="Performance Ratio in the course of a day. Creation date: ".$date_today." for Park: ";
	$interval="Daily";
}elseif($datum_arr[0]=='mon'){
	//$ueberschrift="Performance Ratio in the course of a month. Creation date: ".$date_today." for Park: ";
	$interval="Monthly";
}elseif($datum_arr[0]=='jahr'){
	$interval="Yearly";
	//$ueberschrift="Performance Ratio in the course of a year. Creation date: ".$date_today." for Park: ";

}

//$filename = "SOL-PR-".$zeilen_arr[0]."-".$date_today.".pdf";
$filename = "INDIA_SOL-PR-".$date_today."-Performance Report-".$zeilen_arr[0]."-".$interval.".pdf";


$zeile = $zeilen_arr[2];
$spalten_arr=explode(",",$zeile);

$pdf = new PDF($filename, $zeilen_arr[0], $date_today, $spalten_arr[7]);

$pdf->AliasNbPages();
$pdf->AddPage();
$sh_no=0;
$rows = sizeof($zeilen_arr)-1;




//topLine($pdf);

/////////////////////////

if($datum_arr[0]=='tag'){
	$min = 62;
	$max = sizeof($zeilen_arr)-1;
	//	echo "tag";
}elseif($datum_arr[0]=='monat'){
	$min = 2;
	$max = sizeof($zeilen_arr)-1;
	//	echo "month";
}else{
	$min =2;
	$max = sizeof($zeilen_arr)-1;
	//	echo "year";
}
//echo $min." = ".$zeilen_arr[$min]." / ".$max ." = ".$zeilen_arr[$max];


//echo "<BR>";
for($z=$min; $z<$max; $z++){

	$zeile=$zeilen_arr[$z];
	//echo $zeile."<br>";
	$spalten_arr=explode(",",$zeile);
	// 	if ($z>1 && $spalten_arr[2]==0){
	// 		continue;
	// 	}


	if (sizeof($spalten_arr)<2){
		$pdf->setCurrentPark($spalten_arr[0]);

		if (sizeof($zeilen_arr)<$z+3){
			$pdf->setCurrentCapacity(0);
		}else{

			$zeile=$zeilen_arr[$z+2];
			$spalten_arr=explode(",",$zeile);
			$pdf->setCurrentCapacity($spalten_arr[7]);
		}

		$pdf->AddPage();
			
		// 		$pdf->SetFont('Arial','B',9);
		// 		$pdf->Cell(28,6,$spalten_arr[0],1, 0, 'C');
			
		// 		$pdf->SetFont('Arial','',9);
		// 		$pdf->Ln();
		//	topLine($pdf);
		$z++;
		continue;
	}else if($datum_arr[0]=='tag'){
		$datumerg= $spalten_arr[0];
		$datumv = explode(" ", $datumerg);
		$datum = explode(":", $datumv[1]);
		if(intval($datum[0])<5 || intval($datum[0])>22 || (intval($datum[0])==22 && intval($datum[1])>0) ){
			//echo 		$datumerg."<BR>";
			continue;
		}
	}
$spalte_aus=array(1,3,5,6,7,8,10,12);
		$size=38;
	for($sp=0; $sp<sizeof($spalten_arr); $sp++){


		if(in_array($sp, $spalte_aus )){
			continue;
		}

		$sp_wert=$spalten_arr[$sp];
		if ($sp==0){

		}elseif ($sp==2){
				
			$sp_wert=number_format(round($sp_wert, 0), 0 , ',', '.');
		}elseif ($sp==4){

				
			$sp_wert=number_format(round($sp_wert, 0), 0 , ',', '.');
		}elseif ($sp==11){
			$sp_wert=number_format(round($sp_wert, 1), 1 , ',', '.');

		}elseif ($sp==6) {

			$sp_wert=number_format(round($sp_wert, 1), 1 , ',', '.');
		}elseif ($sp==9){
			$sp_wert=number_format(round($sp_wert, 1), 1 , ',', '.');
		}elseif ($sp==10){
			$sp_wert=number_format(round($sp_wert, 1), 1 , ',', '.');
		}else {
			$sp_wert=number_format(round($sp_wert, 0), 0 , ',', '.');
		}


		$pdf->Cell($size,6,$sp_wert,1, 0, 'R');
			
		//echo $sp_wert." ";


	}


	$pdf->Ln();
	//echo "<BR>";

}


$pdf->setCurrentPark("Legend");
$pdf->AddPage();


$pdf->SetFont('Arial','B',9);
$pdf->Cell(130, 4, "Legend:", 1, 0, 'L');
$pdf->SetFont('Arial','',9);

// $pdf->Ln();
// $pdf->Cell(30, 6, "E total", 1, 0, 'L');
// $pdf->Cell(50, 6, "Energy measured by GSE-Meter", 1, 0, 'L');
// $pdf->Ln();
// $pdf->Cell(30, 6, "[kWh]", 1, 0, 'L');
// $pdf->Cell(50, 6, "E total:", 1, 0, 'L');

$pdf->Ln();
$pdf->Cell(30, 4, "EGSE", 'TLR', 0, 'L');
$pdf->Cell(100, 4, "The enery measured by GSE-meter", 'TLR', 0, 'L');
$pdf->Ln();
$pdf->Cell(30, 4, "> 100 W/sq.m", 'LR', 0, 'L');
$pdf->Cell(100, 4, "when more than 100W/sq.m radiation", 'LR', 0, 'L');
$pdf->Ln();
$pdf->Cell(30, 4, "[kWh]", 'BLR', 0, 'L');
$pdf->Cell(100, 4, "", 'BLR', 0, 'L');

$pdf->Ln();
$pdf->Cell(30, 4, "Radiation", 'LRT', 0, 'L');
$pdf->Cell(100, 4, "", 'TLR', 0, 'L');
$pdf->Ln();
$pdf->Cell(30, 4, "> 100 W/sq.m", 'LR', 0, 'L');
$pdf->Cell(100, 4, "Radiation in module level when higher than 100 W/sq.m", 'LR', 0, 'L');
$pdf->Ln();
$pdf->Cell(30, 4, "[Wh/sq.m]", 'BLR', 0, 'L');
$pdf->Cell(100, 4, "", 'BLR', 0, 'L');

//$pdf->Ln();
//$pdf->Cell(30, 4, "Temperature", 'TLR', 0, 'L');
//$pdf->Cell(100, 4, "Module temperature when higher than 100 W/sq.m", 'TLR', 0, 'L');
//$pdf->Ln();
//$pdf->Cell(30, 4, "> 100 W/sq.m", 'LR', 0, 'L');
//$pdf->Cell(100, 4, "", 'LR', 0, 'L');
//$pdf->Ln();
//$pdf->Cell(30, 4, "[�C]", 'BLR', 0, 'L');
//$pdf->Cell(100, 4, "", 'BLR', 0, 'L');

$pdf->Ln();
$pdf->Cell(30, 4, "Module Capacity", 'TLR', 0, 'L');
$pdf->Cell(100, 4, "Installed module capacity", 'TLR', 0, 'L');
$pdf->Ln();
$pdf->Cell(30, 4, "[kWp]", 'BLR', 0, 'L');
$pdf->Cell(100, 4, "", 'BLR', 0, 'L');

//$pdf->Ln();
//$pdf->Cell(30, 4, "Temperature", 'TLR', 0, 'L');
//$pdf->Cell(100, 4, "Temperature correction factor in", 'TLR', 0, 'L');
//$pdf->Ln();
//$pdf->Cell(30, 4, "correction", 'LR', 0, 'L');
//$pdf->Cell(100, 4, "correlation to the module temperature", 'LR', 0, 'L');
//$pdf->Ln();
//$pdf->Cell(30, 4, "factor", 'BLR', 0, 'L');
//$pdf->Cell(100, 4, "", 'BLR', 0, 'L');

$pdf->Ln();
$pdf->Cell(30, 4, "Eref", 'TLR', 0, 'L');
$pdf->Cell(100, 4, "Product of radiation (> 100 W/sq.m)", 'TLR', 0, 'L');
$pdf->Ln();
$pdf->Cell(30, 4, "[kWh]", 'BLR', 0, 'L');
$pdf->Cell(100, 4, "and module capacity", 'BLR', 0, 'L');

//$pdf->Ln();
//$pdf->Cell(30, 4, "E*GSE", 'TLR', 0, 'L');
//$pdf->Cell(100, 4, "Energy with temperature correction", 'TLR', 0, 'L');
//$pdf->Ln();
//$pdf->Cell(30, 4, "[kWh]", 'BLR', 0, 'L');
//$pdf->Cell(100, 4, "", 'BLR', 0, 'L');
$pdf->Ln();
$pdf->Cell(30, 4, "Performance Ratio", 'TLR', 0, 'L');
$pdf->Cell(100, 4, "Performance Ratio", 'TLR', 0, 'L');
$pdf->Ln();
$pdf->Cell(30, 4, "[%]", 'BLR', 0, 'L');
$pdf->Cell(100, 4, "", 'BLR', 0, 'L');


//$pdf->Ln();
//$pdf->Cell(30, 4, "Performance Ratio", 'TLR', 0, 'L');
//$pdf->Cell(100, 4, "", 'TLR', 0, 'L');
//$pdf->Ln();
//$pdf->Cell(30, 4, "", 'LR', 0, 'L');
//$pdf->Cell(100, 4, "", 'LR', 0, 'L');
//
//
//$pdf->Ln();
//$pdf->Cell(30, 4, "[%]", 'LR', 0, 'L');
//$pdf->Cell(100, 4, "", 'LR', 0, 'L');
//$pdf->Ln();
//$pdf->Cell(30, 4, "", 'BLR', 0, 'L');
//$pdf->Cell(100, 4, "", 'BLR', 0, 'L');


$pdf->Output($filename, "D");



// Let's send the file




?>