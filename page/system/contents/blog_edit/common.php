<?php

namespace page\system\contents\blog_edit;

class common{

	/** Lib **/
	public static $dir = "data/blog/";
	public static $bak = "data/backup/";

	public static function getFileLists($type, $ext="html"){
		$path = self::getType2Dir($type);
		// $path = "data/page/";
		if(!is_dir($path)){return;}

		$lists = array();
		$files = scandir($path);
		for($i=0,$c=count($files); $i<$c; $i++){
			if($files[$i]==="." || $files[$i]===".."){continue;}
			if($ext && !preg_match("/(.+?)\.".$ext."/",$files[$i],$match)){continue;}
			// $lists[] = $files[$i];
			$lists[] = $match[1];
		}
		// print_r($lists);
		return $lists;
	}


	/** HTML proc **/



	// get-value
	public static function getPageInfoString($fileName="", $key=""){
		if($key === "" || $fileName === ""){return;}
		$path = self::$dir;
		if(!is_file($path.$fileName.".json")){return;}
		$json = json_decode(file_get_contents($path."/".$fileName.".json"),true);
		if(!isset($json[$key])){return;}
		return $json[$key];
	}
	// get-picture-value
	public static function getPageInfoString_eyecatch($fileName="", $key=""){
		if($key === "" || $fileName === ""){return;}
		$path = self::$dir.$fileName.".json";
		if(!is_file($path)){return;}
		$json = json_decode(file_get_contents($path),true);
		if(!isset($json["eyecatch"])){return;}
		$path_picture = "data/picture/".$json["eyecatch"].".json";
		if(!is_file($path_picture)){return;}
		$json_picture = json_decode(file_get_contents($path_picture),true);
		if(!isset($json_picture[$key])){return;}
		return $json_picture[$key];
	}
	// date-format(unix2datetime)
	public static function getPageInfoString_datetime($fileName="" , $key=""){
		if($key === "" || $fileName === ""){return;}
		$unix = self::getPageInfoString($fileName , $key);
		return \MYNT\LIB\DATE::format_ymdhis($unix);
	}
	public static function getPageInfoString_schedule($fileName="" , $key=""){
		if($key === "" || $fileName === ""){return self::getCurrentDatetime();}
		$unix = self::getPageInfoString($fileName , $key);
		$date = \MYNT\LIB\DATE::conv($unix);
		// return $unix;
		return $date["year"]."-".$date["month"]."-".$date["date"]."T".$date["hour"].":".$date["minute"];
	}
	public static function getCurrentDatetime(){
		$y = date("Y");
		$m = date("m");
		$d = date("d");
		$h = date("h");
		$i = date("i");
		return $y."-".$m."-".$d."T".$h.":".$i;
	}

	public static function getFileListsOptions($type, $file, $ext="html"){
		// if(!$type){return;}

		$fileNames = self::getFileLists($type, $ext);

		$options = array();
		for($i=0,$c=count($fileNames); $i<$c; $i++){
			// preg_match("/(.+?)\.(.+?)/",$files[$i] , $match);
			$selected = ($file === $fileNames[$i])?"selected":"";
			$viewTitle = self::getPageInfoString($fileNames[$i],"title");
			if(!$viewTitle){$viewTitle = $fileNames[$i].".html";}
			$options[] = "<option value='".$fileNames[$i]."' ".$selected.">".$viewTitle."</option>".PHP_EOL;
		}
		// print_r($options);
		return join("",$options);
	}

	public static function getPageCategoryLists($key=""){
		if(isset($GLOBALS["config"]["pageCategoryLists"][$key])){
			return $GLOBALS["config"]["pageCategoryLists"][$key];
		}
		else if($key === "group"){

		}
		else{
			return array();
		}
	}

	public static function getSetatusListsOptions($file){

		// 登録データの取得
		$val = self::getPageInfoString($file, "status");

		// configデータの取得
		// if(!isset($GLOBALS["config"]["page_status"])){return;}
		// $lists = $GLOBALS["config"]["page_status"];
		$lists = \MYNT\SYSTEM\BLOG_LISTS::getMaster_status();

		// optionタグの作成
		$options = array();
		for($i=0,$c=count($lists); $i<$c; $i++){
			$selected = "";
			if($val !== "" && $val === $lists[$i]["key"]){$selected = " selected";}
			$options[] = "<option value='".$lists[$i]["key"]."'".$selected.">".$lists[$i]["value"]."</option>";
		}
		return join(PHP_EOL,$options);
	}

