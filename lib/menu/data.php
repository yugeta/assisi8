<?php
namespace lib\menu;

class data{
  public static function load($type=""){
    $keys   = array();
    $wheres = array("type"=>$type);
    $sorts  = array("id"=>"SORT_ASC");
    return \mynt::data_load("","lib_menu",$keys,$wheres,$sorts);
  }
  public static function save($type=""){
    
  }
}