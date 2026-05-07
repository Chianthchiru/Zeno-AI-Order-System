<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "my_secret_key";

$headers = getallheaders();

if(isset($headers['Authorization'])){

    $token = str_replace(
        'Bearer ',
        '',
        $headers['Authorization']
    );

    try{

        $decoded = JWT::decode(
            $token,
            new Key($secret_key, 'HS256')
        );

        echo json_encode([
            "success" => true,
            "user" => $decoded
        ]);

    } catch(Exception $e){

        echo json_encode([
            "success" => false,
            "message" => "Invalid Token"
        ]);

    }

}else{

    echo json_encode([
        "success" => false,
        "message" => "Token Missing"
    ]);

}

?>