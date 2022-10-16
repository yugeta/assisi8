<?php
namespace lib\common;

class referer{
	public static function check(){return true;
		if(!isset($_SERVER['HTTP_REFERER']) || !$_SERVER['HTTP_REFERER']){
			return false;
		}
		// リファラが同じサーバー発信でなければNG
		$info_referer = parse_url($_SERVER['HTTP_REFERER']);
		if($_SERVER['HTTP_HOST'] === $info_referer["host"]){
			return true;
		}
		else{
			return false;
		}
	}
}