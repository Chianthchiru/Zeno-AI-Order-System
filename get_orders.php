<?php

header("Content-Type: application/json");

include("db.php");

$query = "SELECT * FROM orders";

$result = mysqli_query($conn, $query);

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

echo json_encode($orders);

?>