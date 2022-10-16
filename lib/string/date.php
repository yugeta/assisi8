<?php
namespace lib\string;
/**
 * Path    : lib/php/date.php
 * Author  : Yugeta.Koji
 * Date    : 2019.07.01
 * Ver     : 0.1.0
 * Summary : 日付に関する処理（文字列変換やフォーマット調整等）
 * Example : 
 */

class date{
  // unix-time -> date-format
  public static function conv($unix){
    if(!$unix){
      return array(
        "year"   => "",
        "month"  => "",
        "date"   => "",
        "hour"   => "",
        "minute" =>"",
        "second" => ""
      );
    }
    $y = date("Y" , $unix);	//年
  	$m = date("m" , $unix); //月
  	$d = date("d" , $unix);	//日
  	$h = date("H" , $unix);	//時
  	$i = date("i" , $unix); //分
  	$s = date("s" , $unix);	//秒
    $w = date("w" , $unix);	//曜日
    
    return array(
      "year"   => date("Y" , $unix),
      "month"  => date("m" , $unix),
      "date"   => date("d" , $unix),
      "hour"   => date("H" , $unix),
      "minute" => date("i" , $unix),
      "second" => date("s" , $unix)
    );
  }
  public static function format_ymd($unix){
    if(!$unix){return "";}
    $data = self::conv($unix);
    return $data["year"]."/".$data["month"]."/".$data["date"];
  }
  public static function format_ymdhis($unix){
    if(!$unix){return "";}
    $data = self::conv($unix);
    return $data["year"]."/".$data["month"]."/".$data["date"]." ".$data["hour"].":".$data["minute"].":".$data["second"];
  }

  public static function ymd2format($ymd){
    if($ymd===""){return "";}
    $y = substr($ymd,0,4);
    $m = substr($ymd,4,2);
    $d = substr($ymd,6,2);
    return $y."/".$m."/".$d;
  }
  public static function his2format($his){
    if($his===""){return "";}
    $h = substr($his,0,2);
    $i = substr($his,2,2);
    $s = substr($his,4,2);
    return $h.":".$i.":".$s;
  }
  public static function ymdhis2format($date=""){
    if($date===""){return "";}
    $y = substr($date,0,4);
    $m = substr($date,4,2);
    $d = substr($date,6,2);
    $h = substr($date,8,2);
    $i = substr($date,10,2);
    $s = substr($date,12,2);
    return $y."/".$m."/".$d." ".$h.":".$i.":".$s;
  }
  // unix-timeをdateに変換
  public static function unix2ymdhis($unix=""){
    if($unix===""){return "";}
    $date = self::unix2date($unix);
    return date($unix);
  }
  public static function unix2date($unix=""){
    if($unix===""){return "";}
    return date($unix);
  }
  public static function getType2Unix($typeDate=""){
    if($typeDate===""){return "";}
    if(preg_match("/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})T([0-9]{1,2}):([0-9]{1,2})$/",$typeDate,$match)){
      $y = $match[1];
      $m = sprintf("%02d",$match[2]);
      $d = sprintf("%02d",$match[3]);
      $h = sprintf("%02d",$match[4]);
      $i = sprintf("%02d",$match[5]);
      return self::ymdhis2unix($y.$m.$d.$h.$i."00");
    }
    return $typeDate;
  }

