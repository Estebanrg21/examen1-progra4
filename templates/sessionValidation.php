<?php
session_start();
if(!$_SESSION['verification'] ){
  header("Location: /index.php");
}

if(isset($sessionCondition)){
    if($sessionCondition){
        header("Location:".$headerLoc);
    }
}


$now = time();

if($now > $_SESSION['expire']) {
  session_destroy();
  session_start();
  $_SESSION['wasRedirected']=true;
  header("Location: /session-expired.php");
}

?>