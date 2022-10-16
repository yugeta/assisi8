<?php
namespace lib\blog\php;

class value{
  public static function get_date($type=1,$id=null){
    // $value = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','get_value',array($type , $id , "schedule"));
    $value = \mynt::exec('\lib\blog\php\data','get_value',array($type , $id , "schedule"));
    if(!$value){return;}
    return \mynt::exec('\lib\string\date','ymd2format',array($value));
  }
  public static function get_author($type=1,$id=null){
    // $uid = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','get_value',array($type , $id , "uid"));
    $uid = \mynt::exec('\lib\blog\php\data','get_value',array($type , $id , "uid"));
    if(!$uid){return;}
    $name = \mynt::exec('\page\system\contents\account\data','getProperty',array($uid,"name"));
    if($name){
      return $name;
    }
    else{
      return "匿名";
    }
  }
  // public static function get_tag($type=1,$id=null){
  //   $tag = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','get_value',array($type , $id , "tag"));
  //   if(!$tag){return;}
  //   $tags = json_decode($tag,true);
  //   $html = "";
  //   foreach($tags as $num => $str){
  //     $html .= "<span class='tag'>#".$str."</span>";
  //   }
  //   return $html;
  // }
  public static function get_eyecatch($type=1,$id=null){
    // $eyecatch = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','get_value',array($type , $id , "eyecatch"));
    $eyecatch = \mynt::exec('\lib\blog\php\data','get_value',array($type , $id , "eyecatch"));
    $url = \mynt::exec('\lib\url\common','getUrl');
    if($eyecatch && !preg_match("/^[http\:\/\/|https\:\/\/|]/" , $eyecatch)){
      $eyecatch = $url . $eyecatch;
    }
    else if(!$eyecatch || !is_file($eyecatch)){
      $eyecatch = $url."page/".$GLOBALS["page"]["page"]."/contents/blog/img/default-banner.jpg";
      if(!is_file($eyecatch)){
        $eyecatch = $url."lib/blog/img/default-banner.jpg";
      }
    }
    return $eyecatch;
  }
  public static function get_eyecatch_local($type=1 , $id=null){
    // $eyecatch = \mynt::exec('\page\\'.$GLOBALS["page"]["page"].'\contents\blog\php\data','get_value',array($type , $id , "eyecatch"));
    $eyecatch = \mynt::exec('\lib\blog\php\data','get_value',array($type , $id , "eyecatch"));
    if($eyecatch && !preg_match("/^[http\:\/\/|https\:\/\/|]/" , $eyecatch)){
      $eyecatch = $eyecatch;
    }
    else if(!$eyecatch || !is_file($eyecatch)){
      $eyecatch = "page/".$GLOBALS["page"]["page"]."/contents/blog/img/default-banner.jpg";
      if(!is_file($eyecatch)){
        $eyecatch = $url."lib/blog/img/default-banner.jpg";
      }
    }
    return $eyecatch;
  }
}