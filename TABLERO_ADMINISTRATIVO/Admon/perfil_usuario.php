<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['usuario'])) {
    echo json_encode([
        "logueado" => true,
        "nombre" => $_SESSION['usuario'],
        "foto" => isset($_SESSION['foto']) ? $_SESSION['foto'] : "img/Foto.png"
    ]);
} else {
    echo json_encode([
        "logueado" => false
    ]);
}
?>
