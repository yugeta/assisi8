<?php
namespace lib\bid;

class common{
  public static function get(){
    $key = "myntpage_bid";
    if(isset($_COOKIE[$key])){
      return $_COOKIE[$key];
    }
    else{
      return null;
    }
  }
}