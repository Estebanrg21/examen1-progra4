<?php
if(!$_SESSION['verification'] ){
  header("Location: /index.php");
}

if(isset($sessionCondition)){
    if($sessionCondition){
        header("Location:".$headerLoc);
    }
}

$maxMinutesInactiveSession = 15;

function checkSessionExpiration(){
  global $maxMinutesInactiveSession;
  if(!$_SESSION["isSuper"] && !$_SESSION["isAdmin"]){
    return time() > $_SESSION['expire'];
  }else if(isset($_SESSION['LAST_ACTIVITY'])){
    return (time() - $_SESSION['LAST_ACTIVITY']) > $maxMinutesInactiveSession * 60;
  }
}


if(checkSessionExpiration()) {
  session_destroy();
  session_start();
  $_SESSION['wasRedirected']=true;
  header("Location: /session-expired.php");
}else if (isset($_SESSION['LAST_ACTIVITY'])) {
    $_SESSION['LAST_ACTIVITY'] = time();
  }


?>