  /*
    日付文字列をunix-timeに変換
    [ yyyymmdd , yyyymmddhhiiss , yyyy-mm-ddThh:ii ]
  */
  public static function ymdhis2unix($date=""){
    $date = trim($date);
    if($date===""){return "";}

    // フォーマット判別

    // YYYYMMDD※時間なし（00:00:00にセット）
    if(preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})$/",$date,$match)){
      $y = $match[1];
      $m = $match[2];
      $d = $match[3];
      $h = "00";
      $i = "00";
      $s = "00";
    }
    // YYYYMMDDHHIISS
    else if(preg_match("/^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$/",$date,$match)){
      $y = $match[1];
      $m = $match[2];
      $d = $match[3];
      $h = $match[4];
      $i = $match[5];
      $s = $match[6];
    }
    // input type="date"の対応（YYYY-MM-DD[T]HH:II）
    else if(preg_match("/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})[ T_]([0-9]{1,2}):([0-9]{1,2})$/",$date,$match)){
      $y = $match[1];
      $m = $match[2];
      $d = $match[3];
      $h = $match[4];
      $i = $match[5];
      $s = "00";
    }
    // YYYY/MM/DD HH:II:SS , YYYY.MM.DD HH:II:SS , ...
    else if(preg_match("/^([0-9]{4})[\/|\.]([0-9]{1,2})[\/|\.]([0-9]{1,2})[ ,\-_]([0-9]{1,2})[:]([0-9]{1,2})[:]([0-9]{1,2})$/",$date,$match)){
      $y = $match[1];
      $m = $match[2];
      $d = $match[3];
      $h = $match[4];
      $i = $match[5];
      $s = $match[6];
    }

    // $y = substr($date,0,4);
    // $m = substr($date,4,2);
    // $d = substr($date,6,2);
    // $h = substr($date,8,2);
    // $i = substr($date,10,2);
    // $s = substr($date,12,2);

    return mktime($h,$i,$s,$m,$d,$y);
  }
  public static function ymd2unix($date=""){
    if($date===""){return "";}
    $y = substr($date,0,4);
    $m = substr($date,4,2);
    $d = substr($date,6,2);
    // $h = substr($date,8,2);
    // $i = substr($date,10,2);
    // $s = substr($date,12,2);
    return mktime(0,0,0,$m,$d,$y);
  }

  // ２つの日付から、経過時間を取得 [ YmdHis ] [ $date1 - $date2 ]
  public static function dateRange($date1 , $date2){
    $d1 = self::ymdhis2unix($date1);
    $d2 = self::ymdhis2unix($date2);
    return $d1 - $d2;
  }

  // YYYYMMDDを取得する
  public static function getYmd($date=""){
    if($date === ""){
      return date("Ymd");
    }
    else{
      return $date;
    }
  }

  // 任意桁（デフォルト３桁）のmicrotimeを返す
  public static function getMtime($digits=3){
    list($mic, $sec) = explode(" ", microtime());
    $pow = pow(10 ,$digits);
    $val_mic = sprintf("%0".$digits."d",(int)($mic * $pow));
    return $val_mic;
  }

  // 任意の日付データを数値8桁データにする
  // -(ハイフン)区切り、/(スラッシュ)区切り、,カンマ区切り
  public static function date8($str){
    if(!$str){return "";}
    if(preg_match("/^([0-9]{4})[,\.\/\-]([0-9]{1,2})[,\.\/\-]([0-9]{1,2})/u" , $str , $match)){
      $y = sprintf("%04d",$match[1]);
      $m = sprintf("%02d",$match[2]);
      $d = sprintf("%02d",$match[3]);
      return $y.$m.$d;
    }
    else if(preg_match("/^[0-9]{8}$/u" , $str , $match)){
      return $str;
    }
    else{
      return "";
    }
  }

  public static function ymd2data($date="" , $split=""){
    if($date===""){return "";}
    $arr = array();
    array_push($arr , substr($date,0,4));
    array_push($arr , substr($date,4,2));
    array_push($arr , substr($date,6,2));
    return implode($split , $arr);
  }

  public static function setDate($fromcCurrentDayCount=0){
    $fromcCurrentDayCount = $fromcCurrentDayCount ? (int)$fromcCurrentDayCount : 0;
    return date("Y-m-d",strtotime($fromcCurrentDayCount." day"));
  }

  // $date @ yyyy-mm-dd
  // return @ yyyy-mm-dd
  public static function putDate($fromcCurrentDayCount=0 , $date){
    // $fromcCurrentDayCount = $fromcCurrentDayCount ? (int)$fromcCurrentDayCount : 0;
    // return date("Y-m-d",strtotime($fromcCurrentDayCount." day"));
    $date = new \DateTime($date);
    $fromcCurrentDayCount = (int)$fromcCurrentDayCount < 0 ? "-".$fromcCurrentDayCount : "+".$fromcCurrentDayCount;
    $date->modify($fromcCurrentDayCount.' Day');
    return $date->format('Y-m-d');
  }

  // 基準日(yyyymmdd)から○日後（マイナス指定で前）の日付を取得
  // $count=0で、当日
  public static function beforeDateCount($base_date="" ,$count=0){
    $date = new \DateTime($base_date);
    // $count = (int)$count < 0 ? "-".$count : "+".$count;
    $date->modify($count.' Day');
    return $date->format('Ymd');
  }

}
