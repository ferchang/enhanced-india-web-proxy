<?php

class Bcrypt {
  
  private $rounds;
  
  //--------------------------------------
  
  public function __construct($rounds = 12) {
  
    if(CRYPT_BLOWFISH != 1) {
      throw new Exception("bcrypt not supported in this installation. See http://php.net/crypt");
    }
	
    $this->rounds = $rounds;
	
  }

  //--------------------------------------
 
  public function hash($input) {
  
	global $pepper;
  
    $hash = crypt(hash('sha256', $pepper.$input), $this->getSalt());
	//bcrypt uses only 72 chars of its input! By prepending a 22 char pepper, only 50 chars of password are left. this seems too limited to me (hamidreza.mz712 -=At=- gmail -=Dot=- com), so i decided to use hash('sha256', $pepper.$input) before feeding input to bcrypt to address this limitation.
 
    if(strlen($hash) > 13)
      return $hash;
 
    return false;
	
  }
  
  //--------------------------------------
 
  public function verify($input, $existingHash) {
  
	global $pepper;
	
    $hash = crypt(hash('sha256', $pepper.$input), $existingHash);
 
    return $hash === $existingHash;
	
  }
  
  //--------------------------------------
 
  private function getSalt() {
  
    $salt = sprintf('$2a$%02d$', $this->rounds);
 
    $bytes = random_bytes(16);
 
    $salt .= $this->encodeBytes($bytes);
	
    return $salt;
	
  }
  
  //--------------------------------------
 
  private function encodeBytes($input) {
  
    // The following is code from the PHP Password Hashing Framework
    $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
 
    $output = '';
    $i = 0;
    do {
      $c1 = ord($input[$i++]);
      $output .= $itoa64[$c1 >> 2];
      $c1 = ($c1 & 0x03) << 4;
      if ($i >= 16) {
        $output .= $itoa64[$c1];
        break;
      }
 
      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 4;
      $output .= $itoa64[$c1];
      $c1 = ($c2 & 0x0f) << 2;
 
      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 6;
      $output .= $itoa64[$c1];
      $output .= $itoa64[$c2 & 0x3f];
    } while (1);
 
    return $output;
	
  }
  
  //--------------------------------------
  
}

?>