<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db.php");

$data = json_decode(file_get_contents("php://input"));

$order_id = $data->order_id;
$status = $data->status;

$query = "UPDATE orders
SET status='$status'
WHERE id='$order_id'";

if(mysqli_query($conn, $query)){

    echo json_encode([
        "success" => true,
        "message" => "Status Updated"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Update Failed"
    ]);

}

?>