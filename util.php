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


function strTieHourAndDate($date, $hour){
    return (new DateTime($date))->format('Y-m-d') . $hour;
}

function hourToDateTime($d,$h){
    return new DateTime(strTieHourAndDate($d,$h)); 
}

/*  Obtenido de https://stackoverflow.com/a/25370978/11449132 */
// Retorna el limite de tamaño de archivo en bytes basado en la variable de PHP 'upload_max_filesize' 
// y post_max_size
function file_upload_max_size() {
    static $max_size = -1;
  
    if ($max_size < 0) {
      // Inicia con post_max_size.
      $post_max_size = parse_size(ini_get('post_max_size'));
      if ($post_max_size > 0) {
        $max_size = $post_max_size;
      }
  
      // Si upload_max_size es menos que post_max_size, entonces se utiliza upload_max_size
      //Esto siempre que upload_max_size sea mayor a 0
      $upload_max = parse_size(ini_get('upload_max_filesize'));
      if ($upload_max > 0 && $upload_max < $max_size) {
        $max_size = $upload_max;
      }
    }
    return $max_size;
  }
  
  function parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Limpia el string de caracteres no unitarios del parámetro size
    $size = preg_replace('/[^0-9\.]/', '', $size); // Limpia el string de caracteres no numéricos del parámetro size

    //Si el size es dado con una unidad de medida (que se obtiene en la variable $unit) entonces ...
    if ($unit) {
      // Explicación:
      //Utiliza el caracter en la posición 0 de la variable unit, el cuál hace referencia a si es en Bytes (b), Kilobytes (k)
      //y así sucesivamente, con la posición en el string ordenado 'bkmgtpezy' donde el index de cada caracter hace referencia
      //a la potencia que se va a elevar el número 1024, por ejemplo si es b, cuya posición es 0, entonces se elevaría 1024 a 0 
      //donde el resultado sería 1, y este se multiplicaría por el tamaño que ya viene dado en bytes.
      //Uso:
      //Se convierte el size dado a bytes.
      //Dependiendo del caracter que se encuentre en $unit[0], va a multiplicar la variable $size para retornar su valor en bytes
      //Por ejemplo, si inicialmente el parámetro size viene dado en 'm' (megabytes) se retorna en bytes mediante la multiplicación 
      // de 1024^2 (donde '2' es la posición de 'm' en el string bkmgtpezy) con la variable $size (donde su valor es dado en megabytes)
      return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    }
    else {
      return round($size);
    }
  }

  //Convierte bytes a otra medida especificada en el parámetro $unit utilizando base decimal
  function convertBytesTo($bytes, $unit='m'){
    $units = "bkmgtpezy";
    return floor($bytes / pow(1000,stripos($units, $unit)));
  }
