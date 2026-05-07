<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db.php");

$data = json_decode(file_get_contents("php://input"));

$order_id = $data->order_id;
$note = $data->note;

$timestamp = date("Y-m-d H:i:s");

$query = "INSERT INTO quality_logs
(order_id, note, timestamp)

VALUES
(
 '$order_id',
 '$note',
 '$timestamp'
)";

if(mysqli_query($conn, $query)){

    echo json_encode([
        "success" => true,
        "message" => "Quality Note Added"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Failed"
    ]);

}

?>
