<?php

function validateFields($container,$fields, $anonFunc, $noNegation = true){
    $isOk = true;
    foreach($fields as &$value){
        $boolResult =  $anonFunc($container,$value);
        $isOk = ($noNegation)?$boolResult:!$boolResult;
        if(!$isOk) break;

    }
    return $isOk;
}

function checkInput($names){
    return validateFields($_POST,$names,function($container,$value){
        return empty($container[$value]);
    },false);
}

function areSubmitted($names){
    return validateFields($_POST,$names,function($container,$value){
        return isset($container[$value]);
    });
}
