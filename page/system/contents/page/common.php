<?php
namespace page\system\contents\page;

class common{

  public static function post(){
    if(!isset($_POST["page"])){
      $_POST["error"] = "System error !!! (code:p-001)";
      return;
    }

    $path = self::getSettingPath();
    
    if(is_file($path)){
      $data = json_decode(file_get_contents($path),true);
    }
    else{
      $data = array();
    }
    
    foreach($_POST["page"] as $key => $val){
      $data[$key] = $val;
    }

    $json = json_encode($data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    file_put_contents($path , $json);

    $_POST["success"] = "正常に登録しました。";
    return;
  }

  public static function getValue($key=""){
    if(!$key){return;}
    // $res = \mynt::date_load('','lib_setting',array($key));print_r($res);exit();
    // if($res["status"] === "ok"){
    //   return $res["data"];
    // }
    // else{
    //   return "";
    // }
    
    $path = self::getSettingPath();
    if(!is_file($path)){return;}
    $json = json_decode(file_get_contents($path),true);
    if(!isset($json[$key])){return;}
    return $json[$key];
  }

  public static function getSettingPath(){
    $setting = \mynt::exec('\lib\data\database','getSetting');
    if(!$setting || !isset($setting["database"])){return;}
    // return "page/".$setting["page"]."/setting.json";
    return "data/".$setting["database"]."/lib_setting.json";
  }

}