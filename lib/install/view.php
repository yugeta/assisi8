<?php
namespace lib\install;

class view{
  public static function setup(){
    $templatePath = "lib/install/index.html";
    $source = file_get_contents($templatePath);
    echo \mynt::exec("\\lib\\html\\replace" , "conv" , $source);
  }

  // database一覧の取得(optionタグ)
  public static function list_database_options(){
    $data_dir = \mynt::exec('\lib\data\database','getDir');
    if(!$data_dir || !is_dir($data_dir)){return;}
    $lists = array_diff(scandir($data_dir) , array(".",".."));

    $setting_database = \mynt::exec('\lib\data\database','getSetting');
    $current_database = (isset($setting_database["database"])) ? $setting_database["database"] : "";

    $html = "";
    foreach($lists as $list){
      if(!is_dir($data_dir.$list)){continue;}
      if($list === "session"){continue;}
      $selected = ($current_database === $list) ? "selected" : "";
      $name = ($current_database === $list) ? $list." *" : $list;
      $html .= "<option value='".$list."' ".$selected.">".$name."</option>".PHP_EOL;
    }
    return $html;
  }

  // page一覧の取得
  public static function list_page_options(){
    $data_dir = \mynt::exec('\lib\page\setting','getDir');
    if(!$data_dir || !is_dir($data_dir)){return;}

    $lists = array_diff(scandir($data_dir) , array(".",".."));
    $setting_database = \mynt::exec('\lib\data\database','getSetting');
    $current_page = (isset($setting_database["page"])) ? $setting_database["page"] : "";

    $html = "";
    foreach($lists as $list){
      if(!is_dir($data_dir.$list)){continue;}
      if($list === "system"){continue;}
      $selected = ($current_page === $list) ? "selected" : "";
      $setting_page = \mynt::exec('\lib\page\setting','load',array($list));
      $name = $setting_page && isset($setting_page["name"]) ? $setting_page["name"] : $list;
      $name = ($current_page === $list) ? $name." *" : $name;
      $html .= "<option value='".$list."' ".$selected.">".$name."</option>".PHP_EOL;
    }
    return $html;
  }
}