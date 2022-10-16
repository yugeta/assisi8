<?php

namespace page\system\contents\media;

class picture{

	// public static $default_page_dir = "data_blog";

	public static $default_pic_path = null;
	public static $default_pic_dir = "media";
	public static function getImageDir(){
		if(self::$default_pic_path === null){
			$setting = \mynt::exec('\lib\data\database','getSetting');
			self::$default_pic_path = "data/".$setting["database"]."/".self::$default_pic_dir."/";
		}
		return self::$default_pic_path;
	}

	public static function getEyecatchFilePath($articleId = ""){
		if($articleId === ""){return "";}

		$page_info_path = self::getImageDir().$articleId.".json";
		if(!is_file($page_info_path)){return "";}

		$jsonPage = json_decode(file_get_contents(self::getImageDir().$articleId.".json") , true);
		if(!isset($jsonPage["eyecatch"]) || !$jsonPage["eyecatch"]){return "";}

		$pic_info_path = self::$default_pic_dir.$jsonPage["eyecatch"].".json";
		if(!is_file($pic_info_path)){return "";}

		$jsonPic = json_decode(file_get_contents($pic_info_path) , true);
		if(!isset($jsonPic["extension"]) || !$jsonPic["extension"]){return "";}

		$pic_file_path = self::$default_pic_dir.$jsonPage["eyecatch"].".".$jsonPic["extension"];
		if(!is_file($pic_file_path)){return "";}

		return $pic_file_path;
	}
}
