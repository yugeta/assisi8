<?php
namespace lib\common;
/**
 * Path    : lib/php/json.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : jsonデータの共通処理
 * Example : 
 */

class json{

  public static function json_enc($data , $format=""){

    switch ($format){
      // 改行整形する、全角文字対応、"/"のバックスラッシュ対応無し
      case "line":
        $json = json_encode($data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        break;
      // 全角文字対応、"/"のバックスラッシュ対応無し
      default:
        $json = json_encode($data , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        break;
    }

    $json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);
    return $json;
  }

}
