<?php
require_once ('../connections/verbindung.php');
mysql_select_db($database_verbindung, $verbindung);
include ('../locale/gettext_header.php');
include ('../functions/dgr_func_jpgraph.php');
include ('../functions/allg_functions.php');
include ('../functions/b_breite.php');
include ('exportHelper.php');

$user_typ = get_user_attribut('usertyp');

$jahr_heute = date('Y');
$monat_heute = date('n');
$tag_heute = date('d');


if (!isset($park_no)) {
    if (!isset($_SESSION ['park_no_s'])) {
        $park_no = 0;
    } else {
        $park_no = $_SESSION ['park_no_s'];
    }
}
$_SESSION ['park_no_s'] = $park_no;




if (!isset($subpark_id)) {
    if (!isset($_SESSION ['subpark_s'])) {
        $subpark_id = 0;
    } else {
        $subpark_id = $_SESSION ['subpark_s'];
    }
}
$_SESSION ['subpark_s'] = $subpark_id;

if (!isset($area_id)) {
    if (!isset($_SESSION ['area_s'])) {
        $area_id = 0;
    } else {
        $area_id = $_SESSION ['area_s'];
    }
}
$_SESSION ['area_s'] = $area_id;

if (!isset($phase)) {
    if (!isset($_SESSION ['phase_s'])) {
        $phase = "tag";
    } else {
        $phase = $_SESSION ['phase_s'];
    }
}
$_SESSION ['phase_s'] = $phase;

if (!isset($jahr)) {
    if (!isset($_SESSION ['jahr_s'])) {
        $jahr = $jahr_heute;
    } else {
        $jahr = $_SESSION ['jahr_s'];
    }
}
$_SESSION ['jahr_s'] = $jahr;

if (!isset($mon)) {
    if (!isset($_SESSION ['mon_s'])) {
        $mon = $monat_heute;
    } else {
        $mon = $_SESSION ['mon_s'];
    }
}
$_SESSION ['mon_s'] = $mon;

if (!isset($tag)) {
    if (!isset($_SESSION ['tag_s'])) {
        $tag = $tag_heute;
    } else {
        $tag = $_SESSION ['tag_s'];
    }
}
$_SESSION ['tag_s'] = $tag;

$anz_tage = $_SESSION ['anz_tage_s'];

$einstrahlungen = array();
$temperaturen = array();



if ($type == "wetter") {
    $area_id = 0;
    $subpark_id = 0;
}

if ($area_id != 0) {
    $query = "select distinct sensorik_igate_id, sensorik_node1_id, einstrahlung_sensor, temperatur_sensor from areas where subpark_id = $subpark_id and area_id = $area_id order by area_id";
} elseif ($subpark_id != 0) {
    $query = "select distinct sensorik_igate_id, sensorik_node1_id, einstrahlung_sensor, temperatur_sensor from areas where subpark_id = $subpark_id order by area_id";
} elseif ($park_no != 0 && $park_no != 99) {
    $query = "select distinct a.sensorik_igate_id, a.sensorik_node1_id, a.einstrahlung_sensor, a.temperatur_sensor from areas as a, subparks as sp where a.subpark_id = sp.id and sp.park_no='$park_no' order by a.id";
} else {
    if ($user_typ != "SuperUser") {

        $query = "select distinct sensorik_igate_id, sensorik_node1_id, einstrahlung_sensor, temperatur_sensor from areas where id in (" . get_user_attribut('area_ids') . ") order by id";
    } else {
        $query = "select distinct sensorik_igate_id, sensorik_node1_id, einstrahlung_sensor, temperatur_sensor from areas order by id";
    }
}

$radNames = array();
$tmpNames = array();

$tmps = 0;
$eins = 0;

$ds1 = mysql_query($query, $verbindung) or die(mysql_error());
while ($row_ds1 = mysql_fetch_array($ds1)) {

    $sens_gate = $row_ds1 ["sensorik_igate_id"];
    $sens_node = $row_ds1 ["sensorik_node1_id"];
    if ($sens_gate != 0 and $sens_node != 0) {

        $ein_sens = $row_ds1 ["einstrahlung_sensor"];
        if ($ein_sens != 0 && !in_array($sens_gate . "_" . $sens_node . "_" . $ein_sens, $radNames)) {
            if ($type == "wetter" || sizeof($radNames) == 0) {
                $eins++;

                $radNames [] = $sens_gate . "_" . $sens_node . "_" . $ein_sens;
                $einstrahlungen [] = array($sens_gate, $sens_node, $ein_sens);
            }
        }
        $tmp_sens = $row_ds1 ["temperatur_sensor"];
        if ($tmp_sens != 0 && !in_array($sens_gate . "_" . $sens_node . "_" . $tmp_sens, $tmpNames)) {
            if ($type == "wetter" || sizeof($tmpNames) == 0) {

                $tmps++;

                $tmpNames [] = $sens_gate . "_" . $sens_node . "_" . $tmp_sens;
                $temperaturen [] = array($sens_gate, $sens_node, $tmp_sens);
            }
        }
    }

    if ($type != "wetter") {
        // break;
    }
}


$queries = array();

$tsMin = 0;
$tsMax = 0;

$pr_phase = $phase;

$str_xls = 'teil_bez' . ": body ( object total)\n" . $str_xls;

$str_xls .= "Date,Yield [kWh],Radiation [W/qm],Temperature [°C]\n";

if ($phase == "mon") {

    $tsMin = mktime(0, 0, 0, $mon, 0, $jahr);
    $tsMax = mktime(0, 0, 0, $mon + 1, 1, $jahr);
} elseif ($phase == "jahr") {

    $tsMin = mktime(0, 0, 0, 1, 0, $jahr);
    $tsMax = mktime(0, 0, 0, 1, 1, $jahr + 1);
} else {

    $tsMin = mktime(0, 0, 0, $mon, $tag, $jahr);
    $tsMax = mktime(7, 0, 0, $mon, $tag + 1, $jahr);
}

$orgPhase = $phase;
// if ($phase == "mon" && $type != "str") {
// 	$phase = "tag";
// }

$masze = array();
$bezeichnungen = array();
$variablen = array();

$source_table = "";
$words = array();
$nenn_word = ", wr.nennleistung";

$variablen = array("yield", "specYield", "current", "specCurrent", "voltage", "power", "specPower", "midVolt");
if ($phase == "tag") {
    $bezeichnungen = array("Inst. yield", "Specific Yield", "Current", "Specific Current", "Voltage", "Power", "Specific Power", "Link voltage");
} else {
    $bezeichnungen = array("Yield", "Specific Yield", "Current", "Specific Current", "Voltage", "Power", "Specific Power", "Link voltage");
}

$yield = 0;
$specYield = 1;
$current = 2;
$specCurrent = 3;
$voltage = 4;
$power = 5;
$specPower = 6;
$midVoltage = 7;


$zaehlerTyp = "";
$pr_anzeige = null;

if ($type == "wr") {
    if ($phase == "tag") {
        $masze = array("kW", "%", "A", "V", "kW", "V");

        $words [0] = ", COALESCE(i_ac_ph1, 0)+ COALESCE(i_ac_ph2, 0)+ COALESCE( i_ac_ph3,0), u_ac_ph1, u_ac_ph2, u_ac_ph3, coalesce(p_ac_ph1, 0)+ COALESCE( p_ac_ph2, 0)+ COALESCE( p_ac_ph3,0), uzwk";
        $words [1] = array("COALESCE(i_ac_ph1, 0)+ COALESCE(i_ac_ph2, 0)+ COALESCE( i_ac_ph3,0)", "V", "coalesce(p_ac_ph1, 0)+ COALESCE( p_ac_ph2, 0)+ COALESCE( p_ac_ph3,0)", "uzwk");
    } else {
        $masze = array("kWh", "kWh/kWp", "A", "V", "kW", "V");
        $words [0] = ", i_ac, u_ac, p_ac, uzwk";
        $words [1] = array("i_ac", "u_ac", "p_ac", "uzwk");
    }
    $words [2] = array(2, 4, 5, 7);
    $source_table = "virt_wr_transtab as t, wechselrichter";
} elseif ($type == "str") {
    if ($phase == "tag") {

        $masze = array("kW", "%", "A", "V", "kW");
    } else {

        $masze = array("kWh", "kWh/kWp", "A", "V", "kW");
    }

    $source_table = "virt_str_transtab as t, strings";
    $words [0] = ", i_dc, u_dc, p_dc";
    $words [1] = array("i_dc", "u_dc", "p_dc");
    $words [2] = array(2, 4, 5);
} elseif ($type == "wetter") {
    
} else {

    if ($styp == 1) {
        if ($pr_phase == "tag") {
            $pr_anzeige = get_pr_data_mon('tag', $park_no, $tag, $mon, $jahr, 'anzeige');
        } elseif ($pr_phase == "mon") {
            $pr_anzeige = get_pr_data_mon('jahr', $park_no, 0, $mon, $jahr, 'anzeige');
        } else {
            $pr_anzeige = get_pr_data_mon('jahr', $park_no, 0, $mon, $jahr, 'jahr_gesamt');
        }
    }
    $zaehlerTyp = " and wr.anl_virt_id = $styp ";
    if ($phase == "tag") {
        $masze = array("kW", "A", "V", "kW");
        $words [0] = ", COALESCE(i_ac_ph1, 0)+ COALESCE(i_ac_ph2, 0)+ COALESCE( i_ac_ph3,0), u_ac_ph1, u_ac_ph2, u_ac_ph3, p_ac_ph1";
        $words [1] = array("COALESCE(i_ac_ph1, 0)+ COALESCE(i_ac_ph2, 0)+ COALESCE( i_ac_ph3,0)", "V", "p_ac_ph1");
    } else {
        $masze = array("kWh", "A", "V", "kW");
        $words [0] = ", i_ac, u_ac, p_ac";
        $words [1] = array("i_ac", "u_ac", "p_ac");
    }
    $words [2] = array(2, 4, 5);

    $nenn_word = "";
    $source_table = "virt_ez_transtab as t, meters";
}


