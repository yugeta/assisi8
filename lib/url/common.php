<?php
namespace lib\url;

class common{
  //port + domain [http://hoge.com:8800/]
	//現在のポートの取得（80 , 443 , その他）
	public static function getSite(){
		//通常のhttp処理
		if(isset($_SERVER['SERVER_PORT'])){
			if($_SERVER['SERVER_PORT']==80){
				$site = 'http://'.$_SERVER['HTTP_HOST'];
			}
			//httpsページ処理
			else if($_SERVER['SERVER_PORT']==443){
				$site = 'https://'.$_SERVER['HTTP_HOST'];
			}
		}
		//その他ペート処理
		else{
			if($_SERVER['SERVER_PORT'] === "80"){
				$site = 'http://'.$_SERVER['HTTP_HOST'];
			}
			else if($_SERVER['SERVER_PORT'] === "443"){
				$site = 'https://'.$_SERVER['HTTP_HOST'];
			}
			else{
				$site = 'http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'];
			}
		}
		return $site;
	}
	public static function getSite_protocol($url=""){
		if(!$url){return "";}
		$sp = explode("/" , $url);
		$sp = array_slice($sp , 0 , 3);
		return implode("/",$sp);
	}
	public static function getSite_host($url=""){
		if(!$url){return "";}
		$sp = explode("/" , $url);
		$sp = array_slice($sp , 3);
		return "/".implode("/",$sp);
	}

	//現在ページのサービスroot階層のパスを返す
	public static function getDir($url=""){
		$uri = $url ? self::getSite_host($url) : $_SERVER['REQUEST_URI'];
		if(!$uri){return;}

		$root = $url ? self::getSite_protocol($url) : self::getSite();
		$req  = explode('?',$uri);

		if(!$req || !count($req) || !$req[0]){
			return $root."/";
		}
		else if($req && count($req) && $req[0] === "/"){
			return $root."/";
		}
		else{
			return $root . dirname($req[0]." ")."/";
		}
	}

	//現在のクエリ無しパスを返す
	public static function getUrl(){
		$uri = self::getSite();
		$req = explode('?',$_SERVER['REQUEST_URI']);
		$uri.= $req[0];
		return $uri;
	}

	//フルパスを返す
	public static function getUri(){
		$uri = self::getSite();
		if($_SERVER['REQUEST_URI']){
			$uri.= $_SERVER['REQUEST_URI'];
		}
		else{
			$uri = self::getUrl.(($_SERVER['QUERY_STRING'])?"?".$_SERVER['QUERY_STRING']:"");
		}
		return $uri;
	}

	//基本ドメインを返す
	public static function getDomain(){
		return $_SERVER['HTTP_HOST'];
	}

	//リダイレクト処理
	public static function setUrl($url){
		if(!$url){return;}
		header("Location: ".$url);
	}

	//
	public static function getDesignRoot(){
		return self::getDir()."design/".$GLOBALS["config"]["design"]["target"]."/";
	}
	public static function getLibraryRoot(){
		return self::getDir()."library/";
	}
	public static function getPluginRoot(){
		return self::getDir()."plugin/";
	}
	public static function getDataRoot(){
		return self::getDir()."data/";
	}
	public static function getSystemRoot(){
		return self::getDir()."system/";
	}

	public static function getLocalDir(){
		$req = explode('?',$_SERVER['REQUEST_URI']);
		return dirname($req[0]." ")."/";
	}
	public static function getLocalFilename(){
		return $_SERVER['SCRIPT_NAME'];
	}

	// 最終階層の文字列を取得
	public static function getBasename($url){
		$sp = explode("/",$url);
		return $sp[count($sp)-1];
	}
}