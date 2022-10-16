<?php
namespace lib\rss;

/**
 * データ
 * rss_url       : rss-xmlのURL
 * title         : podcastタイトル
 * url           : podcast URL
 * description   : podcast 説明
 * docs          : メモ
 * author        : 管理者
 * banner        : バナーURL（大）
 * email         : サービス用メールアドレス
 * category      : main-category
 * category_sub  : sub-category
 * 
 * program_title : 番組タイトル（毎回）
 * program_url   : 番組詳細URL
 * program_mp3   : 番組ファイル(mp3)
 * program_date  : 番組公開日
 * prigram_description : 番組説明
 * 
 */

class podcast{
  public static function xml($header=array() , $items=array()){
    $path_podcast = __DIR__."/podcast.txt";
    $path_header  = __DIR__."/podcast_header.txt";
    $path_item    = __DIR__."/podcast_item.txt";

    if(!is_file($path_podcast)
    || !is_file($path_header)
    || !is_file($path_item)){return null;}

    $txt_podcast = file_get_contents($path_podcast);
    $txt_header  = file_get_contents($path_header);
    $txt_item    = file_get_contents($path_item);
    $header = array_merge($header , self::getBaseInfo());
    
    $txt_header  = self::conv($txt_header , $header);
    $txt_items   = self::items($txt_item , $items , $header);

    $txt_podcast = self::conv($txt_podcast , $header);
    $txt_podcast = str_replace("{{header}}" , $txt_header , $txt_podcast);
    $txt_podcast = str_replace("{{items}}"  , $txt_items  , $txt_podcast);
    
    $xml = new \SimpleXMLElement($txt_podcast);
    return $xml->asXML();
  }

  public static function getBaseInfo(){
    return array(
      "url" => \mynt::exec('\lib\url\common','getUrl')
    );
  }

  public static function conv($txt="" , $datas=array()){
    if(!$txt){return "";}
    foreach($datas as $key => $value){
      switch($key){
        case "categories_itunes":
          $value = self::conv_category_itunes($value);
          break;
        case "categories_googleplay":
          $value = self::conv_category_googleplay($value);
          break;

        // case "program_url":
        // case "program_mp3":
        //   // echo gettype($value) ." @ ". $key." @ ".$value.PHP_EOL;
        //   // $value = htmlspecialchars($value , ENT_XML1 , "UTF-8"); 
        //   $value = str_replace("&" , '&#13;' , $value);
        //   break;

        // case "description":
        //   // $value = htmlspecialchars($value , ENT_XML1 , "UTF-8"); 
        //   break;

        default:
          $value = htmlspecialchars($value , ENT_XML1 , "UTF-8" , true); 
          break;
      }
      // if($key === "categories"){
      //   $value = self::conv_category($value);
      // }
      // if($key === "program_url"){
      //   $value = self::conv_category($value);
      // }
      // else{
      //   $value = htmlspecialchars($value , ENT_XML1 , "UTF-8"); 
      // }
      $txt = str_replace("{{".$key."}}" , $value , $txt);
    }
    return $txt;
  }

  public static function items($tmp="" , $datas=array() , $header=array()){
    if(!$tmp){return "";}
    $newTxt = "";
    foreach($datas as $data){
      $current = $tmp;
      $current = self::conv($current , $data);
      $current = self::conv($current , $header);
      $newTxt .= $current;
    }
    return $newTxt;
  }

  public static function conv_category_itunes($setting_arr=null){
    if(!$setting_arr){return "";}
    if(is_array($setting_arr)){
      $categories = "";
      for($i=0; $i<count($setting_arr); $i++){
        $sp = explode("/",$setting_arr[$i]);
        // シングルカテゴリ
        if(count($sp) === 1){
          $categories .= '<itunes:category text="'.$sp[0].'" />'.PHP_EOL;
        }
        // サブカテ付き
        else{
          $categories .= '<itunes:category text="'.$sp[0].'">'.PHP_EOL;
          $categories .= '  <itunes:category text="'.$sp[1].'" />'.PHP_EOL;
          $categories .= '</itunes:category>'.PHP_EOL;
        }
      }
    }
    return $categories;
  }
  public static function conv_category_googleplay($setting_arr=null){
    if(!$setting_arr){return "";}
    if(is_array($setting_arr)){
      $categories = "";
      for($i=0; $i<count($setting_arr); $i++){
        $sp = explode("/",$setting_arr[$i]);
        // シングルカテゴリ
        if(count($sp) === 1){
          $categories .= '<googleplay:category text="'.$sp[0].'" />'.PHP_EOL;
        }
        // サブカテ付き
        else{
          $categories .= '<googleplay:category text="'.$sp[0].'">'.PHP_EOL;
          $categories .= '  <googleplay:category text="'.$sp[1].'" />'.PHP_EOL;
          $categories .= '</googleplay:category>'.PHP_EOL;
        }
      }
    }
    return $categories;
  }

}
