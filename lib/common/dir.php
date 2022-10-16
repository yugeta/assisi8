<?php
namespace lib\common;

class dir{

  // 対象階層の内部ファイル（ディレクトリ）を取得する。
  // $type @ [""%blank:ファイル&ディレクトリ*default , file:ファイルのみ , dir:ディレクトリのみ]
  // $mode @ [0:再帰的モード（サブフォルダも全て含める）※default , 1:該当1階層のみ , 2:...指定階層分]
  // $reg  @ [任意文字の検索（正規表現） ex) "/\.json$/"]
  // ex) 
  // $files = \mynt::exec('\lib\common\dir','lists',array("data/shop/tables/","file",0,""));
  // 
  private static $cache = array();
  public static function lists($dir="" , $type="" , $mode=0 , $reg=""){
    if(!$dir || !is_dir($dir)){return;}
    if(!isset(self::$cache[$dir])){
      $arr = self::search_dir($dir , $type , $mode , $reg);
      if($arr){
        for($i=0; $i<count($arr); $i++){
          $preg_val = preg_quote($dir , "/");
          $arr[$i] = preg_replace("/^".$preg_val."/" , "" , $arr[$i]);
        }
      }
      else{
        $arr = array();
      }
      self::$cache[$dir] = $arr;
    }
    return self::$cache[$dir];
  }

  public static function search_dir($dir="" , $type="" , $mode=0 , $reg="" , $level_count=0){
    if(!$dir || !is_dir($dir)){return;}
    if($mode && $mode <= $level_count){return;}
    $files = array_diff(scandir($dir) , array(".",".."));
    $arr = array();
    foreach($files as $file){
      if(is_dir($dir.$file)){
        $file .= "/";
      }
      if(self::check_type($dir.$file , $type)
      && self::check_reg($reg , $file)){
        array_push($arr , $dir.$file);
      }
      // 再帰処理
      if(is_dir($dir.$file)){
        $res = self::search_dir($dir.$file , $type , $mode , $reg , $level_count+1);
        if($res){
          $arr = array_merge($arr , $res);
        }
      }
    }
    return $arr;
  }

  // タイプチェック(file or directory)
  public static function check_type($path="" , $type=""){
    if(!$type){return true;}
    switch($type){
      case "file":
        return is_file($path) ? true : false;
      case "dir":
        return is_dir($path) ? true : false;
      default:
        return true;
    }
  }

  public static function check_reg($reg="" , $val=""){
    if(!$reg){
      return true;
    }
    if(!preg_match("/^\/.+?\/(.*?)$/" , $reg)){
      $reg = "/".$reg."/";
    }
    if(preg_match($reg , $val)){
      return true;
    }
    else{
      return false;
    }
  }
}