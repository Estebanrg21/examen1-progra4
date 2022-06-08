<?php

function validateFields($container, $fields, $anonFunc, $noNegation = true)
{
    $isOk = true;
    foreach ($fields as &$value) {
        $boolResult =  $anonFunc($container, $value);
        $isOk = ($noNegation) ? $boolResult : !$boolResult;
        if (!$isOk) break;
    }
    return $isOk;
}

function checkInput($names)
{
    return validateFields($_POST, $names, function ($container, $value) {
        return empty($container[$value]);
    }, false);
}

function isAssoc(array $arr)
{
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/* 
    checkFieldsEmptiness: 
        Método para verificar si algún elemento de un array se encuentra vacío
*/
function checkFieldsEmptiness($fields)
{
    $isOk = true;
    foreach ($fields as $field) {
        $isOk = !empty($field);
        if (!$isOk) return $isOk;
    }
}

/* 
    compareFieldsLength: 
        Método para verificar si algún elemento de un array 
        cumple un criterio de acuerdo a una condición dada
    PARAMS:
        $fields:
            Array asociativo con un key como el nombre del valor a comparar
            y un array como value donde tendrá el operador de comparación,
            el valor a comparar, y el valor con el cual hay que compararlo
    RETURN:
        Retorna un array asociativo con los fields comparados como key
        y como value un valor bool del resultado de la comparación
*/
function compareFieldsLength($fields)
{
    $result = [];
    foreach ($fields as $key => $value) {
        switch ($value[2]) {
            case '==':
                $result += [$key => $value[0] == $value[1]];
                break;
            case '>':
                $result += [$key => $value[0] > $value[1]];
                break;
            case '<':
                $result += [$key => $value[0] < $value[1]];
                break;
            case '>=':
                $result += [$key => $value[0] >= $value[1]];
                break;
            case '<=':
                $result += [$key => $value[0] <= $value[1]];
                break;
            default:
                break;
        }
    }
    return $result;
}


function areSubmitted($names)
{
    return validateFields($_POST, $names, function ($container, $value) {
        return isset($container[$value]);
    });
}

function changeArrayKeys(&$array, $newKeys)
{
    foreach ($newKeys as $k => $v) {
        if ($k != $v) {
            if (isset($array[$k])) {
                $array[$v] = $array[$k];
                unset($array[$k]);
            }
        }
    }
}

/*
    prepareUpdatedFieldsToBind:
        Método para comparar campos de un objeto para hacer un update con 
        un prepare statement
    PARAMETROS:
        $fieldsToCompare: Array asociativo donde el key es el nombre de la columna
        en la base de datos y el value un array con tres valores:
            0: el valor del objeto a comparar
            1: el valor con el que se hace la comparación
            2: tipo de dato para hacer un bind
    RETURN:
        $fields: los valores a que tengan que ser actualizados
        $fieldMap: string con los types de los campos a actualizar
        $fieldSentence:  string con la sentencia de SQL de los campos a actualizar
*/
function prepareUpdatedFieldsToBind($fieldsToCompare)
{
    $fieldsSentence = "";
    $fieldMap = "";
    $fields = [];
    foreach ($fieldsToCompare as $key => $value) {
        if ($value[0] != $value[1]) {
            $fieldsSentence = $fieldsSentence . (($fieldsSentence == "") ? "$key=?" : ", $key=?");
            $fields[] = $value[0];
            $fieldMap = $fieldMap . $value[2];
        }
    }
    return [$fields, $fieldMap, $fieldsSentence];
}

function isDateValid($str)
{

    if (!is_string($str)) {
        return false;
    }

    $stamp = strtotime($str);

    if (!is_numeric($stamp)) {
        return false;
    }

    if (checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp))) {
        return true;
    }
    return false;
}
