<?php
namespace lib\master;

class db{

  public static function getData_tableSetting($page_name="",$table_name=""){
    if(!$page_name || !$table_name){return;}
    return \mynt::exec('\lib\data\database','getTableSetting',array($table_name));
  }

  // ----------
  // Header
  // $tag @ ["th" , "td" , "li" , "div"]
  public static function header($tag="",$page_name="",$table_name="",$mode="",$keys=array()){
    $res = self::getData_tableSetting($page_name , $table_name);
    if($res){
      return self::settingData2header($tag , $res , $mode);
    }
    else{
      return "";
    }
  }
  public static function header_count($page_name="",$table_name="",$add_count=0){
    $res = self::getData_tableSetting($page_name , $table_name);
    if($res){
      $cnt = 0;
      foreach($res["columns"] as $key => $data){
        if($key === "entry"){continue;}
        $cnt++;
      }
      return $cnt + (int)$add_count;
    }
    else{
      return "";
    }
  }


  public static function settingData2header($tag="" , $setting_data=null , $mode=""){
    if(!$tag || !$setting_data){return;}
    $html = "";
    foreach($setting_data["columns"] as $key => $data){
      if($key === "entry"){continue;}
      $name = "";
      switch($mode){
        case "key":
          $name = $key;
          break;
        case "name":
        default: 
          $name = isset($data["name"]) ? $data["name"] : $key;
          break;
      }
      $html .= "<".$tag." class='".$key."'>";
      $html .= $name;
      $html .= "</".$tag.">".PHP_EOL;
    }
    return $html;
  }


  // ----------
  // Lists
  // $tag @ ["tr" , "li" , "div" , ""]
  public static function lists_html($tag="",$page_name="",$table_name="",$keys=array()){
    $setting_data = self::getData_tableSetting($page_name , $table_name);
    $datas = self::data_load($page_name , $table_name);
    if($datas["status"] === "ok"){
      switch($tag){
        case "tr":
          return self::lists_view_tr($page_name , $datas["data"] , $setting_data);
      }
    }
    else{
      return "";
    }
  }

  public static function lists_view_tr($page_name="" , $datas=null , $setting_data=null){
    if(!$datas || !$setting_data){return;}
    $html = "";
    foreach($datas as $num => $data){
      $id_value = isset($data["id"]) ? " data-id='".$data["id"]."'" : "";
      $html .= "<tr ".$id_value.">";
      foreach($setting_data["columns"] as $key => $setting){
        if($key === "entry"){continue;}
        $view = isset($data[$key]) ? $data[$key] : "";
        $data_value = "";
        if(isset($setting["target"]) && $setting["target"]){
          $res = self::data_load($page_name , $setting["target"]);
          if($res && $res["status"] === "ok"){
            for($i=0; $i<count($res["data"]); $i++){
              $id      = isset($res["data"][$i]["id"]) ? $res["data"][$i]["id"] : "";
              $key_val = isset($data[$key]) ? $data[$key] : "";
              if(!$id
              || !$key_val
              || $id != $key_val){continue;}
              $view = $res["data"][$i]["name"];
              // $view = $res["data"][$i]["id"] ."/". json_encode($res["data"][$i] , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
              // $view = 
              $data_value = "data-value='".$id."'";
              break;
            }
          }
        }
        $html .= "<td data-name='".$key."' ".$data_value.">".$view."</td>";
      }
      $html .= "</tr>";
    }
    return $html;
  }

  public static $data_cache = array();
  public static function data_load($page_name="" , $table_name="" , $select=array() , $where=array()){
    if(!$table_name){return;}
    $key = $page_name."_".$table_name;
    if($where || !isset(self::$data_cache[$key])){
      self::$data_cache[$key] = \mynt::data_load(
        $page_name,
        $table_name,
        $select,
        $where,
        ["id"=>"SORT_ASC"]
      );

    }
    return self::$data_cache[$key];
  }
  public static function data_load_html($page_name="" , $table_name=""){
    $where = isset($_POST["where"]) ? json_decode($_POST["where"] , true) : array();
    $setting_data = self::getData_tableSetting($page_name , $table_name , $where);
    $res = self::data_load($page_name , $table_name , array() , $where);
    if($res && $res["status"] === "ok"){
      return self::lists_view_tr($page_name , $res["data"] , $setting_data);
    }
    else{
      return "";
    }
  }


