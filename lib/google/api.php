<?php
namespace lib\google;

class api{
  public static function sheet_append($client_secret_path="" , $spreadsheetId="" , $api_name="" , $values=array() , $sheet_name="シート1" , $sheet_pos="A2"){
    if(!$client_secret_path || !is_file($client_secret_path)){return;}
    if(!$spreadsheetId){return;}
    if(!$values){return;}
    if(!$api_name){return;}

    // ライブラリの読み込み
    require_once 'vendor/autoload.php';

    define('APPLICATION_NAME', $api_name);
    define('CLIENT_SECRET_PATH', $client_secret_path);

    // スコープの設定
    define('SCOPES', implode(' ', array(\Google_Service_Sheets::SPREADSHEETS)));

    // アカウント認証情報インスタンスを作成
    $client = new \Google_Client();
    $client->setScopes(SCOPES);
    $client->setAuthConfig(CLIENT_SECRET_PATH);

    // シートのインスタンスを生成
    $service = new \Google_Service_Sheets($client);

    try {
      // 更新するシートの名前とセルの範囲
      $range = $sheet_name.'!'.$sheet_pos;
      $updateBody = new \Google_Service_Sheets_ValueRange(['values' => $values]);
      // valueInputOption を指定（ USER_ENTERED か RAW から選択）
      $params = ['valueInputOption' => 'USER_ENTERED'];
      $result = $service->spreadsheets_values->append($spreadsheetId, $range, $updateBody, $params);
      return array(
        "status"  => "ok",
        "message" => $result
      );
    }

    // error
    catch (\Google_Exception $e) {
      // $e は json で返ってくる
      $errors = json_decode($e->getMessage(),true);
      $err = "code : ".$errors["error"]["code"]."";
      $err .= "message : ".$errors["error"]["message"];
      return array(
        "status"  => "error",
        "message" => "Google_Exception".$err
      );
    }
  }
}