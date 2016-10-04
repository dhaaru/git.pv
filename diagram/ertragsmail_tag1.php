<?php

//Rudolf 12.12.2007
//Dieses Script ermittelt t�glich den Ertrag einer Anlage bzw. der einzelnen WR und versendet t�glich eine email an den Betreiber


include('../functions/de_datum.php');
include('../functions/b_breite.php');
include('mail_funcs.php'); //EMAIL FUNKTIONEN
include('../betreiber/betr_functions/betr_functions.php'); //Detail_db funktion
include('../functions/dgr_func_jpgraph.php'); //funcs abfrage akt stand des wrs

include('tabelle.php');

include('error_list.php');

//Einf�gen der Funktion zum Registrieren der Ausf�lle
include('input_wr_ausfall.php');

//Einf�gen der DB
include('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);

//$portal_url=getenv('HTTP_HOST');
$portal_url = substr($portal_url, 4);

$zeit_nun = mktime(); // Zeit jetzt
//echo $zeit_nun."<br>";
$retro_secs = 24 * 3600;

$kont_zeit = $zeit_nun - $retro_secs;
//echo $kont_zeit."<br>";

$monat_heute = date('m');
$jahr_heute = date('Y');
$tag_heute = date('d');

//T-Stamps zum abfragen des Z-Standes des WRs
//Wenn Startzustand
if (!isset($mon_j)) {
    $mon_j = $monat_heute;
}
if (!isset($tag_j)) {
    $tag_j = $tag_heute;
}
if (!isset($jahr_j)) {
    $jahr_j = $jahr_heute;
}


if ($mon_j < 10) {
    $mon_j1 = "0" . $mon_j;
} else {
    $mon_j1 = $mon_j;
}

$tag_yest = $tag_j - 1;
$tag_for_yest = $tag_j - 2;

//$datum=$tag_yest.".".$mon_j.".".$jahr_j;
$datum = date('d.m.Y.', $kont_zeit);
echo "Datum: " . $datum . "<BR>";

$time_of_date = mktime(18, 30, 0, $mon_j, $tag_yest, $jahr_j); //Timestamp zum ermitteln des Z�hlerstands

$time_of_date_yest = mktime(18, 30, 0, $mon_j, $tag_for_yest, $jahr_j); //Timestamp zum ermitteln des Z�hlerstands
//$email_adr='thamburaj@iplon.de; wieke@iplon.de; abhijit@iplon.de; murali.dharan@iplon.de; bharath@iplon.de; chalitha@iplon.de';
$email_adr = 'wieke@iplon.de';


$park_no_arr = array(20);
$park_bez_arr = array(20 => 'Charanka');
$park_recip_arr = array(20 => 'Kiran Energy');

foreach ($park_no_arr as $park_no) {



    //$email_adr='rudolf@iplon.de';

    $mail_text = "RECIPIENT: " . $park_recip_arr[$park_no] . "\r\n\r\n";

    $mail_text.="\r\n ************************************************************************";
    $mail_text.="\r\n     REPORT of " . $datum . " for SOLAR PLANT  " . $park_bez_arr[$park_no];
    $mail_text.="\r\n ************************************************************************";


    $e_total = 0;

    $query_ds0 = "select device, max(value) as value from _devicedatavalue where field='e_total' and device in (182, 181) group by device";
    echo $query_ds0 . "<br>";
    $ds0 = mysql_query($query_ds0, $verbindung) or die(mysql_error());

    while ($row_ds0 = mysql_fetch_array($ds0)) {
        $e_total+=$row_ds0[value];
    }

    $query_ds0 = "select device, max(value) as value from _devicedatavalue where field='e_total' and device in (182, 181) and ts < $time_of_date_yest group by device";
    echo $query_ds0 . "<br>";
    $ds0 = mysql_query($query_ds0, $verbindung) or die(mysql_error());

    while ($row_ds0 = mysql_fetch_array($ds0)) {
        $e_total-=$row_ds0[value];
    }

    $mail_text.="Yield = $e_total\r\n";

    $pac = 0;

    $query_ds0 = "select device, max(value) as value from _devicedatavalue where field='PAC' and device in (182, 181) and ts > $time_of_date_yest group by device";
    echo $query_ds0 . "<br>";
    $ds0 = mysql_query($query_ds0, $verbindung) or die(mysql_error());

    while ($row_ds0 = mysql_fetch_array($ds0)) {
        $pac+=$row_ds0[value];
    }

    $mail_text.="Peak Power = $pac\r\n";

    $radiations = array();
    $query_ds0 = "select device, field, value, ts / 3600 as ts from _devicedatavalue where (field = 'U4_900' and device = 5) or (field = 'U1_900' and device = 180) and ts > $time_of_date_yest";
    echo $query_ds0 . "<br>";
    $ds0 = mysql_query($query_ds0, $verbindung) or die(mysql_error());

    while ($row_ds0 = mysql_fetch_array($ds0)) {
        $radiations[$row_ds0[device]][$row_ds0[field]][$row_ds0[ts]][] = $row_ds0[value] * 13;
        $radiations[$row_ds0[device]][$row_ds0[field]][max] = max($radiations[$row_ds0[field]][max], $row_ds0[value] * 13);
    }

    foreach ($radiations[$row_ds0[device]] as $dkey => $device){
        $mail_text.="$dkey\r\n";
        foreach ($device as $fkey => $field){
            $mail_text.="$fkey\r\n";
            $mail_text.="peak = $field[max]\r\n";
            $field[sum]=0;
            
            foreach ($field as $tsvalues){
                if (sizeof($tsvalues)>0){
                    $field[sum] += array_sum($tsvalues)/sizeof($tsvalues);
                    
                    }
            }
            $mail_text.="sum = $field[sum]\r\n";
        }
    }


    //#########################
    //Der Mailvorgang

    $betreff = "INFO-MAIL - Plant " . $park_bez_arr[$park_no] . ", TOTAL PLANT ENERGY: " . $ertrag_anl . " kWh, Date: " . $datum;
    $betreff = encode_iso88591($betreff);

    //Der Mailvorgang

    $header1 = "From:Solaranlage <info@" . $portal_url . ">\r\n";

    $header1.="To: " . $email_adr . "\r\n";    //Betreiber-Adressen

    $header3 = "Subject: " . $betreff . "\r\n";
    $header3.="Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
    $header3.="Content-Transfer-Encoding: quoted-printable\r\n\r\n";


    $mail_text = strtr($mail_text, $uml_html);


    echo "V O L L T E X T: <br>" . $mail_text . "<BR>";

    $message = quoted_printable($mail_text);

    echo "M E S S A G E: <br>" . $message . "<BR>";

    mail($email_adr, $betreff, $message, $header1 . $header3);
}//foreach Sup

mysql_free_result($ds1);
?>
