<?php
namespace lib\blog;

class source{

  public static $dirname = "data_blog";
  
  public static function getDir($category=""){
    $category = $category ? $category : "1";
    $setting = \mynt::exec('\lib\data\database','getSetting');
    return "data/".$setting["database"]."/".self::$dirname."/".$category."/";
  }

  public static function getSource($category="" , $id=""){
    if(!$category || !$id){return;}
    $category = $category ? $category : "1";
    
		$info = \mynt::exec('\lib\blog\info','getInfo',array($category,$id));
    if(!$info){return;}

    $file = isset($info["file"]) && $info["file"] ? $info["file"] : $info["id"];

    $setting = \mynt::exec('\lib\data\database','getSetting');
    $dir  = self::getDir($category,$id);
		$path = $dir.$file.".html";
		if(!$path || !is_file($path)){return;}
		$txt = file_get_contents($path);
		return htmlspecialchars($txt);
  }

}