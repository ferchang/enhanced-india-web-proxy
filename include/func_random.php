<?php
if(ini_get('register_globals')) exit("<center><h3>Error: Turn that damned register globals off!</h3></center>");
if(!defined('CAN_INCLUDE')) exit("<center><h3>Error: Direct access denied!</h3></center>");

//====================================

$pepper='place a random string here';

define('EXTRA_ENTROPY', sha1(microtime().$pepper.$_SERVER['REMOTE_ADDR'].$_SERVER['REMOTE_PORT'].$_SERVER['HTTP_USER_AGENT'].serialize($_POST).serialize($_GET).serialize($_COOKIE)));

//====================================

function crypt_random($min = 0, $max = 0x7FFFFFFF)
{
    if ($min == $max) {
        return $min;
    }

    if (function_exists('openssl_random_pseudo_bytes')) {
        // openssl_random_pseudo_bytes() is slow on windows per the following:
        // http://stackoverflow.com/questions/1940168/openssl-random-pseudo-bytes-is-slow-php
        if ((PHP_OS & "\xDF\xDF\xDF") !== 'WIN') { // PHP_OS & "\xDF\xDF\xDF" == strtoupper(substr(PHP_OS, 0, 3)), but a lot faster
            extract(unpack('Nrandom', pack('H*', sha1(openssl_random_pseudo_bytes(4).EXTRA_ENTROPY.microtime()))));
            return abs($random) % ($max - $min) + $min; 
        }
    }

    // see http://en.wikipedia.org/wiki//dev/random
    static $urandom = true;
    if ($urandom === true) {
        // Warning's will be output unles the error suppression operator is used.  Errors such as
        // "open_basedir restriction in effect", "Permission denied", "No such file or directory", etc.
        $urandom = @fopen('/dev/urandom', 'rb');
    }
    if (!is_bool($urandom)) {
        extract(unpack('Nrandom', pack('H*', sha1(fread($urandom, 4).EXTRA_ENTROPY.microtime()))));
        // say $min = 0 and $max = 3.  if we didn't do abs() then we could have stuff like this:
        // -4 % 3 + 0 = -1, even though -1 < $min
        return abs($random) % ($max - $min) + $min;
    }
	
	
	if(function_exists('mcrypt_create_iv') and version_compare(PHP_VERSION, '5.3.0', '>=')) {
		@$tmp16=mcrypt_create_iv(4, MCRYPT_DEV_URANDOM);
		if($tmp16!==false) {
			extract(unpack('Nrandom', pack('H*', sha1($tmp16.EXTRA_ENTROPY.microtime()))));
			return abs($random) % ($max - $min) + $min;
		}
	}

	static $seeded;
    if (!isset($seeded) and version_compare(PHP_VERSION, '5.2.5', '<=')) { 
        $seeded = true;
        mt_srand(fmod(time() * getmypid(), 0x7FFFFFFF) ^ fmod(1000000 * lcg_value(), 0x7FFFFFFF));
    }

    extract(unpack('Nrandom', pack('H*', sha1(mt_rand(0, 0x7FFFFFFF).EXTRA_ENTROPY.microtime()))));
    return abs($random) % ($max - $min) + $min;

}

//#########################################################################

function random_bytes($length) {

	$bytes = '';

	for($i = 0; $i < $length; $i++) $bytes.=chr(crypt_random(0, 255));

    return $bytes;
}

//#########################################################################

function random_string($length, $chars=null) {

	if(is_null($chars)) $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

	$random_string='';

	for($i=0; $i<$length; $i++)
	$random_string.=$chars[crypt_random(0, strlen($chars)-1)];

	return $random_string;

}

?>
