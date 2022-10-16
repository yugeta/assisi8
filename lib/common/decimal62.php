<?php
namespace lib\common;
/**
 * Path    : lib/php/decimal62.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : 整数値データを62進数に変換、戻す処理
 * Example : 
 */

class decimal62{
  // chara-value
	public static function chars(){
		return array_merge(
			range(0,9),
			range('a','z'),
			range('A','Z')
		);
	}

	// num->id
	public static function encode($n){
		$char = self::chars();
		$cn = count($char);
		$str = '';
		while ($n != 0) {
			$a1 = (int) ($n / $cn);
			$a2 = $n - ($a1*$cn);
			$str = $char[$a2].$str;
			$n = $a1;
		}
		return $str;
	}

	// id->num
	public static function decode($n){
		$char = self::chars();
		$cn = count($char);
		for ($i=0; $i< $cn; $i++) {
			$chars[$char[$i]] = $i;
		}
		$str = 0;
		for ($i=0; $i<strlen($n); $i++) {
			$str += $chars[substr($n, ($i+1)*-1, 1)] * pow($cn, $i);
		}
		return $str;
	}
}
