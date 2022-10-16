<?php
namespace lib\blog\php;

class type{
  public static $table_name = "blog/type";

  public static function load(){
    $res = \mynt::data_load($GLOBALS["page"]["page"],self::$table_name,[],[],["id"=>"SORT_ASC"]);
    if($res["status"] === "ok"){
      return $res["data"];
    }
    else{
      return array();
    }
  }

  public static function save($name){
    $data = array(
      "name" => $name
    );
    $res = \mynt::data_save($GLOBALS["page"]["page"],self::$table_name,$data);
    return $res;
  }

  public static function html_view_select($select=""){
    $datas = self::load();
    if(!$datas || !count($datas)){return;}
    $html = "<select id='type'>";
    for($i=0; $i<count($datas); $i++){
      $selected = $select && $select == $datas[$i]["id"] ? " selected" : "";
      $html .= "<option value='".$datas[$i]["id"]."'".$selected.">".$datas[$i]["name"]."</option>";
    }
    $html .= "<select>";
    return $html;
  }
}