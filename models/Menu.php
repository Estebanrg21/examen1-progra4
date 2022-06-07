<?php
require_once(__DIR__.'/../database/database.php');
require_once(__DIR__."/../util.php");

class Menu{
    public static $INSERT_REQUIRED_FIELDS = ["name","description"];
    public static $UPDATE_REQUIRED_FIELDS = [];

    function __construct($name,$description,$id=null){
        $this->id =$id;
        $this->name =$name;
        $this->description = $description;
        $this->connection = null;
    }

    public static function getMenu($connection,$id,$onlyCheckExistance=true){
        $statement = $connection->prepare("SELECT ".(($onlyCheckExistance)?"id":"*")." FROM menus WHERE id=?");
        $statement->bind_param('i',$id);
        $statement->execute();
        if($statement)
            $result = $statement->get_result();
            return (($onlyCheckExistance)?$result->num_rows>=1:$result->fetch_array(MYSQLI_ASSOC));
        return null;
    }

    function save(){
        $response = 500;
        if((empty($this->description) || strlen($this->description)>100) && (empty($this->name) || strlen($this->name)>20)){
            $response=400;
            return $response;
        }
        if(strlen($this->description)>100 || strlen($this->name)>20){
            $response=400;
            return $response;
        }
        $existingMenu = Menu::getMenu($this->connection,$this->id,false);
        if($existingMenu){
            [$fields,$fieldMap,$fieldsSentence] = prepareUpdatedFieldsToBind([
                "name"=>[$this->name,$existingMenu['name'],"s"],
                "description"=>[$this->description,$existingMenu['description'],"s"],
            ]);
            if(count($fields) == 0){
                $response = 205;
            }else{
                $statement = $this->connection->prepare("UPDATE menus SET ".$fieldsSentence." WHERE id=?");
                $fields = [...$fields,$this->id];
                $fieldMap = $fieldMap."s";
                $statement->bind_param($fieldMap, ...$fields);
                $response = 200;
            }
        }else{
            $statement = $this->connection->prepare("INSERT INTO menus(name,description) VALUES (?, ?)");
            $statement->bind_param('ss',$this->name,$this->description);
            $response = 201;
        }
        if(isset($statement)){
            $statement->execute();
            if(!$statement) $response = 500;
        }    
        return $response;
    }

    public static function removeMenu($connection,$id){
        $response=500;
        $statement = $connection->prepare("DELETE FROM menus WHERE id=?");
        $statement->bind_param('s',$id);
        if(isset($statement)){
            $statement->execute();
            if($statement) $response=204;
        }
        return $response;
    }

    public static function getAllMenus($connection){
        $result = $connection->query("SELECT * FROM menus");
        return $result;
    } 
}