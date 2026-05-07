<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'vendor/autoload.php';

use Firebase\JWT\JWT;

include("db.php");

$secret_key = "my_super_secret_jwt_key_for_zeno_hackathon_project_2026";

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$password = $data->password;

$query = "SELECT * FROM users WHERE email='$email'";

$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){

    $user = mysqli_fetch_assoc($result);

    if(password_verify($password, $user['password'])){

        $payload = [
            "id" => $user['id'],
            "email" => $user['email'],
            "time" => time()
        ];

        $jwt = JWT::encode(
            $payload,
            $secret_key,
            'HS256'
        );

        echo json_encode([
            "success" => true,
            "message" => "Login Successful",
            "token" => $jwt
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Invalid Password"
        ]);

    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "User Not Found"
    ]);

}

?>