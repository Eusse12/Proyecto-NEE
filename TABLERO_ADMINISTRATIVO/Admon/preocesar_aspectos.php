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

$nombreA = $_POST['nombre_academico'] ?? '';
$descripcionA = $_POST['descripcion_academico'] ?? '';
$areaA = $_POST['area_academica'] ?? '';
$horasA = $_POST['horas_academicas'] ?? 0;
$responsableA = $_POST['responsable_academico'] ?? '';
$gradoA = $_POST['grado_academico'] ?? '';
$estadoA = $_POST['estado_academico'] ?? 'Activo';

$nombreC = $_POST['nombre_complementario'] ?? '';
$descripcionC = $_POST['descripcion_complementario'] ?? '';
$categoriaC = $_POST['categoria_complementaria'] ?? 'Otro';
$observacionesC = $_POST['observaciones_complementario'] ?? '';
$responsableC = $_POST['responsable_complementario'] ?? '';
$gradoC = $_POST['grado_complementario'] ?? '';
$estadoC = $_POST['estado_complementario'] ?? 'Activo';

try {
    // Insertar aspecto académico
    $stmtA = $conn->prepare("INSERT INTO aspecto_academico (nombre, descripcion, area, intensidad_horaria, responsable, grado_asignado, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtA->bind_param("sssisss", $nombreA, $descripcionA, $areaA, $horasA, $responsableA, $gradoA, $estadoA);
    $stmtA->execute();
    $stmtA->close();

    // Insertar aspecto complementario
    $stmtC = $conn->prepare("INSERT INTO aspecto_complementario (nombre, descripcion, categoria, observaciones, responsable, grado_asignado, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtC->bind_param("sssssss", $nombreC, $descripcionC, $categoriaC, $observacionesC, $responsableC, $gradoC, $estadoC);
    $stmtC->execute();
    $stmtC->close();

    header("Location: aspectos.php?mensaje=✅ Aspectos guardados correctamente");
    exit();
} catch (Exception $e) {
    die("Error al registrar los aspectos: " . $e->getMessage());
}
$conn->close();
?>
