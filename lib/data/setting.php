<?php
namespace lib\data;

class setting{

  public static $setting = null;
  public static function getSetting(){
    if(self::$setting === null){
      $res = \mynt::data_load('','lib_setting');
      if($res["status"] === "ok"){
        self::$setting = $res["data"];
      }
      else{
        self::$setting = array();
      }
    }
    return self::$setting;
    
  }
}