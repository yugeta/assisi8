<?php
namespace page\system\contents\blog;

class lists{

  // Article-lists (table-tr)
  public static $page_counts = 10;
  public static function getCounts(){
    return self::$page_counts;
  }
	public static function viewPageLists_tr($status="" , $page_num=1 , $category=""){
    $page_num = ($page_num <= 0) ? 1 : $page_num;
    // $category = $category ? $category : "1";
    $category = \mynt::exec('\page\system\contents\blog\category','getDefaultCategory');

    $statusMaster = \mynt::exec('\page\system\contents\blog\status','getMaster');
    $lists = \mynt::exec('\page\system\contents\blog\common' , 'getLists' , array($status,$category));
		$html = "";
		$st = self::$page_counts * ($page_num -1);
		$ed = ($st + self::$page_counts);
    $ed = (count($lists) < $ed)?count($lists) : $ed;
    $num = 0;
    $cnt = 0;
    foreach($lists as $data){
      if($st > $num || $ed <= $num){
        $num++;
        continue;
      }
      else{
        $num++;
      }

      $fileName   = $data["file"];
			$title      = (isset($data["title"]))?$data["title"] : "<b class='string-blue'>File:</b> ".$data["file"].".html";
			$update     = $data["entry"];
			$release    = (isset($data["date"]))? $data["date"] : "";
      $listStatus = \mynt::exec('\page\system\contents\blog\common','getKey2Value',array($statusMaster , $data["status"]));
      
      // eyecatch
      // $eyecatchPicPath   = (isset($data["image"]) && $data["image"]) ? "data/picture/".$data["image"] : "";
      // $eyecatchDir  = \mynt::exec('\lib\media\common','getDir');
      $eyecatchPath = $data["image"];

			if($eyecatchPath && is_file($eyecatchPath) ){
				$eyecatch = "<img class='eyecatch-mini' src='".$eyecatchPath."'>";
			}
			else{
				$eyecatch = "<div class='eyecatch-mini'></div>";
			}

			// group-data
      // $groupData = \mynt::exec('\page\system\contents\blog\group','getData');
      $groupData = array();
      
			$group = "";
      if(isset($data["group_id"])
      && $data["group_id"]
      && isset($groupData[$data["group_id"]])){
        $group = $groupData[$data["group_id"]]["name"];
      }

      $querys = self::getQueryArray(array(
        "c"        => "blog_edit/index",
        "id"       => $data["id"],
        "file"     => $fileName,
        "status"   => $status,
        "category" => $category
      ));
      $url = "?".join("&",$querys);
      
			$html .= "<tr class='titleList' onclick='location.href=\"".$url."\"'>".PHP_EOL;
			$html .= "<th style='width:50px;'>". ($st + $cnt + 1) ."</th>".PHP_EOL;
			$html .= "<td>".$eyecatch."</td>".PHP_EOL;
			$html .= "<td>".$group."</td>".PHP_EOL;
			$html .= "<td>".$title."</td>".PHP_EOL;
			$html .= "<td>".$listStatus."</td>".PHP_EOL;
      $html .= "<td>".\mynt::exec('\lib\common\date','ymdhis2format',array($release))."</td>".PHP_EOL;
      // $html .= "<td>".$release."</td>".PHP_EOL;
			// $html .= "<td>".MYNT_DATE::format_ymdhis($update)."</td>".PHP_EOL;
			// $html .= "<td>".MYNT_DATE::format_ymdhis($regist)."</td>".PHP_EOL;
      $html .= "</tr>".PHP_EOL;
      $cnt++;
    }

		return $html;
  }
  
  public static function getQueryArray($options = array()){
    $querys = array();
    // page
    if(isset($options["p"])){
      array_push($querys , "p=".$options["p"]);
    }
    else if(isset($_GET["p"])){
      array_push($querys , "p=".$_GET["p"]);
    }
    if($options){
      foreach($options as $key => $val){
        array_push($querys , $key ."=". $val);
      }
    }
    return $querys;
  }


}