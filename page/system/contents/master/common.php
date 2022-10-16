<?php
namespace page\system\contents\master;

class common{
  public static function get_tables_option($uid=""){
    if(!$uid){return;}
    $files = \mynt::exec('\lib\common\dir','lists',array("data/".$GLOBALS["page"]["page"]."/tables/","file",0,"/\.json$/"));
    if(!$files){return;}

    $html = "";
    for($i=0; $i<count($files); $i++){
      $key = self::adjust_key($files[$i]);
      $val = $files[$i];
      $html .= "<option value='".$key."'>".$val."</option>".PHP_EOL;
    }
    return $html;
  }

  public static function adjust_key($key=""){
    if(!$key){return;}
    return preg_replace("/\.json$/","",$key);
  }

  private $table_setting_dir = "data/shop/tables/";
  public static function table_info($table_name=""){
    if(!$table_name){return;}

  }


}