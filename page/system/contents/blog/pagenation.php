<?php
namespace page\system\contents\blog;

class pagenation{
  // pagenationの表示
	public static function view_li($status="",$page_num=0,$category=""){
    $category = $category ? $category : "1";
		$page_num = ($page_num <= 0 || !$page_num)?1:$page_num;
    $listsCount = \mynt::exec('\page\system\contents\blog\common','getCount',array($status,$category));
		$system_pageList_count = \mynt::exec('\page\system\contents\blog\lists','getCounts');
		$pagenation_count = ($listsCount / $system_pageList_count);

    $baseurl = \mynt::exec('\lib\url\common','getUrl');

    // 長過ぎる時の対処法（今はまだ...）
    // num=1 : 1,2,3,4,5
    // num=1 : 1,2,3,4,5,6
    // num=1 : 1,2,3,4,5,6,7 -> 1,2,3,4,5...7
    // num=1 : 1,2,3,4,5,6,7,8,9,10 -> 1,2,3,4,5...10
    // num=5 : 1...2,3,5,6,7...10
    // num=7 : 1...6,7,8,9,10

		$html  = "";
		for($i=0; $i<$pagenation_count; $i++){
			$num = ($i+1);
      $p = isset($_REQUEST["p"]) ? $_REQUEST["p"] : "";
      $c = isset($_GET["c"])     ? $_GET["c"]     : "";
			$url = $baseurl."?p=".$p."&c=".$c."&page=".$num."&status=".$status."&category=".$category;
			$active = ($num == $page_num)?"class='active'":"";
			$html .= "<li ".$active."><a href='".$url."'>".$num."</a></li>";
		}
		return $html;
  }



  // １つ手前シフト
	public static function getUrl_prev($status="release" , $page=1){
    $num = ($page <= 1 || !$page)?1 : ($page -1);
    $url = \mynt::exec('\lib\url\common','getUrl');
		return $url."?p=".$_GET["p"]."&c=".$_GET["c"]."&page=".$num."&status=".$status;
  }
  // １つ次にシフト
	public static function getUrl_next($status="" , $page=1){
    $listsCount = \mynt::exec('\page\system\contents\blog\common','getCount',array($status));
		$system_pageList_count = \mynt::exec('\page\system\contents\blog\lists','getCounts');
		$pagenation_count = ($listsCount / $system_pageList_count);
		$page = (!$page)?1 : $page;
    $num = ($page >= $pagenation_count)?$page : $page+1;
    $url = \mynt::exec('\lib\url\common','getUrl');
		return $url."?p=".$_GET["p"]."&c=".$_GET["c"]."&page=".$num."&status=".$status;
  }
  // 最初に移動
  public static function getUrl_start($status="release" , $page=1){
    $num = ($page <= 1 || !$page)?1 : ($page -1);
    $url = \mynt::exec('\lib\url\common','getUrl');
    $p = isset($_GET["p"]) ? $_GET["p"] : "";
    $c = isset($_GET["c"]) ? $_GET["c"] : "";
		return $url."?p=".$p."&c=".$c."&status=".$status;
  }
  // 最後に移動
	public static function getUrl_end($status="" , $page=1){
    $listsCount = \mynt::exec('\page\system\contents\blog\common','getCount',array($status));
		$system_pageList_count = \mynt::exec('\page\system\contents\blog\lists','getCounts');
		$pagenation_count = ceil($listsCount / $system_pageList_count);
		// $page = (!$page)?1 : $page;
    // $num = ($page >= $pagenation_count)?$page : $page+1;
    $url = \mynt::exec('\lib\url\common','getUrl');
    $p = isset($_GET["p"]) ? $_GET["p"] : "";
    $c = isset($_GET["c"]) ? $_GET["c"] : "";
		return $url."?p=".$p."&c=".$c."&page=".$pagenation_count."&status=".$status;
	}
}