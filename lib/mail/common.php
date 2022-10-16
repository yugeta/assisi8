<?php
namespace lib\mail;

class common{
  public static function sendMail($to , $from , $subject , $message){
    mb_language("Ja");
    mb_internal_encoding("UTF-8");

    // $header1  = 'MIME-Version: 1.0\r\n';
    // $header1 .= 'From: '. $from .'\r\n';
    // $header1 .= 'Reply-To: '. $from .'\r\n';
    // $header1 .= 'Content-Type: text/plain;charset=ISO-2022-JP\r\n';
    // $header1 .= 'X-Mailer: PHP/' . phpversion();
    // $header1 .= '\r\n';

    // $headers  = 'From: '. $from . "\r\n";
    // $headers .= 'X-Mailer: PHP/' . phpversion();

// print_r($headers1);exit();
// echo "to : ".$to.PHP_EOL;
// echo "subject : ".$subject.PHP_EOL;
// echo "message : ".$message.PHP_EOL;
// die($headers1);

    // // 事務局に送る
    // return mb_send_mail(
    //   $to,
    //   $subject, 
    //   $message,
    //   $header1
    // );

    //headerを設定
    $charset = "UTF-8";
    $headers['MIME-Version'] 	= "1.0";
    $headers['Content-Type'] 	= "text/plain; charset=".$charset;
    $headers['From'] 		      = $from;
    $headers['To'] 		        = $to;
    $headers['Reply-to'] 		  = $from;
    $headers['Return-Path']   = $from;
    $headers['Content-Transfer-Encoding'] = "8bit";
    // $headers["Date"]          = date("r (T)");
    // $headers['X-Priority']    = 3;
    $headers['Sender']      = $from;

    //headerを編集
    foreach ($headers as $key => $val) {
      $arrheader[] = $key . ': ' . $val;
    }
    $strHeader = implode(" \r\n", $arrheader);

    //件名を設定（JISに変換したあと、base64エンコードをしてiso-2022-jpを指定する）
    $subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($subject , "JIS" , $charset)). "?=";

    $message = str_replace("\n.", "\n..", $message);

    $param = "-f".$to;
    // $param = "-f ".$from;

    return mail(
      $to,
      $subject,
      $message,
      $strHeader,
      $param
    );
  }




  public static function sendMail2($to , $from , $subject , $message){
    mb_language("Ja");
    mb_internal_encoding("UTF-8");


    //headerを設定
    $charset = "UTF-8";
    $headers['MIME-Version'] 	= "1.0";
    $headers['Content-Type'] 	= "text/plain; charset=".$charset;
    $headers['Content-Transfer-Encoding'] 	= "8bit";
    $headers['From'] 		= $from;

    //headerを編集
    foreach ($headers as $key => $val) {
      $arrheader[] = $key . ': ' . $val;
    }
    $strHeader = implode("\r\n", $arrheader);

    //件名を設定（JISに変換したあと、base64エンコードをしてiso-2022-jpを指定する）
    $subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($subject,"JIS","UTF-8"))."?=";

    return mail(
      $to,
      $subject,
      $message,
      $strHeader,
      "From: ".$from,
      "-f".$from
    );

  }
  public static function sendMail_mb($to , $from , $subject , $message){
    mb_language("ja");
    mb_internal_encoding("UTF-8");
    
    $from_mail = $from;
    $from_name = $from;

    // 送信者情報の設定
    $header = '';
    $header .= "Content-Type: text/plain \r\n";
    $header .= "Return-Path: " . $from_mail . " \r\n";
    $header .= "From: " . $from ." \r\n";
    $header .= "Sender: " . $from ." \r\n";
    $header .= "Reply-To: " . $from_mail . " \r\n";
    $header .= "Organization: " . $from_name . " \r\n";
    $header .= "X-Sender: " . $from_mail . " \r\n";
    $header .= "X-Priority: 3 \r\n";

    // //メール送信
    // $response = mb_send_mail( $to, $subject, $text, $header);

    // //件名を設定（JISに変換したあと、base64エンコードをしてiso-2022-jpを指定する）
    // $subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($subject , "JIS" , $charset)). "?=";

    $message = str_replace("\n.", "\n..", $message);

    return mb_send_mail(
      $to,
      $subject,
      $message,
      $header
    );
  }

}