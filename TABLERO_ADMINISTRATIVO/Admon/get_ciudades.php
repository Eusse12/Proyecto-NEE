<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}
$conn->set_charset("utf8mb4");

$id_departamento = intval($_GET['id_departamento'] ?? 0);

if ($id_departamento > 0) {
    $stmt = $conn->prepare("SELECT id, nombre FROM ciudad WHERE id_departamento = ? ORDER BY nombre ASC");
    $stmt->bind_param("i", $id_departamento);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $ciudades = [];
    while ($row = $result->fetch_assoc()) {
        $ciudades[] = $row;
    }
    
    echo json_encode($ciudades);
    $stmt->close();
} else {
    echo json_encode([]);
}

$conn->close();
?>