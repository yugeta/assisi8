<?php
namespace lib\blog\php;

class group{
  public static $table_name = "blog/group";

  public static $cache_datas = array();
  public static function load($type=""){
    $type = $type ? (int)$type : 1;
    if(!isset(self::$cache_datas[$type])){
      $where = array(
        "type" => $type
      );
      $res = \mynt::data_load($GLOBALS["page"]["page"],self::$table_name,[],$where,["id"=>"SORT_ASC"]);
      if($res["status"] === "ok"){
        self::$cache_datas[$type] = $res["data"];
      }
      else{
        self::$cache_datas[$type] = array();
      }
    }
    return self::$cache_datas[$type];
  }

  public static function save($type="" , $name){
    $type = $type ? (int)$type : 1;
    $data = array(
      "type" => $type,
      "name" => $name,
      "entry" => date("YmdHis")
    );
    $res = \mynt::data_save($GLOBALS["page"]["page"],self::$table_name,$data);
    return $res;
  }

  public static function html_view_li($type=""){
    $datas = self::load($type);
    $html = "";
    for($i=0; $i<count($datas); $i++){
      // $active = $select && $select == $datas[$i]["id"] ? " data-active='1'" : "";
      $html .= "<li data-id='".$datas[$i]["id"]."'>".$datas[$i]["name"]."</li>".PHP_EOL;
    }
    return $html;
  }

  // selected : $group_id > $sub_id
  public static function html_view_option($type="",$group_id="",$data_id=""){
    if($data_id){
      $group_id = \mynt::exec('\lib\blog\php\data','get_value',array($type,$data_id,"group"));
      // $group_id = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','get_value',array($type,$data_id,"group"));
    }
    
    $datas = self::load($type);
    $html = "";
    for($i=0; $i<count($datas); $i++){
      // if($data_id){
      //   $selected = $data_id == $datas[$i]["id"] ? " selected" : "";
      // }
      // else{
        $selected = $group_id && $group_id == $datas[$i]["id"] ? " selected" : "";
      // }
      $html .= "<option value='".$datas[$i]["id"]."'".$selected.">".$datas[$i]["name"]."</option>".PHP_EOL;
    }
    return $html;
  }
  public static function load_datas($type=""){
    $datas = self::load($type);
    if(!$datas){return;}
    $res = array();
    foreach($datas as $data){
      $res[$data["id"]] = $data;
    }
    return $res;
  }

  public static function load_json($type=""){
    $res = self::load($type);
    if(!$res){return;}
    return json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
}