<?php
namespace lib\url;

class post{
  public static function send($url , $query){
    if(!$url){return;}
    
    // $urlinfo = parse_url($url);
    // $urls = explode("?" , $url);
    
    $options = array(
      "http" => array(
        "method" => 'POST',
        "content" => http_build_query($query)
      )
    );
    
    $res = file_get_contents($url , false, stream_context_create($options));
  }
}