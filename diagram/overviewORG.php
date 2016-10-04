<?php 
	require_once ('../connections/verbindung.php');
	mysql_select_db ( $database_verbindung, $verbindung );
	include ('../locale/gettext_header.php');
	include ('../functions/dgr_func_jpgraph.php');
	include ('../functions/allg_functions.php');
	include ('../functions/b_breite.php');
	include ('exportHelper.php');

	
	$query = "select * from areas where subpark_id in ( select id from subparks where park_no = '$park_no' )";
	$total_nenn = 0;
	$inst_prod = 0;
	$day_prod = 0;
	$e_total=0;
	
	$ds1 = mysql_query ( $query, $verbindung ) or die ( mysql_error () );
	while ( $row_ds1 = mysql_fetch_array ( $ds1 ) ) {
		$query2 = "select w.* from wechselrichter as w, virt_wr_transtab as t where w.status=1 and t.area_id = ".$row_ds1[id]." and w.igate_id = t.igate_id and w.sn = t.sn";
		$ds2 = mysql_query ( $query2, $verbindung ) or die ( mysql_error () );
		while ( $row_ds2 = mysql_fetch_array ( $ds2 ) ) {
			
			$total_nenn += $row_ds2[nennleistung];
			
			$date = mktime(); 
			
			$query3 = "select e_total, p_ac_ph1 from ".$row_ds2[igate_id]."_messdaten_wr where sn='".$row_ds2[sn]."' and ts < $date and e_total>0 order by ts desc";
			$ds3 = mysql_query ( $query3, $verbindung ) or die ( mysql_error () );
			
			
			$tmp_e=0;
			while ( $row_ds3 = mysql_fetch_array ( $ds3 ) ) {
				//echo " ".$row_ds2[sn].": p".$row_ds3[p_ac_ph1]." e_neu: ".$row_ds3[e_total];
				
				$inst_prod += $row_ds3[p_ac_ph1];
				
				$tmp_e = $row_ds3[e_total];
				$e_total += $tmp_e;
				break;	
			}
			
			$query3 = "select max(e_total) as e from ".$row_ds2[igate_id]."_messdaten_wr where sn='".$row_ds2[sn]."' and ts < ($date-24*3600)";
			$ds3 = mysql_query ( $query3, $verbindung ) or die ( mysql_error () );
			while ( $row_ds3 = mysql_fetch_array ( $ds3 ) ) {
				//echo " ".$row_ds3[e]."<br>";
				$day_prod += ($tmp_e - $row_ds3[e]);
				
				break;	
			}
		
			
		}
		
	}
?>






<HTML>

<HEAD>

<TITLE>-- Solar park India --</TITLE>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/scroll.css" rel="stylesheet" type="text/css">
<link href="css/text.css" rel="stylesheet" type="text/css">
<style type="text/css">
html,body,form {
	margin: 0;
	padding: 0;
	height: 100%;
	width: 100%;
	font-family: arial, sans-serif;
}


.grau {width:200; background-color: #C0C0C0; text-align: center;}

.name-td {width:400;}

</style>

</head>


<body style="background:#F7FE2E; position:absolute;left:100px;top:100px">


<table class="ScadaOverview" border="0" cellpadding="0" cellspacing="0">



<center>



<thead><tr>


</tr>

</thead>







<tbody>


<tr>
<td class="name-td"> <h2>Actual produced Power <h2></td>
<td class="grau"> <h2><?php echo $inst_prod;?></h2></td>
<td><h2> kW <h2></td>
</tr>


<tr>
<td class="name-td"> <h2>Energy acc. for the day <h2></td>
<td class="grau"> <h2> <?php echo $day_prod;?></h2></td>
<td><h2> kWh<h2></td>
</tr>


<td class="name-td"> <h2>Total produced Energy<h2></td>
<td class="grau"> <h2><?php echo $e_total;?></h2></td>
<td><h2> kWh<h2></td>
</tr>


<td class="name-td"> <h2>Total saved Energy <h2></td>
<td class="grau"> <h2><?php echo $e_total*0.55;?></h2></td>
<td><h2>kg<h2></td>
</tr>

<td class="name-td"> <h2>Actual Irradiation<h2></td>
<td class="grau"> <h2></h2></td>
<td><h2> W/sq.m<h2></td>
</tr>

<td class="name-td"> <h2>Ambient Temperature </h2></td>
<td class="grau"> <h2></h2></td>
<td><h2>&deg;C<h2></td>
</tr>


<td class="name-td"> <h2>Module Surface Temperature</h2></td>
<td class="grau"> <h2></h2></td>
<td><h2>&deg;C<h2></td>
</tr>


<td class="name-td"> <h2>Actual Wind speed</h2></td>
<td class="grau"> <h2></h2></td>
<td><h2>m/h<h2></td>
</tr>


<td class="name-td"> <h2>Actual Wind Direction</h2></td>
<td class="grau"> <h2></h2></td>
<td><h2>&deg;deg<h2></td>

</tr>



</tbody>

</table>

</div>



</body>

</html>

