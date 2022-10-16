<?php
namespace lib\url;

/**
 * Query設定処理
 * - Summary
 * 	query情報で簡易表示またはハッシュ処理をしている場合に適切な情報に内部変換する仕組み
 * - Specification
 * 	q= : base64に変換されたqueryを元のクエリにデコードしてグローバル変数にセットする
 * 	b= : ブログ用に簡易表示されたブログ番号をグローバル変数にセットする。(t=はtype=にセットする)
 */

class query{

	public static function get(){
		return $_SERVER['QUERY_STRING'];
	}

	public static function param_check(){
		$flg = 0;
		// base64
    if(isset($_GET["q"]) && $_GET["q"]){
			$querys = self::param_decode($_GET["q"]);
			$arr    = explode("&" , $querys);
			for($i=0; $i<count($arr); $i++){
				$sp    = explode("=" , $arr[$i]);
				$key   = $sp[0];
				$value = implode("" , array_splice($sp , 1));
				if(isset($_GET[$key])){continue;}
				$_REQUEST[$key] = $_GET[$key] = $value;
				$flg++;
			}
		}
		// ブログ用
		if(isset($_GET["b"]) && $_GET["b"]){
			$_REQUEST["c"]  = $_GET["c"]  = "blog/article";
			$_REQUEST["id"] = $_GET["id"] = $_GET["b"];
			if(isset($_GET["t"])){
				$_REQUEST["type"] = $_GET["type"] = $_GET["t"];
			}
		}
    return $flg;
  }

  // 文字列エンコード
	public static function param_encode($str){
		return base64_encode($str);
		// return gzcompress(base64_encode($str) , 9);
	}
	// 文字列デコード
	public static function param_decode($str){
		return base64_decode($str);
		// return base64_decode(gzuncompress($str));
	}
}