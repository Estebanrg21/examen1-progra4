<?php
require_once(__DIR__.'/../database/database.php');
require_once(__DIR__."/../util.php");
require_once(__DIR__."/Section.php");
class Food{

    public static $MAX_LENGTH_OF_FIELD = ["student"=>12];

    function __construct($student,$foodTime){
        $this->student =$student;
        $this->foodTime =$foodTime;
        $this->foodDate = (new DateTime())->format('Y-m-d');
        $this->connection = null;
    }


    function canStudentExchange(){
        $statement = $this->connection->prepare("SELECT id FROM foods WHERE id_student=? AND food_date=? AND id_food_time=?");
        $statement->bind_param('sss',$this->student,$this->foodDate,$this->foodTime);
        $statement->execute();
        if($statement)
            $result = $statement->get_result();
            return !($result->num_rows>=1);
        throw new Exception("Consulta de estado de estudiante no ejecutada");
        
    }

    function save(){
        $response = 500;
        if(!$this->canStudentExchange()){
            return 400;
        }else{
            if(strlen($this->student)>12 || empty($this->student) || empty($this->foodTime)){
                return 400;
            }
            $statement = $this->connection->prepare("INSERT INTO foods(id_student,food_date,id_food_time) VALUES (?, ?, ?)");
            $statement->bind_param('sss',$this->student, $this->foodDate, $this->foodTime);
            $response = 201;
        }
        if(isset($statement)){
            $statement->execute();
            if(!$statement) $response = 500;
        }    
        return $response;
    }

    public static function genReport($date1,$date2,$foodTime=null){
        [$db,$connection] = Database::getConnection();
        $statement = $connection->prepare("select id_student as cedula, food_date as fecha,  
        students.name as `nombreEstudiante`, lastnames as `apellidos`,  
        id_section as seccion, food_times.name as `tiempo` from foods inner 
        join students on foods.id_student=students.id inner join food_times on foods.id_food_time=food_times.id 
        where food_date BETWEEN ? AND ?".(($foodTime)?" AND food_times.id=?":""));
        $fields = [$date1,$date2];
        $fieldsMap = 'ss';
        if($foodTime){
            $fields [] =$foodTime;
            $fieldsMap=$fieldsMap."s";
        }
        $statement->bind_param($fieldsMap,...$fields);
        $statement->execute();
        if($statement){
            $result = $statement->get_result();
            if($result->num_rows>=1){ 
                $export = '
                    <table> 
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cedula</th>
                        <th>Nombre</th>                    
                        <th>Apellidos</th>
                        <th>Sección</th>
                        <th>Tiempo de alimentación</th>
                    </tr>
                    </thead>
                ';
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $export .= '
                        <tr>
                            <td>'.$row["fecha"].'</td> 
                            <td>'.$row["cedula"].'</td> 
                            <td>'.$row["nombreEstudiante"].'</td> 
                            <td>'.$row["apellidos"].'</td> 
                            <td>'.$row["seccion"].'</td> 
                            <td>'.$row["tiempo"].'</td> 
                        </tr>
                    ';
                }
                $export .= '</table>';
                header('Content-Type: application/xls');
                header('Content-Disposition: attachment; filename=registroPorDia.xls');
                echo $export;
                die;
            }
            
        }else{
            return null;
        }
    }

}