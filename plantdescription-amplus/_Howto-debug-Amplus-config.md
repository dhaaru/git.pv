HowTo debug Amplus plant configuration
=========================================
in your local test environment, or on git.pv-india.net

create *.json file in /plantdescription-amplus

prepare fresh test database table:

  ```
  DROP TABLE `amplus_test_calculations`;
  CREATE TABLE `amplus_test_calculations` (
    `ts` int(14) NOT NULL,
    `device` int(14) NOT NULL,
    `field` varchar(30) NOT NULL,
    `park_no` int(11) NOT NULL,
    `value` double NOT NULL,
    PRIMARY KEY (`ts`,`device`,`field`,`park_no`)
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
  ```

execute test:  

  debug parameters:  

    `testdb=1`
        write results to _amplus_test_calculations_ 
        instead of _amplus_all_calculations_

    `showQueries=1`
        debug output

    `plantname=xyz`
        execute only config files where 
        xyz occurs in plantname   (case-insensitive)

  test url:  

    `view-source:http://pvindia.local:3080/diagram/amplus_calculation_influx.php?plantname=raiso&showQueries=1&testdb=1`

  additional parameters:  

    `time=1`
        range: one day (instead of three days) ...

    `endtime=95`	
        until (including) 95 days before today 


