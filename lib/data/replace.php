<?php
namespace lib\data;

class replace{

  // \r\n -> \n
  public static function crlf2lf($hash_data=null){
    if($hash_data){
      foreach($hash_data as $key => $val){
        if(gettype($val) === "array"){
          $val = self::crlf2lf($val);
          $hash_data[$key] = $val;
        }
        else if(gettype($val) === "string"){
          $val = str_replace("\r\n" , "\n" , $val);
          $hash_data[$key] = $val;
        }
      }
    }

    return $hash_data;
  }
}