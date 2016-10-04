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
}else if($phase == "energyg2"&& $park_no = 36){ 
	header('Content-Disposition: attachment; filename="EM_System_PR' .'.xls"');
}else if($phase == "energyg3"&& $park_no = 36){
	header('Content-Disposition: attachment; filename="INV PR(%)' .'.xls"');
}else if($phase == "energy4"&& $park_no = 36){
	header('Content-Disposition: attachment; filename="INV AC PR(%)' .'.xls"');
}else if($phase == "energy5"&& $park_no = 36){
	header('Content-Disposition: attachment; filename="INV DC Voltage(V)' .'.xls"');
}else if($phase == "weather"&& $park_no = 36){
	header('Content-Disposition: attachment; filename="WeatherStation'.'.xls"');
}else if($phase == "smu"&& $park_no = 36){
	header('Content-Disposition: attachment; filename="SMU'.'.xls"');
}else if($phase == "system"&& $park_no = 36){
	header('Content-Disposition: attachment; filename="SystemView'.'.xls"');
}else if($phase =="energy"&& $park_no = 36){
	header('Content-Disposition: attachment; filename="EnergyMeter'.'.xls"');
}else if($phase =="graph4" && $park_no = 36){
	header('Content-Disposition: attachment; filename="Inverter Efficiency '.'.xls"');
}
else if($phase =="INV1G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV1(REFSU1) PR(%)'.'.xls"');
}else if($phase =="INV1G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV1(REFSU1) Efficiency'.'.xls"');
}else if($phase =="INV1G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV1(REFSU1) AC PR(%)'.'.xls"');
}else if($phase =="INV1G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV1(REFSU1)DC Voltage (v) '.'.xls"');
}else if($phase =="INV7G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV7(REFSU2)PR(%)'.'.xls"');
}else if($phase =="INV7G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV7(REFSU2)Efficiency'.'.xls"');
}else if($phase =="INV7G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV7(REFSU2) AC PR(%)'.'.xls"');
}else if($phase =="INV7G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV7(REFSU2)DC Voltage (v)'.'.xls"');
}else if($phase =="INV9G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV9(REFSU3)Efficiency'.'.xls"');
}else if($phase =="INV9G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV9(REFSU3)PR(%)'.'.xls"');
}else if($phase =="INV9G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV9(REFSU3) AC PR(%)'.'.xls"');
}else if($phase =="INV9G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV9(REFSU3)DC Voltage (v)'.'.xls"');
}else if($phase =="INV13G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV13(REFSU4)Efficiency'.'.xls"');
}else if($phase =="INV13G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV13(REFSU4)PR(%)'.'.xls"');
}else if($phase =="INV13G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV13(REFSU4) AC PR(%)'.'.xls"');
}else if($phase =="INV13G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV13(REFSU4)DC Voltage (v)'.'.xls"');
}else if($phase =="INV2G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV2(SMA1)PR(%)'.'.xls"');
}else if($phase =="INV2G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV2(SMA1)Efficiency'.'.xls"');
}else if($phase =="INV2G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV2(SMA1) AC PR(%)'.'.xls"');
}else if($phase =="INV2G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV2(SMA1)DC Voltage (v)'.'.xls"');
}else if($phase =="INV3G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV3(SMA2)PR(%)'.'.xls"');
}else if($phase =="INV3G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV3(SMA2)Efficiency'.'.xls"');
}else if($phase =="INV3G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV3(SMA2) AC PR(%)'.'.xls"');
}else if($phase =="INV3G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV3(SMA2)DC Voltage (v)'.'.xls"');
}
else if($phase =="INV4G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV4(SMA3)PR(%)'.'.xls"');
}else if($phase =="INV4G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV4(SMA3)Efficiency'.'.xls"');
}else if($phase =="INV4G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV4(SMA3) AC PR(%)'.'.xls"');
}else if($phase =="INV4G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV4(SMA3)DC Voltage (v)'.'.xls"');
}else if($phase =="INV5G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV5_INV6(SMA4_SMA5)PR(%)'.'.xls"');
}else if($phase =="INV5G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV5_INV6(SMA4_SMA5)Efficiency'.'.xls"');
}else if($phase =="INV5G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV5_INV6(SMA4_SMA5)AC PR(%)'.'.xls"');
}else if($phase =="INV5G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV5_INV6(SMA4_SMA5)DC Voltage (v)'.'.xls"');
}else if($phase =="INV8G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV8(SMA6)PR(%)'.'.xls"');
}else if($phase =="INV8G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV8(SMA6)Efficiency'.'.xls"');
}else if($phase =="INV8G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV8(SMA6)AC PR(%)'.'.xls"');
}else if($phase =="INV8G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV8(SMA6)DC Voltage (v)'.'.xls"');
}else if($phase =="INV10G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV10_INV11_INV12(SMA7_SMA8_SMA9)PR(%)'.'.xls"');
}else if($phase =="INV10G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV10_INV11_INV12(SMA7_SMA8_SMA9)Efficiency'.'.xls"');
}else if($phase =="INV10G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV10_INV11_INV12(SMA7_SMA8_SMA9)AC PR(%)'.'.xls"');
}else if($phase =="INV10G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV10_INV11_INV12(SMA7_SMA8_SMA9)DC Voltage (v)'.'.xls"');
}else if($phase =="INV14G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV14(SMA10)PR(%)'.'.xls"');
}else if($phase =="INV14G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV14(SMA10)Efficiency'.'.xls"');
}else if($phase =="INV14G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV14(SMA10)AC PR(%)'.'.xls"');
}else if($phase =="INV14G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV14(SMA10)DC Voltage (v)'.'.xls"');
}else if($phase =="INV15G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV15(SMA11)PR'.'.xls"');
}else if($phase =="INV15G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV15(SMA11)Efficiency'.'.xls"');
}else if($phase =="INV15G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV15(SMA11)AC PR(%)'.'.xls"');
}else if($phase =="INV15G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV15(SMA11)DC Voltage (v)'.'.xls"');
}else if($phase =="INV16G4" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV16(SMA12)PR(%)'.'.xls"');
}else if($phase =="INV16G5" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV16(SMA12)Efficiency'.'.xls"');
}else if($phase =="INV16G6" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV16(SMA12)AC PR(%)'.'.xls"');
}else if($phase =="INV16G7" && $park_no = 43){
	header('Content-Disposition: attachment; filename="INV16(SMA12)DC Voltage (v)'.'.xls"');
}

