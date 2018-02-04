<?php
/*		   
                     GNU GENERAL PUBLIC LICENSE
		       Version 2, June 1991

   Copyright (C)  2009 Arunava Bhowmick ( http://arunava.in ).
                       Kolkata India
 Everyone is permitted to copy and distribute verbatim copies
 of this license document, but changing it is not allowed.
 If it is run professionally Powered by India web proxy must be mentioned.


If any portion of this section is held invalid or unenforceable under
any particular circumstance, the balance of the section is intended to
apply and the section as a whole is intended to apply in other
circumstances.

It is not the purpose of this section to induce you to infringe any
patents or other property right claims or to contest validity of any
such claims; this section has the sole purpose of protecting the
integrity of the free software distribution system, which is
implemented by public license practices.  Many people have made
generous contributions to the wide range of software distributed
through that system in reliance on consistent application of that
system; it is up to the author/donor to decide if he or she is willing
to distribute software through any other system and a licensee cannot
impose that choice.
*/


// Set execution time : 5 mins
//set_time_limit(300);

error_reporting(0);
// Should be same as defined in java constant file.
// should be between 1-50
$encKey =20;
/*
$myFile = "log.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fclose($fh);
$myFile = "log.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
*/

$line = file_get_contents("php://input");
$encryptEnable = substr($line,0,1);
$line =  substr($line,1);


//fwrite($fh, ":INPUTTTTTTT:".$line.":INPUTTTTTTTTTTT:"); 

if($encryptEnable=="Y"){
$line = deccrypt_string($line);   }


$hostport = substr($line,0,61);
$bodyData = substr($line,61);
$line ='';

$host = substr($hostport,0,50);
$port = substr($hostport,50,10);
$issecure = substr($hostport,60,1);
//fwrite($fh, $host); fwrite($fh, $port);  fwrite($fh, $issecure); 

if($issecure=="Y"){
$host = "ssl://".$host;
}

$fsok = fsockopen(trim($host) , intval(trim($port))); 
if(FALSE == $fsok ) {echo "Target Host not Found/Down"; return ;}
fwrite($fsok, $bodyData ); 
$port ='';$host ='';$hostport= '';$bodyData='';

while ($line = fread($fsok, 25000))
{
if($encryptEnable=="Y")
echo encrypt_string($line);
else
echo $line;
}

fclose($fsok); 
//fclose($fh); 


///////////////////////////////////////////////////////////////////////////////////////

// Sample encrypt.Keeping the ouput size same.
function encrypt_string($input)   
{
global $encKey;   
$line="";
for($i=0;$i<strlen($input);$i++){
$line .= chr(ord($input[$i])+$encKey); 
}
    return $line;   
}   
  
// Sample decrypt.Keeping the ouput size same.
function deccrypt_string($input)   
{   
global $encKey; 
$line="";
for($i=0;$i<strlen($input);$i++){
$line .= chr(ord($input[$i])-$encKey); 
}
    return $line;   
}   
?>