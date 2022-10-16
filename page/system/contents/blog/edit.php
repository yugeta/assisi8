<?php
namespace page\system\contents\blog;

class edit{

  // base-dir
  public static function getDir($category=""){
    $category = $category ? $category : "1";
    $setting = \mynt::exec('\lib\data\database','getSetting');
    return "data/".$setting["database"]."/".$category."/";
  }

  


  // get-database-value
	public static function getPageInfoString($key="" , $category="1" , $id=""){
    if(!$key || !$id){return;}
    $category = $category ? $category : "1";
    $info = \mynt::exec('\page\system\contents\blog\common','getInfo',array($category , $id));
// print_r($info);exit();

    if(isset($info[$key])){
      return $info[$key];
    }
    else{
      return "";
    }

		// $dir = self::getDir($category);
		// if(!is_file($dir.$fileName.".html")){return;}
		// $json = json_decode(file_get_contents($path."/".$fileName.".json"),true);
		// if(!isset($json[$key])){return;}
		// return $json[$key];
  }
  


}