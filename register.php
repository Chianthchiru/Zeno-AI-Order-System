<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db.php");

$data = json_decode(file_get_contents("php://input"));

$username = $data->username;
$email = $data->email;
$password = password_hash($data->password, PASSWORD_DEFAULT);

$query = "INSERT INTO users
(username, email, password)

VALUES
('$username', '$email', '$password')";

if(mysqli_query($conn, $query)){

    echo json_encode([
        "success" => true,
        "message" => "User Registered"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Registration Failed"
    ]);

}

?>