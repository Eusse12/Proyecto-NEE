<?php
// ===================================
// CONFIGURACIÓN DE CONEXIÓN
// ===================================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "traspasemos";

// Conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// ===================================
// GUARDAR DATOS DEL FORMULARIO
// ===================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombresede"] ?? '');
    $direccion = trim($_POST["direccion"] ?? '');

    if (empty($nombre) || empty($direccion)) {
        echo "<script>alert('⚠️ Debes llenar todos los campos.'); window.history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO sede (nombre, direccion) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $direccion);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Sede guardada correctamente.'); window.location.href='sede.html';</script>";
    } else {
        echo "<script>alert('❌ Error al guardar: " . $conn->error . "'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
