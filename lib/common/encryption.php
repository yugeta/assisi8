<?php
namespace lib\common;

class encryption{
  public static function md5_encode(){

  }

  public static function password_encode(){

  }

  public static function password_decode(){

  }

  public static function crypt_key_encode($str="" , $phrase=""){
    return crypt($str , $phrase);
  }

  public static function sha_base64_encode($pass="" , $phrase=""){
    if($pass === "" || $key === ""){return null;}
    $method = 'AES-256-CBC';
    $iv_size = openssl_cipher_iv_length($method); 
    $iv = openssl_random_pseudo_bytes($iv_size);
    $options = OPENSSL_RAW_DATA;
    $encrypted = openssl_encrypt($pass, $method, $phrase, $options, $iv);
    $base64 = base64_encode($encrypted);
    return $base64;
  }

  public static function sha_base64_decode($pass="" , $phrase=""){
    $iv = base64_decode($pass);
    $encrypted = base64_decode($base64_encrypted);
  }

}