<?php
require_once(__DIR__ . '/../database/database.php');
require_once(__DIR__ . "/FoodTime.php");
require_once(__DIR__ . "/../util.php");


class DateMenu
{
    public static $INSERT_REQUIRED_FIELDS = ["idFood", "idMenu", "endTime", "description"];
    public static $UPDATE_REQUIRED_FIELDS = ["id"];
    public static $responseCodes = [
        0 => ["Asignación creada correctamente!", true],
        1 => ["Asignación eliminada correctamente!", true],
        2 => ["Asignación actualizada correctamente!", true],
        10 => ["Campos en formato erróneo", false],
        12 => ["Hubo un error en el servidor", false],
        13 => ["Ya existe una asignación con el menú", false],
        14 => ["Asignación no necesita actualizarse", false],

    ];
    function __construct($startTime, $endTime, $idFoodTime, $idMenu, $description, $id = null)
    {
        $this->id = $id;
        $this->startTime = (new DateTime($startTime))->format('Y-m-d H:i:s');
        $this->dayServed = (new DateTime($_SESSION["date"]))->format('Y-m-d');
        $this->endTime = (new DateTime($endTime))->format('Y-m-d H:i:s');
        $this->idFoodTime = $idFoodTime;
        $this->idMenu = $idMenu;
        $this->creator = $_SESSION['user'];
        $this->description = $description;
        $this->connection = null;
    }

    public static function getDateMenu($connection, $id, $onlyCheckExistance = true, $retrieveAllData = false)
    {

        if ($retrieveAllData && !$onlyCheckExistance) {
            $query = "select menus_details.id as id,food_times.id as idFood,food_times.name as tname, 
            menus.id as idMenu,menus.name as mname, menus_details.description as description, start,end   
            from menus_details inner JOIN food_times on food_times.id=menus_details.id_food_time 
            inner JOIN menus on menus.id=menus_details.id_menu where menus_details.id=?";
        } else {
            $query = "SELECT " . (($onlyCheckExistance) ? "id" : "*") . " FROM menus_details WHERE id=?";
        }
        $statement = $connection->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();
        if ($statement) {
            $result = $statement->get_result();
            return (($onlyCheckExistance) ? $result->num_rows >= 1 : $result->fetch_array(MYSQLI_ASSOC));
        }
        return null;
    }

    public function isUnique()
    {
        $statement = $this->connection->prepare("SELECT count(id) as numb FROM menus_details WHERE id_menu=? AND day_served=?");
        $statement->bind_param('is', $this->idMenu, $this->dayServed);
        $statement->execute();
        if ($statement) {
            $result = $statement->get_result();
            $result = $result->fetch_array(MYSQLI_ASSOC)["numb"];
            return !$result;
        }
        return null;
    }

    function save()
    {
        $response = 12;
        if ((empty($this->description) || strlen($this->description) > 100)) {
            $response = 10;
            return $response;
        }
        if (
            strlen($this->description) > 100 || !is_numeric($this->idFoodTime)
            || !is_numeric($this->idMenu) || !isDateValid($this->dayServed)
            || !isDateValid($this->startTime) || !isDateValid($this->endTime)
        ) {
            $response = 10;
            return $response;
        }
        $existingDateMenu = DateMenu::getDateMenu($this->connection, $this->id, false);
        if ($existingDateMenu) {
            [$fields, $fieldMap, $fieldsSentence] = prepareUpdatedFieldsToBind([
                "creator" => [$this->creator, $existingDateMenu['creator'], "i"],
                "id_food_time" => [$this->idFoodTime, $existingDateMenu['id_food_time'], "i"],
                "id_menu" => [$this->idMenu, $existingDateMenu['id_menu'], "i"],
                "description" => [$this->description, $existingDateMenu['description'], "s"],
                "start" => [$this->startTime, $existingDateMenu['start'], "s"],
                "end" => [$this->endTime, $existingDateMenu['end'], "s"],
            ]);
            if (count($fields) == 0) {
                $response = 14;
            } else {
                if ($this->isUnique()) {
                    $statement = $this->connection->prepare("UPDATE menus_details SET " . $fieldsSentence . " WHERE id=?");
                    $fields = [...$fields, $this->id];
                    $fieldMap = $fieldMap . "s";
                    $statement->bind_param($fieldMap, ...$fields);
                    $response = 2;
                } else {
                    $response = 13;
                }
            }
        } else {
            if ($this->isUnique()) {
                $statement = $this->connection->prepare("INSERT INTO menus_details(day_served,id_food_time,creator,id_menu,description,start,end) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $statement->bind_param('sisisss', $this->dayServed, $this->idFoodTime, $this->creator, $this->idMenu, $this->description, $this->startTime, $this->endTime);
                $response = 0;
            } else {
                $response = 13;
            }
        }
        if (isset($statement)) {
            $statement->execute();
            if (!$statement) $response = 12;
        }
        return $response;
    }

    public static function removeMenu($connection, $id)
    {
        $response = 12;
        $statement = $connection->prepare("DELETE FROM menus_details WHERE id=?");
        $statement->bind_param('s', $id);
        if (isset($statement)) {
            $statement->execute();
            if ($statement) $response = 1;
        }
        return $response;
    }

    public static function getAllDateMenus($connection, $date)
    {
        if (isDateValid($date)) {

            $statement = $connection->prepare("select menus_details.id as id,food_times.name as tname, 
            menus.name as mname, menus_details.description as description, start, end 
            from menus_details inner JOIN food_times on food_times.id=menus_details.id_food_time 
            inner JOIN menus on menus.id=menus_details.id_menu where day_served=?");
            $statement->bind_param('s', $date);
            $statement->execute();
            if ($statement)
                return $statement->get_result();
            return null;
        }
    }

    public static function getAllDateMenusWithRange($connection, $start, $end)
    {
        $statement = $connection->prepare("select menus.name as title, start,end, start as dbStart, 
        menus_details.id as identificator from menus_details inner JOIN menus on menus.id=menus_details.id_menu 
        where day_served BETWEEN ? and ?");
        $statement->bind_param('ss', $start, $end);
        $statement->execute();
        if ($statement) {
            $result = $statement->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($result);
        }
    }
}
