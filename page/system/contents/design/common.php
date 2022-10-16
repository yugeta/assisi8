<?php
namespace page\system\contents\design;

class common{
  public static function save(){
    die("design-save");
  }

  public static $dir = "design/";
  public static function getLists(){
    if(!self::$dir || !is_dir(self::$dir)){return;}
    return array_diff(scandir(self::$dir) , array(".",".."));
  }
  public static function viewLists_li(){
    $lists = self::getLists();
    if(!$lists){return;}

    $setting_page = self::getSetting_page();
// print_r($setting_page);exit();

    $tmp_data = self::template_li();
    preg_match_all("/{{setting\[(.*?)\]}}/" , $tmp_data , $tmp_match);
    $html = "";
    foreach($lists as $dir_name){
      $setting_design = self::getSetting_design($dir_name);
      $setting_design["id"] = $dir_name;

      $tmp = $tmp_data;
      $tmp = str_replace("{{design_id}}" , $dir_name , $tmp);

      if(isset($setting_page["design"]) && $setting_page["design"]
      && $dir_name === $setting_page["design"]){
        $tmp = str_replace("{{active}}" , "1" , $tmp);
      }
      else{
        $tmp = str_replace("{{active}}" , "" , $tmp);
      }
      if($tmp_match && $tmp_match[1]){
        foreach($tmp_match[1] as $key){
          $val = (isset($setting_design[$key])) ? $setting_design[$key] : "";
          $tmp = str_replace("{{setting[".$key."]}}" , $val , $tmp);
        }
      }
      $html .= $tmp;
    }
    return $html;
  }

  public static $file_template_li = "page/system/contents/design/template_li.html";
  public static $cache_template_li = null;
  public static function template_li(){
    if(!self::$file_template_li || !is_file(self::$file_template_li)){return;}
    if(self::$cache_template_li === null){}
    self::$cache_template_li = file_get_contents(self::$file_template_li);
    return self::$cache_template_li;
  }

  public static $setting_file_name = "setting.json";
  public static function getSetting_design($design_id=""){
    if(!$design_id){return;}
    $path = self::$dir . $design_id ."/" . self::$setting_file_name;
    if(!is_file($path)){return;}
    return json_decode(file_get_contents($path),true);
  }


  public static function getSetting_page(){
    // $database_setting = \mynt::exec('\lib\data\database','getSetting');
    // if(!isset($database_setting["page"]) || !$database_setting["page"]){return;}
    $res = \mynt::data_load('','lib_setting');
    // if($res["status"] !== "ok"){return;}
    return ($res["status"] === "ok") ? $res["data"] : null;
    // $path = "page/".$res["data"]["page"]."/".self::$setting_file_name;
    // if(!is_file($path)){return;}
    // $txt = file_get_contents($path);
    // return json_decode($txt , true);
  }

  // databaseで設定されているpageコンテンツのsetting.json(design指定)を変更
  public static function changeDesign(){
    $new_design_id = $_POST["design_id"];
    if(!$new_design_id){return;}
    $current = \mynt::data_load('','lib_setting');
    $data = $current["status"] === "ok" ? $current["data"] : array();
    $data["design"] = $new_design_id;
    $data["entry"]  = date("YmdHis");
    $res = \mynt::data_save('','lib_setting' , $data);
    return $res["status"] === "ok" ? 1 : 0;
  }



}