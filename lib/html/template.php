<?php
namespace lib\html;

class template{
  public static $template = array();
  public static function get($path=""){
    if(!$path || !is_file($path)){return;}
    if(!isset(self::$template[$path])){
      self::$template[$path] = file_get_contents($path);
    }
    return self::$template[$path];
  }
}