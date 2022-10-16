<?php
namespace lib\install;

class check{

  // installチェック
  public static function data(){

    // dataディレクトリの確認
    $dir = \mynt::exec('\lib\data\database','getDir');
    if(!is_dir($dir)){
      return false;
    }

    // data/database.jsonファイルの確認
    $databaseFile = \mynt::exec('\lib\data\database','getSettingFile');
    if(!is_file($databaseFile)){
      return false;
    }

    $settingData = \mynt::exec('\lib\data\database','getSetting');
    if(!isset($settingData["database"]) || !$settingData["database"]
    || !is_dir($dir . $settingData["database"])){
      return false;
    }
    
    return true;
  }
}