<?php
namespace lib\blog\php;

class status{
  public static $table_name = "blog/status";

  public static $cache_datas = null;
  public static function load(){
    if(self::$cache_datas === null){
      $res = \mynt::data_load($GLOBALS["page"]["page"],self::$table_name,[],[],["id"=>"SORT_ASC"]);
      if($res["status"] === "ok"){
        self::$cache_datas = $res["data"];
      }
      else{
        self::$cache_datas = array();
      }
    }
    return self::$cache_datas;
  }

  public static function save($name){
    $data = array(
      "name" => $name
    );
    $res = \mynt::data_save($GLOBALS["page"]["page"],self::$table_name,$data);
    return $res;
  }

  public static function html_view_li($type="",$group_id=""){
    $status_count_all = self::get_status_counts($type,$group_id);
    $datas = self::load();
    $html = "";
    for($i=0; $i<count($datas); $i++){
      $status_count = isset($status_count_all[$datas[$i]["id"]]) ? $status_count_all[$datas[$i]["id"]] : 0;
      $html .= "<li data-id='".$datas[$i]["id"]."'>".$datas[$i]["name"]."(".$status_count.")</li>".PHP_EOL;
    }
    return $html;
  }
  public static function html_view_option($type="",$id=""){
    $status = \mynt::exec('\lib\blog\php\data','get_value',array($type,$id,"status"));
    // $status = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','get_value',array($type,$id,"status"));
    $datas = self::load();
    $html = "";
    for($i=0; $i<count($datas); $i++){
      $selected = $status == $datas[$i]["id"] ? " selected" : "";
      $html .= "<option value='".$datas[$i]["id"]."'".$selected.">".$datas[$i]["name"]."</option>".PHP_EOL;
    }
    return $html;
  }


  public static function load_datas(){
    $datas = self::load();
    if(!$datas){return;}
    $res = array();
    foreach($datas as $data){
      $res[$data["id"]] = $data;
    }
    return $res;
  }

  public static $release_id = null;
  public static function get_release_id(){
    if(self::$release_id === null){
      $datas = self::load();
      foreach($datas as $data){
        if(isset($data["release"]) && $data["release"] === 1){
          self::$release_id = $data["id"];
          break;
        }
      }
    }
    return self::$release_id;
  }

  public static $status_counts=array();
  public static function get_status_counts($type="",$group_id=""){
    $group_id = $group_id ? $group_id : 0;
    if(!isset(self::$status_counts[$group_id])){
      $res = \mynt::exec('\lib\blog\php\data','load_all',array($type));
      // $res = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','load_all',array($type));
      if($res["status"] === "ok"){
        $datas = array(
          count($res["data"])
        );
        foreach($res["data"] as $data){
          if(!isset($datas[$data["status"]])){$datas[$data["status"]] = 0;}
          $datas[$data["status"]]++;
        }
        self::$status_counts[$group_id] = $datas;
      }
      else{
        self::$status_counts[$group_id] = array();
      }
    }
    return self::$status_counts[$group_id];
  }
  public static function get_all_counts($type="",$group_id=""){
    // $arr = self::get_status_counts($type,$group_id);
    $res = \mynt::exec('\lib\blog\php\data','load_all',array($type));
    if($res["status"] === "ok"){
      $count = 0;
      foreach($res["data"] as $data){
        if($data["status"] === 5){continue;}
          $count++;
      }
      return $count;
    }
    else{
      return 0;
    }
  }
}