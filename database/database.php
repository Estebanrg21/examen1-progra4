<?php

$host_db = "localhost";
$user_db = "comidaAdmin";
$passwd_db = "root123";
$db_name = "scot";
$tbl_name = "";

class Database
{
    function __construct($h = null, $u = null, $p = null, $db = null)
    {
        global $host_db, $user_db, $passwd_db, $db_name;

        $this->host = $h ?? $host_db;
        $this->user_db = $u ?? $user_db;
        $this->user_pass_db = $p ?? $passwd_db;
        $this->db = $db ?? $db_name;
    }

    public static function getConnection(){
        $db = new Database();
        $connection = $db->connect();
        return [$db,$connection];
    }

    function connect()
    {
        return (new mysqli(
            $this->host,
            $this->user_db,
            $this->user_pass_db,
            $this->db
            ));
    }

 /*   function execSelect($connection,$tableName, $cols='*'){
        return $connection->query("SELECT".(($cols=="*"?$cols:join(",",$cols)))."FROM $tableName");
    }

    /*
    * PARAMS:
    *   $connection: conexion a una base de datos
    *   $tableName: nombre de tabla donde ejecutar el SELECT
    *   $colsToSelect: array asociativo con las columnas que debe traer el select como key
    *   y sus alias deseados como value
    *   $colsToCompare: array asociativo con las columnas a comparar como key
    *   y con los valores a comparar y el operador de comparación como value
    * RETURN:
    *   SQL select
    
    function execSelectOnCondition($connection, $tableName,$colsToSelect, $colsToCompare=null,$operator="OR"){
        $tablesToRetrieve="";
        foreach ($colsToSelect as $key => $value) {
            if($value==null){
                $tablesToRetrieve+=(($tablesToRetrieve=="")?$key:", ".$key);
            }else{
                $tablesToRetrieve+=(($tablesToRetrieve=="")?"$key as $value":", $key as $value");
            }
        }
        $whereConditions="";
        foreach ($colsToCompare as $key => $value) {
            # code...
        }
    }*/
}