//Amplus Mumbai
else if($phase =="energy2"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="SystemPR'.'.xls"');
}else if($phase =="SMA1M4"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}else if($phase =="SMA2M4"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}else if($phase =="SMA3M4"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV3 PR(%)'.'.xls"');
}else if($phase =="SMA4M4"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV4 PR(%)'.'.xls"');
}else if($phase =="SMA5M4"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV5 PR(%)'.'.xls"');
}else if($phase =="SMA6M4"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV6 PR(%)'.'.xls"');
}else if($phase =="SMA7M4"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV7 PR(%)'.'.xls"');
}else if($phase =="SMA8M4"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV8 PR(%)'.'.xls"');
}else if($phase =="SMA1M5"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV1 Efficiency'.'.xls"');
}else if($phase =="SMA1M6"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV1 AC PR(%)'.'.xls"');
}else if($phase =="SMA1M7"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV1 DC Voltage (v)'.'.xls"');
}else if($phase =="SMA2M5"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV2 Efficiency'.'.xls"');
}else if($phase =="SMA2M6"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV2 AC PR(%)'.'.xls"');
}else if($phase =="SMA2M7"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV2 DC Voltage (v)'.'.xls"');
}else if($phase =="SMA3M5"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV3 Efficiency'.'.xls"');
}else if($phase =="SMA3M6"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV3 AC PR(%)'.'.xls"');
}else if($phase =="SMA3M7"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV3 DC Voltage (v)'.'.xls"');
}else if($phase =="SMA4M5"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV4 Efficiency'.'.xls"');
}else if($phase =="SMA4M6"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV4 AC PR(%)'.'.xls"');
}else if($phase =="SMA4M7"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV4 DC Voltage (v)'.'.xls"');
}else if($phase =="SMA5M5"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV5 Efficiency'.'.xls"');
}else if($phase =="SMA5M6"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV5 AC PR(%)'.'.xls"');
}else if($phase =="SMA5M7"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV5 DC Voltage (v)'.'.xls"');
}else if($phase =="SMA6M5"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV6 Efficiency'.'.xls"');
}else if($phase =="SMA6M6"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV6 AC PR(%)'.'.xls"');
}else if($phase =="SMA6M7"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV6 DC Voltage (v)'.'.xls"');
}else if($phase =="SMA7M5"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV7 Efficiency'.'.xls"');
}else if($phase =="SMA7M6"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV7 AC PR(%)'.'.xls"');
}else if($phase =="SMA7M7"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV7 DC Voltage (v)'.'.xls"');
}else if($phase =="SMA8M5"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV8 Efficiency'.'.xls"');
}else if($phase =="SMA8M6"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV8 AC PR(%)'.'.xls"');
}else if($phase =="SMA8M7"&& $park_no = 39){
	header('Content-Disposition: attachment; filename="INV8 DC Voltage (v)'.'.xls"');
}
//Amplus Raisoni3
else if($phase =="energy2"&& $park_no = 46){
	header('Content-Disposition: attachment; filename="SystemPR'.'.xls"');
}
else if($phase =="INVPR" && $park_no = 46){
	header('Content-Disposition: attachment; filename="INV PR(%)'.'.xls"');
}
else if($phase =="INVEFF" && $park_no = 46){
	header('Content-Disposition: attachment; filename="INV Efficiency'.'.xls"');
}

