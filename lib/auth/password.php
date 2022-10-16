<?php
namespace lib\auth;

class password{
  public static function encode($pass=""){
    if(!$pass){return;}
    return password_hash($pass, PASSWORD_DEFAULT);
  }

  public static function check($pass="" , $hash=""){
    if(!$pass || !$hash){return false;}
    
    if(password_verify($pass , $hash)){
      return true;
    }
    else{
      return false;
    }
  }
}