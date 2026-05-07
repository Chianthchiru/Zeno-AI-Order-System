<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db.php");

$data = json_decode(file_get_contents("php://input"));

$part_name = $data->part_name;
$material = $data->material;
$quantity = $data->quantity;
$deadline = $data->deadline;

$query = "INSERT INTO orders
(part_name, material, quantity, deadline, status)

VALUES
(
 '$part_name',
 '$material',
 '$quantity',
 '$deadline',
 'Received'
)";

if(mysqli_query($conn, $query)){

    echo json_encode([
        "success" => true,
        "message" => "Order Created"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Order Failed"
    ]);

}

?>