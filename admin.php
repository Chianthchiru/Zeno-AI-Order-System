<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db.php");

/*
-----------------------------------
GET ALL ORDERS
-----------------------------------
*/

if(isset($_GET['get_orders'])){

    $orders_query = mysqli_query(
        $conn,
        "SELECT * FROM orders ORDER BY id DESC"
    );

    $orders = [];

    while($row = mysqli_fetch_assoc($orders_query)){
        $orders[] = $row;
    }

    echo json_encode([
        "success" => true,
        "orders" => $orders
    ]);

    exit;
}

/*
-----------------------------------
ACCEPT ORDER
-----------------------------------
*/

if(isset($_POST['accept_order'])){

    $order_id = $_POST['accept_order'];

    mysqli_query(
        $conn,
        "UPDATE orders
         SET status='Accepted'
         WHERE id='$order_id'"
    );

    echo json_encode([
        "success" => true,
        "message" => "Order Accepted"
    ]);

    exit;
}

/*
-----------------------------------
RECHECK ORDER
-----------------------------------
*/

if(isset($_POST['recheck_order'])){

    $order_id = $_POST['recheck_order'];

    mysqli_query(
        $conn,
        "UPDATE orders
         SET status='In Review'
         WHERE id='$order_id'"
    );

    echo json_encode([
        "success" => true,
        "message" => "Order Sent For Recheck"
    ]);

    exit;
}

/*
-----------------------------------
DELETE ORDER
-----------------------------------
*/

if(isset($_POST['delete_order'])){

    $order_id = $_POST['delete_order'];

    mysqli_query(
        $conn,
        "DELETE FROM orders
         WHERE id='$order_id'"
    );

    echo json_encode([
        "success" => true,
        "message" => "Order Deleted"
    ]);

    exit;
}

/*
-----------------------------------
GET ALL USERS
-----------------------------------
*/

if(isset($_GET['get_users'])){

    $users_query = mysqli_query(
        $conn,
        "SELECT id, username, email
         FROM users
         ORDER BY id DESC"
    );

    $users = [];

    while($row = mysqli_fetch_assoc($users_query)){
        $users[] = $row;
    }

    echo json_encode([
        "success" => true,
        "users" => $users
    ]);

    exit;
}

/*
-----------------------------------
DELETE USER
-----------------------------------
*/

if(isset($_POST['delete_user'])){

    $user_id = $_POST['delete_user'];

    mysqli_query(
        $conn,
        "DELETE FROM users
         WHERE id='$user_id'"
    );

    echo json_encode([
        "success" => true,
        "message" => "User Deleted"
    ]);

    exit;
}

/*
-----------------------------------
INVALID REQUEST
-----------------------------------
*/

echo json_encode([
    "success" => false,
    "message" => "Invalid Admin Request"
]);

?>