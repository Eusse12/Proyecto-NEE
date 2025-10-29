<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "traspasemos";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$area = $_POST['area'] ?? '';
$horas = $_POST['horas'] ?? 0;
$responsable = $_POST['responsable'] ?? '';
$grado = $_POST['grado'] ?? '';
$estado = $_POST['estado'] ?? 'Activo';

if ($nombre !== '') {
    $stmt = $conn->prepare("INSERT INTO aspecto_academico (nombre, descripcion, area, intensidad_horaria, responsable, grado_asignado, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisss", $nombre, $descripcion, $area, $horas, $responsable, $grado, $estado);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: aspecto_academico.php?mensaje=✅ Aspecto académico guardado correctamente");
exit();
?>