	public static function getGroupListsOptions($file){

		// 登録データの取得
		$val = self::getPageInfoString($file, "group");

		// configデータの取得
		if(!isset($GLOBALS["config"]["group"])){return;}
		$lists = $GLOBALS["config"]["group"];

		// optionタグの作成
		$options = array();
		for($i=0,$c=count($lists); $i<$c; $i++){
			$selected = "";
			if($val !== "" && $val === $lists[$i]["id"]){$selected = " selected";}
			$options[] = "<option value='".$lists[$i]["id"]."'".$selected.">".$lists[$i]["name"]."</option>";
		}
		return join(PHP_EOL,$options);
	}

	// public static function getTemplateFile($path=""){
	// 	if(!$path || !is_file($path)){return;}
	// 	$temp = file_get_contents($path);
	// 	return \MYNT::exec('\lib\html\replace','conv',array($temp));
	// }

	/** Proc **/

	// [page-edit] load-source-file-data
	public static function getSource($fileName){
		$path = self::$dir;
		$filePath = $path.$fileName.".html";

		$data = "";
		if(is_file($filePath)){
			$data = file_get_contents($filePath);
			$data = str_replace("&lt;","&amp;lt;",$data);
			$data = str_replace("&gt;","&amp;gt;",$data);
			$data = str_replace("<","&lt;",$data);
			$data = str_replace(">","&gt;",$data);
		}
		return $data;
	}

	//
	public static function getType2Dir(){
		return self::$dir;
	}

	//
	public static function setDirSlash($dir){
		if(!preg_match("/.+?\/$/",$dir)){
			$dir .= "/";
		}
		return $dir;
	}

	// page-data-save
	public static function setSystemPage(){
		$current_time = time();
		// データ削除処理
		if($_REQUEST["mode"] === "remove" && isset($_REQUEST["file"]) && $_REQUEST["file"]){
			self::removeData($_REQUEST["file"],$_REQUEST["status"]);
		}
		// adjust-query
		self::adjustSaveQuery($current_time);

		// set-Path
		$default_path  = self::getType2Dir();
		$backupDir     = self::$bak;
		// backup
		self::setData2Backup($_REQUEST["file"],$current_time);
		// data-save
		self::saveSource($_REQUEST["file"],$_REQUEST["source"],$current_time);
		//redirect
		header("Location: ". \MYNT\LIB\URL::getUrl()."?p=".$_REQUEST["p"]."&file=".$_REQUEST["file"]);
	}

