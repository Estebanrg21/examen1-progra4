<?php
require_once(__DIR__.'/../database/database.php');
require_once(__DIR__."/../util.php");

class Section{

    public static $INSERT_REQUIRED_FIELDS = ["section","description"];
    public static $UPDATE_REQUIRED_FIELDS = ["description"];

    function __construct($section,$description){
        $this->section =$section;
        $this->description = $description;
        $this->connection = null;
    }

    public static function getSection($connection,$section,$onlyCheckExistance=true){
        $statement = $connection->prepare("SELECT ".(($onlyCheckExistance)?"id":"*")." FROM sections WHERE id=?");
        $statement->bind_param('s',$section);
        $statement->execute();
        if($statement)
            $result = $statement->get_result();
            return (($onlyCheckExistance)?$result->num_rows>=1:$result->fetch_array(MYSQLI_ASSOC));
        return null;
    }

    function save(){
        $response = 500;
        if(empty($this->section)) return $response;
        if(empty($this->description) || strlen($this->description)>100){
            $response=400;
             return $response;
        }
        $existingSection = Section::getSection($this->connection,$this->section,false);
        if($existingSection){
            [$fields,$fieldMap,$fieldsSentence] = prepareUpdatedFieldsToBind([
                "description"=>[$this->description,$existingSection['description'],"s"],
            ]);
            if(count($fields) == 0){
                $response = 205;
            }else{
                $statement = $this->connection->prepare("UPDATE sections SET ".$fieldsSentence." WHERE id=?");
                $fields = [...$fields,$this->section];
                $fieldMap = $fieldMap."s";
                $statement->bind_param($fieldMap, ...$fields);
                $response = 200;
            }
        }else{
            $statement = $this->connection->prepare("INSERT INTO sections VALUES (?, ?)");
            $statement->bind_param('ss',$this->section,$this->description);
            $response = 201;
        }
        if(isset($statement)){
            $statement->execute();
            if(!$statement) $response = 500;
        }    
        return $response;
    }

    public static function removeSection($connection,$section){
        $respose=500;
        $statement = $connection->prepare("DELETE FROM sections WHERE id=?");
        $statement->bind_param('s',$section);
        if(isset($statement)){
            $statement->execute();
            if($statement) $response=204;
        }
        return $response;
    }

    public static function getAllSections($connection){
        $result = $connection->query("SELECT id,description FROM sections");
        return $result;
    } 
}