  // ----------
  // modal
  // $tag @ ["td" , "li" , "div" , ""]
  public static function modal($tag="",$page_name="",$table_name="",$keys=array()){
    $res = self::getData_tableSetting($page_name , $table_name);
    if($res){
      return self::settingData2modal($page_name , $tag , $res);
    }
    else{
      return "";
    }
  }
  public static function settingData2modal($page_name="" , $tag="" , $setting_data=null){
    if(!$tag || !$setting_data){return;}
    $html = "";
    foreach($setting_data["columns"] as $key => $data){
      if($key === "entry"){continue;}

      $type = "text";
      if($key === "id" && isset($data["option"]) && strstr($data["option"],"AUTO_INCREMENT",true)){
        $type = "hidden";
      }

      $mode = $data["type"];
      $target = "";
      if(isset($data["target"])){
        $mode = "TARGET";
        $target = $data["target"];
      }
      $data_name = isset($data["name"]) ? $data["name"] : "";

      $html .= self::modal_input_tag($page_name , $tag , $mode , $type , $key , $data_name , $target);
    }
    return $html;
  }

  public static function modal_input_tag($page_name="" , $tag="" , $mode , $type , $key , $value , $target=""){
    $html = $tag ? "<".$tag." class='modal-form'>" : "";
    if($type !== "hidden"){
      $html .= "<span class='label'>".$value."</span>";
    }
    switch($mode){
      case "INT":
        $html .= "<input type='number' name='".$key."' placeholder='".$value." (int)'>";
        break;
      case "FLOAT":
        $html .= "<input type='number' step='0.1' name='".$key."' placeholder='".$value." (float)'>";
        break;

      case "VARCHAR":
        $html .= "<input type='text' name='".$key."' placeholder='".$value."'>";
        break;

      case "TEXT":
        $html .= "<textarea type='text' name='".$key."' placeholder='".$value."'></textarea>";
        break;
      case "TARGET":
        $html .= self::view_select($page_name , $target , $value , $key , "");
    }
    $html .= $tag ? "</".$tag.">" : "";
    $html .= PHP_EOL;
    return $html;
  }

  public static function view_select($page_name , $table_name , $default_value="" , $name , $value="" , $className=""){
    $datas = self::data_load($page_name , $table_name);
    $html  = "<select name='".$name."' class='".$className."'>";
    $html  .= "<option value=''>".$default_value." (※選択してください)</option>";
    if($datas["status"] === "ok"){
      // foreach($datas["data"] as $data){
      //   $select = $value && $value == $data["id"] ? " selected" : "";
      //   $html .= "<option value='".$data["id"]."'".$select.">".$data["name"]."</option>";
      // }
      $html .= self::view_option($page_name , $table_name , $value);
    }
    $html .= "</select>";
    $html .= PHP_EOL;
    return $html;
  }

  public static function view_option($page_name="" , $table_name="" , $value=""){
    $page_name = $page_name ? $page_name : $GLOBALS["page"]["page"];
    if(!$page_name || !$table_name){return;}
    $datas = self::data_load($page_name , $table_name);
    $html = "";
    if($datas["status"] === "ok"){
      foreach($datas["data"] as $data){
        $id   = isset($data["id"])   ? $data["id"]   : "";
        $name = isset($data["name"]) ? $data["name"] : "";
        $select = $value !== "" && $value == $id ? " selected" : "";
        $html .= "<option value='".$id."'".$select.">".$name."</option>";
      }
    }
    $html .= PHP_EOL;
    return $html;
  }

  // ----------
  // データ操作
  public static function data_save($page_name="",$table_name=""){
    if(!$table_name){return;}
    if(!isset($_POST["json"])){return;}
    $data = json_decode($_POST["json"] , true);
    if(isset($data["id"]) && $data["id"] === ""){
      unset($data["id"]);
    }
    $data["entry"] = date("YmdHis");
    return \mynt::data_save($page_name , $table_name , $data);
  }

  public static function data_save_html($page_name="",$table_name=""){
    $setting_data = self::getData_tableSetting($page_name , $table_name);
    if(!$setting_data){return;}
    $res = self::data_save($page_name , $table_name);
    if($res && $res["status"] === "ok"){
      $res["html"] = self::lists_view_tr($page_name  , [$res["data"]] , $setting_data);
    }
    return json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  public static function data_del($page_name="",$table_name="" , $id=""){
    if(!$table_name || !$id){return;}
    $where = array(
      "id" => $id
    );
    $res = \mynt::data_del($page_name , $table_name , [] , $where);
    return json_encode($res , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
  
}