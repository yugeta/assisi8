<?php
namespace lib\html;

class page{

  public static $default_page = "service";
  public static $default_file = "index";

  public static $page = null;
  public static $file = null;

  public static $dir = "page/";

  public static function getPage(){
    return self::$page;
  }

  public static function view($file="" , $page=""){
    // 未指定の場合はデフォルト設定
    $page = self::getPagePath($page);
    $file = self::getFilePath($file);

    // キャッシュ保存
    self::$page = $page;
    self::$file = $file;
    
    if(!$page || !$file){
      die("Error : file empty.");
    }

    $path = self::$dir . $page . "/". $file .".html";
    if(!is_file($path)){
      die("Error : file not found. (".$path.")");
    }

    // ファイル選択
    $source = file_get_contents($path);
    echo \mynt::exec("\\lib\\html\\replace","conv" , array($source));
  }


  // ?p=***で実行される表示処理
	public static function contents($contents_file="",$page=""){
    $page = self::getPagePath($page);
		$contents_file = ($contents_file) ? $contents_file : self::$default_file;

		// else{
    $path  = self::$dir . $page."/contents/" . $contents_file .".html";
    $path2 = self::$dir . $page."/contents/" . $contents_file ."/".self::$default_file.".html";

    // load-data
    if(is_file($path)){
      $source = file_get_contents($path);
      return \mynt::exec('\lib\html\replace' , "conv" , array($source));
    }
    // default-root処理
    else if(is_file($path2)){
      $source = file_get_contents($path2);
      return \mynt::exec('\lib\html\replace' , "conv" , array($source));
    }
  }
  
  public static function getPagePath($page=""){
    if($page){
      return $page;
    }
    else if(isset($_GET["p"]) && $_GET["p"] !== ""){
      return $_GET["p"];
    }
    
    // $config  = \mynt::exec('\lib\data\database' , "getSetting");
    // if($config && isset($config["page"])){
    //   return $config["page"];
    // }
    $db = \mynt::data_load('','lib_setting');
    if($db["status"] === "ok" && isset($db["data"]["page"]) && $db["data"]["page"]){
      return $db["data"]["page"];
    }
		
    return self::$default_page;
  }
  
  public static function getFilePath($file=""){
    if($file){
      return $file;
    }
    else if(isset($_GET["f"]) && $_GET["f"] !== ""){
      return $_GET["f"];
    }
    
    // $config  = \mynt::exec("\\lib\\data\\database" , "getSetting");
    // if($config && isset($config["page"])){
    //   return $config["page"];
    // }
		
    return self::$default_file;
  }
  
}