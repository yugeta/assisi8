<?php
namespace page\system\contents\blog;

class lists{
  
  // public static $dir = "data/blog/";
	public static $path_blog = "data/blog/";
	public static $data = array();
	public static $max_pagenation = 10;

	/** Library **/

	// public static function getDefaultKey_type(){
	// 	return $GLOBALS["config"]["pageCategoryLists"]["type"][0]["key"];
	// }

	// get type-info [data/config/pageCategoryLists.json] keys[ key , value , dir , baseFile , SCRIPT_NAME ]
	public static function getType2Info($key = ""){
		$data = array();
		$types = $GLOBALS["config"]["pageCategoryLists"]["type"];
		for($i=0,$c=count($types); $i<$c; $i++){
			if($types[$i]["key"] === $key){
				$data = $types[$i];
			}
		}
		return $data;
	}

	public static function getFileName2ID($path){
		$sp0 = explode("/",$path);
		$sp1 = explode(".",$sp0[count($sp0)-1]);
		$sp2 = array_pop($sp1);
		return join(".",$sp1);
	}

	public static function getKey2Value($data , $key){
		$res = "--";
		for($i=0; $i<count($data); $i++){
			if(isset($data[$i]["key"]) && $data[$i]["key"] === $key){
				$res = $data[$i]["value"];
				break;
			}
		}
		return $res;
	}

	public static function getGroupData(){
		if(isset(self::$data["group"])){return self::$data["group"];}
		if(!isset($GLOBALS["config"]["group"]) || !count($GLOBALS["config"]["group"])){return null;}

		self::$data["group"] = array();

		for($i=0,$c=count($GLOBALS["config"]["group"]); $i<$c; $i++){
			self::$data["group"][$GLOBALS["config"]["group"][$i]["id"]] = $GLOBALS["config"]["group"][$i];
		}
		return self::$data["group"];
	}

	public static function getPageInfoFromPath($path){
		$datas = array();
		if(is_file($path)){
			$datas = json_decode(file_get_contents($path),true);
		}
		return $datas;
	}

	public static function getPageCount($status=""){
		// if($type===""){$type=self::getDefaultKey_type();}
		$lists = self::getPageLists($status);
		// echo "<pre>";
		// print_r($lists);
		// echo "</pre>";
		return count($lists);
	}

	/**
	* *blank : without remove
	* all : all
  */
  public static $pageLists = array();
	public static function getPageLists($status=""){

		$dir = self::$path_blog;

		$datas = array();

		if(!is_dir($dir)){return $datas;}

		$lists = scandir($dir);

		for($i=0,$c=count($lists); $i<$c; $i++){
			if($lists[$i]==="." || $lists[$i]===".."){continue;}

			if(!preg_match("/^(.+?)\.html$/",$lists[$i],$match)){continue;}

			// page-info
			$pageInfo = self::getPageInfoFromPath($dir . $match[1] . ".json");

			// check
			if($status === ""){
				$datas[] = $lists[$i];
			}
			else if($status === "unregist" && (!isset($pageInfo["status"]) || !$pageInfo["status"])){
				$datas[] = $lists[$i];
			}
			else if(isset($pageInfo["status"]) && $status === $pageInfo["status"]){
				$datas[] = $lists[$i];
			}
		}
		return $datas;
  }
  
