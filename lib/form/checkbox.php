<?php
namespace lib\form;

class checkbox{
  public static $checkbox_cache = array();
  public static function view($group="" , $name="" , $value_json=array()){
    if(!$group || !$name){return "";}

    if(!isset(self::$checkbox_cache[$group])){
      $res = \mynt::data_load('','form',array(),array("group"=>$group),array("id"=>"SORT_ASC"));
      if($res["status"] === "error"){return "";}
// die($value_json);
      // $value_array = ($value_json) ? json_decode($value_json , true) : array();

      $html = "";
      foreach($res["data"] as $num => $data){

        $checked = ($value_json && in_array($data["value"] , $value_json)) ? "checked" : "";
// echo $data["value"]."/".json_encode($value_array).PHP_EOL;
        $html .= $data["html_before"] ? $data["html_before"] : "";
        $html .= "<input type='checkbox' name='".$name."' value='".$data["value"]."' ".$data["attribute"]." ".$checked." />";
        $html .= $data["html_after"] ? $data["html_after"] : "";
        $html .= PHP_EOL;
      }
      self::$checkbox_cache[$group] = $html;
    }
    
    return self::$checkbox_cache[$group];
  }
}