<?php
require_once(__DIR__ . '/../database/database.php');
require_once(__DIR__ . "/../util.php");
require_once(__DIR__ . "/Section.php");
class Student
{

    public static $INSERT_REQUIRED_FIELDS = ["id", "name", "lastNames", "sectionId"];
    public static $DB_FIELDS = ["id" => "id", "name" => "name", "lastNames" => "lastnames", "sectionId" => "id_section"];
    public static $MAX_LENGTH_OF_FIELD = ["id" => 12, "name" => 20, "lastNames" => 100, "sectionId" => 10];
    public static $TYPE_OF_FIELD = ["id" => "s", "name" => "s", "lastNames" => "s", "sectionId" => "s"];
    public static $UPDATE_REQUIRED_FIELDS = [];
    public static $responseCodes = [
        0 => ["Estudiante creado correctamente!", true],
        1 => ["Estudiante eliminado correctamente!", true],
        2 => ["Estudiante actualizado correctamente!", true],
        10 => ["Campos en formato erróneo", false],
        12 => ["Hubo un error en el servidor", false],
        13 => ["Ya existe un estudiante con el mismo id", false],
        14 => ["Estudiante no necesita actualizarse", false],
        15 => ["Sección no existe", false],
    ];

    function __construct($id, $name, $lastNames, $sectionId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastNames = $lastNames;
        $this->sectionId = $sectionId;
        $this->connection = null;
    }
    public static function createStudentFromCsv($line)
    {
        $exception = new Exception("Campos de estudiante con longitud inválida");
        if (strlen($line[0]) > Student::$MAX_LENGTH_OF_FIELD["id"])
            throw $exception;
        if (strlen($line[1]) > Student::$MAX_LENGTH_OF_FIELD["name"])
            throw $exception;
        if (strlen($line[2]) > Student::$MAX_LENGTH_OF_FIELD["lastNames"])
            throw $exception;
        if (strlen($line[3]) > Student::$MAX_LENGTH_OF_FIELD["sectionId"])
            throw $exception;
        return new Student(trim($line[0]), $line[1], $line[2], trim($line[3]));
    }
    public static function createObject($array)
    {
        if (empty($array)) return null;
        try {
            return new Student($array["id"], $array["name"], $array["lastnames"], $array["id_section"]);
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getStudent($connection, $id, $onlyCheckExistance = true)
    {
        $statement = $connection->prepare("SELECT " . (($onlyCheckExistance) ? "id" : "*") . " FROM students WHERE id=?");
        $statement->bind_param('s', $id);
        $statement->execute();
        if ($statement)
            $result = $statement->get_result();
        return (($onlyCheckExistance) ? $result->num_rows >= 1 : $result->fetch_array(MYSQLI_ASSOC));
        return null;
    }

    function save()
    {
        $response = 12;
        $thisArray = (array) $this;
        unset($thisArray["connection"]);
        $fieldsLength = array_map(function ($t, $f) {
            return [strlen($t), $f, '<='];
        }, $thisArray, Student::$MAX_LENGTH_OF_FIELD);
        $fieldsLength = compareFieldsLength($fieldsLength);
        if (!(Section::getSection($this->connection, $this->sectionId))) return 15;
        $existingStudent = Student::createObject(Student::getStudent($this->connection, $this->id, false));
        if ($existingStudent) {
            if (in_array(true, $fieldsLength)) {
                array_walk($thisArray, function (&$item, $k, $objs) {
                    $item = [$item, $objs[0][$k], $objs[1][$k]];
                }, [((array)$existingStudent), Student::$TYPE_OF_FIELD]);
                changeArrayKeys($thisArray, Student::$DB_FIELDS);
                [$fields, $fieldMap, $fieldsSentence] = prepareUpdatedFieldsToBind($thisArray);
                if (count($fields) == 0) {
                    $response = 14;
                } else {
                    $statement = $this->connection->prepare("UPDATE students SET " . $fieldsSentence . " WHERE id=?");
                    $fields = [...$fields, $this->id];
                    $fieldMap = $fieldMap . "s";
                    $statement->bind_param($fieldMap, ...$fields);
                    $response = 2;
                }
            } else {
                return 10;
            }
        } else {
            if (checkFieldsEmptiness($thisArray) || array_search(false, $fieldsLength, true)) {
                return 10;
            }
            $statement = $this->connection->prepare("INSERT INTO students VALUES (?, ?, ?, ?)");
            $statement->bind_param('ssss', $this->id, $this->name, $this->lastNames, $this->sectionId);
            $response = 0;
        }
        if (isset($statement)) {
            $statement->execute();
            if (!$statement) $response = 12;
        }
        return $response;
    }

    public static function removeStudent($connection, $id)
    {
        $respose = 12;
        $statement = $connection->prepare("DELETE FROM students WHERE id=?");
        $statement->bind_param('s', $id);
        if (isset($statement)) {
            $statement->execute();
            if ($statement) $response = 1;
        }
        return $response;
    }

    public static function readCsvStudents($connection, $csvString)
    {
        $data = explode("\n", $csvString, -1);
        $exception =  new Exception("No se pudo leer el archivo");
        if (empty($data))
            $data = explode("\r\n", $csvString, -1);
        if (empty($data))
            throw $exception;
        array_shift($data);
        foreach ($data as &$student) {
            $student = explode(",", $student);
            if (
                empty($data) || (count($student) > count(Student::$INSERT_REQUIRED_FIELDS))
                || (count($student) < count(Student::$INSERT_REQUIRED_FIELDS))
            )
                throw $exception;
            $student = Student::createStudentFromCsv($student);
        }
        //se separa el guardado de la creación para efectos de evitar guardados a medias (en caso que el siguiente dato tenga errores)
        if (isset($student))
            unset($student);
        $results = [];
        foreach ($data as $student) {
            $student->connection = $connection;
            $response = $student->save();
            $results[$student->id] = Student::$responseCodes[$response];
        }

        $export = '
                    <table> 
                    <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Resultado</th>
                    </tr>
                    </thead>
                ';
        foreach ($results as $studentId => $result) {
            $export .= '
                        <tr>
                            <td>' . $studentId . '</td> 
                            <td>' . $result[0] . '</td> 
                        </tr>
                    ';
        }
        $export .= '</table>';
        header('Content-Type: application/xls');
        header('Content-Disposition: attachment; filename=resultadoCsvEstudiantes.xls');
        echo $export;
        die;
    }
}
