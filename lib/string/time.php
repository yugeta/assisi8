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

class time{
  public static function his2data($time="" , $split=""){
    if($time===""){return "";}
    $arr = array();
    array_push($arr , substr($time,0,2));
    array_push($arr , substr($time,2,2));
    array_push($arr , substr($time,4,2));
    return implode($split , $arr);
  }
  public static function hi2data($time="" , $split=""){
    if($time===""){return "";}
    $arr = array();
    array_push($arr , substr($time,0,2));
    array_push($arr , substr($time,2,2));
    return implode($split , $arr);
  }
}