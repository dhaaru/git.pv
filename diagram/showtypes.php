<?php


require_once ('../connections/verbindung.php');
mysql_select_db ( $database_verbindung, $verbindung );
include ('../locale/gettext_header.php');
include ('../functions/dgr_func_jpgraph.php');
include ('../functions/allg_functions.php');
include ('../functions/b_breite.php');
include ('exportHelper.php');


$query = "select distinct field from _devicedatavalue where device=$device";
$ds1 = mysql_query ( $query, $verbindung ) or die ( mysql_error () );
while ( $row_ds1 = mysql_fetch_array ( $ds1 ) ) {
    echo $row_ds1[field]."<br>";
    
}