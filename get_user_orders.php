<?php
$secret_key =
"my_super_secret_jwt_key_for_zeno_hackathon_project_2026";

$headers = getallheaders();

if(!isset($headers['Authorization'])){

    echo json_encode([
        "success" => false,
        "message" => "Token Missing"
    ]);

    exit;
}

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

    $user_id = $decoded->id;

}catch(Exception $e){

    echo json_encode([
        "success" => false,
        "message" => "Invalid Token"
    ]);

    exit;
}

$query = mysqli_query(
    $conn,
    "SELECT * FROM orders
     WHERE user_id='$user_id'
     ORDER BY id DESC"
);

$orders = [];

while($row = mysqli_fetch_assoc($query)){

    $orders[] = $row;
}

echo json_encode([
    "success" => true,
    "orders" => $orders
]);

?>