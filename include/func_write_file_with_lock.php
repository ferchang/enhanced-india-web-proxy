<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
if(!defined('CAN_INCLUDE')) exit("<center><h3>Error: Direct access denied!</h3></center>");

function write_file_with_lock($file, $contents) {
	$fp=fopen($file, 'c');
	flock($fp, LOCK_EX);
	ftruncate($fp, 0);
	fseek($fp, 0);
	fwrite($fp, $contents);
	fflush($fp);
	flock($fp, LOCK_UN);
	fclose($fp);
}

?>
