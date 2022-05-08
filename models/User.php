<?php
require_once(__DIR__.'/../database/database.php');

class User{

    function __construct($email,$password, $name="",$isAdmin=null,$isSu=null){
        $this->email =$email;
        $this->password =md5($password);
        $this->name=$name;
        $this->isSu=$isSu;
        $this->isAdmin=$isAdmin;
        $this->connection = null;
    }

    function login(){
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
        $this->isSu=0;
        $this->name=preg_replace('/[^,;a-zA-Z0-9_-]/s', '', $this->name);
        $existingUser = User::getUser($this->connection,$this->email,false);
        if($existingUser){
            $fieldsSentence="";
            $fieldMap = "";
            $fields = [];
            $fieldsToCompare = [
                "name"=>[$this->name,$existingUser->name,"s"],
                "password"=>[$this->password,$existingUser->password,"s"],
                "is_admin"=>[$this->isAdmin,$existingUser->isAdmin,"i"]
            ];
            foreach ($fieldsToCompare as $key => $value) {
                if($value[0]!=$value[1]){
                    $fields+=(($fieldsSentence=="")?"$key=?":", $key=?");
                    $fields []=$value[0];
                    $fieldMap+=$value[2];
                }
            }
            if($fields == ""){
                return true;
            }else{
                $this->isAdmin=(int)((bool)$this->isAdmin);
                $statement = $this->connection->prepare("UPDATE users SET ".$fields."WHERE users=$this->email");
                $statement.bind_param($fieldMap,...$fields);
            }
        }else{
            $this->isAdmin=1;
            $statement = $this->connection->prepare("INSERT INTO users VALUES (?, ?, ?, ?, ?)");
            $statement->bind_param('sssii',$this->email,$this->password,$this->name,$this->isSu,$this->isAdmin);
        }
        if($statement)
            $statement->execute();
        return $statement;
    }

    public static function getAllUsers($connection){
        $result = $connection->query("SELECT email,name, is_su, is_admin FROM users");
        return $result;
    }   

}