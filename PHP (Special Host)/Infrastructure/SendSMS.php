<?php

$confing = include('config.php');

$SMSaddress=$confing['SMSaddress']; 
$SMSusername=$confing['SMSusername']; 
$SMSpassword=$confing['SMSpassword']; 
$SMSfrom=$confing['SMSfrom'];
$SMSisflash=$confing['SMSisflash']; 
$SMSfooter=$confing['SMSfooter']; 
$MIKROTIKip=$confing['MIKROTIKip']; 
$MIKROTIKusername=$confing['MIKROTIKusername']; 
$MIKROTIKpassword=$confing['MIKROTIKpassword']; 
$AddressBack=$confing['AddressBack']; 

$Zuser = $_GET["Zuser"] ;
$Zdownload = $_GET["Zdownload"] ;
$Zupload = $_GET["Zupload"] ;
$Zphone = $_GET["Zphone"] ;
$Zuptime  = $_GET["Zuptime"] ;
$Ztotal = $_GET["Ztotal"] ;
 
$sharh = nl2br("دانلود: $Zdownload\nآپلود: $Zupload\nمدت اتصال: $Zuptime\nکل ترافیک: $Ztotal\n$SMSfooter");


// turn off the WSDL cache

ini_set("soap.wsdl_cache_enabled", "0");
$sms_client = new SoapClient($SMSaddress, array('encoding'=>'UTF-8'));

$parameters['username'] = $SMSusername;
$parameters['password'] = $SMSpassword;
$parameters['to'] = $Zphone ;
$parameters['from'] = $SMSfrom;
$parameters['text'] = str_replace( "<br />", '', $sharh );
$parameters['isflash'] =$SMSisflash ;

$sms_client->SendSimpleSMS2($parameters)->SendSimpleSMS2Result;

?>