if ($park_no == 0 || $park_no == 99) {
    if ($user_typ == "SuperUser") {
        $queries [] = "select distinct t.sn, wr.bezeichnung, t.igate_id $nenn_word from $source_table as wr where wr.status=1 and wr.igate_id = t.igate_id and wr.sn = t.sn " . $zaehlerTyp;
    } else {
        $queries [] = "select distinct t.sn, wr.bezeichnung, t.igate_id $nenn_word from $source_table as wr where wr.status=1 and wr.igate_id = t.igate_id and wr.sn = t.sn and t.area_id in (" . get_user_attribut('area_ids') . ") " . $zaehlerTyp;
    }
} elseif ($subpark_id == 0) {
    $query = "select distinct a.id from areas as a where subpark_id in ( select distinct id as subpark_id from subparks where park_no = '$park_no') ";
    // echo "$query<br>";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    $areaCollection = "-1";
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $areaCollection .= ", " . $row_ds1 ['id'];
    }
    $queries [] = "select distinct t.sn, wr.bezeichnung, t.igate_id $nenn_word from $source_table as wr where wr.status=1 and t.area_id in ( " . $areaCollection . ")" . $zaehlerTyp . " and wr.sn = t.sn and wr.igate_id = t.igate_id";
    // echo $queries[0];
} elseif ($area_id == 0) {
    $query = "select distinct id from areas where subpark_id = $subpark_id";
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    $areaCollection = "-1";
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $areaCollection .= ", " . $row_ds1 ['id'];
    }
    $queries [] = "select distinct t.sn, wr.bezeichnung, t.igate_id $nenn_word from $source_table as wr where wr.status=1 and t.area_id in ( " . $areaCollection . ")" . $zaehlerTyp . " and wr.sn = t.sn and wr.igate_id = t.igate_id";
} else {

    $query = "select distinct id from areas where subpark_id = $subpark_id and area_id=" . $area_id;
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $queries [] = "select distinct t.sn, wr.bezeichnung, t.igate_id $nenn_word from $source_table as wr where wr.status=1 and t.area_id = " . $row_ds1 ['id'] . $zaehlerTyp . " and wr.sn = t.sn and wr.igate_id = t.igate_id";
        // $queries [] = "select t.sn, t.igate_id $nenn_word from $source_table
        // as wr where t.area_id = $area and wr.sn = t.sn and wr.igate_id =
        // t.igate_id";
    }
}



$tabellenArt = "_zwstand";
if ($phase == "tag") {
    $tabellenArt = "_messdaten";
}

$dataIndex = 0;
$typeIndex = 0;

$dataSets = array();
$setIndex = 0;

$totalProduction = 0;

$ydata = array();
$ydata2 = array();
$ydata3 = array();
$ydataIndex = 0;
$tsStepper = $tsMin + 3600 * 5 - 60;
$alarms = array();

$text = "";
$device = "";
$tsOld = 0;

