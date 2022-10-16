<?php
namespace lib\analytics;

class google{

  public static function tag($tracking_code="" , $owner_key=""){
    if(!$tracking_code){return;}
    $tag_template = __DIR__."/google.txt";
    if(!is_file($tag_template)){return;}
    $tag = file_get_contents($tag_template);
    $tag = str_replace("{{tracking-code}}",$tracking_code,$tag);
    $tag = str_replace("{{owner-key}}",$owner_key,$tag);
    return $tag;
  }

  public static function tag2($tracking_code=""){
    if(!$tracking_code){return;}
    $tag_template = __DIR__."/google_v2.txt";
    if(!is_file($tag_template)){return;}
    $tag = file_get_contents($tag_template);
    $tag = str_replace("{{tracking-code}}",$tracking_code,$tag);
    // $tag = str_replace("{{owner-key}}",$owner_key,$tag);
    return $tag;
  }
}
