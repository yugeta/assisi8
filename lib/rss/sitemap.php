<?php
namespace lib\rss;

class sitemap{
  public static $temp_base = __DIR__."/sitemap.xml";
  public static $temp_url  = __DIR__."/sitemap_url.xml";

  public static function view($datas=array()){
    if(!$datas){return;}
// return json_encode($datas);
// return self::$temp_base ." @". self::$temp_url;
    $xml       = self::get_base();
    $url_lists = self::convert_url($datas);
    $xml = str_replace("{{url-lists}}" , $url_lists , $xml);

    // $xml = str_replace("{{url-lists}}" , "hoge" , $xml);

    // echo $url_lists;
// echo $xml;
// // // echo $xml;
// // // gettype($xml);
// exit();

    header('Content-Type: text/xml');
    $xml_data = new \SimpleXMLElement($xml);
    echo $xml_data->asXML();
    exit();
  }

  public static function get_base(){
    return file_get_contents(self::$temp_base);
  }

  public static function convert_url($datas){
    $temp_url = file_get_contents(self::$temp_url);
    $txt = "";
    foreach($datas as $data){
      $tmp = $temp_url;

      $url        = isset($data["url"])        ? $data["url"]        : "";
      $lastmod    = isset($data["lastmod"])    ? $data["lastmod"]    : "";
      $priority   = isset($data["priority"])   ? $data["priority"]   : "1.0";
      $changefreq = isset($data["changefreq"]) ? $data["changefreq"] : "";

      $tmp = str_replace("{{url}}"        , $url        , $tmp);
      $tmp = str_replace("{{lastmod}}"    , $lastmod    , $tmp);
      $tmp = str_replace("{{changefreq}}" , $changefreq , $tmp);
      $tmp = str_replace("{{priority}}"   , $priority   , $tmp);

      $txt .= $tmp;
    }
    return $txt;
  }

}