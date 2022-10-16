<?php
namespace lib\html;
/**
 * Path    : lib/php/design.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : designテーマに関する表示処理等
 * Example : 
 */

class view{

	
	// // ?p=***で実行される表示処理
	// public static $page_dir = "page/";
	// public static function page($file="" , $page=""){
	// 	// $contents = $contents ? $contents : "sample";
	// 	$page = ($page && is_dir(self::$page_dir.$page)) ? $page : self::getPage();
	// 	$page = ($page) ? $page : "sample";

	// 	// get-dir
	// 	$dir  = self::$page_dir . $page ."/";
	// 	$file = ($file) ? $file : "index";

	// 	// else{
	// 	$path = $dir . $file .".html";

	// 	// load-data
	// 	$source = file_get_contents($path);
	// 	return \mynt::exec("\\lib\\html\\replace","conv" , array($source));
	// }

	// public static function getPage(){
	// 	$config  = \mynt::exec("\\lib\\data\\database" , "getSetting");
	// 	if($config && isset($config["database"])){
	// 		return $config["database"];
	// 	}
	// 	else{
	// 		return "";
	// 	}
	// }





	// // View-Design-frame (query-check -> get-base) -- contents.php
	// public static $dir = "design/";
  // public static function design_bak($design="" , $file=""){
	// 	// $design = $design ? $design : \mynt::exec("\lib\main\config","get",array("design"));
	// 	// $design = self::selectDesignFolder($design);
	// 	// $design = $design ? $design : die("Error : design : no-select-database");
	// 	$design = (!$design && isset($_GET["d"]) && $_GET["d"]) ? $_GET["d"] : $design;
	// 	$design = $design ? $design : die("Error : design : no-select-database");

	// 	$file = $file ? $file : "";
	// 	$file = $file ? $file : $_GET["f"];
	// 	$file = $file ? $file : "index";

	// 	// path
	// 	$path = "design/".$design."/" . $file  . ".html";

	// 	// check:404
	// 	if(!is_file($path)){
	// 		die("404 (".$path.")");
	// 		$path = "lib/design/404.html";
	// 	}

	// 	// load-template
	// 	$source = file_get_contents($path);

	// 	// convert-strings
	// 	return \mynt::exec("\\lib\\html\\replace" , "conv" , array($source));
	// }


	// public static function selectDesignFolder($design=""){
	// 	if($design !== ""){
	// 		return $design;
	// 	}

	// 	$database_config = \mynt::exec("\\lib\\data\\database" , "getSetting");
	// 	if(!is_dir($database_config["dir"])){
	// 		return "system";
	// 	}

	// 	die("Error : design : no-select-database");
	// }



	

	

	/**
	* Contents
	* 1. ? blog=** / default=** / system=** / etc=**
	* 2. ?b=**&p=** (data/page/base/page.html)
	*/
	
// 	public static function page_bak($file="" , $page=""){

// 		// $contents = $contents ? $contents : "sample";
// 		$page = ($page && is_dir(self::$page_dir.$page)) ? $page : \mynt::exec("\lib\main\config","get",array("page"));
// 		$page = ($page) ? $page : "sample";

// 		// get-dir
// 		$dir  = self::$page_dir . $page ."/";
// 		$file = ($file) ? $file : "index";

// 		// else{
// 		$path = $dir . $file .".html";
// // return $path;
// 		// if(!is_file($path)){
// 		// 	$path = "mynt/lib/html/404.html";
// 		// }

// 		// load-data
// 		$source = file_get_contents($path);
// 		return \mynt::exec("\\lib\\html\\replace","conv" , array($source));
// 	}

	public static function contents_dir(){
		if(isset($GLOBALS["config"]["service"]["path"])
		&& $GLOBALS["config"]["service"]["path"]
		&& is_dir($GLOBALS["config"]["service"]["path"]."page/")){
			return $GLOBALS["config"]["service"]["path"]."page/";
		}
		else{
			return null;
		}
	}

	
}