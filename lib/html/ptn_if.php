<?php
namespace lib\html;

class ptn_if{

  // pattern (if)
  public static function check($source , $tags){
    $ptn = $tags[0].'if\:(.+?)'.$tags[1].'(.+?)'.$tags[0].'\/if'.$tags[1];
    preg_match_all("/".$ptn."/is" , $source  , $match);
    
    if(count($match[1])){
      for($i=0, $c=count($match[1]); $i<$c; $i++){
        if($match[0][$i]===""){continue;}

        // else-check
        $val_else = "";
        $val_then = $match[2][$i];
        $ptn_else = '(.*)'.$tags[0].'else'.$tags[1].'(.*)';
        preg_match_all("/".$ptn_else."/is" , $val_then , $match_else);
        if(count($match_else[0])){
          $val_else = $match_else[2][0];
          $val_then = $match_else[1][0];
        }

        // if-elif-else
        $ptn_elif = $tags[0]."elif";
        if(preg_match("/".$ptn_elif."/is" , $val_then)){
          $ptn2 = $tags[0].'elif\:(.+?)'.$tags[1] ."(.+?)";
          $str = $val_then;
          $str = str_replace(array("\n","\r"),"",$str);
          preg_match_all("/".$ptn2."/is" , $str  , $elifs);
          $elif = "";
          for($j=0; $j<count($elifs[0]); $j++){
            $elif .= $tags[0]."elif\:(.+?)".$tags[1] ."(.+?)";
          }
          $ptn3  = $tags[0].'if\:(.+?)'.$tags[1] .'(.+?)';
          $ptn3 .= $elif;
          $ptn3 .= $tags[0].'else'.$tags[1]. '(.+?)' .$tags[0].'\/if'.$tags[1];
          preg_match("/".$ptn3."/is" , $match[0][$i]  , $elifs2);
          $evalStr = "if(".$elifs2[1]."){return '".str_replace("'","\'",$elifs2[2])."';}".PHP_EOL;
          for($j=3; $j<count($elifs2)-1; $j=$j+2){
            if($elifs2[$j]===""){continue;}
            $evalStr .= "else if(".$elifs2[$j]."){return '".str_replace("'","\'",$elifs2[$j+1])."';}".PHP_EOL;
          }
          $evalStr .= "else{return '".str_replace("'","\'",$val_else)."';}";

          $res = eval($evalStr);
        }

        // if-else
        else{
          $ptn = $match[1][$i];
          $val_then = str_replace("'","\'",$val_then);
          $evalStr = "if(".$match[1][$i]."){return '".$val_then."';}";
          if($val_else !== ""){
            $val_else = str_replace("'","\'",$val_else);
            $evalStr .= "else{return '".$val_else."';}";
          }
          $res = eval($evalStr);
        }
        
        // replace
        $source = str_replace($match[0][$i] , $res , $source);
      }
    }
    return $source;
  }
}