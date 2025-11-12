<?php
include("Model.php");

class Car extends Model {
    private int $id;
    private string $name;
    private string $year;
    private string $color;

    protected static string $table = "cars";

    public function __construct(array $data){
        if (!isset($data["name"], $data["year"], $data["color"])) {
            throw new InvalidArgumentException("Car/contructor: name, year, and color are required");
        }

        $this->id = isset($data["id"]) ? (int)$data["id"] : 0;
        $this->name = $data["name"];
        $this->year = (int)$data["year"];
        $this->color = $data["color"];
    }


    public function toArray(): array {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "year" => $this->year,
            "color" => $this->color
        ];
    }

    public static function create(mysqli $connection,string $name,string $year,string $color){
        $sql = "INSERT INTO `cars`(`name`,`year`,`color`) VALUES(?,?,?)";
        $query = $connection->prepare($sql);
        if (!$query) {
            throw new Exception("Prepare failed: " . $connection->error);
        }
        $query->bind_param("sss", $name, $year, $color);
        $success = $query->execute();

        if (!$success) {
            $err = $query->error;
            $query->close();
            throw new Exception("Execute create failed: " . $err);
        }

        $insertedId = $connection->insert_id;

        $query->close();

        $data = [
            "id" => $insertedId,
            "name" => $name,
            "year" => $year,
            "color" => $color
        ];

        return new Car($data);
    
    }

    // public function read(mysqli $connection, $id){
    // }

    public static function update(mysqli $connection,int $id,string $field,string $newValue){
        $allowedFields = ['name', 'year', 'color'];
            if (!in_array($field, $allowedFields)) {
            throw new Exception("Invalid field name");
        }
        
        $sql = "UPDATE `cars` SET `$field` = ?  WHERE `id` = ?";
        $query = $connection->prepare($sql);
        if (!$query) {
            throw new Exception("Prepare failed: " . $connection->error);
        }
        $query->bind_param("si", $newValue, $id);
        $success = $query->execute();

        if (!$success) {
            $err = $query->error;
            $query->close();
            throw new Exception("Execute update failed: " . $err);
        }

        return self::find($connection, $id);
    }


    public static function delete(mysqli $connection,Car $car){
        $id = $car->getID();
        $sql = "DELETE FROM `cars` WHERE `id` = ?";
        $query = $connection->prepare($sql);
        if (!$query) {
            throw new Exception("Prepare failed: " . $connection->error);
        }
        $query->bind_param("i", $id);
        $success = $query->execute();

        if(!$success) {
            $err = $query->error;
            $query->close();
            throw new Exception("Execute delete failed: " . $err);
        }
        
        return $car->toArray();
    }

    public function getID(){
        return $this->id;
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }

    public function setColor(string $color){
        $this->color = $color;
    }

    public function getColor(){
        return $this->color;
    }

    public function __toString(){
        return $this->id . " | " . $this->name . " | " . $this->year. " | " . $this->color;
    }
    

}

?>