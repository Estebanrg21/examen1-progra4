<?php
require 'database/database.php';

class User{

    function __construct($email,$password){
        $this->email =$email;
        $this->password =md5($password);
    }

    function login($connection=null){
        if(!$connection){
            $db = new Database();
            $connection = $db->connect();
        }
        $query="SELECT * FROM users WHERE email='$this->email' AND password='$this->password'";
        $result = $connection->query($query);
        $connection->close();
        return $result->num_rows>=1;
    }
    
}