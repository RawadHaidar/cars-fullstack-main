<?php
include("../models/Car.php");
include("../connection/connection.php");
include("../services/ResponseService.php");

function getCarByID($id){
    global $connection;

    $car = Car::find($connection, $id);
    if($car){
        echo ResponseService::response(200, $car->toArray());
    }else{
        echo ResponseService::response(404, "Car not found");
    }
}
function getCars(){
    global $connection;

    if(isset($_GET["id"]) && !empty($_GET["id"])){
        $id = $_GET["id"];
        getCarByID($id);
        return;
    }

    $cars = Car::findAll($connection);

    if(empty($cars)){
        echo ResponseService::response(404, "No cars found");
        return;
    }

    $carList = array_map(fn($car) => $car->toArray(), $cars);
    echo ResponseService::response(200, $carList);
}

function create_car_from_input(){
    global $connection;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'], $data['year'], $data['color'])) {
        echo ResponseService::response(400, "Missing required fields: name || year || color");
        return;
    }

    $name = $data['name'];
    $year = $data['year'];
    $color = $data['color'];

    try {
        $createdCar = Car::create($connection, $name, $year, $color);
        echo ResponseService::response(201, $createdCar->toArray());
    } catch (Exception $e) {
        echo ResponseService::response(500, "Failed to create car: " . $e->getMessage());
    }
}

function updateCar(){
    global $connection;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'], $data['field'], $data['value'])) {
        echo ResponseService::response(400, "Car Update failed, missing fields: id || field || value");
        return;
    }

    $id = (int) $data['id'];
    $field = $data['field'];
    $new_value = $data['value'];

    try {
        $updatedCar = Car::update($connection, $id, $field, $new_value);
        echo ResponseService::response(201, $updatedCar->toArray());
    } catch (Exception $e) {
        echo ResponseService::response(500, "Failed to update car: " . $e->getMessage());
    }
}

// read car already implemented in function getCarByID($id)

function deleteCar($id){
    global $connection;
    $car = Car::find($connection, $id);
    if (!$car) {
    echo ResponseService::response(404, "Car not found, cannot delete.");
    return;
    }

    try {
        $deletedCar = Car::delete($connection, $car);
        echo ResponseService::response(200, $deletedCar);
    } catch (Exception $e) {
        echo ResponseService::response(500, "Failed to delete car: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    create_car_from_input();

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    getCars();

} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    updateCar();

} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id']) && !empty($data['id'])) {
        deleteCar((int) $data['id']);
    }else{
        echo ResponseService::response(400, "Car Delete failed, missing fields: id");
        // exit;
    }

}

?>