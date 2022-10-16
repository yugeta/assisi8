<?php
namespace page\system\php;

class blog{

  public static function viewPageLists_div($page_counts=0 , $page_num=1 , $category=""){
    $page_num = ($page_num <= 0) ? 1 : $page_num;
    $category     = \mynt::exec('\page\system\contents\blog\category' , 'getDefaultCategory');
    $statusMaster = \mynt::exec('\page\system\contents\blog\status'   , 'getMaster');
    $lists        = \mynt::exec('\page\system\contents\blog\common'   , 'getLists' , array('release' , $category , "past"));
    
    if($page_counts == 0){
      $st = 1;
      $ed = count($lists);
    }
    else{
      $st = $page_counts * ($page_num -1);
      $ed = ($st + $page_counts);
      $ed = (count($lists) < $ed)?count($lists) : $ed;
    }

    $template = file_get_contents("page/radio/contents/blog/lists_template.html");
		
    $num  = 0;
    $cnt  = 0;
    $html = "";
    foreach($lists as $data){
      if($st > $num || $ed <= $num){
        $num++;
        continue;
      }
      else{
        $num++;
      }

      $fileName   = $data["file"];
			$update     = $data["entry"];
			$release    = (isset($data["date"]))? $data["date"] : "";
      $listStatus = \mynt::exec('\page\system\contents\blog\common','getKey2Value',array($statusMaster , $data["status"]));
      
      // eyecatch
      $eyecatchPath = $data["image"];

			if($eyecatchPath && is_file($eyecatchPath) ){
				$eyecatch = "<img class='eyecatch-mini' src='".$eyecatchPath."'>";
			}
			else{
        $eyecatch = "<img class='eyecatch-mini' src='page/".$GLOBALS["config"]["page"]."/img/no-image.jpg'>";
				// $eyecatch = "<div class='eyecatch-mini'></div>";
      }
      

			// group-data
      $groupData = array();
			$group     = (isset($data["group_id"]) && $data["group_id"] && isset($groupData[$data["group_id"]])) ? $groupData[$data["group_id"]]["name"] : "";

      $querys = \mynt::exec('\page\system\contents\blog\lists','getQueryArray',array(array(
        "c"        => "blog/view",
        "id"       => $data["id"],
        "category" => $category
      )));
      $url = "?".join("&",$querys);

      $tmp = $template;
      
      $num = ($st + $cnt + 1);
      $date = \mynt::exec('\lib\common\date','ymdhis2format',array($release));
      $tmp = str_replace("{{num}}"      , $num        , $tmp);
      $tmp = str_replace("{{url}}"      , $url        , $tmp);
      $tmp = str_replace("{{eyecatch}}" , $eyecatch   , $tmp);
      $tmp = str_replace("{{group}}"    , $group      , $tmp);
      $tmp = str_replace("{{title}}"    , $data["title"]      , $tmp);
      $tmp = str_replace("{{status}}"   , $listStatus , $tmp);
      $tmp = str_replace("{{date}}"     , $date       , $tmp);


      $html .= $tmp;
      $cnt++;
    }

		return $html;
  }

  public static function view_li($page_counts=0,$page_num=0,$category="1"){
		$page_num = ($page_num <= 0 || !$page_num)?1:$page_num;
    $listsCount = \mynt::exec('\page\system\contents\blog\common','getCount',array("release",$category));
		$system_pageList_count = \mynt::exec('\page\system\contents\blog\lists','getCounts');
		$pagenation_count = ($listsCount / $system_pageList_count);

    $baseurl = \mynt::exec('\lib\url\common','getUrl');

		$html  = "";
		for($i=0; $i<$pagenation_count-1; $i++){
			$num = ($i+1);
			$url = $baseurl."?p=".$_REQUEST["p"]."&c=".$_GET["c"]."&page=".$num."&status=".$status."&category=".$category;
			$active = ($num == $page_num)?"class='active'":"";
			$html .= "<li ".$active."><a href='".$url."'>".$num."</a></li>";
		}
		return $html;
  }


  // ----------
  // view
  public static $data = array();
  public static function data($category="1",$id=""){
    if(!$category || !$id){return;}
    if(!isset(self::$data[$id])){
      $res = \mynt::data_load('','lib_blog',array(),array("category"=>$category,"id"=>$id));
      if($res["status"] !== "ok"){return;}
      if(!$res["data"] || !count($res["data"]) || !$res["data"][0]){return;}
      self::$data[$id] = $res["data"][0];

      $file_id = isset($res["data"][0]["file"]) && $res["data"][0]["file"] ? $res["data"][0]["file"] : $id;

      $source_path = "data/9chat_e/data_blog/".$category."/".$file_id.".html";
      // self::$data[$id]["source"] = $source_path;
      if(is_file($source_path)){
        $source = file_get_contents($source_path);
        self::$data[$id]["source"] = $source;
      }
      else{
        self::$data[$id]["source"] = $source_path;
      }
    }
    return self::$data[$id];
  }

  public static function str($category="1",$id="",$key=""){
    if(!$category || !$id || !$key){return;}
    $data = self::data($category,$id);

    if(!$data){return;}

    switch($key){
      case "eyecatch":
        // $eyecatch = isset($data[$key]) && $data[$key] && is_file($data[$key]) ? $data[$key] : "page/radio/img/no-image.jpg";
        // return "<img class='eyecatch' src='".$eyecatch."' />";
        $eyecatch = isset($data["image"]) && $data["image"] && is_file($data["image"]) ? "<img class='eyecatch' src='".$data["image"]."' />" : "";
        return $eyecatch;
      case "date":
        $date = isset($data["date"]) && $data["date"] ? $data["date"] : "";
        return \mynt::exec('\lib\string\date','ymd2format',array($data["date"]));
      default:
        return isset($data[$key]) ? $data[$key] : "";
    }
  }

}