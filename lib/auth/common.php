<?php
namespace lib\auth;

class common{
  
  // アカウントidの作成
  public static function makeAccountId(){
    return self::getMsTime().".".rand(0,999);
  }

}