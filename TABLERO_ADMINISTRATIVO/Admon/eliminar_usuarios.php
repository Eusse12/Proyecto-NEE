<?php
session_start();

// Configuración de base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db   = "traspasemos";

// Conexión
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Verificar si se recibió el ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensaje'] = "Error: ID de usuario no válido";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: usuarios.php");
    exit();
}

$id = intval($_GET['id']); // Convertir a entero para seguridad

// Verificar si el usuario existe antes de eliminar
$sql_check = "SELECT nombre_completo FROM usuarios WHERE id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = "Error: El usuario no existe";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: usuarios.php");
    exit();
}

$usuario = $result->fetch_assoc();
$nombre = $usuario['nombre_completo'];

// Eliminar el usuario usando prepared statement
$sql_delete = "DELETE FROM usuarios WHERE id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $id);

if ($stmt_delete->execute()) {
    $_SESSION['mensaje'] = "✓ Usuario '$nombre' eliminado exitosamente";
    $_SESSION['tipo_mensaje'] = "success";
} else {
    $_SESSION['mensaje'] = "✗ Error al eliminar el usuario: " . $conn->error;
    $_SESSION['tipo_mensaje'] = "danger";
}

$stmt_check->close();
$stmt_delete->close();
$conn->close();

// Redirigir de vuelta a la página de usuarios
header("Location: usuarios.php");
exit();
?>