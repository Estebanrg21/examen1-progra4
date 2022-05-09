<?php
require_once(__DIR__.'/../database/database.php');
require_once(__DIR__."/../util.php");
require_once(__DIR__."/Section.php");
class Student{

    public static $INSERT_REQUIRED_FIELDS = ["id","name","lastNames","sectionId"];
    public static $DB_FIELDS = ["id"=>"id","name"=>"name","lastNames"=>"lastnames","sectionId"=>"id_section"];
    public static $MAX_LENGTH_OF_FIELD = ["id"=>12,"name"=>20,"lastNames"=>100,"sectionId"=>10];
    public static $TYPE_OF_FIELD = ["id"=>"s","name"=>"s","lastNames"=>"s","sectionId"=>"s"];
    public static $UPDATE_REQUIRED_FIELDS = [];

    function __construct($id,$name,$lastNames,$sectionId){
        $this->id =$id;
        $this->name =$name;
        $this->lastNames =$lastNames;
        $this->sectionId = $sectionId;
        $this->connection = null;
    }

    public static function createObject($array){
        if(empty($array))return null;
        try {
            return new Student($array["id"],$array["name"],$array["lastnames"],$array["id_section"]);
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getStudent($connection,$id,$onlyCheckExistance=true){
        $statement = $connection->prepare("SELECT ".(($onlyCheckExistance)?"id":"*")." FROM students WHERE id=?");
        $statement->bind_param('i',$id);
        $statement->execute();
        if($statement)
            $result = $statement->get_result();
            return (($onlyCheckExistance)?$result->num_rows>=1:$result->fetch_array(MYSQLI_ASSOC));
        return null;
    }

    function save(){
        $response = 500;
        $thisArray = (array) $this;
        unset($thisArray["connection"]);
        $fieldsLength = array_map(function($t,$f){return [strlen($t),$f,'<='];},$thisArray,Student::$MAX_LENGTH_OF_FIELD);
        $fieldsLength = compareFieldsLength($fieldsLength);
        if(!(Section::getSection($this->connection,$this->sectionId)))return 403;
        $existingStudent = Student::createObject(Student::getStudent($this->connection,$this->id,false));
        if($existingStudent){
            if(in_array(true, $fieldsLength)){
                array_walk($thisArray,function(&$item, $k,$objs){
                    $item = [$item,$objs[0][$k],$objs[1][$k]];
                },[((array)$existingStudent),Student::$TYPE_OF_FIELD]);
                changeArrayKeys($thisArray,Student::$DB_FIELDS);
                [$fields,$fieldMap,$fieldsSentence] = prepareUpdatedFieldsToBind($thisArray);
                if(count($fields) == 0){
                    $response = 205;
                }else{
                    $statement = $this->connection->prepare("UPDATE students SET ".$fieldsSentence." WHERE id=?");
                    $fields = [...$fields,$this->id];
                    $fieldMap = $fieldMap."s";
                    $statement->bind_param($fieldMap, ...$fields);
                    $response = 200;
                }
            }else{
                return 400;
            }
        }else{
            if(checkFieldsEmptiness($thisArray) || array_search(false, $fieldsLength, true)){
                return 400;
            }
            $statement = $this->connection->prepare("INSERT INTO students VALUES (?, ?, ?, ?)");
            $statement->bind_param('ssss',$this->id, $this->name, $this->lastNames, $this->sectionId);
            $response = 201;
        }
        if(isset($statement)){
            $statement->execute();
            if(!$statement) $response = 500;
        }    
        return $response;
    }

    public static function removeStudent($connection,$id){
        $respose=500;
        $statement = $connection->prepare("DELETE FROM students WHERE id=?");
        $statement->bind_param('s',$id);
        if(isset($statement)){
            $statement->execute();
            if($statement) $response=204;
        }
        return $response;
    }

}