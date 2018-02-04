<?php
if(ini_get('register_globals')) exit('Error: register_globals is on!');
if(!defined('CAN_INCLUDE')) exit('Error: Direct access denied!');

function duration2friendly_str($t, $c=2, $abb=false, $no_prep=false) {

	if($c===0) $c=100;

	$y=$mth=$d=$h=$m=$s=0;

	$y=(int)floor($t/(365*24*60*60));
	if($y) $t=$t%(365*24*60*60);
	$mth=(int)floor($t/(30*24*60*60));
	if($mth) $t=$t%(30*24*60*60);
	$d=(int)floor($t/(24*60*60));
	if($d) $t=$t%(24*60*60);
	$h=(int)floor($t/(60*60));
	if($h) $t=$t%(60*60);
	$m=(int)floor($t/60);
	$s=$t%60;

//---------------------------

	$n=0;
	$f=false;

	do {

		if($y and $n+1===$c and $mth>=6) {
			$y++;
			$mth=0;
		}

		if($y) $f=true;

		if($f and ++$n===$c) break;

		if(($f or $mth) and $n+1===$c and $d>=15) {
			$mth++;
			$d=0;
		}

		if($mth) $f=true;
		
		if($f and ++$n===$c) break;
		
		if(($f or $d) and $n+1===$c and $h>=12) {
			$d++;
			$h=0;
		}

		if($d) $f=true;

		if($f and ++$n===$c) break;

		if(($f or $h) and $n+1===$c and $m>=30) {
			$h++;
			$m=0;
		}
		
		if($h) $f=true;

		if($f and ++$n===$c) break;

		if(($f or $m) and $n+1===$c and $s>=30) {
			$m++;
			$s=0;
		}

	} while(false);

//---------------------------

while($mth===12 or $d===30 or $h===24 or $m===60) {
	if($mth===12) {
		$y++;
		$mth=0;
	}
	if($d===30) {
		$mth++;
		$d=0;
	}
	if($h===24) {
		$d++;
		$h=0;
	}
	if($m===60) {
		$h++;
		$m=0;
	}
}

//---------------------------

	$n=0;
	$msg='';
	if($c<6 and !$no_prep) $prep='about'.' ';
	else $prep='';

	do {

		if($y) {
			$msg.="$y ".'year';
			if($y>1) $msg.='s';
		}

		if($msg and ++$n===$c) break;

		if($mth) {
			if($y) $msg.=" ".'&'." $mth ".'month';
			else $msg="$mth ".'month';
			if($mth>1) $msg.='s';
		}
		
		if($msg and ++$n===$c) break;
		
		if($d) {
			if($y or $mth) $msg.=" ".'&'." $d ".'day';
			else $msg="$d ".'day';
			if($d>1) $msg.='s';
		}

		if($msg and ++$n===$c) break;

		if($h) {
			if($y or $mth or $d) $msg.=" ".'&'." $h ".'hour';
			else $msg="$h ".'hour';
			if($h>1) $msg.='s';
		}

		if($msg and ++$n===$c) break;

		if($m) {
			if($y or $mth or $d or $h) $msg.=($abb)? " ".'&'." $m ".'min':" ".'&'." $m ".'minute';
			else $msg=($abb)? "$m ".'min':"$m ".'minute';
			if($m>1) $msg.='s';
		}

		if($msg and ++$n===$c) break;

		if($s) {
			if($y or $mth or $d or $h or $m) $msg.=($abb)? " ".'&'." $s ".'sec':" ".'&'." $s ".'second';
			else $msg=($abb)? "$s ".'sec':"$s ".'second';
			if($t>1) $msg.='s';
		}
		
	} while(false);

//---------------------------

	if($msg==='1 day') $msg='24 hours';
	else if($msg==='2 days') $msg='48 hours';
	
	if($msg==='') $msg='0 '.(($abb)? 'secs':'seconds');

	return $prep.$msg;

}

?>