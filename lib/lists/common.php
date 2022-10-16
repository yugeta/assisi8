<?php
namespace lib\lists;

class common{

  public static function load_datas($page="",$table="" , $where=""){
    if(!$page || !$table){return;}
    $sort  = array("id" => "SORT_ASC");
    return \mynt::data_load($page , $table , array() , $where , $sort);
  }
  public static function load_jsons($page="" , $table=""){
    if(!$page || !$table){return;}
    $where = isset($_POST["data"]) ? $_POST["data"] : array();
    $res = self::load_datas($page , $table , $where);
    if($res["status"] === "ok"){
      return json_encode($res["data"] , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    else{
      return;
    }
  }

  public static function save($page="",$table="",$id=""){
    if(!$page || !$table){return;}
    $lang = "";
    if(!isset($_POST["data"])){return;}
    $data = $_POST["data"];
    $data["entry"] = date("YmdHis");
    $where = isset($_POST["data"]) ? $_POST["data"] : array();
    if($id){
      $where["id"] = $id;
    }
    return \mynt::data_save($GLOBALS["page"]["page"] , 'q' , $data , $where);
  }
  public static function save_json($lang="",$id=""){
    $res = self::save($lang,$id);
    if($res && $res["status"] === "ok"){
      return json_encode($res["data"] , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    else{
      return;
    }
  }

  public static function del_json($page="",$table=""){
    $where = $_POST["data"];
    $res = \mynt::data_del($page,$table,array(),$where);
    if($res["status"] === "ok"){
      return json_encode($res["data"] , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    else{
      return;
    }
    
  }
}