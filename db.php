<?php

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "zeno";

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}



?>