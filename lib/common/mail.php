<?php
namespace lib\common;

\mynt::checkModuleLoad('\lib\mail\common');
class mail extends \lib\mail\common {}

// class mail{
//   public static function sendMail($to , $from , $subject , $message){
//     mb_language("Ja");
//     mb_internal_encoding("UTF-8");

//     $headers1  = 'MIME-Version: 1.0\r\n';
//     $headers1 .= 'From: '. $from .'\r\n';
//     $headers1 .= 'Reply-To: '. $from .'\r\n';
//     $headers1 .= 'Content-Type: text/plain;charset=ISO-2022-JP\r\n';
//     $headers1 .= '\r\n';

//     // 事務局に送る
//     return mb_send_mail(
//       $to,
//       $subject, 
//       $message, 
//       $headers1
//     );
//   }

// }