if ($type != "wetter") {
    foreach ($queries as $query) {
        $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

        while ($row_ds1 = mysql_fetch_array($ds1)) {

            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') == false && $type == "wr") {

                $data_query = "select a.id, a.tstamp +19800 as tstamp, a.igate_id, a.seriennummer, b.fehler_txt from alarm as a, wr_spez_fehler as b where (b.wr_hersteller='PowerOne' or b.wr_hersteller='Sharp') and (b.fehler_nr = a.fehler_nr or b.fehler_nr = a.fehler_txt) and a.igate_id = " . $row_ds1 ['igate_id'] . " and a.seriennummer = '" . $row_ds1 ['sn'] . "' and a.tstamp >= " . $tsMin . " and a.tstamp < " . $tsMax . " order by a.igate_id, a.seriennummer, a.tstamp";


                $ids = "(-1";
                $ds2 = mysql_query($data_query, $verbindung) or die(mysql_error());
                $alarms[$setIndex] = array();
                while ($row_ds2 = mysql_fetch_array($ds2)) {
                    $ids.="," . $row_ds2[id];
                    if (( ($row_ds2["tstamp"] - $tsOld) > 3600 * 23) || $text != $row_ds2["fehler_txt"] || $device != $row_ds2["igate_id"] . "_" . $row_ds2["seriennummer"]) {
                        $tsOld = $row_ds2["tstamp"];
                        $igate_id = $row_ds2["igate_id"];
                        $_SESSION['igateid'] = $igate_id;
                        $device = $row_ds2["igate_id"] . "_" . $row_ds2["seriennummer"];
                        $text = $row_ds2["fehler_txt"];

                        $alarms[$setIndex][] = array($row_ds2["tstamp"], $device . ": " . $text);
                    }
                }

                $ids.=")";

                $data_query = "select tstamp, igate_id, seriennummer, fehler_txt from alarm where id not in $ids and igate_id = " . $row_ds1 ['igate_id'] . " and seriennummer = '" . $row_ds1 ['sn'] . "' and tstamp >= " . $tsMin . " and tstamp < " . $tsMax . " order by igate_id, seriennummer, tstamp";



                $ds2 = mysql_query($data_query, $verbindung) or die(mysql_error());
                while ($row_ds2 = mysql_fetch_array($ds2)) {
                    if (( ($row_ds2["tstamp"] - $tsOld) > 3600 * 23) || $text != $row_ds2["fehler_txt"] || $device != $row_ds2["igate_id"] . "_" . $row_ds2["seriennummer"]) {
                        $tsOld = $row_ds2["tstamp"];
                        $igate_id = $row_ds2["igate_id"];
                        $_SESSION['igateid'] = $igate_id;
                        $device = $row_ds2["igate_id"] . "_" . $row_ds2["seriennummer"];
                        $text = $row_ds2["fehler_txt"];
                        $alarms[$setIndex][] = array($row_ds2["tstamp"], $device . ": " . $text);
                    }
                }
            } else {
                $alarms[$setIndex] = array();
            }

            // echo $data_query."<br>";


            $data_query = "select ts, e_total" . $words [0] . " from " . $row_ds1 ['igate_id'] . $tabellenArt . "_" . $type . " where e_total>0 and  sn = '" . $row_ds1 ['sn'] . "' and ts >= " . $tsMin . " and ts < " . $tsMax . " order by ts";
            // echo $data_query."<br>";

            $ds2 = mysql_query($data_query, $verbindung) or die(mysql_error());

            $oldEtotal = 0;
            $oldTs = 0;
            $nennleistung = 1;
            if ($type != "s0") {
                $nennleistung = $row_ds1 ['nennleistung'];
            }
            $first = true;

            //$dataSets [$setIndex] [0] = $row_ds1 ['igate_id'] . "_" . $row_ds1 ['sn'];


            $tmp = $row_ds1[bezeichnung] . "." . $row_ds1 ['igate_id'];
            if ($park_no == 35 && $row_ds1[bezeichnung] == "ICD_02") {
                $tmp = "EM ICD";
            } else if ($park_no == 35 && $row_ds1[bezeichnung] == "WR_010") {
                $tmp = "Kaco 390TL";
            }

            $dataSets [$setIndex] [0] = $tmp;

            $dataSets [$setIndex] [1] [0] [] = "[" . ($tsMin * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [2] [0] [] = "[" . ($tsMin * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [3] [0] [] = "[" . ($tsMin * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [4] [0] [] = "[" . ($tsMin * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [5] [0] [] = "[" . ($tsMin * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [6] [0] [] = "[" . ($tsMin * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [7] [0] [] = "[" . ($tsMin * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [8] [0] [] = "[" . ($tsMin * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [9] [0] [] = "[" . ($tsMin * 1000 - 1) . ", null])\n";

            $ydataIndex = 0;
            $tsStepper = $tsMin + 3600 * 5 - 1860;
            if ($orgPhase != "tag") {
                $tsStepper += 24 * 60 * 60 - 3600 * 5 + 1860;
            }

            while ($row_ds2 = mysql_fetch_array($ds2)) {
                if ($phase == "tag" && $oldTs == 0) {
                    $oldTs = $row_ds2 ['ts'];
                    $oldEtotal = $row_ds2 ['e_total'];

                    continue;
                }
                $newTs = $row_ds2 ['ts'];
                $newEtotal = $row_ds2 ["e_total"];
                while ($newTs > $tsStepper) {
                    if (sizeof($ydata [$ydataIndex]) == 0) {
                        $ydata [$ydataIndex] [] = 0;
                    }
                    $ydataIndex++;
                    if ($orgPhase != "tag") {
                        $tsStepper += 24 * 3600;
                    } else {
                        $tsStepper += 3600;
                    }
                }

                if ($phase == "tag") {
                    $ydata [$ydataIndex] [] = ($newEtotal - $oldEtotal);
                } else {
                    $ydata [$ydataIndex] [] = ($newEtotal);
                }

                if ($phase == "tag") {
                    if (false && $newEtotal == $oldEtotal) {
                        
                    } else {
                        $totalProduction += ($newEtotal - $oldEtotal);
                        $gap = 420;
                        if (($type == "wr" && (($mon < 7 ) || $mon == 7 && $tag < 20)) || $park_no == 20 || $park_no == 35) {
                            $gap = 1200;
                        }

                        if (($newTs - $oldTs) > $gap) {
                            for ($index3 = 1; $index3 < 10; $index3++) {
                                $dataSets [$setIndex] [$index3] [0] [] = "[" . (($oldTs + 19801) * 1000) . ", 0]);\n";
                            }
                            for ($index3 = 1; $index3 < 10; $index3++) {
                                $dataSets [$setIndex] [$index3] [0] [] = "[" . (($newTs + 19799) * 1000) . ", 0]);\n";
                            }
                        }

                        $dataSets [$setIndex] [1] [0] [] = "[" . (($newTs + 19800) * 1000) . ", " . (($newEtotal - $oldEtotal) / ($newTs - $oldTs) * 3600.0) . "]);\n";
                        $dataSets [$setIndex] [1] [1] = true;
                        if ($nennleistung != 1 && $nennleistung != 0) {
                            $dataSets [$setIndex] [2] [0] [] = "[" . (($newTs + 19800) * 1000) . ", " . (100 * ($newEtotal - $oldEtotal) / ($newTs - $oldTs) * 3600.0 / $nennleistung) . "]);\n";
                            $dataSets [$setIndex] [2] [1] = true;
                        } else {
                            $dataSets [$setIndex] [2] [0] [] = "[" . (($newTs + 19800) * 1000) . ", 0]);\n";
                        }
                        $oldEtotal = $newEtotal;
                        $oldTs = $newTs;
                    }
                } else {
                    $totalProduction += ($newEtotal);
                    $dataSets [$setIndex] [1] [0] [] = "[" . (($newTs + 19800) * 1000) . ", " . 3600.0 * 24 * $newEtotal / ($newTs - $oldTs) . "]);\n";
                    $dataSets [$setIndex] [1] [1] = true;
                    if ($nennleistung != 1 && $nennleistung != 0) {
                        $dataSets [$setIndex] [2] [0] [] = "[" . (($newTs + 19800) * 1000) . ", " . (3600.0 * 24 * $newEtotal / ($newTs - $oldTs) / $nennleistung) . "]);\n";
                        $dataSets [$setIndex] [2] [1] = true;
                    } else {
                        $dataSets [$setIndex] [2] [0] [] = "[ 0]);\n";
                    }
                    $oldTs = $newTs;
                }

                $wertIndex = 0;
                foreach ($words [1] as $dataQuery) {

                    if ($dataQuery == "V") {
                        $u_ac = 0;
                        $u_ac_vals = 0;
                        if (null == $row_ds2 ["u_ac_ph1"] || $row_ds2 ["u_ac_ph1"] == 0) {
                            
                        } else {
                            $u_ac += $row_ds2 ["u_ac_ph1"];
                            $u_ac_vals++;
                        }
                        if (null == $row_ds2 ["u_ac_ph2"] || $row_ds2 ["u_ac_ph2"] == 0) {
                            
                        } else {
                            $u_ac += $row_ds2 ["u_ac_ph2"];
                            $u_ac_vals++;
                        }
                        if (null == $row_ds2 ["u_ac_ph3"] || $row_ds2 ["u_ac_ph3"] == 0) {
                            
                        } else {
                            $u_ac += $row_ds2 ["u_ac_ph3"];
                            $u_ac_vals++;
                        }
                        if ($u_ac_vals == 0) {
                            
                        } else {
                            if ($phase == "tag") {

                                $dataSets [$setIndex] [$words [2] [$wertIndex] + 1] [0] [] = "[" . (($newTs + 19800) * 1000) . ", " . ($u_ac / $u_ac_vals) . "]);\n";
                            } else {
                                $dataSets [$setIndex] [$words [2] [$wertIndex] + 1] [0] [] = "[" . ($newTs * 1000) . ", " . ($u_ac / $u_ac_vals) . "]);\n";
                            }
                            $dataSets [$setIndex] [$words [2] [$wertIndex] + 1] [1] = true;
                        }
                    } else {
                        $wert = $row_ds2 [$dataQuery];
                        if ($phase == "tag") {
                            $dataSets [$setIndex] [$words [2] [$wertIndex] + 1] [0] [] = "[" . (($newTs + 19800) * 1000) . ", " . $wert . "]);\n";
                        } else {

                            $dataSets [$setIndex] [$words [2] [$wertIndex] + 1] [0] [] = "[" . ($newTs * 1000) . ", " . $wert . "]);\n";
                        }
                        if ($wert != null && $wert != 0) {
                            $dataSets [$setIndex] [$words [2] [$wertIndex] + 1] [1] = true;
                        }
                        if (($dataQuery == "i_dc" || $dataQuery == "i_ac" || $dataQuery == "p_dc" || $dataQuery == "p_ac") && $nennleistung != 1 && $nennleistung != 0) {
                            if ($phase == "tag") {

                                $dataSets [$setIndex] [$words [2] [$wertIndex] + 2] [0] [] = "[" . (($newTs + 19800) * 1000) . ", " . $wert / $nennleistung . "]);\n";
                            } else {
                                $dataSets [$setIndex] [$words [2] [$wertIndex] + 2] [0] [] = "[" . ($newTs * 1000) . ", " . $wert / $nennleistung . "]);\n";
                            }
                            if ($wert != null && $wert != 0 && $nennleistung != 1) {
                                $dataSets [$setIndex] [$words [2] [$wertIndex] + 2] [1] = true;
                            }
                        }
                    }
                    $wertIndex++;
                }
            }


            $dataSets [$setIndex] [1] [0] [] = "[" . ($tsMax * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [2] [0] [] = "[" . ($tsMax * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [3] [0] [] = "[" . ($tsMax * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [4] [0] [] = "[" . ($tsMax * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [5] [0] [] = "[" . ($tsMax * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [6] [0] [] = "[" . ($tsMax * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [7] [0] [] = "[" . ($tsMax * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [8] [0] [] = "[" . ($tsMax * 1000 - 1) . ", null])\n";
            $dataSets [$setIndex] [9] [0] [] = "[" . ($tsMax * 1000 - 1) . ", null])\n";



            $setIndex++;
        }
        $typeIndex++;
    }
}


$tmpValues = array();
$temperaturIndex = 0;



foreach ($temperaturen as $currentValue) {
    if ($tsIndex == -1 && $temperaturIndex > 0) {
        $temperaturIndex--;

        for ($xIndex = $temperaturIndex; $xIndex < sizeof($tmpNames) - 1; $xIndex++) {
            $tmpNames[$xIndex] = $tmpNames[$xIndex + 1];
        }
        // unset($tmpNames[sizeof($tmpNames)-1]);
    }
    if ($phase == 'tag') {
        $query = "select ts, input_" . $currentValue [2] . " from " . $currentValue [0] . "_sensorik where input_" . $currentValue [2] . "<> 0 and node_id = " . $currentValue [1] . " and ts>= " . $tsMin . " and ts < " . $tsMax . " order by ts";
        $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

        $nextTs = 0;
        $tsIndex = - 1;

        $ydataIndex = 0;
        $tsStepper = $tsMin + 3600 * 5 - 1860;
        if ($orgPhase != "tag") {
            $tsStepper += 24 * 60 * 60 - 3600 * 5 + 1860;
        }

        while ($row_ds1 = mysql_fetch_array($ds1)) {
            $ts = $row_ds1 ["ts"];
            $input = $row_ds1 ["input_" . $currentValue [2]];

            while ($ts > $tsStepper) {
                if (sizeof($ydata3 [$ydataIndex]) == 0) {
                    $ydata3 [$ydataIndex] [] = 0;
                }
                $ydataIndex++;
                if ($orgPhase != "tag") {
                    $tsStepper += 24 * 60 * 60;
                } else {
                    $tsStepper += 3600;
                }
            }

            $ydata3 [$ydataIndex] [] = ($input);

            if ($ts > $nextTs) {
                $tsIndex++;
                if ($tsIndex < sizeof($tmpValues [$temperaturIndex])) {
                    $nextTs = $tmpValues [$temperaturIndex] [$tsIndex] [0] + 60 * 15;
                    $tmpValues [$temperaturIndex] [$tsIndex] [1] [] = $input;
                } else {
                    $tmpValues [$temperaturIndex] [$tsIndex] = array(($ts + 19800), array($input));
                    $nextTs = $ts + 60 * 15;
                }
            } else {
                $tmpValues [$temperaturIndex] [$tsIndex] [1] [] = $input;
            }
        }
    } else {
        $in = (int) $currentValue [2];
        // if ($currentValue[0]=="2061"){
        //     $in = $in+4;
        //     }
        $query = "select tstamp, input_" . $in . " from " . $currentValue [0] . "_estag where input_" . $in . "<> 0 and node_id = " . $currentValue [1] . " and tstamp>= " . $tsMin . " and tstamp < " . $tsMax . " order by tstamp";
        //echo $query;

        $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

        $nextTs = 0;
        $tsIndex = - 1;

        $ydataIndex = 0;
        $tsStepper = $tsMin + 3600 * 5 - 1860;
        if ($orgPhase != "tag") {
            $tsStepper += 24 * 60 * 60 - 3600 * 5 + 1860;
        }


        while ($row_ds1 = mysql_fetch_array($ds1)) {
            $ts = $row_ds1 ["tstamp"];
            $input = $row_ds1 ["input_" . $in];
            while ($ts > $tsStepper) {
                if (sizeof($ydata3 [$ydataIndex]) == 0) {
                    $ydata3 [$ydataIndex] [] = 0;
                }
                $ydataIndex++;
                if ($orgPhase != "tag") {
                    $tsStepper += 24 * 60 * 60;
                } else {
                    $tsStepper += 3600;
                }
            }

            $ydata3 [$ydataIndex] [] = ($input);


            if ($ts > $nextTs) {
                $tsIndex++;
                if ($tsIndex < sizeof($tmpValues [$temperaturIndex])) {
                    $nextTs = $tmpValues [$temperaturIndex] [$tsIndex] [0] + 3600 * 24 - 600;
                    $tmpValues [$temperaturIndex] [$tsIndex] [1] [] = $input;
                } else {
                    $tmpValues [$temperaturIndex] [$tsIndex] = array(($ts + 19800), array($input));
                    $nextTs = $ts + 3600 * 24 - 600;
                }
            } else {
                $tmpValues [$temperaturIndex] [$tsIndex] [1] [] = $input;
            }
        }
    }
    if ($type == "wetter") {
        $temperaturIndex++;
    }
}

for ($i1 = 1; $i1 < sizeof($tmpValues); $i1++) {
    for ($i2 = 0; $i2 < sizeof($tmpValues [$i1]); $i2++) {
        $tmpValues [$i1] [$i2] [1] = array_sum($tmpValues [$i1] [$i2] [1]) / sizeof($tmpValues [$i1] [$i2] [1]);
    }
}

// foreach ( $tmpValues as $eineTemperatur ) {
// foreach ($eineTemperatur as $value){
// $value [1] = array_sum ( $value [1] ) / sizeof ( $value [1] );
// }
// }

$einstrahlungsIndex = 0;
$einValues = array();


foreach ($einstrahlungen as $currentValue) {
    if ($tsIndex == -1 && $einstrahlungsIndex > 0) {
        $einstrahlungsIndex--;
        for ($xIndex = $einstrahlungsIndex; $xIndex < sizeof($radNames) - 1; $xIndex++) {
            $radNames[$xIndex] = $radNames[$xIndex + 1];
        }
        //unset($radNames[sizeof($radNames)-1]);
    }
    if ($phase == 'tag') {
        $query = "select ts, input_" . $currentValue [2] . " from " . $currentValue [0] . "_sensorik where input_" . $currentValue [2] . "<> 0 and node_id = " . $currentValue [1] . " and ts>= " . $tsMin . " and ts < " . $tsMax . " order by ts";
        $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

        $nextTs = 0;
        $tsIndex = - 1;

        $ydataIndex = 0;
        $tsStepper = $tsMin + 3600 * 5 - 1860;
        if ($orgPhase != "tag") {
            $tsStepper += 24 * 60 * 60 - 3600 * 5 + 1860;
        }

        while ($row_ds1 = mysql_fetch_array($ds1)) {
            $ts = $row_ds1 ["ts"];
            $input = $row_ds1 ["input_" . $currentValue [2]];
            if ($input < 0) {
                continue;
            }

            while ($ts > $tsStepper) {
                if (sizeof($ydata2 [$ydataIndex]) == 0) {
                    $ydata2 [$ydataIndex] [] = 0;
                }
                $ydataIndex++;
                if ($orgPhase != "tag") {
                    $tsStepper += 24 * 60 * 60;
                } else {
                    $tsStepper += 3600;
                }
            }

            //$input = 1.305*$input+0.1576*tanh(0.1576-1,691*$input)+0.06646*cos(2.155+955*$input+$input*input*input*input*input*input*input);
            $ydata2 [$ydataIndex] [] = ($input);

            if ($ts > $nextTs) {
                $tsIndex++;
                if ($tsIndex < sizeof($einValues [$einstrahlungsIndex])) {
                    $nextTs = $einValues [$einstrahlungsIndex] [$tsIndex] [0] + 60 * 15;
                    $einValues [$einstrahlungsIndex] [$tsIndex] [1] [] = $input;
                } else {
                    $einValues [$einstrahlungsIndex] [$tsIndex] = array(($ts + 19800), array($input));
                    $nextTs = $ts + 60 * 15;
                }
            } else {
                $einValues [$einstrahlungsIndex] [$tsIndex] [1] [] = $input;
            }
        }
    } else {
        $in = (int) $currentValue [2];
        //  if ($currentValue[0]=="2061"){
        //      $in = $in+4;
        //      }
        $query = "select tstamp, input_" . $in . " from " . $currentValue [0] . "_estag where input_" . $in . "<> 0 and node_id = " . $currentValue [1] . " and tstamp>= " . $tsMin . " and tstamp < " . $tsMax . " order by tstamp";
        //  echo $query;

        $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

        $nextTs = 0;
        $tsIndex = - 1;

        $ydataIndex = 0;
        $tsStepper = $tsMin + 3600 * 5 - 1860;
        if ($orgPhase != "tag") {
            $tsStepper += 24 * 60 * 60 - 3600 * 5 + 1860;
        }

        while ($row_ds1 = mysql_fetch_array($ds1)) {
            $ts = $row_ds1 ["tstamp"];
            $input = $row_ds1 ["input_" . $in];
            //echo $input."<br>";
            //$input = 1.305*$input+0.1576*tanh(0.1576-1,691*$input)+0.06646*cos(2.155+955*$input+$input*input*input*input*input*input*input);

            while ($ts > $tsStepper) {
                if (sizeof($ydata2 [$ydataIndex]) == 0) {
                    $ydata2 [$ydataIndex] [] = 0;
                }
                $ydataIndex++;
                if ($orgPhase != "tag") {
                    $tsStepper += 24 * 60 * 60;
                } else {
                    $tsStepper += 3600;
                }
            }
            $ydata2 [$ydataIndex] [] = ($input);

            if ($ts > $nextTs) {
                $tsIndex++;
                if ($tsIndex < sizeof($einValues [$einstrahlungsIndex])) {
                    $nextTs = $einValues [$einstrahlungsIndex] [$tsIndex] [0] + 3600 * 24 - 600;
                    $einValues [$einstrahlungsIndex] [$tsIndex] [1] [] = $input;
                } else {
                    $einValues [$einstrahlungsIndex] [$tsIndex] = array(($ts + 19800), array($input));
                    $nextTs = $ts + 3600 * 24 - 600;
                }
            } else {
                $einValues [$einstrahlungsIndex] [$tsIndex] [1] [] = $input;
            }
        }
    }
    if ($type == "wetter") {
        $einstrahlungsIndex++;
    }
}


for ($i1 = 1; $i1 < sizeof($einValues); $i1++) {
    for ($i2 = 0; $i2 < sizeof($einValues [$i1]); $i2++) {
        $einValues [$i1] [$i2] [1] = array_sum($einValues [$i1] [$i2] [1]) / sizeof($einValues [$i1] [$i2] [1]);
    }
}

$einAvg = 0;

for ($i = 0; $i < sizeof($einValues [0]); $i++) {
    $val = array_sum($einValues [0] [$i] [1]) / sizeof($einValues [0] [$i] [1]);
    $einValues [0] [$i] [1] = $val;
    $einAvg += $val;
}
if (sizeof($einValues [0]) > 0) {
    $einAvg = number_format($einAvg / sizeof($einValues [0]), 2, ".", "");
} else {
    $einAvg = null;
}

$tmpAvg = 0;
for ($i = 0; $i < sizeof($tmpValues [0]); $i++) {
    $val = array_sum($tmpValues [0] [$i] [1]) / sizeof($tmpValues [0] [$i] [1]);
    $tmpValues [0] [$i] [1] = $val;
    $tmpAvg += $val;
}
if (sizeof($tmpValues [0]) > 0) {
    $tmpAvg = number_format(($tmpAvg / sizeof($tmpValues [0])), 2, ".", "") . " °C";
} else {
    $tmpAvg = null;
}

if ($phase == "tag") {
    if ($park_no == "20") {
        $query = "select input_1 as ein, input_2 as tmp from 2061_estag where node_id=107 and datum = '$jahr-$mon-$tag'";
    } else {
        $query = "select input_3 as ein, input_1 as tmp from 2501_estag where node_id=101 and datum = '$jahr-$mon-$tag'";
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

    while ($row_ds1 = mysql_fetch_array($ds1)) {
        $tmpAvg = $row_ds1[tmp];
        $einAvg = $row_ds1[ein];
    }
}


$grenze = 0;

if ($orgPhase == "tag") {
    $grenze = 20;
} elseif ($orgPhase == "mon") {
    $grenze = $anz_tage;
} else {

    if (($jahr % 4 == 0 && !($jahr % 100 == 0)) || ($jahr % 400 == 0)) {
        $grenze = 366;
    } else {
        $grenze = 365;
    }
}

for ($i = 0; $i < $grenze; $i++) {
    if ($i > sizeof($ydata)) {
        $ydata [$i] = 0;
        continue;
    }
    if (sizeof($ydata [$i]) == 0) {
        $ydata [$i] = 0;
        continue;
    }
    $sum = array_sum($ydata [$i]);

    $ydata [$i] = number_format($sum, 2, ".", "");
}

for ($i = 0; $i < $grenze; $i++) {
    if ($i > sizeof($ydata2)) {
        $ydata2 [$i] = 0;
        continue;
    }

    if (sizeof($ydata2 [$i]) == 0) {
        $ydata2 [$i] = 0;
        continue;
    }

    $ydata2 [$i] = number_format(array_sum($ydata2 [$i]) / sizeof($ydata2 [$i]), 2, ".", "");
}

for ($i = 0; $i < $grenze; $i++) {
    if ($i > sizeof($ydata3)) {
        $ydata3 [$i] = 0;
        continue;
    }

    if (sizeof($ydata3 [$i]) == 0) {
        $ydata3 [$i] = 0;
        continue;
    }

    $ydata3 [$i] = number_format(array_sum($ydata3 [$i]) / sizeof($ydata3 [$i]), 2, ".", "");
}




if ($orgPhase == "jahr") {
    $newYdata = array();
    // 	$newYdata2 = array ();
    // 	$newYdata3 = array ();
    $monatsListe = tage_monat($jahr);

    $monatsTagesIndex = 1;
    $monatsIndex = 1;
    $tagesIndex = 0;

    while ($tagesIndex < $grenze) {

        if ($monatsTagesIndex > $monatsListe [$monatsIndex]) {

            $newYdata3 [$monatsIndex - 1] = $newYdata3 [$monatsIndex - 1] / $monatsListe [$monatsIndex];

            $monatsIndex++;
            $monatsTagesIndex = 1;
        }

        $newYdata [$monatsIndex - 1] += $ydata [$tagesIndex];
        $newYdata2 [$monatsIndex - 1] += $ydata2 [$tagesIndex];
        $newYdata3 [$monatsIndex - 1] += $ydata3 [$tagesIndex];

        $tagesIndex++;
        $monatsTagesIndex++;
    }

    $ydata = $newYdata;

    $ydata2 = $newYdata2;

    $ydata3 = $newYdata3;
}

if ($orgPhase == "tag") {
    $str_xls .= formatStunde($ydata, $ydata2, $ydata3, $tag, $mon, $jahr);
} elseif ($orgPhase == "mon") {
    $str_xls .= formatTag($ydata, $ydata2, $ydata3, $mon, $jahr, $anz_tage);
} else {
    $str_xls .= formatMonat($ydata, $ydata2, $ydata3, $jahr);
}
?>
<html>
    <head>

        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
        <link rel="stylesheet" href="style.css" type="text/css" />
        <!--[if lte IE 9]><script type="text/javascript" src="../functions/flot/excanvas.js"></script><![endif]-->
        <script type="text/javascript" src="../functions/flot/jquery-1.7.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.min.js"></script>

        <script type="text/javascript"
        src="../functions/flot/jquery.flot.crosshair.min.js"></script>

        <script type="text/javascript"
        src="../functions/flot/jquery.flot.selection.min.js"></script>
        <script type="text/javascript"
        src="../functions/flot/jquery.flot.stack.min.js"></script>
    </head>
    <body>
        <div style="font-family: arial, sans-serif"><?php
$optionWord = "";
$optionsAvailable = false;
$kurven = array();


if (sizeof($dataSets) > 0) {
    $dataTypes = array();
    $optionWord .= '<select id="select" onChange="plotWithOptions()">';
    foreach ($dataSets as $value) {
        if (in_array($value [0], $kurven)) {
            
        } else {
            $kurven [] = $value [0];
        }

        $anyDatasets = true;
        for ($i = 1; $i < sizeof($value); $i++) {
            if (null != $value [$i] [1] && false != $value [$i] [1]) {
                $optionsAvailable = true;

                if ($phase == "tag" && ($bezeichnungen [$i - 1] == "Inst. yield" || ($bezeichnungen [$i - 1] == "Specific Yield"))) {
                    continue;
                }

                if (in_array($bezeichnungen [$i - 1], $dataTypes)) {
                    
                } else {
                    $dataTypes [] = ($bezeichnungen [$i - 1]);
                    if (($type == "str") && ($bezeichnungen [$i - 1] == "Current")) {
                        $optionWord .= '<option selected="selected">' . $bezeichnungen [$i - 1] . '</option>';
                    } else if (($phase == "tag") && ($type == "wr") && ($bezeichnungen [$i - 1] == "Power")) {
                        $optionWord .= '<option selected="selected">' . $bezeichnungen [$i - 1] . '</option>';
                    } else if (($phase == "tag") && ($type == "s0") && ($bezeichnungen [$i - 1] == "Power")) {
                        $optionWord .= '<option selected="selected">' . $bezeichnungen [$i - 1] . '</option>';
                    } else if (($phase != "tag") && ($type == "wr") && ($bezeichnungen [$i - 1] == "Yield")) {
                        $optionWord .= '<option selected="selected">' . $bezeichnungen [$i - 1] . '</option>';
                    } else if (($phase != "tag") && ($type == "s0") && ($bezeichnungen [$i - 1] == "Yield")) {
                        $optionWord .= '<option selected="selected">' . $bezeichnungen [$i - 1] . '</option>';
                    } else {
                        $optionWord .= "<option>" . $bezeichnungen [$i - 1] . "</option> ";
                    }
                }
            }
        }
    }

    $optionWord .= '</select>';
}

if ($optionsAvailable || ($type == "wetter")) {
    echo $optionWord;
    echo $title;
} else {
    echo $title . "<br>";
    echo "<p>";
    echo "There is no data available for the specified area/timespan";
    echo "</p>";
    return;
}
?></div>
        <div style="height: 94%; width: 99%; text-align: center;">
            <div
                style="float: left; height: 95%; width: 84%; padding-top: 7px; overflow: auto;">
                <div id="placeholder" style="font-size: 85%; width: 95%; height: 95%;"></div>
            </div>
            <div style="float: left; height: 96%; width: 15%; overflow: auto">

                <div
                    style="padding: 3px; background-color: BlanchedAlmond; font-size: 85%; height: 40%; overflow: auto;"
                    id="legend"><br>
                </div>
                <div
                    style="padding: 3px; background-color: Gainsboro; font-size: 85%; height: 37%; overflow: auto;"
                    id="infoText"><?php
            if ($type != "wetter" && $park_no != "20" && $einAvg != null) {
                $irrTit = "Acc. Irradiation";
                $irrUnit = " Wh/m²";
                if ($phase == "mon") {
                    $irrTit = "Avg. " . $irrTit;
                }
                echo '<p style = "font-size: 90%">' . $irrTit . '<br>' . $einAvg . $irrUnit . '</p>';
            }
            if ($type != "wetter" && $park_no != "20" && $tmpAvg != null) {
                echo '<p style = "color: red;font-size: 90%">Average Temperature<br>' . $tmpAvg . '</p>';
            }
            if ($park_no != "20" && $totalProduction > 0) {

                if ($park_no == "25") {

                    if ($phase == "tag") {
                        $stamp = mktime(10, 30, 0, $mon, $tag, $jahr, 0);
                        $endstamp = mktime(6, 30, 0, $mon, $tag + 1, $jahr, 0);

                        if ($type == "wr") {

                            $query = "select max(value) as max, device from _devicedatavalue where field = 'e_day' and device in ( 109, 110, 111, 112) and ts > $stamp and ts < $endstamp group by device";
                            $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

                            $totalProduction = 0;
                            while ($row_ds1 = mysql_fetch_array($ds1)) {
                                $totalProduction += $row_ds1[max];
                            }
                        } else if ($type == "s0") {
                            $query = "select max(value) as max from _devicedatavalue where field = 'e_day' and device = 113 and ts > $stamp and ts < $endstamp";

                            $ds1 = mysql_query($query, $verbindung) or die(mysql_error());

                            $totalProduction = 0;
                            while ($row_ds1 = mysql_fetch_array($ds1)) {
                                $totalProduction += $row_ds1[max];
                            }
                        }
                        //$totalProduction =  
                    }
                }

                $totalProduction = number_format($totalProduction, 2, ".", "");
                $phaseword = "Day";
                if ($phase == "mon") {
                    $phaseword = "Month";
                } elseif ($phase == "jahr") {
                    $phaseword = "Year";
                }

                echo '<p style = "font-size: 90%">' . $phaseword . '\'s Production<br>' . $totalProduction . ' kWh</p>';
            }
// && $park_no != "20"
            if ($type != "wetter" && $phase == "tag") {
                include ('patPr.php');

                echo '<p style = "font-size: 90%">Performance Ratio<br>' . number_format($pr_value, 2, ".", "") . ' %</p>';
            }
?></div>

                <div style="height: 20%; background-color: beige; overflow: auto;"
                     id="buttons">
                        <?php
                        if ($_SESSION['user'] != "charanka" && $phase == "tag") {
                            echo '<a href="pat.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&portal=' . $park_no . '">';
                            echo '     <img title="Export diagram as .csv file" src="../imgs/xls.png">';
                            echo '</a>';
                        }
                        ?>

<?php
if ($tmpAvg != null && $einAvg != null && $phase == "tag" && $park_no == 25) {
    echo '<a href="patPyr.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&name=WeatherStation&device=108&offset=19800&useTs=0&showQueries=0">';
    echo '    <img title="Export Pyranometer as .csv file" src="../imgs/sun.png">';
    echo '</a>';
}
?>
                         <?php
                         //$_SESSION['user'] != "charanka" && 
                         if ($phase == "tag") {
                             echo '<a href="patPr.php?jahr=' . $jahr . '&mon=' . $mon . '&tag=' . $tag . '&print=true&park_no=' . $park_no . '">';
                             echo '    <img title="Export Performance Ratio as .csv file" src="../imgs/xls_pr.png">';
                             echo '</a>';
                         }
                         ?>
                    <br>
                    <input title="Toggle between a stacked diagram showing the total yields and an easily comparable line diagram" id="stackbtn" onclick="toggleStack()" type="image" src="../imgs/balken.png">

                    <input title="Reset the zoom"
                           style="flow: left;" id="resetZoom" onclick="resetZoom()" type="image"
                           src="../imgs/lupe_grey.png" disabled> <input
                           title="Show/Hide temperature and irradiation data" style="flow: left;"
                           id="toggleTemp" onclick="toggleTemp()" type="image"
                           src="../imgs/sensor_grey.png" disabled></div>
            </div>

            <script type="text/javascript">

			
                var zeigeKurve = {};
                var alarms = [];
                var alarmTexts = {};
	
<?php {
    $sourceIndex = 0;
    foreach ($alarms as $wrAlarms) {
        echo "alarms.push([]);\n";
        foreach ($wrAlarms as $alarm) {
            $date = date("G", $alarm[0]);

            if ($date > 7 && $date < 22) {

                echo "alarms[$sourceIndex].push([$alarm[0]*1000, 0]);\n";
                echo "alarmTexts['$alarm[0]']='$alarm[1]';\n";
            }
        }
        $sourceIndex++;
    }
}

$tsMin = $tsMin + 11 * 3600;
$tsMax = $tsMax - 5 * 3600;
echo "var tsMin = " . ($tsMin) . ";";
echo "var tsMax = " . $tsMax . ";";

$ticks = 'mode:"time", min: ' . ($tsMin * 1000) . ', max: ' . ($tsMax * 1000);
if ($phase != "tag") {
    $ticks .= ', minTickSize: [1, "day"]';
}

foreach ($kurven as $kurve) {
    echo "zeigeKurve['" . $kurve . "']= true;";
}
?>

    var min, max, data;

    var showTemp = <?php
if ($park_no == "20" && $type != "wetter") {
    echo "false";
} else {
    echo "true";
}
?>;
    var plot;
    var placeholder = $("#placeholder");
    var stack = null;
    var legends;
    var masz;
	
    if (stack == "null"){
        stack = null;
    }

    
    var options = {};
	
<?php
$indexes = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

foreach ($dataSets as $set) {
    for ($i = 1; $i < sizeof($set); $i++) {
        if (sizeof($set [$i]) == 2) {
            echo "var " . $variablen [$i - 1] . $indexes [$i - 1] . "=[];\n";

            foreach ($set [$i] [0] as $valuepair) {
                echo $variablen [$i - 1] . $indexes [$i - 1] . ".push($valuepair";
            }

            $indexes [$i - 1]++;
        } else {
            
        }
    }
}

echo "var temp = [];\n";
echo "var tmpNames = [];\n";

$tmpOffset = 0;

if (sizeof($tmpValues [0]) > 0) {
    if ($type == "wetter") {
        
    } else {
        echo 'document.getElementById("toggleTemp").disabled=false;';

        if ($park_no == "20") {
            echo 'document.getElementById("toggleTemp").src="../imgs/sensor.png";';
        } else {
            echo 'document.getElementById("toggleTemp").src="../imgs/sensor_off.png";';
        }
    }
    $i = 0;


    foreach ($tmpValues as $temp) {
        echo "temp.push([]);";
        if ($type == "wetter") {
            echo "tmpNames.push('" . $tmpNames [$i] . "_');\n";
            echo "zeigeKurve['" . $tmpNames [$i] . "_Temperature']=true;\n";
        } else {
            echo "zeigeKurve['Temperature']=true;\n";
            echo "tmpNames.push('');\n";
        }
        $lastTs = $tsMin;
        foreach ($temp as $val) {
            if ($phase == "tag" && ($val[0] - $lastTs) > 1201) {
                echo "temp[" . $i . "].push([" . (($tmpOffset + $lastTs + 1) * 1000) . ", 0]);\n";

                for ($tmpTs = 1200; ($tmpTs + $lastTs) < $val[0]; $tmpTs+=900) {
                    echo "temp[" . $i . "].push([" . (($tmpOffset + $lastTs + $tmpTs) * 1000) . ", 0]);\n";
                }

                echo "temp[" . $i . "].push([" . (($tmpOffset + $val [0] - 1) * 1000) . ", 0]);\n";
            }

            echo "temp[" . $i . "].push([" . (($val [0] + $tmpOffset) * 1000) . ", " . $val [1] . "]);\n";
            $lastTs = $val[0];
        }
        $i++;
    }
}

echo "var ein = [];\n";
echo "var einNames = [];\n";

if (sizeof($einValues [0]) > 0) {
    if ($type == "wetter") {
        
    } else {
        if ($park_no == "20") {
            echo 'document.getElementById("toggleTemp").src="../imgs/sensor.png";';
        } else {
            echo 'document.getElementById("toggleTemp").src="../imgs/sensor_off.png";';
        }
        echo 'document.getElementById("toggleTemp").disabled=false;';
    }

    $i = 0;
    foreach ($einValues as $ein) {
        echo "ein.push([]);";
        if ($type == "wetter") {
            echo "einNames.push('" . $radNames [$i] . "_');\n";
            echo "zeigeKurve['" . $radNames [$i] . "_Irradiation']=true;\n";
        } else {
            echo "zeigeKurve['Irradiation']=true;\n";
            echo "einNames.push('');\n";
        }

        $lastTs = $tsMin;
        foreach ($ein as $val) {
            if ($phase == "tag" && ($val[0] - $lastTs) > 1200) {
                echo "ein[" . $i . "].push([" . (($tmpOffset + $lastTs + 1) * 1000) . ", 0]);\n";

                for ($tmpTs = 1200; ($tmpTs + $lastTs) < $val[0]; $tmpTs+=900) {
                    echo "ein[" . $i . "].push([" . (($tmpOffset + $lastTs + $tmpTs) * 1000) . ", 0]);\n";
                }

                echo "ein[" . $i . "].push([" . (($tmpOffset + $val [0] - 1) * 1000) . ", 0]);\n";
            }

            echo "ein[" . $i . "].push([" . ($tmpOffset + $val [0] * 1000) . ", " . $val [1] . "]);\n";
            $lastTs = $val[0];
        }
        $i++;
    }
}
?>

    window.onresize = plotWithOptions;

    function toggleStack(){
        if (stack==null){
            stack = 1;
        }else {
		 	
            stack = null;	
        }
        plotWithOptions();
    }

    function toggleTemp(){
        if (showTemp){
            showTemp = false;
            document.getElementById("toggleTemp").src="../imgs/sensor.png"
        }else{
            showTemp = true;
            document.getElementById("toggleTemp").src="../imgs/sensor_off.png";
        }
        plotWithOptions();
    }
	
    function plotWithOptions() {
<?php if ($type != "wetter") { ?>
            var s = document.getElementById('select');
            var selectedItem = s.options[s.selectedIndex].text;
            if (<?php
    if ($park_no == "20") {
        echo "true";
    } else {
        echo "false";
    }
    ?> || ( selectedItem=="Voltage"  || selectedItem=="Link voltage"))
                {
                    document.getElementById("stackbtn")
                    document.getElementById("stackbtn").disabled=true;
                    document.getElementById("stackbtn").src="../imgs/balken_grey.png";
                    stack = null;
                }else{
                    document.getElementById("stackbtn").disabled=false;

                    if (stack==null){
                        document.getElementById("stackbtn").src="../imgs/balken.png";
                    }else {
                        document.getElementById("stackbtn").src="../imgs/linien.png";
                        				 	
                    }
                        				
                }
                                                            
    <?php
    if ($_SESSION['user'] == "charanka") {
        echo 'document.getElementById("stackbtn").style.visibility="hidden";';
        echo 'document.getElementById("stackbtn").style.display="none";';
    }
    ?>
                        	
                   
<?php } else { ?>
            document.getElementById("stackbtn").disabled=true;
            document.getElementById("stackbtn").src="../imgs/balken_grey.png";
                        			
<?php } ?>
		
        options = {
            xaxis: {<?php echo $ticks; ?> },
            crosshair: {mode: "x"},
            selection: {mode: "x"},
            yaxes: [
								
<?php
$posi = "right";
if ($type == "wetter") {
    $posi = "left";
}

echo '{tickFormatter: function(v, axis){return v.toFixed(axis.tickDecimals) +masz}},';
echo '{tickFormatter: function(v, axis){return v.toFixed(axis.tickDecimals) +"W/m²"}, position: "' . $posi . '"},';
echo '{tickFormatter: function(v, axis){return v.toFixed(axis.tickDecimals) +"°C"}, position: "right"},';
echo '{min: 0, max: 10, tickFormatter: function(v, axis){return ""}}';
?>
            ],
            legend: {
                container: $("#legend"),
                labelFormatter: function (label, series) {
                    var cutLabel = label.substr(0, label.indexOf("="));
							

					
                    var zeige = ""
                    if (zeigeKurve[cutLabel]){
                        zeige = 'checked="checked"';

                    }
				                
                    var cb = '<input type="checkbox" name="' + cutLabel + '" '+zeige+' id="id' + cutLabel + '" onClick="toggleKurve(\''+cutLabel+'\');"> ' + label+'</input>';
                    return cb;
                }
            },
            points: {show: true},
            lines: { show: true, fill: stack},
            grid: { hoverable: true, autoHighlight: true } 
        };

        fillData();
        if (document.getElementById("resetZoom").disabled==false){
            plot = $.plot(placeholder, data, $.extend(true, {}, options, {xaxis: {min: min, max: max} }));
        }else{
            plot = $.plot(placeholder, data, options);
        }
        legendify();

        //placeholder.append('<div style="position:absolute;left:' + (o.left + 4) + 'px;top:' + o.top + 'px;color:#666;font-size:smaller">Actual measurements</div>');
        // draw a little arrow on top of the last label to demonstrate
        // canvas drawing


    }

    function toggleKurve(label){
        zeigeKurve[label] = !zeigeKurve[label];
        plotWithOptions();
    }
	
    var colors = [];
<?php
$colors = array("3", "4", "5", "6", "8", "11", "13", "1", "14", "16", "17", "18", "23", "24", "25");

foreach ($colors as $color) {
    echo "colors.push($color);\n";
}
?>
    function fillData(){

        data = [];
<?php if ($type != "wetter") { ?>
                        		

            var s = document.getElementById('select');
            var selectedItem = s.options[s.selectedIndex].text;
            if (selectedItem=="Yield" || selectedItem == "Inst. yield"){
                        			
    <?php
    if ($phase == "tag") {
        echo 'masz="kW";';
        $masz = "kW";
    } else {
        $masz = "kWh";
        echo 'masz="kWh";';
    }
    for ($i = 0; $i < $indexes [$yield]; $i++) {

        echo 'var showLine = false;';
        echo "var tmp = stack;";
        echo "if(zeigeKurve['" . $dataSets [$i] [0] . "']) { showLine = true; }else {tmp = null;}";
        echo "data.push({data: " . $variablen [$yield] . $i . ", stack: tmp, color: " . $colors[($i % (sizeof($colors)))] . ",points:{show: showLine}, lines: {show: (showLine)}, label: '" . $dataSets [$i] [0] . "= 0" . $masz . "'});\n";
    }
    ?>
                }else if(selectedItem=="Specific Yield"){
    <?php
    if ($phase == "tag") {
        echo 'masz="%";';
        $masz = "%";
    } else {
        $masz = "kWh/kWp";
        echo 'masz="kWh/kWp";';
    }
    echo "if (stack){toggleStack();\n}";
    for ($i = 0; $i < $indexes [$specYield]; $i++) {
        echo 'var showLine = false;';
        echo "var tmp = stack;";
        echo "if(zeigeKurve['" . $dataSets [$i] [0] . "']) { showLine = true; }else {tmp = null;}";
        echo "data.push({data: " . $variablen [$specYield] . $i . ", stack: tmp, color: " . $colors[($i % (sizeof($colors)))] . ",points:{show: showLine}, lines: {show: (showLine)}, label: '" . $dataSets [$i] [0] . "= 0" . $masz . "'});\n";
    }
    ?>
                }else if(selectedItem=="Current"){
    <?php
    echo 'masz="A";';
    $masz = "A";
    for ($i = 0; $i < $indexes [$current]; $i++) {
        echo 'var showLine = false;';
        echo "var tmp = stack;";
        echo "if(zeigeKurve['" . $dataSets [$i] [0] . "']) { showLine = true; }else {tmp = null;}";
        echo "data.push({data: " . $variablen [$current] . $i . ", stack: tmp, color: " . $colors[($i % (sizeof($colors)))] . ",points:{show: showLine}, lines: {show: (showLine)}, label: '" . $dataSets [$i] [0] . "= 0" . $masz . "'});\n";
    }
    ?>
                }else if(selectedItem=="Specific Current"){
    <?php
    echo 'masz="A/kWp";';
    $masz = "A/kWp";
    echo "if (stack){toggleStack();\n}";
    for ($i = 0; $i < $indexes [$specCurrent]; $i++) {
        echo 'var showLine = false;';
        echo "var tmp = stack;";
        echo "if(zeigeKurve['" . $dataSets [$i] [0] . "']) { showLine = true; }else {tmp = null;}";
        echo "data.push({data: " . $variablen [$specCurrent] . $i . ", stack: tmp, color: " . $colors[($i % (sizeof($colors)))] . ",points:{show: showLine}, lines: {show: (showLine)}, label: '" . $dataSets [$i] [0] . "= 0" . $masz . "'});\n";
    }
    ?>
                }else if(selectedItem=="Voltage"){
                        					
    <?php
    echo 'masz= "V";';
    $masz = "V";
    for ($i = 0; $i < $indexes [$voltage]; $i++) {
        echo 'var showLine = false;';
        echo "var tmp = stack;";
        echo "if(zeigeKurve['" . $dataSets [$i] [0] . "']) { showLine = true; }else {tmp = null;}";
        echo "data.push({data: " . $variablen [$voltage] . $i . ", stack: tmp, color: " . $colors[($i % (sizeof($colors)))] . ",points:{show: showLine}, lines: {show: (showLine)}, label: '" . $dataSets [$i] [0] . "= 0" . $masz . "'});\n";
    }
    ?>
                }else if(selectedItem=="Power"){
    <?php
    echo 'masz="kW";';
    $masz = "kW";
    for ($i = 0; $i < $indexes [$power]; $i++) {
        echo 'var showLine = false;';
        echo "var tmp = stack;";
        echo "if(zeigeKurve['" . $dataSets [$i] [0] . "']) { showLine = true; }else {tmp = null;}";
        echo "data.push({data: " . $variablen [$power] . $i . ", stack: tmp, color: " . $colors[($i % (sizeof($colors)))] . ",points:{show: showLine}, lines: {show: (showLine)}, label: '" . $dataSets [$i] [0] . "= 0" . $masz . "'});\n";
    }
    ?>
                }else if(selectedItem=="Specific Power"){
    <?php
    echo 'masz="kW/kWp";';
    $masz = "kW/kWp";
    echo "if (stack){toggleStack();\n}";
    for ($i = 0; $i < $indexes [$specPower]; $i++) {
        echo 'var showLine = false;';
        echo "var tmp = stack;";
        echo "if(zeigeKurve['" . $dataSets [$i] [0] . "']) { showLine = true; }else {tmp = null;}";
        echo "data.push({data: " . $variablen [$specPower] . $i . ", stack: tmp, color: " . $colors[($i % (sizeof($colors)))] . ",points:{show: showLine}, lines: {show: (showLine)}, label: '" . $dataSets [$i] [0] . "= 0" . $masz . "'});\n";
    }
    ?>
                }else if(selectedItem=="Link voltage"){
    <?php
    echo 'masz="V";';
    $masz = "V";
    for ($i = 0; $i < $indexes [$midVoltage]; $i++) {
        echo 'var showLine = false;';
        echo "var tmp = stack;";
        echo "if(zeigeKurve['" . $dataSets [$i] [0] . "']) { showLine = true; }else {tmp = null;}";
        echo "data.push({data: " . $variablen [$midVoltage] . $i . ", stack: tmp, color: " . $colors[($i % (sizeof($colors)))] . ", points:{show: showLine}, lines: {show: (showLine)}, label: '" . $dataSets [$i] [0] . "= 0" . $masz . "'});\n";
    }
    ?>
                }

    <?php
}
?>

        for (var i = 0; i< ein.length; i++){
            var showLine = showTemp;
            if (!zeigeKurve[einNames[i]+'Irradiation']){
                showLine = false; }
            if (i == 0){
                data.push({data: ein[i], label: einNames[i]+'Irradiation= 0W/m²', yaxis: 2, color: '#FFD510' ,  stack: null, points: {show: showLine}, lines: {align: 'center', show: (showLine), steps: false, fill: false}});
            }else {
                data.push({data: ein[i], label: einNames[i]+'Irradiation= 0W/m²', yaxis: 2, color: '#DE8822' ,  stack: null, points: {show: showLine}, lines: {align: 'center', show: (showLine), steps: false, fill: false}});
            }
			
        }

        for (var i = 0; i< temp.length; i++){
            var showLine = showTemp;
            if (!zeigeKurve[tmpNames[i]+'Temperature']){
                showLine = false; }
            data.push({data: temp[i], label: tmpNames[i]+'Temperature= 0°C', yaxis: 3, color: 12, stack: null, points: {show: showLine}, lines: {align: 'center', show: (showLine), steps: false, fill: false}});
        }
		
        for(var i = 0; i< alarms.length; i++){
            data.push({data: alarms[i], yaxis: 4, stack: null, color: colors[i], lines: {show: false}, points: {show: true, size: 8}, label: ''});
        }
		
		
    }

    function showTooltip(x, y, contents) {
        if (contents.indexOf(" ( ") == 0){
            contents = contents.substr(3, 10);
            contents = alarmTexts[contents];
            $('<div id="tooltip">' + contents + '</div>').css( {
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                border: '1px solid #fdd',
                padding: '2px',
                'background-color': '#fee',
                opacity: 0.80
            }).appendTo("body").fadeIn(200);
			
        }else {

            $('<div id="tooltip">' + contents + '</div>').css( {
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                border: '1px solid #fdd',
                padding: '2px',
                'background-color': '#fee',
                opacity: 0.80
            }).appendTo("body").fadeIn(200);
        }
		
		
    }

    var previousPoint = null;
    $("#placeholder").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x);
        $("#y").text(pos.y);

        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;
                    
                $("#tooltip").remove();
                var x = item.datapoint[0]/1000;
                var  y = item.datapoint[1].toFixed(2);
                var tmpName = item.series.label.substring(0, item.series.label.indexOf("="));

                var tmpMasz = "";
                if (tmpName.indexOf("Temperature")!= -1){
                    tmpMasz = " °C";
                }else if (tmpName.indexOf("Irradiation")!=-1){
                    tmpMasz = " W/m²";
                }else {
                    tmpMasz = masz;
                }

                showTooltip(item.pageX, item.pageY,
                tmpName +" ( "+x + " ) = " + y + tmpMasz);
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;            
        }
    });

    function resetZoom(){
        document.getElementById("resetZoom").disabled=true;
        document.getElementById("resetZoom").src="../imgs/lupe_grey.png";
        plotWithOptions();
    }

    $("#placeholder").bind("plotselected", function(event, ranges)
    {
        document.getElementById("resetZoom").src="../imgs/lupe.png";
        document.getElementById("resetZoom").disabled=false;
        min = ranges.xaxis.from;
        max = ranges.xaxis.to;
<?php
if ($phase == "tag") {
    echo "max = Math.max(max, min+3600000*2);";
} else {
    echo "max = Math.max(max, min+3600000*48*2);";
}
?>
        plotWithOptions();	
    }
);

	

    function legendify() {
        legends = $("#legend .legendLabel");
        legends.each(function() {
            $(this).css('width', $(this).width());
        });


        var updateLegendTimeout = null;
        var latestPosition = null;
        function updateLegend() {
            updateLegendTimeout = null;
            
            var pos = latestPosition;
            
            var axes = plot.getAxes();
            if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
                pos.y < axes.yaxis.min || pos.y > axes.yaxis.max){
                return;
            }

            
            
            var i, j, dataset = plot.getData();
            for (i = 0; i < dataset.length; ++i) {
                var series = dataset[i];

                // find the nearest points, x-wise
                for (j = 0; j < series.data.length; ++j){
                    if (series.data[j][0] > pos.x){
                        break;
                    }
                }
                
                // now interpolate
                var y, p1 = series.data[j - 1], p2 = series.data[j];
                if (p1 == null){
                    if (p2==null){
                        return;
                    }
                    y = p2[1];
                }
                else {
                    y = p1[1];
                }

                var tmpName = series.label;
                var cutLabel = tmpName.substr(0, tmpName.indexOf("="));
				
                var tmpMasz = "";
                if (tmpName.indexOf("Temperature")!= -1){
                    tmpMasz = "°C";
                }else if (tmpName.indexOf("Irradiation")!=-1){
                    tmpMasz = "W/m²";
                }else {
                    tmpMasz = masz;
                }
				
                var zeige = "";
                if (zeigeKurve[cutLabel]){
                    zeige = 'checked="checked"';
		
                }

                label = document.getElementById('id'+cutLabel);
                if (label==null){
                    return;
                }
                var labelText = series.label.replace(/=.*/, "= " + y.toFixed(2) + tmpMasz );
                var cb = '<input type="checkbox" name="'+cutLabel+'" ' + zeige + ' id="id'+cutLabel+'">'+labelText+'</input>';
                var tmp = label.nextSibling;
                tmp.data = labelText;

					

            }
        }
        placeholder.bind("plothover",  function (event, pos, item) {
            latestPosition = pos;
            if (!updateLegendTimeout){
                updateLegendTimeout = setTimeout(updateLegend, 50);
            }
        });
    }

    $(function () {
        $("#frame").resize();
        plotWithOptions();			
				
    });


	
<?php
if ($_SESSION['user'] == "charanka") {
    echo 'document.getElementById("stackbtn").style.visibility="hidden";';
    echo 'document.getElementById("stackbtn").style.display="none";';
}
?>
	
	
            </script>

    </body>
</html>



<?php
if ($type == "s0") {
    if ($styp == 2) {
        $_SESSION ['xls_diag_balken_enel'] = $str_xls;
        $_SESSION ['xls_diag_balken'] = $str_xls;
    } elseif ($styp == 1) {
        $_SESSION ['xls_diag_balken_gse'] = $str_xls;
        $_SESSION ['xls_pr_gse'] = $phase . ";" . $park_no . ";" . $tag . ";" . $mon . ";" . $jahr . ";" . $anz_tage;
        $_SESSION ['xls_diag_balken'] = $str_xls;
    }
} elseif ($type != "wetter") {
    $_SESSION ['xls_diag_balken'] = $str_xls;
}
?>