	public static function removeData($file,$status){
		self::setPageRemove($file);
		header("Location: ". \MYNT\LIB\URL::getUrl()."?system=blogLists&status=".$status);
		exit();
	}
	public static function setData2Backup($file,$current_time){
		$default_path  = self::getType2Dir();
		$backupDir     = self::$bak;
		// bak-dir
		if(!is_dir($backupDir)){
			mkdir($backupDir.$default_path , 0777 , true);
		}
		if(is_file($default_path.$file.".html")){
			rename($default_path.$file.".html" , $backupDir.$default_path.$file.".html.".$current_time);
		}
		if(is_file($default_path.$file.".json")){
			rename($default_path.$file.".json" , $backupDir.$default_path.$file.".json.".$current_time);
		}
	}
	public static function saveSource($file,$source,$current_time){
		$default_path  = self::getType2Dir();
		// save-dir
		if(!is_dir($default_path)){
			mkdir($default_path , 0777 , true);
		}
		// source-save
		$source = str_replace("\r\n","\n",$source);
		$source = str_replace("\r","\n",$source);
		file_put_contents($default_path.$file.".html" , $source);

		// info-save
		$info = self::getRequest2Info($current_time);
		$json = json_encode($info, JSON_PRETTY_PRINT);
		$json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);
		file_put_contents($default_path.$file.".json" , $json);
	}
	public static function adjustSaveQuery($current_time){
		// file-name
		$_REQUEST["file"]        = (isset($_REQUEST["file"]) && $_REQUEST["file"]) ? $_REQUEST["file"] : $current_time;
		$_REQUEST["title"]       = (isset($_REQUEST["title"]))       ? $_REQUEST["title"]       : "";
		$_REQUEST["discription"] = (isset($_REQUEST["discription"])) ? $_REQUEST["discription"] : "";
		$_REQUEST["source"]      = (isset($_REQUEST["source"]))      ? $_REQUEST["source"]      : "";
		$_REQUEST["eyecatch"]    = (isset($_REQUEST["eyecatch"]))    ? $_REQUEST["eyecatch"]    : "";
		$_REQUEST["eyecatch_ext"]= (isset($_REQUEST["eyecatch_ext"]))? $_REQUEST["eyecatch_ext"]: "";
		$_REQUEST["status"]      = (isset($_REQUEST["status"]))      ? $_REQUEST["status"]      : "";
		$_REQUEST["tag"]         = (isset($_REQUEST["tag"]))         ? $_REQUEST["tag"]         : "";
		$_REQUEST["group"]       = (isset($_REQUEST["group"]))       ? $_REQUEST["group"]       : "";
		$_REQUEST["regist"]      = (isset($_REQUEST["regist"]))      ? $_REQUEST["regist"]      : $current_time;
		$_REQUEST["schedule"]    = (isset($_REQUEST["schedule"]))    ? $_REQUEST["schedule"]    : $current_time;
		$_REQUEST["type"]        = (isset($_REQUEST["type"]))        ? $_REQUEST["type"]        : "";
		$_REQUEST["category"]    = (isset($_REQUEST["category"]))    ? $_REQUEST["category"]    : "";

		$_REQUEST["uid"]         = (isset($_REQUEST["uid"]))         ? $_REQUEST["id"]          : $_SESSION["id"];
	}
	public static function getRequest2Info($current_time){
		return array(
			"id"          => $_REQUEST["file"],
			"title"       => $_REQUEST["title"],
			"discription" => $_REQUEST["source"],
			"source"      => $_REQUEST["source"],
			"eyecatch"    => $_REQUEST["eyecatch"],
			"eyecatch_ext"=> $_REQUEST["eyecatch_ext"],
			"type"        => $_REQUEST["type"],
			"status"      => $_REQUEST["status"],
			"schedule"    => (string) \MYNT\LIB\DATE::getType2Unix($_REQUEST["schedule"]),
			"tag"         => $_REQUEST["tag"],
			"group"       => $_REQUEST["group"],
			"category"    => $_REQUEST["category"],
			"regist"      => (string) $_REQUEST["regist"],
			"uid"         => $_REQUEST["uid"],
			"update"      => (string) $current_time
		);
	}

	// public static function formatStringDate2unixDate($str){
	// 	return $str;
	// }


	public static function setPageRemove($file){
		$current_time = date("YmdHis");
		$default_path  = self::getType2Dir($type);
		$htmlPath = $default_path.$file.".html";
		$infoPath = $default_path.$file.".json";
		// backup-dir
		if(!is_dir(self::$bak .$default_path)){
			mkdir(self::$bak .$default_path , 0777 , true);
		}
		// html
		if(isset($htmlPath)){
			rename($htmlPath , self::$bak.$htmlPath.".".$current_time);
		}
		// info
		if(isset($infoPath)){
			rename($infoPath , self::$bak.$infoPath.".".$current_time);
		}
	}


	/**
	* statusが「trash」の時のみ「removeボタンが表示される」
	*/
	public static function setRemoveButton(){
		if(!isset($_REQUEST["file"])){return "none";}

		$status = self::getPageInfoString($_REQUEST["file"], "status");
		if($status==="trash"){
			return "inline-block";
		}
		else{
			return "none";
		}
	}

	/**
	*
	*/
	public static function saveTempSourceValue(){
		// echo date("YmdHis").PHP_EOL;
		// echo $_REQUEST["source"];
		$data = array(
			"date"   => date("YmdHis"),
			"source" => $_REQUEST["source"]
		);
		// file_put_contents();
		exit();
	}

}