	public static $articleLists_cache = array();
	public static function getArticleLists($status="release" , $standard="date" , $sort="ascending"){
		if(!is_dir(self::$path_blog)){return array();}
		if(isset(self::$articleLists_cache[$status])){return self::$articleLists_cache[$status];}

		$lists = scandir(self::$path_blog);
		$datas = array();

		for($i=0,$c=count($lists); $i<$c; $i++){
			if($lists[$i]==="." || $lists[$i]===".."){continue;}

			if(preg_match("/^(.+?)\.json$/",$lists[$i],$m)){

				$json = self::getPageInfoFromPath(self::$path_blog.$lists[$i]);

				$json["status"] = (isset($json["status"]) && $json["status"]) ? $json["status"] : "unregist";
				if($status !== "" && $status !== $json["status"]){continue;}

				// standard処理 [ date , title ] ※昇順で配列に追加
				if($standard === "date"){
					// $datasは array( [dates,fileName] );の構造になる
					$datas = self::setStanderd_date($datas , $json , $lists[$i]);
				}
				else{
					$datas[] = $lists[$i];
				}
			}
		}

		// 日付処理
		if($standard === "date"){
			$tmpData = array();
			for($i=0,$c=count($datas); $i<$c; $i++){
				$tmpData[] = $datas[$i]["fileName"];
			}
			$datas = $tmpData;
		}

		// sort処理
		if($sort === "discending"){
			$datas = array_reverse($datas);
		}
		self::$articleLists_cache[$status] = $datas;
		return $datas;
	}
	public static function setStanderd_date($data , $json , $fineName){
		$newArray = array();
		$json_date = self::getArticleDate($json);
		// 初回は普通に追加
		if(!count($data)){
			$newArray[] = array("date" => $json_date , "fileName"  => $fineName);
		}
		// $jsonの日付よりも新しい場合は、その手前に追加する。
		else{
			$flg = 0;
			for($i=0,$c=count($data); $i<$c; $i++){
				// 追加データが既存データよりも新しい場合は、追加データを追加してから既存データを追加する
				if($flg === 0 && (int)$json_date < (int)$data[$i]["date"]){
						$newArray[] = array("date"  => $json_date , "fileName"  => $fineName);
						$flg++;
				}
				$newArray[] = $data[$i];
			}
			if($flg===0){
				$newArray[] = array("date"  => $json_date , "fileName"  => $fineName);
			}
		}
		return $newArray;
	}


	// status情報をマスターファイルから取得
	public static $master_status = null;
	public static function getMaster_status(){
    if(self::$master_status !== null){return self::$master_status;}
    $config_path = "page/system/contents/blog/config/page_status.json";
		if(is_file($config_path)){
			$lists = json_decode(file_get_contents($config_path) , true);
		}
		else{
			$lists = array();
		}
		self::$master_status = $lists;
		return $lists;
	}


	// [page-list] status-tab-tag(li) (release , make...)
	public static function getPageCategoryLists_li($key="status"){
		if($key===""){return "";}

		$lists = self::getMaster_status();
		$status = (isset($_GET["status"])) ? $_GET["status"] : "";
    $url = \mynt::exec('\lib\url\common','getUrl');

		// optionタグの作成
		$html = "";
		for($i=0,$c=count($lists); $i<$c; $i++){
			$query = array();
			$page = (isset($_GET["p"]))? $_GET["p"] : "";
			$key = $lists[$i]["key"];

			$link_url = $url."?p=".$page."&status=".$key;

			$active = ($key === $status)? $active = "active" : "";

			$html .= "<li role='presentation' class='".$active."'>";
			$html .= "<a class='dropdown-toggle' role='button' aria-haspopup='true' aria-expanded='false' href='".$link_url."'>";
			$html .= $lists[$i]["value"];
			$html .= " (".self::getPageCount($key).")</a>";
			$html .= "</li>";
			$html .= PHP_EOL;
		}
		return $html;
	}


