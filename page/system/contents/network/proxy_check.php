<?php

if(isset($_REQUEST["port"]) && $_REQUEST["port"]){
  echo $_SERVER["REMOTE_ADDR"] .":". $_SERVER["REMOTE_PORT"];
}
else{
  echo $_SERVER["REMOTE_ADDR"];
}
