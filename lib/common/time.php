<?php
namespace lib\common;

class time{
  // css,jsを読み込む時のユニークURL対応用
  public static function current(){
		return date("YmdHis");
	}
}