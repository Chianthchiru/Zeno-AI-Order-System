<?php



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);



header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


include("db.php");
include("ai.php");


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







$data = json_decode(file_get_contents("php://input"));

$message = strtolower($data->message);

/*
-----------------------------------
AI RESPONSE
-----------------------------------
*/

$ai_response = extractOrderData($message);

/*
-----------------------------------
CHECK AI RESPONSE
-----------------------------------
*/

if(
    !isset(
        $ai_response['choices'][0]['message']['content']
    )
){

    echo json_encode([

        "success" => false,

        "message" => "AI Response Failed",

        "ai_response" => $ai_response

    ]);

    exit;
}

/*
-----------------------------------
GET AI CONTENT
-----------------------------------
*/

$content =
$ai_response['choices'][0]['message']['content'];

/*
-----------------------------------
REMOVE ```json
-----------------------------------
*/

$content = str_replace(
    "```json",
    "",
    $content
);

$content = str_replace(
    "```",
    "",
    $content
);

/*
-----------------------------------
REMOVE EXTRA SPACES
-----------------------------------
*/

$content = trim($content);

/*
-----------------------------------
EXTRACT JSON ONLY
-----------------------------------
*/

preg_match(
    '/\{.*\}/s',
    $content,
    $matches
);

if(!isset($matches[0])){

    echo json_encode([

        "success" => false,

        "message" => "Invalid AI JSON",

        "raw_ai_response" => $content

    ]);

    exit;
}

$json_text = $matches[0];

/*
-----------------------------------
DECODE JSON
-----------------------------------
*/

$parsed = json_decode(
    $json_text,
    true
);

/*
-----------------------------------
CHECK JSON ERROR
-----------------------------------
*/

if(json_last_error() !== JSON_ERROR_NONE){

    echo json_encode([

        "success" => false,

        "message" => "JSON Decode Failed",

        "json_error" =>
        json_last_error_msg(),

        "raw_json" => $json_text

    ]);

    exit;
}


$response = [];




/*
-----------------------------------
CREATE ORDER INTENT
-----------------------------------
*/

if(
    strpos($message, "need") !== false ||
    strpos($message, "order") !== false
){

    preg_match('/\d+/', $message, $qty_match);

    $quantity = $qty_match[0] ?? 0;

    $material = "Unknown";

    if(strpos($message, "steel") !== false){
        $material = "Steel";
    }

    if(strpos($message, "aluminum") !== false){
        $material = "Aluminum";
    }

    if(strpos($message, "titanium") !== false){
        $material = "Titanium";
    }

    $part_name = "Part";

    if(strpos($message, "bolt") !== false){
        $part_name = "Bolt";
    }

    if(strpos($message, "flange") !== false){
        $part_name = "Flange";
    }

    if(strpos($message, "rod") !== false){
        $part_name = "Rod";
    }

    preg_match(
        '/by ([a-zA-Z0-9\- ]+)/',
        $message,
        $deadline_match
    );

    $deadline = $deadline_match[1] ?? "Not Specified";

    $query = "INSERT INTO orders
    (
        user_id,
        part_name,
        material,
        quantity,
        deadline,
        status
    )

    VALUES
    (
        '$user_id',
        '$part_name',
        '$material',
        '$quantity',
        '$deadline',
        'Received'
    )";

    mysqli_query($conn, $query);

    $response = [
        "success" => true,
        "intent" => "create_order",
        "message" => "Order Created Successfully",
        "order" => [
            "part_name" => $part_name,
            "material" => $material,
            "quantity" => $quantity,
            "deadline" => $deadline,
            "status" => "Received"
        ]
    ];

}

/*
-----------------------------------
UPDATE STATUS INTENT
-----------------------------------
*/

elseif(
    strpos($message, "accepted") !== false ||
    strpos($message, "review") !== false
){

    preg_match('/\d+/', $message, $id_match);

    $order_id = $id_match[0] ?? 0;

    $status = "In Review";

    if(strpos($message, "accepted") !== false){
        $status = "Accepted";
    }

    $query = "UPDATE orders
    SET status='$status'
    WHERE id='$order_id'";

    mysqli_query($conn, $query);

    $response = [
        "success" => true,
        "intent" => "update_status",
        "message" => "Order Status Updated",
        "order_id" => $order_id,
        "status" => $status
    ];

}

/*
-----------------------------------
QUALITY NOTE INTENT
-----------------------------------
*/

elseif(
    strpos($message, "inspection") !== false ||
    strpos($message, "quality") !== false
){

    preg_match('/\d+/', $message, $id_match);

    $order_id = $id_match[0] ?? 0;

    $timestamp = date("Y-m-d H:i:s");

    $query = "INSERT INTO quality_logs
    (
        order_id,
        note,
        timestamp
    )

    VALUES
    (
        '$order_id',
        '$message',
        '$timestamp'
    )";

    mysqli_query($conn, $query);

    $response = [
        "success" => true,
        "intent" => "quality_update",
        "message" => "Quality Note Added",
        "order_id" => $order_id
    ];

}

/*
-----------------------------------
UNKNOWN INTENT
-----------------------------------
*/

else{

    $response = [
        "success" => false,
        "message" => "Intent Not Recognized"
    ];

}

echo json_encode($response);

?>