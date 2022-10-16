<?php
namespace lib\install;

class common{

  // 
  public static function classProperty($class="" , $prop=""){
    // check-class (& require)
    if(!self::checkModuleLoad($class)){
      return;
    }

    // get-string
    $classData = get_class_vars($class);
    if(isset($classData[$prop])){
      return $classData[$prop];
    }
    else{
      return null;
    }
  }
}