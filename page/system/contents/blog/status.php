<?php
namespace page\system\contents\blog;

class status{

  public static $path = "page/system/contents/blog/config/page_status.json";

  // status情報をマスターファイルから取得
	public static $master_status = null;
	public static function getMaster(){
    if(self::$master_status !== null){return self::$master_status;}
		if(is_file(self::$path)){
			$lists = json_decode(file_get_contents(self::$path) , true);
		}
		else{
			$lists = array();
		}
		self::$master_status = $lists;
		return self::$master_status;
  }


  // status一覧
  // [page-list] status-tab-tag(li) (release , make...)
	public static function getPageCategoryLists_li($key="status" , $category=""){
		if($key===""){return "";}
		$category = $category ? $category : "1";

    $lists  = self::getMaster();
		$status = (isset($_GET["status"])) ? $_GET["status"] : "";

		// optionタグの作成
		$html = "";
		for($i=0,$c=count($lists); $i<$c; $i++){
      $page     = (isset($_GET["p"]))? $_GET["p"] : "";
      $contents = (isset($_GET["c"]))? $_GET["c"] : "";
			$key      = $lists[$i]["key"];
			$link_url = "?p=".$page."&c=".$contents."&status=".$key."&category=".$category;
			$active   = ($key === $status)? $active = "active" : "";
			$html .= "<li class='".$active."'>";
			$html .= "<a class='dropdown-toggle' role='button' aria-haspopup='true' aria-expanded='false' href='".$link_url."'>";
			$html .= $lists[$i]["value"];
			$html .= " (". \mynt::exec('\page\system\contents\blog\common','getCount',array($key , $category)) .")</a>";
			$html .= "</li>";
			$html .= PHP_EOL;
		}
		return $html;
	}
	
	// optionタグ
	public static function getSetatusListsOptions($category="" , $id=""){
		$category = $category ? $category : "1";

		$lists  = self::getMaster();
		$status = (isset($_GET["status"])) ? $_GET["status"] : "";

		// 登録データの取得
		// $value = self::getPageInfoString($file, "status");
		$info = \mynt::exec('\page\system\contents\blog\common','getInfo',array($category , $id));
		// configデータの取得
		// if(!isset($GLOBALS["config"]["page_status"])){return;}
		// $lists = $GLOBALS["config"]["page_status"];
		// $lists = \MYNT\SYSTEM\BLOG_LISTS::getMaster_status();

		// optionタグの作成
		$htmls = array();
		foreach($lists as $list){
			$key = isset($list["key"])   ? $list["key"]   : "";
			$val = isset($list["value"]) ? $list["value"] : "";

			if(isset($info["status"]) && $info["status"] === $key){
				$htmls[] = "<option value='".$key."' selected>* ".$val."</option>";
			}
			else{
				$htmls[] = "<option value='".$key."'>- ".$val."</option>";
			}
		}
		return join(PHP_EOL , $htmls);
	}
}