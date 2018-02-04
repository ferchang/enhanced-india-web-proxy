<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

$line = file_get_contents("php://input");
$up_size=strlen($line);
$encryptEnable = substr($line,0,1);
$line =  substr($line,1);

if($encryptEnable==="Y") $line = deccrypt_string($line);

$hostport = substr($line,0,61);
$bodyData = substr($line,61);
$line ='';

$host = substr($hostport,0,50);
$port = substr($hostport,50,10);
$issecure = substr($hostport,60,1);

if($issecure==="Y") $host = "ssl://".$host;

?>