  // Article-lists (table-tr)
  public static $page_counts = 10;
	public static function viewPageLists_tr($status="" , $page_num=1){
    
		$page_num   = ($page_num <= 0) ? 1 : $page_num;

		$statusMaster = self::getMaster_status();

		$dir = self::$path_blog;

		$lists = self::getArticleLists($status,"date","discending");

		$html = "";

		$st = self::$page_counts * ($page_num -1);
		$ed = ($st + self::$page_counts);
		$ed = (count($lists) < $ed)?count($lists) : $ed;
		for($i=$st; $i<$ed; $i++){
			$fileName   = self::getFileName2ID($lists[$i]);
			$htmlFile   = $dir.$lists[$i];
			$infoFile   = $dir.$fileName.".json";
			$info       = self::getPageInfoFromPath($infoFile);
			$title      = (isset($info["title"]))?$info["title"] : "<b class='string-blue'>File:</b> ".$lists[$i];
			$update     = (isset($info["update"]))?$info["update"]:filemtime($dir.$lists[$i]);
			$regist     = (isset($info["regist"]))?$info["regist"]:"-";
			$release    = (isset($info["schedule"]))? $info["schedule"] : "";
			$listStatus = self::getKey2Value($statusMaster , $info["status"]);

			// eyecatch
			$eyecatchPicPath   = "";
			if(isset($info["eyecatch"]) && $info["eyecatch"]){
				$eyecatchInfoPath = "data/picture/".$info["eyecatch"].".json";
				if(is_file($eyecatchInfoPath)){
					$eyecatchInfoData = json_decode(file_get_contents($eyecatchInfoPath) , true);
					if(isset($eyecatchInfoData["extension"]) && $eyecatchInfoData["extension"]){
						$eyecatchPicPath = "data/picture/".$info["eyecatch"].".".$eyecatchInfoData["extension"];
					}
				}
			}
			if($eyecatchPicPath !== ""){
				$eyecatch = "<img class='eyecatch-mini' src='".$eyecatchPicPath."'>";
			}
			else{
				$eyecatch = "";
			}

			// group-data
			$groupData = self::getGroupData();
			$group      = (isset($groupData[$info["group"]]))?$groupData[$info["group"]]["name"]:"";

			$html .= "<tr class='titleList' onclick='location.href=\"?p=blog/edit&file=".$fileName."\"'>".PHP_EOL;
			$html .= "<th style='width:50px;'>".($i+1)."</th>".PHP_EOL;
			$html .= "<td>".$eyecatch."</td>".PHP_EOL;
			$html .= "<td>".$group."</td>".PHP_EOL;
			$html .= "<td>".$title."</td>".PHP_EOL;
			$html .= "<td>".$listStatus."</td>".PHP_EOL;
			$html .= "<td>".\mynt::exec('≠lib\common\date','format_ymdhis',array($release))."</td>".PHP_EOL;
			// $html .= "<td>".MYNT_DATE::format_ymdhis($update)."</td>".PHP_EOL;
			// $html .= "<td>".MYNT_DATE::format_ymdhis($regist)."</td>".PHP_EOL;
			$html .= "</tr>".PHP_EOL;
		}

		return $html;
	}

	// 記事情報から公開日を取得する
	public static function getArticleDate($json){
		if(isset($json["schedule"]) && $json["schedule"] !== ""){
			return $json["schedule"];
			return \MYNT\LIB\DATE::ymdhis2unix($json["schedule"]);
		}
		else if(isset($json["update"]) && $json["update"] !== ""){
			return $json["update"];
		}
		else{
			return "";
		}
	}

	// pagenationの表示
	public static function viewPagenation_li($status="",$page_num=0){
		$page_num = ($page_num <= 0 || !$page_num)?1:$page_num;
		$lists = self::getArticleLists($status,"date","discending");
		$system_pageList_count = self::$page_counts;
		$pagenation_count = (count($lists) / $system_pageList_count);

    $baseurl = \mynt::exec('\lib\url\common','getUrl');
		$html  = "";
		for($i=0; $i<$pagenation_count; $i++){
			$num = ($i+1);
			$url = $baseurl."?p=".$_REQUEST["p"]."&page=".$num."&status=".$status;
			$active = ($num == $page_num)?"class='active'":"";
			$html .= "<li ".$active."><a href='".$url."'>".$num."</a></li>";
		}
		return $html;
	}
	public static function getUrl_pagenation_prev($status="release" , $page=1){
    $num = ($page <= 1 || !$page)?1 : ($page -1);
    $url = \mynt::exec('\lib\url\common','getUrl');
		return $url."?p=".$_GET["p"]."&page=".$num."&status=".$status;
	}
	public static function getUrl_pagenation_next($status="" , $page=1){
		$lists = self::getArticleLists($status,"date","discending");
		$system_pageList_count = self::$page_counts;
		$pagenation_count = (count($lists) / $system_pageList_count);
		$page = (!$page)?1 : $page;
    $num = ($page >= $pagenation_count)?$page : $page+1;
    $url = \mynt::exec('\lib\url\common','getUrl');
		return $url."?p=".$_GET["p"]."&page=".$num."&status=".$status;
	}
  

}