<?php
$host = "localhost";
$user = "root";        // en XAMPP por defecto es root
$pass = "";            // en XAMPP no lleva contraseña
$database = "traspasemos";

$conn = new mysqli($host, $user, $pass, $database);

// Comprobar conexión
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}
?>

// esto es un testeo de github
