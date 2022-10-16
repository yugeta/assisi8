<?php
namespace lib\blog\php;

class tag{

  public static function get_tag_lists($type=1,$id=null){
    $tag = \mynt::exec('\lib\blog\php\data','get_value',array($type , $id , "tag"));
    if(!$tag){return;}
    return json_decode($tag,true);

  }

  public static function view_tag($type=1,$id=null){
    // $tag = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','get_value',array($type , $id , "tag"));
    $tag = \mynt::exec('\lib\blog\php\data','get_value',array($type , $id , "tag"));
    if(!$tag){return;}
    $tags = json_decode($tag,true);
    $html = "";
    foreach($tags as $num => $str){
      $html .= "<span class='tag'>#".$str."</span>";
    }
    return $html;
  }
  
  // $max_view : 上位◯件のみ表示。
  public static function html_view_li($type="" , $max_view=20 , $release_flg=""){
    // $release_status = $release_flg ? \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\status','get_release_id',array($type)) : "";
    // $res = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','load_all',array($type , $release_status));
    $release_status = $release_flg ? \mynt::exec('\lib\blog\php\status','get_release_id',array($type)) : "";
    $res            = \mynt::exec('\lib\blog\php\data','load_all',array($type , $release_status));
    $html = "";
    if($res["status"] === "ok"){
      $arr = array();
      $total_count = 0;
      foreach($res["data"] as $data){
        if(!isset($data["tag"]) || !$data["tag"]){continue;}
        $json = json_decode($data["tag"],true);
        if(!$json){continue;}
        foreach($json as $tag){
          if(!isset($arr[$tag])){
            $total_count++;
            $arr[$tag] = 0;
          }
          $arr[$tag]++;
        }
      }
      if($arr){
        $num = 0;
        foreach($arr as $key => $count){
          if($num >= $max_view){break;}
          $html .= "<li data-count='".$count."' data-tag='".$key."'>#".$key."</li>".PHP_EOL;
          $num++;
        }
        if($total_count > $max_view){
          $html .= "<li class='over'>...</li>".PHP_EOL;
        }
      }
    }
    return $html;
  }

  // return true:ヒット false:含まず
  public static function check_inner_tag($search_tag="" , $data_tag_json=""){
    if(!$data_tag_json || !$search_tag){return false;}
    $json = json_decode($data_tag_json , true);
    if(in_array($search_tag , $json)){
      return true;
    }
    else{
      return false;
    }
  }
}