else if($phase =="INVACPR" && $park_no = 46){
	header('Content-Disposition: attachment; filename="INV AC PR(%)'.'.xls"');
}
else if($phase =="INVDCPR" && $park_no = 46){
	header('Content-Disposition: attachment; filename="INV DC Voltage (v) '.'.xls"');
}

//Amplus Dominos Nagpur


else if($phase =="INV1PR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}
else if($phase =="INV2PR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}
else if($phase =="INV3PR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV3 PR(%)'.'.xls"');
}
else if($phase =="INV4PR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV4 PR(%)'.'.xls"');
}
else if($phase =="INV5PR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV5 PR(%)'.'.xls"');
}

else if($phase =="INV1EFF" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV1 Efficiency'.'.xls"');
}
else if($phase =="INV2EFF" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV2 Efficiency'.'.xls"');
}

else if($phase =="INV3EFF" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV3 Efficiency'.'.xls"');
}

else if($phase =="INV4EFF" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV4 Efficiency'.'.xls"');
}

else if($phase =="INV5EFF" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV5 Efficiency'.'.xls"');
}

else if($phase =="INV1ACPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV1 AC PR(%)'.'.xls"');
}
else if($phase =="INV2ACPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV2 AC PR(%)'.'.xls"');
}

else if($phase =="INV3ACPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV3 AC PR(%)'.'.xls"');
}

else if($phase =="INV4ACPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV4 AC PR(%)'.'.xls"');
}

else if($phase =="INV5ACPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV5 AC PR(%)'.'.xls"');
}

else if($phase =="INV1DCPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV1 DC Voltage (v)'.'.xls"');
}
else if($phase =="INV2DCPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV2 DC Voltage (v)'.'.xls"');
}

else if($phase =="INV3DCPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV3 DC Voltage (v)'.'.xls"');
}

else if($phase =="INV4DCPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV4 DC Voltage (v)'.'.xls"');
}

else if($phase =="INV5DCPR" && $park_no = 52){
	header('Content-Disposition: attachment; filename="INV5 DC Voltage (v)'.'.xls"');
}




else if($phase =="energy2"&& $park_no = 52){
	header('Content-Disposition: attachment; filename="SystemPR'.'.xls"');
}


//Amplus Royal Pune


else if($phase =="INV1PR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}
else if($phase =="INV2PR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}
else if($phase =="INV3PR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV3 PR(%)'.'.xls"');
}
else if($phase =="INV4PR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV4 PR(%)'.'.xls"');
}
else if($phase =="INV5PR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV5 PR(%)'.'.xls"');
}

else if($phase =="INV6PR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV6 PR(%)'.'.xls"');
}
else if($phase =="INV7PR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV7 PR(%)'.'.xls"');
}
else if($phase =="INV8PR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV8 PR(%)'.'.xls"');
}

else if($phase =="RINV1EFF" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV1 Efficiency'.'.xls"');
}
else if($phase =="RINV2EFF" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV2 Efficiency'.'.xls"');
}

else if($phase =="RINV3EFF" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV3 Efficiency'.'.xls"');
}

else if($phase =="RINV4EFF" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV4 Efficiency'.'.xls"');
}

else if($phase =="RINV5EFF" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV5 Efficiency'.'.xls"');
}

else if($phase =="RINV6EFF" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV6 Efficiency'.'.xls"');
}
else if($phase =="RINV7EFF" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV7 Efficiency'.'.xls"');
}
else if($phase =="RINV8EFF" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV8 Efficiency'.'.xls"');
}
else if($phase =="RINV1ACPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV1 AC PR(%)'.'.xls"');
}
else if($phase =="RINV2ACPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV2 AC PR(%)'.'.xls"');
}

else if($phase =="RINV3ACPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV3 AC PR(%)'.'.xls"');
}

else if($phase =="RINV4ACPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV4 AC PR(%)'.'.xls"');
}



else if($phase =="RINV5ACPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV5 AC PR(%)'.'.xls"');
}
else if($phase =="RINV6ACPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV6 AC PR(%)'.'.xls"');
}
else if($phase =="RINV7ACPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV7 AC PR(%)'.'.xls"');
}
else if($phase =="RINV8ACPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV8 AC PR(%)'.'.xls"');
}

else if($phase =="RINV1DCPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV1 DC Voltage (v)'.'.xls"');
}
else if($phase =="RINV2DCPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV2 DC Voltage (v)'.'.xls"');
}

else if($phase =="RINV3DCPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV3 DC Voltage (v)'.'.xls"');
}

else if($phase =="RINV4DCPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV4 DC Voltage (v)'.'.xls"');
}

else if($phase =="RINV5DCPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV5 DC Voltage (v)'.'.xls"');
}

else if($phase =="RINV6DCPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV6 DC Voltage (v)'.'.xls"');
}
else if($phase =="RINV7DCPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV7 DC Voltage (v)'.'.xls"');
}
else if($phase =="RINV8DCPR" && $park_no = 53){
	header('Content-Disposition: attachment; filename="INV8 DC Voltage (v)'.'.xls"');
}


else if($phase =="energy2"&& $park_no = 53){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}
//Amplus Indus
else if($phase =="energygIndus"&& $park_no =54){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}

else if($phase == "IndINV1PR" && $park_no =54){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}
else if($phase == "IndINV2PR" && $park_no =54){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}
else if($phase =="IndINV1ACPR" && $park_no =54){
	header('Content-Disposition: attachment; filename="INV1 AC PR(%)'.'.xls"');
}
else if($phase =="IndINV2ACPR" && $park_no =54){
	header('Content-Disposition: attachment; filename="INV2 AC PR(%)'.'.xls"');
}
else if($phase =="IndINV1EFF" && $park_no =54){
	header('Content-Disposition: attachment; filename="INV1 Efficiency'.'.xls"');
}
else if($phase =="IndINV2EFF" && $park_no =54){
	header('Content-Disposition: attachment; filename="INV2 Efficiency'.'.xls"');
}
else if($phase =="IndINV1DCPR" && $park_no =54 ){
	header('Content-Disposition: attachment; filename="INV1 DC Voltage (v)'.'.xls"');
}
else if($phase =="IndINV2DCPR" && $park_no =54){
	header('Content-Disposition: attachment; filename="INV2 DC Voltage (v)'.'.xls"');
}
//Amplus Lalpur
else if($phase =="Lalenergyg"&& $park_no =55){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}
else if($phase == "LAPINV1PR"){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}
else if($phase == "LAPINV2PR"){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}
else if($phase == "LAPINV3PR"){
	header('Content-Disposition: attachment; filename="INV3 PR(%)'.'.xls"');
}
else if($phase == "LAPINV4PR"){
	header('Content-Disposition: attachment; filename="INV4 PR(%)'.'.xls"');
}
else if($phase =="LAPINV1ACPR" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV1 AC PR(%)'.'.xls"');
}
else if($phase =="LAPINV2ACPR" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV2 AC PR(%)'.'.xls"');
}

else if($phase =="LAPINV3ACPR" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV3 AC PR(%)'.'.xls"');
}

else if($phase =="LAPINV4ACPR" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV4 AC PR(%)'.'.xls"');
}
else if($phase =="LAPINV1EFF" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV1 Efficiency'.'.xls"');
}
else if($phase =="LAPINV2EFF" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV2 Efficiency'.'.xls"');
}

else if($phase =="LAPINV3EFF" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV3 Efficiency'.'.xls"');
}

else if($phase =="LAPINV4EFF" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV4 Efficiency'.'.xls"');
}
else if($phase =="LAPINV1DCPR" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV1 DC Voltage (v)'.'.xls"');
}
else if($phase =="LAPINV2DCPR" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV2 DC Voltage (v)'.'.xls"');
}

else if($phase =="LAPINV3DCPR" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV3 DC Voltage (v)'.'.xls"');
}

else if($phase =="LAPINV4DCPR" && $park_no = 55){
	header('Content-Disposition: attachment; filename="INV4 DC Voltage (v)'.'.xls"');
}
//Amplus Origami

else if($phase =="Orgenergyg2"&& $park_no =57){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}
else if($phase == "ORGINV1PR"){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}
else if($phase == "ORGINV2PR"){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}
else if($phase == "ORGINV3PR"){
	header('Content-Disposition: attachment; filename="INV3 PR(%)'.'.xls"');
}
else if($phase == "ORGINV4PR"){
	header('Content-Disposition: attachment; filename="INV4 PR(%)'.'.xls"');
}
else if($phase == "ORGINV5PR"){
	header('Content-Disposition: attachment; filename="INV5 PR(%)'.'.xls"');
}
else if($phase == "ORGINV6PR"){
	header('Content-Disposition: attachment; filename="INV6 PR(%)'.'.xls"');
}


else if($phase =="ORGINV1ACPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV1 AC PR(%)'.'.xls"');
}
else if($phase =="ORGINV2ACPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV2 AC PR(%)'.'.xls"');
}

else if($phase =="ORGINV3ACPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV3 AC PR(%)'.'.xls"');
}

else if($phase =="ORGINV4ACPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV4 AC PR(%)'.'.xls"');
}

else if($phase =="ORGINV5ACPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV5 AC PR(%)'.'.xls"');
}
else if($phase =="ORGINV6ACPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV6 AC PR(%)'.'.xls"');
}

else if($phase =="ORGINV1EFF" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV1 Efficiency'.'.xls"');
}
else if($phase =="ORGINV2EFF" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV2 Efficiency'.'.xls"');
}

else if($phase =="ORGINV3EFF" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV3 Efficiency'.'.xls"');
}

else if($phase =="ORGINV4EFF" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV4 Efficiency'.'.xls"');
}

else if($phase =="ORGINV5EFF" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV5 Efficiency'.'.xls"');
}

else if($phase =="ORGIV6EFF" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV6 Efficiency'.'.xls"');
}


else if($phase =="ORGINV1DCPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV1 DC Voltage (v)'.'.xls"');
}
else if($phase =="ORGINV2DCPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV2 DC Voltage (v)'.'.xls"');
}

else if($phase =="ORGINV3DCPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV3 DC Voltage (v)'.'.xls"');
}

else if($phase =="ORGINV4DCPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV4 DC Voltage (v)'.'.xls"');
}

else if($phase =="ORGINV5DCPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV5 DC Voltage (v)'.'.xls"');
}

else if($phase =="ORGINV6DCPR" && $park_no = 57){
	header('Content-Disposition: attachment; filename="INV6 DC Voltage (v)'.'.xls"');
}
else if($phase =="Polyenergyg2"&& $park_no =58){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}
else if($phase =="PolyINV1EFF" && $park_no = 58){
	header('Content-Disposition: attachment; filename="INV1 Efficiency'.'.xls"');
}
else if($phase =="PolyINV2EFF" && $park_no = 58){
	header('Content-Disposition: attachment; filename="INV2 Efficiency'.'.xls"');
}

else if($phase =="PolyINV3EFF" && $park_no = 58){
	header('Content-Disposition: attachment; filename="INV3 Efficiency'.'.xls"');
}
else if($phase =="PolyINV1ACPR" && $park_no = 58){
	header('Content-Disposition: attachment; filename="INV1 AC PR(%)'.'.xls"');
}
else if($phase =="PolyINV2ACPR" && $park_no = 58){
	header('Content-Disposition: attachment; filename="INV2 AC PR(%)'.'.xls"');
}

else if($phase =="PolyINV3ACPR" && $park_no = 58){
	header('Content-Disposition: attachment; filename="INV3 AC PR(%)'.'.xls"');
}
else if($phase =="PolyINV1DCPR" && $park_no = 58){
	header('Content-Disposition: attachment; filename="INV1 DC Voltage (v)'.'.xls"');
}
else if($phase =="PolyINV2DCPR" && $park_no = 58){
	header('Content-Disposition: attachment; filename="INV2 DC Voltage (v)'.'.xls"');
}

else if($phase =="PolyINV3DCPR" && $park_no = 58){
	header('Content-Disposition: attachment; filename="INV3 DC Voltage (v)'.'.xls"');
}
else if($phase == "PolyINV1PR"){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}
else if($phase == "PolyINV2PR"){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}
else if($phase == "PolyINV3PR"){
	header('Content-Disposition: attachment; filename="INV3 PR(%)'.'.xls"');
}
else if($phase =="Yamahaenergyg2"&& $park_no =59){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}


else if($phase == "YamahaEnBlkA"){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}
else if($phase == "YamahaEnBlkB"){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}
else if($phase == "YamahaEnBlkC"){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}
else if($phase == "YamahaEnBlkD"){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}
else if($phase == "YamahaEnBlkE"){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}

else if($phase =="YamaINV1EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV1 Efficiency'.'.xls"');
}
else if($phase =="YamaINV2EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV2 Efficiency'.'.xls"');
}
else if($phase =="YamaINV3EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV3 Efficiency'.'.xls"');
}
else if($phase =="YamaINV4EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV4 Efficiency'.'.xls"');
}
else if($phase =="YamaINV5EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV5 Efficiency'.'.xls"');
}
else if($phase =="YamaINV6EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV6 Efficiency'.'.xls"');
}
else if($phase =="YamaINV7EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV7 Efficiency'.'.xls"');
}
else if($phase =="YamaINV8EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV8 Efficiency'.'.xls"');
}
else if($phase =="YamaINV9EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV9 Efficiency'.'.xls"');
}
else if($phase =="YamaINV10EFF" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV10 Efficiency'.'.xls"');
}

else if($phase =="YamaINV1ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV1 AC PR(%)'.'.xls"');
}
else if($phase =="YamaINV2ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV2 AC PR(%)'.'.xls"');
}
else if($phase =="YamaINV3ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV3 AC PR(%)'.'.xls"');
}
else if($phase =="YamaINV4ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV4 AC PR(%)'.'.xls"');
}
else if($phase =="YamaINV5ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV5 AC PR(%)'.'.xls"');
}
else if($phase =="YamaINV6ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV6 AC PR(%)'.'.xls"');
}
else if($phase =="YamaINV7ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV7 AC PR(%)'.'.xls"');
}
else if($phase =="YamaINV8ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV8 AC PR(%)'.'.xls"');
}
else if($phase =="YamaINV9ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV9 AC PR(%)'.'.xls"');
}

else if($phase =="YamaINV10ACPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV10 AC PR(%)'.'.xls"');
}

else if($phase =="YamaINV1DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV1 DC Voltage (v)'.'.xls"');
}
else if($phase =="YamaINV2DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV2 DC Voltage (v)'.'.xls"');
}
else if($phase =="YamaINV3DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV3 DC Voltage (v)'.'.xls"');
}
else if($phase =="YamaINV4DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV4 DC Voltage (v)'.'.xls"');
}

else if($phase =="YamaINV5DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV5 DC Voltage (v)'.'.xls"');
}
else if($phase =="YamaINV6DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV6 DC Voltage (v)'.'.xls"');
}
else if($phase =="YamaINV7DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV7 DC Voltage (v)'.'.xls"');
}
else if($phase =="YamaINV8DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV8 DC Voltage (v)'.'.xls"');
}
else if($phase =="YamaINV9DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV9 DC Voltage (v)'.'.xls"');
}
else if($phase =="YamaINV10DCPR" && $park_no = 59){
	header('Content-Disposition: attachment; filename="INV10 DC Voltage (v)'.'.xls"');
}

else if($phase == "YamaINV1PR"){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}else if($phase == "YamaINV2PR"){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}
else if($phase == "YamaINV3PR"){
	header('Content-Disposition: attachment; filename="INV3 PR(%)'.'.xls"');
}
else if($phase == "YamaINV4PR"){
	header('Content-Disposition: attachment; filename="INV4 PR(%)'.'.xls"');
}
else if($phase == "YamaINV5PR"){
	header('Content-Disposition: attachment; filename="INV5 PR(%)'.'.xls"');
}
else if($phase == "YamaINV6PR"){
	header('Content-Disposition: attachment; filename="INV6 PR(%)'.'.xls"');
}
else if($phase == "YamaINV7PR"){
	header('Content-Disposition: attachment; filename="INV7 PR(%)'.'.xls"');
}
else if($phase == "YamaINV8PR"){
	header('Content-Disposition: attachment; filename="INV8 PR(%)'.'.xls"');
}
else if($phase == "YamaINV9PR"){
	header('Content-Disposition: attachment; filename="INV9 PR(%)'.'.xls"');
}
else if($phase == "YamaINV10PR"){
	header('Content-Disposition: attachment; filename="INV10 PR(%)'.'.xls"');
}

else if($phase == "RUDINV1PR"){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}else if($phase == "RUDINV2PR"){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}
else if($phase == "RUDINV3PR"){
	header('Content-Disposition: attachment; filename="INV3 PR(%)'.'.xls"');
}
else if($phase == "RUDINV4PR"){
	header('Content-Disposition: attachment; filename="INV4 PR(%)'.'.xls"');
}
else if($phase == "RUDINV5PR"){
	header('Content-Disposition: attachment; filename="INV5 PR(%)'.'.xls"');
}
else if($phase =="RUDINV1EFF" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV1 Efficiency'.'.xls"');
}
else if($phase =="RUDINV2EFF" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV2 Efficiency'.'.xls"');
}
else if($phase =="RUDINV3EFF" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV3 Efficiency'.'.xls"');
}
else if($phase =="RUDINV4EFF" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV4 Efficiency'.'.xls"');
}
else if($phase =="RUDINV5EFF" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV5 Efficiency'.'.xls"');
}

else if($phase =="RUDINV1ACPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV1 AC PR(%)'.'.xls"');
}
else if($phase =="RUDINV2ACPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV2 AC PR(%)'.'.xls"');
}
else if($phase =="RUDINV3ACPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV3 AC PR(%)'.'.xls"');
}
else if($phase =="RUDINV4ACPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV4 AC PR(%)'.'.xls"');
}
else if($phase =="RUDINV5ACPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV5 AC PR(%)'.'.xls"');
}
else if($phase =="RUDINV1DCPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV1 DC Voltage (v)'.'.xls"');
}
else if($phase =="RUDINV2DCPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV2 DC Voltage (v)'.'.xls"');
}
else if($phase =="RUDINV3DCPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV3 DC Voltage (v)'.'.xls"');
}
else if($phase =="RUDINV4DCPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV4 DC Voltage (v)'.'.xls"');
}

else if($phase =="RUDINV5DCPR" && $park_no = 56){
	header('Content-Disposition: attachment; filename="INV5 DC Voltage (v)'.'.xls"');
}
else if($phase == "RUDINV1PR"){
	header('Content-Disposition: attachment; filename="INV1 PR(%)'.'.xls"');
}else if($phase == "RUDINV2PR"){
	header('Content-Disposition: attachment; filename="INV2 PR(%)'.'.xls"');
}
else if($phase == "RUDINV3PR"){
	header('Content-Disposition: attachment; filename="INV3 PR(%)'.'.xls"');
}
else if($phase == "RUDINV4PR"){
	header('Content-Disposition: attachment; filename="INV4 PR(%)'.'.xls"');
}
else if($phase == "RUDINV5PR"){
	header('Content-Disposition: attachment; filename="INV5 PR(%)'.'.xls"');
}
else if($phase == "RUDenergyg2"){
	header('Content-Disposition: attachment; filename="SystemPR(%)'.'.xls"');
}
else {
    header('Content-Disposition: attachment; filename="Export-' . $jahr . '.xls"');
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

if($park_no == 43)
{
$sourcetable = "pune_calculation";

}
if($park_no == 36){
$sourcetable = "amplus_calculation";	

}
if($park_no == 39){
$sourcetable = "mumbai_calculation";	

}
if($park_no == 46)
{
$sourcetable = "raisoni3_calculation";
}

if($park_no == 52)
{
$sourcetable = "ampdominos_calculation";
}

if($park_no == 54)
{
$sourcetable = "ampIndus_calculation";
}

if($park_no == 53)
{
$sourcetable = "ampRoyal_calculation";
}

if($park_no == 57)
{
$sourcetable = "origami_calculation";
}
if($park_no == 55)
{
$sourcetable = "ampLalpur_calculation";
}

if($park_no == 56)
{
$sourcetable = "amprudrapur_calculation";
}

if($park_no == 58)
{
$sourcetable = "polymers_calculation";
}
if($park_no == 59)
{
$sourcetable = "yamaha_calculation";
}
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
	
	//$arrvl=explode("_",$words[3]);
	//if(count($arrvl)>1)
	if($translatedField == "Solar_Radiation")
	{
		//$ids=implode(",",$arrvl);
		$query = "select ts+$offset as ts, sum((((value+$words[0])*$words[1])+$words[2])) as value from _devicedatavalue where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp group by ts";
	}
	else
	{
		$query = "select ts+$offset as ts, (((value+$words[0])*$words[1])+$words[2]) as value from $sourcetable where value is not null and device = $words[3] and field = '$translatedField' and ts > $stamp and ts < $endstamp group by ts";
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
