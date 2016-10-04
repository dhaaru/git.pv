<?php
header('Content-type: text/csv');
/*if ($phase == "tag") {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '-' . $mon . '-' . $tag . '.xls"');
} else if ($phase == "mon") {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '-' . $mon . '.xls"');
}else if($phase == "graph1"){
	header('Content-Disposition: attachment; filename="Graph1' .'.xls"');
}else if($phase == "graph2"){
	header('Content-Disposition: attachment; filename="Graph2' .'.xls"');
}else if($phase == "graph3"){
	header('Content-Disposition: attachment; filename="Graph3' .'.xls"');
}else if($phase == "weather"){
	header('Content-Disposition: attachment; filename="WeatherStation'.'.xls"');
}else if($phase == "smu"){
	header('Content-Disposition: attachment; filename="SMU'.'.xls"');
}else if($phase == "system"){
	header('Content-Disposition: attachment; filename="SystemView'.'.xls"');
}else if($phase =="energy"){
	header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
}else {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '.xls"');
}*/
if ($phase == "tag") {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '-' . $mon . '-' . $tag . '.xls"');
} else if ($phase == "mon") {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '-' . $mon . '.xls"');
}else if($phase == "inv1"){
	header('Content-Disposition: attachment; filename="Inverter1' .'.xls"');
}else if($phase == "inv2"){
	header('Content-Disposition: attachment; filename="Inverter2' .'.xls"');
}else if($phase == "inv3"){
	header('Content-Disposition: attachment; filename="Inverter3' .'.xls"');
}else if($phase == "weather"){
	header('Content-Disposition: attachment; filename="WeatherStation'.'.xls"');
}else if($phase == "smu"){
	header('Content-Disposition: attachment; filename="SMU'.'.xls"');
}else if($phase == "system"){
	header('Content-Disposition: attachment; filename="SystemView'.'.xls"');
}else if($phase == "SMA1"){
	header('Content-Disposition: attachment; filename="SMA Inverter'.'.xls"');
}else if($phase == "Refg1"){
	header('Content-Disposition: attachment; filename="Refusol_Inverter_Graph1'.'.xls"');
}else if($phase == "Refg2"){
	header('Content-Disposition: attachment; filename="Refusol_Inverter_Graph2'.'.xls"');
}else if($phase == "Refg3"){
	header('Content-Disposition: attachment; filename="Refusol_Inverter_Graph3'.'.xls"');
}
/*else if($phase == "smu20D1"){
	header('Content-Disposition: attachment; filename="SMU1_20Degrees'.'.xls"');
}else if($phase == "smu20D2"){
	header('Content-Disposition: attachment; filename="SMU2_20Degrees'.'.xls"');
}else if($phase == "smu20D3"){
	header('Content-Disposition: attachment; filename="SMU3_20Degrees'.'.xls"');
}else if($phase == "smu20D4"){
	header('Content-Disposition: attachment; filename="SMU4_20Degrees'.'.xls"');
}else if($phase == "smu10D1"){
	header('Content-Disposition: attachment; filename="SMU1_10Degrees'.'.xls"');
}else if($phase == "smu10D2"){
	header('Content-Disposition: attachment; filename="SMU2_10Degrees'.'.xls"');
}else if($phase == "smu10D3"){
	header('Content-Disposition: attachment; filename="SMU3_10Degrees'.'.xls"');
}else if($phase == "smu10D4"){
	header('Content-Disposition: attachment; filename="SMU4_10Degrees'.'.xls"');
}*/
else if($phase == "smu1inv1"){
	header('Content-Disposition: attachment; filename="INV1/080161339/REFU20/SPR435/11X5'.'.xls"');
}else if($phase == "smu1inv2"){
	header('Content-Disposition: attachment; filename="INV2/2007307583/SMA11/SPR435/7X4'.'.xls"');
}else if($phase == "smu1inv3"){
	header('Content-Disposition: attachment; filename="INV3/2007307578/SMA11/TRINA300/14X3'.'.xls"');
}else if($phase == "smu1inv4"){
	header('Content-Disposition: attachment; filename="INV4/2007307494/SMA11/TRINA300/14X3'.'.xls"');
}else if($phase == "smu2inv1"){
	header('Content-Disposition: attachment; filename="INV5/2007310156/SMA11/RENE300/14X3'.'.xls"');
}else if($phase == "smu2inv2"){
	header('Content-Disposition: attachment; filename="INV6/2007310056/SMA11/RENE300/14X3'.'.xls"');
}else if($phase == "smu2inv3"){
	header('Content-Disposition: attachment; filename="INV7/80161337/REFU20/RENE300/21X4'.'.xls"');
}else if($phase == "smu2inv4"){
	header('Content-Disposition: attachment; filename="INV8/2007307640/SMA11/SPR435/7X4'.'.xls"');
}else if($phase == "smu3inv1"){
	header('Content-Disposition: attachment; filename="INV9/080161338/REFU20/TRAINA300/21X4'.'.xls"');
}else if($phase == "smu3inv2"){
	header('Content-Disposition: attachment; filename="INV10/2007310159/SMA11/SPR327/9X4'.'.xls"');
}else if($phase == "smu3inv3"){
	header('Content-Disposition: attachment; filename="INV11/2007310055/SMA11/SPR327/9X4'.'.xls"');
}else if($phase == "smu3inv4"){
	header('Content-Disposition: attachment; filename="INV12/2007310115/SMA11/SPR327/9X4'.'.xls"');
}else if($phase == "smu4inv1"){
	header('Content-Disposition: attachment; filename="INV13/080161340/REFU20/RENE300/21X4'.'.xls"');
}else if($phase == "smu4inv2"){
	header('Content-Disposition: attachment; filename="INV14/2007307718/SMA11/SPR327/9X4'.'.xls"');
}else if($phase == "smu4inv3"){
	header('Content-Disposition: attachment; filename="INV15/2007310120/SMA11/SPR327/9X4'.'.xls"');
}else if($phase == "smu4inv4"){
	header('Content-Disposition: attachment; filename="INV16/2007310116/SMA11/TRAINA300/14X3'.'.xls"');
}
else if($phase == "refusolG120"){
	header('Content-Disposition: attachment; filename="Refusol_20Degrees'.'.xls"');
}else if($phase == "refusolG220"){
	header('Content-Disposition: attachment; filename="Refusol_20Degrees'.'.xls"');
}else if($phase == "refusolG320"){
	header('Content-Disposition: attachment; filename="Refusol_20Degrees'.'.xls"');
}else if($phase == "refusolG110"){
	header('Content-Disposition: attachment; filename="Refusol_10Degrees'.'.xls"');
}else if($phase == "refusolG210"){
	header('Content-Disposition: attachment; filename="Refusol_10Degrees'.'.xls"');
}else if($phase == "refusolG310"){
	header('Content-Disposition: attachment; filename="Refusol_10Degrees'.'.xls"');
}else if($phase == "refusolG1both"){
	header('Content-Disposition: attachment; filename="Refusol_20&10Degrees'.'.xls"');
}else if($phase == "refusolG2both"){
	header('Content-Disposition: attachment; filename="Refusol_20&10Degrees'.'.xls"');
}else if($phase == "refusolG3both"){
	header('Content-Disposition: attachment; filename="Refusol_20&10Degrees'.'.xls"');
}
else if($phase == "SMA20G1"){
	header('Content-Disposition: attachment; filename="SMA_Inverter_20Degrees'.'.xls"');
}else if($phase == "SMA20G2"){
	header('Content-Disposition: attachment; filename="SMA_Inverter_20Degrees'.'.xls"');
}else if($phase == "SMA20G3"){
	header('Content-Disposition: attachment; filename="SMA_Inverter_20Degrees'.'.xls"');
}else if($phase == "SMA10G1"){
	header('Content-Disposition: attachment; filename="SMA_Inverter_10Degrees'.'.xls"');
}else if($phase == "SMA10G2"){
	header('Content-Disposition: attachment; filename="SMA_Inverter_10Degrees'.'.xls"');
}else if($phase == "SMA10G3"){
	header('Content-Disposition: attachment; filename="SMA_Inverter_10Degrees'.'.xls"');
}else if($phase == "SMAbothG1"){
	header('Content-Disposition: attachment; filename="SMA_Inverter_20&10Degrees'.'.xls"');
}else if($phase == "SMAbothG2"){
	header('Content-Disposition: attachment; filename="SMA_Inverter_20&10Degrees'.'.xls"');
}else if($phase == "SMAbothG3"){
	header('Content-Disposition: attachment; filename="SMA_Inverter_20&10Degrees'.'.xls"');
}
else if($phase == "weather20"){
	header('Content-Disposition: attachment; filename="Weather_Station_20Degrees'.'.xls"');
}else if($phase == "weather10"){
	header('Content-Disposition: attachment; filename="Weather_Station_10Degrees'.'.xls"');
}
/* Inverter Start */
else if($phase == "INV1G1"){
		header('Content-Disposition: attachment; filename="INV1_Graph1'.'.xls"');
}else if($phase == "INV1G2"){
		header('Content-Disposition: attachment; filename="INV1_Graph2'.'.xls"');
}else if($phase == "INV1G3"){
		header('Content-Disposition: attachment; filename="INV1_Graph3'.'.xls"');
}else if($phase == "INV2G1"){
		header('Content-Disposition: attachment; filename="INV2_Graph1'.'.xls"');
}else if($phase == "INV2G2"){
		header('Content-Disposition: attachment; filename="INV2_Graph2'.'.xls"');
}else if($phase == "INV2G3"){
		header('Content-Disposition: attachment; filename="INV2_Graph3'.'.xls"');
}else if($phase == "INV3G1"){
		header('Content-Disposition: attachment; filename="INV3_Graph1'.'.xls"');
}else if($phase == "INV3G2"){
		header('Content-Disposition: attachment; filename="INV3_Graph2'.'.xls"');
}else if($phase == "INV3G3"){
		header('Content-Disposition: attachment; filename="INV3_Graph3'.'.xls"');
}else if($phase == "INV4G1"){
		header('Content-Disposition: attachment; filename="INV4_Graph1'.'.xls"');
}else if($phase == "INV4G2"){
		header('Content-Disposition: attachment; filename="INV4_Graph2'.'.xls"');
}else if($phase == "INV4G3"){
		header('Content-Disposition: attachment; filename="INV4_Graph3'.'.xls"');
}else if($phase == "INV5G1"){
		header('Content-Disposition: attachment; filename="INV5,6_Graph1'.'.xls"');
}else if($phase == "INV5G2"){
		header('Content-Disposition: attachment; filename="INV5,6_Graph2'.'.xls"');
}else if($phase == "INV5G3"){
		header('Content-Disposition: attachment; filename="INV5,6_Graph3'.'.xls"');
}else if($phase == "INV7G1"){
		header('Content-Disposition: attachment; filename="INV7_Graph1'.'.xls"');
}else if($phase == "INV7G2"){
		header('Content-Disposition: attachment; filename="INV7_Graph2'.'.xls"');
}else if($phase == "INV7G3"){
		header('Content-Disposition: attachment; filename="INV7_Graph3'.'.xls"');
}else if($phase == "INV8G1"){
		header('Content-Disposition: attachment; filename="INV8_Graph1'.'.xls"');
}else if($phase == "INV8G2"){
		header('Content-Disposition: attachment; filename="INV8_Graph2'.'.xls"');
}else if($phase == "INV8G3"){
		header('Content-Disposition: attachment; filename="INV8_Graph3'.'.xls"');
}else if($phase == "INV9G1"){
		header('Content-Disposition: attachment; filename="INV9_Graph1'.'.xls"');
}else if($phase == "INV9G2"){
		header('Content-Disposition: attachment; filename="INV9_Graph2'.'.xls"');
}else if($phase == "INV9G3"){
		header('Content-Disposition: attachment; filename="INV9_Graph3'.'.xls"');
}else if($phase == "INV10G1"){
		header('Content-Disposition: attachment; filename="INV10,11,12_Graph1'.'.xls"');
}else if($phase == "INV10G2"){
		header('Content-Disposition: attachment; filename="INV10,11,12_Graph2'.'.xls"');
}else if($phase == "INV10G3"){
		header('Content-Disposition: attachment; filename="INV10,11,12_Graph3'.'.xls"');
}else if($phase == "INV13G1"){
		header('Content-Disposition: attachment; filename="INV13_Graph1'.'.xls"');
}else if($phase == "INV13G2"){
		header('Content-Disposition: attachment; filename="INV13_Graph2'.'.xls"');
}else if($phase == "INV13G3"){
		header('Content-Disposition: attachment; filename="INV13_Graph3'.'.xls"');
}else if($phase == "INV14G1"){
		header('Content-Disposition: attachment; filename="INV14_Graph1'.'.xls"');
}else if($phase == "INV14G2"){
		header('Content-Disposition: attachment; filename="INV14_Graph2'.'.xls"');
}else if($phase == "INV14G3"){
		header('Content-Disposition: attachment; filename="INV14_Graph3'.'.xls"');
}else if($phase == "INV15G1"){
		header('Content-Disposition: attachment; filename="INV15_Graph1'.'.xls"');
}else if($phase == "INV15G2"){
		header('Content-Disposition: attachment; filename="INV15_Graph2'.'.xls"');
}else if($phase == "INV15G3"){
		header('Content-Disposition: attachment; filename="INV15_Graph3'.'.xls"');
}else if($phase == "INV16G1"){
		header('Content-Disposition: attachment; filename="INV16_Graph1'.'.xls"');
}else if($phase == "INV16G2"){
		header('Content-Disposition: attachment; filename="INV16_Graph2'.'.xls"');
}else if($phase == "INV16G3"){
		header('Content-Disposition: attachment; filename="INV16_Graph3'.'.xls"');
}
/* Inverter End */
/*else if($phase == "smu02"){
	header('Content-Disposition: attachment; filename="SMU-01'.'.xls"');
}else if($phase == "smu05"){
	header('Content-Disposition: attachment; filename="SMU-02'.'.xls"');
}else if($phase == "smu09"){
	header('Content-Disposition: attachment; filename="SMU-03'.'.xls"');
}else if($phase == "smu10"){
	header('Content-Disposition: attachment; filename="SMU-04'.'.xls"');
}*/
/*amplus Mumbai start*/
else if($phase == "ampsmu1inv1"){
	header('Content-Disposition: attachment; filename="INV1/1900705898/SMA1'.'.xls"');
}else if($phase == "ampsmu1inv2"){
	header('Content-Disposition: attachment; filename="INV2/1900705882/SMA2'.'.xls"');
}else if($phase == "ampsmu1inv3"){
	header('Content-Disposition: attachment; filename="INV3/1900705878/SMA3'.'.xls"');
}else if($phase == "ampsmu1inv4"){
	header('Content-Disposition: attachment; filename="INV4/1900705201/SMA4'.'.xls"');
}else if($phase == "ampsmu2inv5"){
	header('Content-Disposition: attachment; filename="INV5/1900704340/SMA5'.'.xls"');
}else if($phase == "ampsmu2inv6"){
	header('Content-Disposition: attachment; filename="INV6/1900705823/SMA6'.'.xls"');
}else if($phase == "ampsmu2inv7"){
	header('Content-Disposition: attachment; filename="INV7/1900705766/SMA7'.'.xls"');
}else if($phase == "ampsmu2inv8"){
	header('Content-Disposition: attachment; filename="INV8/1900705913/SMA8'.'.xls"');
}else if($phase == "ampweather"){
	header('Content-Disposition: attachment; filename="Weather_Station'.'.xls"');
}
else if($phase == "SMA1M1"){
	header('Content-Disposition: attachment; filename="SMA1Graph1'.'.xls"');
}else if($phase == "SMA1M2"){
	header('Content-Disposition: attachment; filename="SMA1Graph2'.'.xls"');
}else if($phase == "SMA1M3"){
	header('Content-Disposition: attachment; filename="SMA1Graph3'.'.xls"');
}else if($phase == "SMA2M1"){
	header('Content-Disposition: attachment; filename="SMA2Graph1'.'.xls"');
}else if($phase == "SMA2M2"){
	header('Content-Disposition: attachment; filename="SMA2Graph2'.'.xls"');
}else if($phase == "SMA2M3"){
	header('Content-Disposition: attachment; filename="SMA2Graph3'.'.xls"');
}else if($phase == "SMA3M1"){
	header('Content-Disposition: attachment; filename="SMA3Graph1'.'.xls"');
}else if($phase == "SMA3M2"){
	header('Content-Disposition: attachment; filename="SMA3Graph2'.'.xls"');
}else if($phase == "SMA3M3"){
	header('Content-Disposition: attachment; filename="SMA3Graph3'.'.xls"');
}else if($phase == "SMA4M1"){
	header('Content-Disposition: attachment; filename="SMA4Graph1'.'.xls"');
}else if($phase == "SMA4M2"){
	header('Content-Disposition: attachment; filename="SMA4Graph2'.'.xls"');
}else if($phase == "SMA4M3"){
	header('Content-Disposition: attachment; filename="SMA4Graph3'.'.xls"');
}else if($phase == "SMA5M1"){
	header('Content-Disposition: attachment; filename="SMA5Graph1'.'.xls"');
}else if($phase == "SMA5M2"){
	header('Content-Disposition: attachment; filename="SMA5Graph2'.'.xls"');
}else if($phase == "SMA5M3"){
	header('Content-Disposition: attachment; filename="SMA5Graph3'.'.xls"');
}else if($phase == "SMA6M1"){
	header('Content-Disposition: attachment; filename="SMA6Graph1'.'.xls"');
}else if($phase == "SMA6M2"){
	header('Content-Disposition: attachment; filename="SMA6Graph2'.'.xls"');
}else if($phase == "SMA6M3"){
	header('Content-Disposition: attachment; filename="SMA6Graph3'.'.xls"');
}else if($phase == "SMA7M1"){
	header('Content-Disposition: attachment; filename="SMA7Graph1'.'.xls"');
}else if($phase == "SMA7M2"){
	header('Content-Disposition: attachment; filename="SMA7Graph2'.'.xls"');
}else if($phase == "SMA7M3"){
	header('Content-Disposition: attachment; filename="SMA7Graph3'.'.xls"');
}else if($phase == "SMA8M1"){
	header('Content-Disposition: attachment; filename="SMA8Graph1'.'.xls"');
}else if($phase == "SMA8M2"){
	header('Content-Disposition: attachment; filename="SMA8Graph2'.'.xls"');
}else if($phase == "SMA8M3"){
	header('Content-Disposition: attachment; filename="SMA8Graph3'.'.xls"');
}
else if ($phase == "Mumtag") {
		header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
/*amplus Mumbai end*/
/*amplus Dominos Nagpur start*/
	else if ($phase == "Domtag") {
		header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	else if ($phase == "weather" && $park_no=="52") {
	header('Content-Disposition: attachment; filename="Weather Station'.'.xls"');
	}
	else if ($phase == "DOMINV1graph1" || $phase == "DOMINV2graph1" || $phase == "DOMINV3graph1" || $phase == "DOMINV4graph1" || $phase == "DOMINV5graph1") {
	header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if ($phase == "DOMINV1graph2" | $phase == "DOMINV2graph2" || $phase == "DOMINV3graph2" || $phase == "DOMINV4graph2" || $phase == "DOMINV5graph2") {
	header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if ($phase == "DOMINV1graph3" | $phase == "DOMINV2graph3" || $phase == "DOMINV3graph3" || $phase == "DOMINV4graph3" || $phase == "DOMINV5graph3") {
	header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}
	else if($phase =="energy"){
		header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	else if($phase =="domsmu1inv1"){
		header('Content-Disposition: attachment; filename="INV1/1900723491/SMA1'.'.xls"');
	}
	else if($phase =="domsmu1inv2"){
		header('Content-Disposition: attachment; filename="INV2/1900723458/SMA2'.'.xls"');
	}
	else if($phase =="domsmu1inv3"){
		header('Content-Disposition: attachment; filename="INV3/1900723466/SMA3'.'.xls"');
	}
	else if($phase =="domsmu1inv4"){
		header('Content-Disposition: attachment; filename="INV4/1900723427/SMA4'.'.xls"');
	}
	else if($phase =="domsmu1inv5"){
		header('Content-Disposition: attachment; filename="INV5/1900724435/SMA5'.'.xls"');
	}
	else if ($phase == "domweather") {
	header('Content-Disposition: attachment; filename="Weather Station'.'.xls"');
	}
	/*amplus Dominos Nagpur end*/


	/*amplus Royal Nagpur start*/
	else if ($phase == "Roytag") {
		header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	else if($phase =="roysmu1inv1"){
		header('Content-Disposition: attachment; filename="SMU1/1900724647/SMA1'.'.xls"');
	}
	else if($phase =="roysmu1inv2"){
		header('Content-Disposition: attachment; filename="SMU1/1900723471/SMA2'.'.xls"');
	}
	else if($phase =="roysmu1inv3"){
		header('Content-Disposition: attachment; filename="SMU1/1900723352/SMA3'.'.xls"');
	}
	else if($phase =="roysmu1inv4"){
		header('Content-Disposition: attachment; filename="SMU1/1900725115/SMA4'.'.xls"');
	}
	else if($phase =="roysmu1inv5"){
		header('Content-Disposition: attachment; filename="SMU2/1900723486/SMA5'.'.xls"');
	}
	else if($phase =="roysmu1inv6"){
		header('Content-Disposition: attachment; filename="SMU2/1900725116/SMA6'.'.xls"');
	}
	else if($phase =="roysmu1inv7"){
		header('Content-Disposition: attachment; filename="SMU2/1900724640/SMA7'.'.xls"');
	}
	else if($phase =="roysmu1inv8"){
		header('Content-Disposition: attachment; filename="SMU2/1900724384/SMA8'.'.xls"');
	}
	else if ($phase == "royweather") {
	header('Content-Disposition: attachment; filename="Weather Station'.'.xls"');
	}
	else if ($phase == "RYINV1G1" || $phase == "RYINV2G1" || $phase == "RYINV3G1" || $phase == "RYINV4G1" || $phase == "RYINV5G1" || $phase == "RYINV6G1" || $phase == "RYINV7G1" || $phase == "RYINV8G1")  {
	header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if ($phase == "RYINV1G2" | $phase == "RYINV2G2" || $phase == "RYINV3G2" || $phase == "RYINV4G2" || $phase == "RYINV5G2" || $phase == "RYINV6G2" || $phase == "RYINV7G2" || $phase == "RYINV8G2") {
	header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if ($phase == "RYINV1G3" | $phase == "RYINV2G3" || $phase == "RYINV3G3" || $phase == "RYINV4G3" || $phase == "RYINV5G3" || $phase == "RYINV6G3" || $phase == "RYINV7G3" || $phase == "RYINV8G3") {
	header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}

	/* Royal Nagpur end */

/* Amplus Indus Nagpur*/
	else if($phase =="indsmu1inv1"){
		header('Content-Disposition: attachment; filename="SMU1/139F5003392201N275/SMA1'.'.xls"');
	}
	else if($phase =="indsmu1inv2"){
		header('Content-Disposition: attachment; filename="SMU2/139F5003386601N275/SMA2'.'.xls"');
	}
	else if ($phase == "IndINV1G1")  {
		header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if ($phase == "IndINV1G2")  {
		header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if ($phase == "IndINV1G3")  {
		header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}
	else if ($phase == "IndINV2G1")  {
		header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if ($phase == "IndINV2G2")  {
		header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if ($phase == "IndINV2G3")  {
		header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}


	else if($phase =="indweather"){
		header('Content-Disposition: attachment; filename="Weather Station'.'.xls"');
	}
	else if ($phase == "raigraph1")  {
		header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if ($phase == "raigraph2")  {
		header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if ($phase == "raigraph3")  {
		header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}
	else if ($phase == "Rai3tag") {
		header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	else if ($phase == "Indtag") {
		header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	/* Amplus Origami start */
	else if ($phase == "Orgtag") {
	header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	else if ($phase == "OMGweather") {
	header('Content-Disposition: attachment; filename="Weather Station'.'.xls"');
	}
	else if ($phase == "ORGINV1graph1")  {
		header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if ($phase == "ORGINV1graph2")  {
		header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if ($phase == "ORGINV1graph3")  {
		header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}
	else if($phase =="orgsmu1inv1"){
		header('Content-Disposition: attachment; filename="SMU1/1900741419/SMA1'.'.xls"');
	}
	else if($phase =="orgsmu1inv2"){
		header('Content-Disposition: attachment; filename="SMU1/1900741251/SMA2'.'.xls"');
	}
	else if($phase =="orgsmu1inv3"){
		header('Content-Disposition: attachment; filename="SMU1/1900741379/SMA3'.'.xls"');
	}
	else if($phase =="orgsmu1inv4"){
		header('Content-Disposition: attachment; filename="SMU1/1900741315/SMA4'.'.xls"');
	}
	else if($phase =="orgsmu2inv1"){
		header('Content-Disposition: attachment; filename="SMU2/1900740897/SMA5'.'.xls"');
	}
	else if($phase =="orgsmu2inv2"){
		header('Content-Disposition: attachment; filename="SMU2/1900741253/SMA6'.'.xls"');
	}
	else if ($phase == "ORGINV1graph1" || $phase == "ORGINV2graph1" || $phase == "ORGINV3graph1" || $phase == "ORGINV4graph1" || $phase == "ORGINV5graph1" || $phase == "ORGINV6graph1")  {
	header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if ($phase == "ORGINV1graph2" | $phase == "ORGINV2graph2" || $phase == "ORGINV3graph2" || $phase == "ORGINV4graph2" || $phase == "ORGINV5graph2" || $phase == "ORGINV6graph2") {
	header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if ($phase == "ORGINV1graph3" | $phase == "ORGINV2graph3" || $phase == "ORGINV3graph3" || $phase == "ORGINV4graph3" || $phase == "ORGINV5graph3" || $phase == "ORGINV6graph3" ) {
	header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}
	/* Amplus Origami end */
	/* Amplus Lalpur start */
	else if ($phase == "LALtag") {
		header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	else if ($phase == "LAPINV1graph1" || $phase == "LAPINV2graph1" || $phase == "LAPINV3graph1" || $phase == "LAPINV4graph1")  {
	header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if ($phase == "LAPINV1graph2" | $phase == "LAPINV2graph2" || $phase == "LAPINV3graph2" || $phase == "LAPINV4graph2") {
	header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if ($phase == "LAPINV1graph3" | $phase == "LAPINV2graph3" || $phase == "LAPINV3graph3" || $phase == "LAPINV4graph3") {
	header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}
	/* Amplus Lalpur end */

	/* Amplus Rudrapur start */
	else if ($phase == "Rudtag") {
		header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	else if ($phase == "RUDweather") {
	header('Content-Disposition: attachment; filename="Weather Station'.'.xls"');
	}
	else if($phase =="Rudsmu1inv1"){
		header('Content-Disposition: attachment; filename="INV1/1900732799/SMA1'.'.xls"');
	}
	else if($phase =="Rudsmu1inv2"){
		header('Content-Disposition: attachment; filename="INV2/1900732898/SMA2'.'.xls"');
	}
	else if($phase =="Rudsmu1inv3"){
		header('Content-Disposition: attachment; filename="INV3/1900732554/SMA3'.'.xls"');
	}
	else if($phase =="Rudsmu2inv4"){
		header('Content-Disposition: attachment; filename="INV4/1900732574/SMA4'.'.xls"');
	}
	else if($phase =="Rudsmu2inv5"){
		header('Content-Disposition: attachment; filename="INV5/1900732572/SMA5'.'.xls"');
	}
	
	/* Amplus Rudrapur end */

	/* Amplus Polymer start */
	else if ($phase == "Polytag") {
	header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	else if ($phase == "Polyweather") {
	header('Content-Disposition: attachment; filename="Weather Station'.'.xls"');
	}
	else if($phase =="polysmu1inv1"){
		header('Content-Disposition: attachment; filename="SMCB1/139F5003535101N445/INV1'.'.xls"');
	}
	else if($phase =="polysmu2inv2"){
		header('Content-Disposition: attachment; filename="SMCB1/139F5003534601N445/INV2'.'.xls"');
	}
	else if($phase =="polysmu3inv3"){
		header('Content-Disposition: attachment; filename="SMCB1/139F5003534901N445/INV3'.'.xls"');
	}
	else if ($phase == "PolyINV1graph1" || $phase == "PolyINV2graph1" || $phase == "PolyINV3graph1")  {
	header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if ($phase == "PolyINV1graph2" | $phase == "PolyINV2graph2" || $phase == "PolyINV3graph2") {
	header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if ($phase == "PolyINV1graph3" | $phase == "PolyINV2graph3" || $phase == "PolyINV3graph3") {
	header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}
	/* Amplus Polymer end */
	/* Amplus Yamaha start */
	else if($phase == "YamahaBlkA"){
	header('Content-Disposition: attachment; filename="Energy Meter(A)'.'.xls"');
}
else if($phase == "YamahaBlkB"){
	header('Content-Disposition: attachment; filename="Energy Meter(B)'.'.xls"');
}
else if($phase == "YamahaBlkC"){
	header('Content-Disposition: attachment; filename="Energy Meter(C)'.'.xls"');
}
else if($phase == "YamahaBlkD"){
	header('Content-Disposition: attachment; filename="Energy Meter(D)'.'.xls"');
}
else if($phase == "YamahaBlkE"){
	header('Content-Disposition: attachment; filename="Energy Meter(E)'.'.xls"');
}
	else if ($phase == "Yamaweather") {
	header('Content-Disposition: attachment; filename="Weather Station'.'.xls"');
	}
	else if($phase =="Yamasmu4inv4"){
		header('Content-Disposition: attachment; filename="SMCB4/1900740859/INV4'.'.xls"');
	}
	else if($phase =="Yamasmu3inv5"){
		header('Content-Disposition: attachment; filename="SMCB3/139F5003543001N455/INV5'.'.xls"');
	}
	else if($phase =="Yamasmu9inv6"){
		header('Content-Disposition: attachment; filename="SMCB9/1046869A006/INV6'.'.xls"');
	}
	else if($phase =="Yamasmu5inv7"){
		header('Content-Disposition: attachment; filename="SMCB5/1046869A005/INV7'.'.xls"');
	}
	else if($phase =="Yamasmu8inv10"){
		header('Content-Disposition: attachment; filename="SMCB8/1046869A003/INV10'.'.xls"');
	}
	else if($phase =="Yamasmu2inv8"){
		header('Content-Disposition: attachment; filename="SMCB2/1046869A004/INV8'.'.xls"');
	}
	else if($phase =="Yamasmu10inv9"){
		header('Content-Disposition: attachment; filename="SMCB10/1046869A001/INV9'.'.xls"');
	}
	else if($phase == "YamaINV1graph1" ||$phase == "YamaINV2graph1" ||$phase == "YamaINV3graph1" ||$phase == "YamaINV4graph1" ||$phase == "YamaINV5graph1" ||$phase == "YamaINV6graph1" ||$phase == "YamaINV7graph1" ||
	$phase == "YamaINV8graph1" || $phase == "YamaINV9graph1" || $phase == "YamaINV10graph1")  {
	header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if($phase == "YamaINV1graph2" || $phase == "YamaINV2graph2" || $phase == "YamaINV3graph2" || $phase == "YamaINV4graph2" || $phase == "YamaINV5graph2" || $phase == "YamaINV6graph2" || $phase == "YamaINV7graph2" ||
	$phase == "YamaINV8graph2" || $phase == "YamaINV9graph2" || $phase == "YamaINV10graph2") {
	header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if($phase == "YamaINV1graph3" ||$phase == "YamaINV2graph3" ||$phase == "YamaINV3graph3" ||$phase == "YamaINV4graph3" ||$phase == "YamaINV5graph3" ||$phase == "YamaINV6graph3" ||$phase == "YamaINV7graph3" ||
	$phase == "YamaINV8graph3" || $phase == "YamaINV9graph3" || $phase == "YamaINV10graph3") {
	header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}
	/* Amplus Yamaha end */
	
	else if($phase == "RUDINV1graph1" ||$phase == "RUDINV2graph1" ||$phase == "RUDINV3graph1" ||$phase == "RUDINV4graph1" ||$phase == "RUDINV5graph1")  {
	header('Content-Disposition: attachment; filename="Graph1'.'.xls"');
	}
	else if($phase == "RUDINV1graph2" || $phase == "RUDINV2graph2" || $phase == "RUDINV3graph2" || $phase == "RUDINV4graph2" || $phase == "RUDINV5graph2") {
	header('Content-Disposition: attachment; filename="Graph2'.'.xls"');
	}
	else if($phase == "RUDINV1graph3" ||$phase == "RUDINV2graph3" ||$phase == "RUDINV3graph3" ||$phase == "RUDINV4graph3" ||$phase == "RUDINV5graph3") {
	header('Content-Disposition: attachment; filename="Graph3'.'.xls"');
	}
	else if ($phase == "RUDweather") {
	header('Content-Disposition: attachment; filename="Weather Station'.'.xls"');
	}
	else if ($phase == "RUDtag") {
	header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
	}
	/* Amplus Rudrapur start */
	
	/* Amplus Rudrapur end */
	else {
    header('Content-Disposition: attachment; filename="Export-' . $phase.$jahr . '.xls"');
	}

if (!isset($delta)) {
    $delta = 0;
}
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
date_default_timezone_set('UTC');
if (is_null($args)) {
    return;
}
set_time_limit(600);
$header = "Timestamp";
$header2 = "\r\nDD/MM/YY HH:mm";
$offset = 19800;
$headerData = array();
$args = split(";", $args);
foreach ($args as $arg) {
    $words = split(',', $arg);
    if (sizeof($words) < 9) {
        continue;
    }
    $headerData[] = $words[5];
    $translatedField = $words[4];
    $translatedField = str_replace("PLUS", "+", $translatedField);
	///for amplus pune avg of 20 & 10 deg
	
	if($words[3]=="8414_8412"){
	$arrv2=explode("_",$words[3]);
	$devid=implode(",",$arrv2);
	 $query = "select ts+$offset as ts, ROUND(sum(value),2)/2 as value FROM _devicedatavalue where ts >= $stamp and ts < $endstamp and device in (".$devid.") and (field='".$translatedField."') group by ts";
    } ///
	else if($translatedField=='Upv-Ist'){
		$query = "select ts+$offset as ts, ROUND((((value+$words[0])*$words[1])+$words[2]),2) as value from _devicedatavalue where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
	}
	else{
	$arrvl=explode("_",$words[3]);
	if(count($arrvl)>1)
	{
		$ids=implode(",",$arrvl);
		$query = "select ts+$offset as ts, sum((((value+$words[0])*$words[1])+$words[2])) as value from _devicedatavalue where value is not null and device IN (".$ids.") and field = '$translatedField' and ts > $stamp and ts < $endstamp group by ts";
	}
	else
	{
		if($translatedField == 'Pac' || $translatedField == 'PAC'  ){
			$query = "select ts+$offset as ts, ROUND((((value+$words[0])*$words[1])+$words[2])/1000,2) as value from _devicedatavalue where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
		}
		else if($translatedField == 'PAC_Total'){
				$query = "select ts+19800 as ts, ROUND((((value+$words[0])*$words[1])+$words[2])/100,2) as value from _devicedatavalue where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
		}
		else{
			$arrv3=explode("-",$translatedField);
			$numItems = count($arrv3);
			if(count($arrv3)>1){
					$fields ='';$i=0;
					foreach($arrv3 as $key=>$value){
						$fields.="'".$value."'";
						if($numItems!=++$i){$fields.=",";}
					}
					$query = "select ts+$offset as ts, ROUND(sum(value),2)/2 as value FROM _devicedatavalue  where ts >= $stamp and ts < $endstamp and device = $words[3] and field IN(".$fields.") group by ts";
					$fields='';
			}
			else{
				$query = "select ts+$offset as ts, ROUND((((value+$words[0])*$words[1])+$words[2]),2) as value from _devicedatavalue where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp";
			}
		}
	}
	}
    $header.="\t" . $words[5];
    $unit = $words[7];
    $unit = str_replace("DEG", "DEG ", $unit);
    $unit = str_replace("SQUA", "^2", $unit);
    $header2.="\t" . $unit;
    if ($showQueries == 1) {
        echo $query . "\r\n";
    }
    $ds1 = mysql_query($query, $verbindung) or die(mysql_error());
    $values = array();
    while ($row_ds1 = mysql_fetch_array($ds1)) {
      //  echo ($row_ds1[ts])."=".$row_ds1[value]."<br>";

        if (!(isset($data[floor($row_ds1[ts] / 300)][$words[5]][difference]))) {
            //           echo "new ".round($row_ds1[ts] / 900) ."=".($row_ds1[ts] / 900);

          //  echo "<br>------------------------<br>";
          //  echo "new Value for ".(900*round($row_ds1[ts]/900))."<br><br>";
            $data[floor($row_ds1[ts] / 300)][$words[5]][value] = $row_ds1[value];
            $data[floor($row_ds1[ts] / 300)][$words[5]][difference] = abs(($row_ds1[ts] / 300) - floor($row_ds1[ts] / 300));
        }else if ($data[floor($row_ds1[ts] / 900)][$words[5]][difference] > abs(($row_ds1[ts] / 300) - floor($row_ds1[ts] / 300))){
           //echo "bigger ".($row_ds1[ts] / 900);
       //     echo "closer to ".(900*round($row_ds1[ts]/900))."<br><br>";

            $data[floor($row_ds1[ts] / 300)][$words[5]][value] = $row_ds1[value];
            $data[floor($row_ds1[ts] / 300)][$words[5]][difference] = abs(($row_ds1[ts] / 300) - floor($row_ds1[ts] /300));

            //echo "<br>";
            }else {
            //         echo "smaller ".($row_ds1[ts] / 900);
            //echo "<br>";
                }

    }
}
echo $header;
echo $header2;
ksort($data);
foreach ($data as $key => $values) {
    if ($useTs == 0) {
        echo "\r\n" . date('d-m-Y H:i', ($key * 300));
    } else {
        echo "\r\n" . ($key * (300));
    }
    foreach ($headerData as $currentheader) {
        //echo $currentheader;
        if (!is_null($values[$currentheader])) {
            echo "\t" . $values[$currentheader][value];
        } else {
            echo "\t";
        }
    }
}
return;
?>
