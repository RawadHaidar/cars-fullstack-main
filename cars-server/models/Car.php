<?php
include("Model.php");

class Car extends Model {
    private int $id;
    private string $name;
    private string $year;
    private string $color;

    protected static string $table = "cars";

    public function __construct(array $data){
        $this->id = $data["id"];
        $this->name = $data["name"];
        $this->year = $data["year"];
        $this->color = $data["color"];
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
    
    public function toArray(){
        return ["id" => $this->id, "name" => $this->name, "year" => $this->year, "color" => $this->color];
    }

}

?>