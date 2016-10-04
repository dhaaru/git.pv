What about this folder "plantdescription-amplus"?
========================
contains plant description files especially for Amplus plants.
Intended as configuration input.

What?
-------------------------
plant structure configuration for each Amplus plant, 
- handwritten in yaml format (extension .yaml)
- converted to json format, UTF-8 without BOM  (extension .json).
Only the .json file is used for plant calculations.

Why?
-------------------------
- intended as an intermediary solution for the database switch from _devicedatavalue to InfluxDB
- for replacement of php mysql scripts for Amplus calculations with one php influx replacement script
- the intended php calculations influx script needs configuration from these files, in json format.
- see (pv-india issue "transition: Amplus calculations")[https://github.com/iplon/pv-india/issues/87]

