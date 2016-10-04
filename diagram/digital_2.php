<?php


require_once ('../connections/verbindung.php');
mysql_select_db ( $database_verbindung, $verbindung );

$query = "select * from digitaleingang where portal = ".$park." order by ebene, name";
$ds1 = mysql_query ( $query, $verbindung ) or die ( mysql_error () );

$tree = array();

$ebene = "";
$ebenenIndex = -1;
$portIndex = 0;

$IGATE=0;
$NODE = 1;
$INPUT = 2;
$NAME=3;

while ( $row_ds1 = mysql_fetch_array ( $ds1 ) ) {
	if ($ebene != $row_ds1["ebene"]){
		$ebene = $row_ds1["ebene"];
		$ebenenIndex++;
		$tree[$ebenenIndex]=array();
		$tree[$ebenenIndex][0]=$ebene;
		$tree[$ebenenIndex][1]=array();
		$portIndex=0;
	}
	
	$tree[$ebenenIndex][1][$portIndex][$IGATE]=$row_ds1["igate"];
	$tree[$ebenenIndex][1][$portIndex][$NODE]=$row_ds1["node"];
	$tree[$ebenenIndex][1][$portIndex][$INPUT]=$row_ds1["input"];
	$tree[$ebenenIndex][1][$portIndex][$NAME]=$row_ds1["name"];
	$portIndex++;
}



?>

<html>
<head>

</style>
<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
</head>
<body>
		<?php
foreach ($tree as $ebene){
	echo "<div>";
	echo $ebene[0];
	
	foreach ($ebene[1] as $port){
		//echo "trying to show port".$port[$NAME];
		$query="select max(id) as val from ".$port[$IGATE]."_LDM where sn like '%".$port[$NODE]."'";
		$ds1 = mysql_query ( $query, $verbindung ) or die ( mysql_error () );
		$id=0;
		while ( $row_ds1 = mysql_fetch_array ( $ds1 ) ) {
			$id=$row_ds1[val];
			//echo $id."=found an ID<br>";
		}
			
		$query="select in_switch".$port[$INPUT]." as port from ".$port[$IGATE]."_LDM where id = ".$id;
		$ds1 = mysql_query ( $query, $verbindung ) or die ( mysql_error () );
		while ( $row_ds1 = mysql_fetch_array ( $ds1 ) ) {
			$value=$row_ds1[port];
			
			echo "<p>";
			if ($value){
				echo '<img src="../imgs/red.png">';
			}else {
				echo '<img src="../imgs/green.png">';	
			}
			echo $port[$NAME];
			echo"</p>";
			
		}
	}
	
	echo "</div>";
}
?>

	

</body>