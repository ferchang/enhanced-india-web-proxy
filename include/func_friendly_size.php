<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
if(!defined('CAN_INCLUDE')) exit("<center><h3>Error: Direct access denied!</h3></center>");

function friendly_size($bytes, $no_infin=false) {
	if((int)$bytes===0 and !$no_infin) echo '&infin;';
	else if($bytes<1000) echo "$bytes B";
	else if($bytes<1000000) echo number_format($bytes/1000, 1), ' KB';
	else if($bytes<1000000000) echo number_format($bytes/1000000, 1), ' MB';
	else echo number_format($bytes/1000000, 1), ' GB';
}

?>
