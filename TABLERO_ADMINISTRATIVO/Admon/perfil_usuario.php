<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['usuario'])) {
    echo json_encode([
        "logueado" => true,
        "nombre" => $_SESSION['usuario'],
        "foto" => "img/Foto.png" // O la ruta real desde la base de datos
    ]);
} else {
    echo json_encode([
        "logueado" => false
    ]);
}
?>
