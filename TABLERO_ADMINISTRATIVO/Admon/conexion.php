<?php
$host = "localhost";
$user = "root";
$pass = "";
$database = "traspasemos";

$conn = new mysqli($host, $user, $pass, $database);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
