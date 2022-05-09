<?php
require_once(__DIR__.'/../database/database.php');

class User{

    public static $INSERT_REQUIRED_FIELDS = ["email","password","name"];
    public static $UPDATE_REQUIRED_FIELDS = ["name"];

    function __construct($email,$password=null, $name="",$isAdmin=null,$isSu=null){
        $this->email =$email;
        $this->password = $password;
        $this->name=$name;
        $this->isSu=$isSu;
        $this->isAdmin=$isAdmin;
        $this->connection = null;
    }

    function encryptPassword(){
        $this->password = md5($this->password);
    }

    function login(){
        $this->encryptPassword();
        $query="SELECT * FROM users WHERE email='$this->email' AND password='$this->password'";
        $result = $this->connection->query($query);
        $wasSuccessfully = $result->num_rows>=1;
        if($wasSuccessfully){
            $row=$result->fetch_array(MYSQLI_ASSOC);
            $this->isSu=$row['is_su'];
            $this->isAdmin=$row['is_admin'];
        }
        $this->connection->close();
        return $wasSuccessfully;
    }
    

    public static function getUser($connection,$userEmail,$onlyCheckExistance=true){
        $statement = $connection->prepare("SELECT ".(($onlyCheckExistance)?"email":"*")." FROM users WHERE email=?");
        $statement->bind_param('s',$userEmail);
        $statement->execute();
        if($statement)
            $result = $statement->get_result();
            return (($onlyCheckExistance)?$result->num_rows>=1:$result->fetch_array(MYSQLI_ASSOC));
        return null;
    }

    function save(){
        $response = 500;
        if(empty($this->email)) return $response;
        $this->isSu=0;
        $existingUser = User::getUser($this->connection,$this->email,false);
        if($existingUser){
            if(empty($this->password)){
                $this->password =$existingUser["password"];
            }else{
                $this->encryptPassword();
            }
            $fieldsSentence="";
            $fieldMap = "";
            $fields = [];
            $fieldsToCompare = [
                "name"=>[$this->name,$existingUser['name'],"s"],
                "password"=>[$this->password,$existingUser['password'],"s"],
                "is_admin"=>[$this->isAdmin,$existingUser['is_admin'],"i"]
            ];
            foreach ($fieldsToCompare as $key => $value) {
                if($value[0]!=$value[1]){
                    $fieldsSentence=$fieldsSentence.(($fieldsSentence=="")?"$key=?":", $key=?");
                    $fields []=$value[0];
                    $fieldMap=$fieldMap.$value[2];
                }
            }
            if(count($fields) == 0){
                $response = 205;
            }else{
                $this->isAdmin=(int)((bool)$this->isAdmin);
                $statement = $this->connection->prepare("UPDATE users SET ".$fieldsSentence." WHERE email=?");
                $fields = [...$fields,$this->email];
                $fieldMap = $fieldMap."s";
                $statement->bind_param($fieldMap, ...$fields);
                $response = 200;
            }
        }else{
            if(empty($this->password)){
                $response=400;
                 return $response;
            }
            $this->encryptPassword();
            $this->isAdmin=1;
            $statement = $this->connection->prepare("INSERT INTO users VALUES (?, ?, ?, ?, ?)");
            $statement->bind_param('sssii',$this->email,$this->password,$this->name,$this->isSu,$this->isAdmin);
            $response = 201;

        }
        
        if(isset($statement)){
            $statement->execute();
            if(!$statement) $response = 500;
        }    
        return $response;
    }

    public static function removeUser($connection,$email){
        $respose=500;
        $statement = $connection->prepare("DELETE FROM users WHERE email=?");
        $statement->bind_param('s',$email);
        if(isset($statement)){
            $statement->execute();
            if($statement) $response=204;
        }
        return $response;
    }

    public static function getAllUsers($connection){
        $result = $connection->query("SELECT email,name, is_su, is_admin FROM users");
        return $result;
    }   

}