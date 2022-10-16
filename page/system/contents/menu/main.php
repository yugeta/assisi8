<?php
namespace page\system\contents\menu;

class main{

  public static $data_cache = array();
  public static function getData($page_id=""){
    if(!$page_id){return;}
    if(!isset(self::$data_cache[$page_id])){
      $res = \mynt::data_load('','lib_menu',array(),array(),array("id"=>"SORT-ASC","sort"=>"SORT-ASC"));
      if($res["status"] === "error"){return;}
      self::$data_cache[$page_id] = $res["data"];
    }
    return self::$data_cache[$page_id];
  }

  // タイプ別データをtableで表示
  public static function datas($page_id="" , $type=""){
    $page_id = self::get_pageId($page_id);
    if(!$page_id){return;}
    $datas = self::getData($page_id);
    if(!$datas){return;}

    // $datas = self::datas_parent($datas);

    $html = "";
    foreach($datas as $data){
      if($type || $type === $data["type"]){continue;}
      $html .= "<tr data-type='". (isset($data["type"]) ? $data["type"] : "") ."'>";
      $html .= "<th class='table-num'></th>";
      $html .= "<td class='sort'>". (isset($data["sort"]) ? $data["sort"] : "") ."</td>";
      $html .= "<td class='parent'>". (isset($data["parent_id"]) ? $data["parent_id"] : "") ."</td>";
      $html .= "<td class='name'>". (isset($data["name"]) ? $data["name"] : "") ."</td>";
      $html .= "<td class='auth'>". (isset($data["auth"]) ? $data["auth"] : "") ."</td>";
      $html .= "<td class='icon'>". htmlentities(isset($data["icon"]) ? $data["icon"] : "") ."</td>";
      $html .= "<td class='link'>". htmlentities(isset($data["link"]) ? $data["link"] : "") ."</td>";
      $html .= "<td class='html'>". htmlentities(isset($data["html"]) ? $data["html"] : "") ."</td>";
      $html .= "</tr>";
      $html .= PHP_EOL;
    }
    return $html;
  }

  // public static function datas_parent($datas){
  //   $newDatas = array();

  //   // 
  //   return $newDatas;
  // }



  // タイプ一覧を表示(option)
  public static function types($page_id=""){
    $page_id = self::get_pageId($page_id);
    if(!$page_id){return;}
    $datas = self::getData($page_id);
    if(!$datas){return;}

    $types = array();
    foreach($datas as $data){
      if(in_array($data["type"] , $types)){continue;}
      array_push($types , $data["type"]);
    }
    if(!$types){return;}
    // sort($types);

    $html = "";
    foreach($types as $type){
      $html .= "<option value='".$type."'>".$type."</option>".PHP_EOL;
    }
    return $html;
  }

  public static function get_pageId($page_id=""){
    return $page_id ? $page_id : $GLOBALS["config"]["page"];
  }


}