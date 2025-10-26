<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "traspasemos";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $tipo_usuario = $_POST['tipo_usuario'];
    $tipo_documento = $_POST['tipo_documento'];
    $identificacion = $_POST['identificacion'];
    $nombre = $_POST['nombre_completo'];
    $correo = $_POST['correo'];
    $celular = $_POST['celular'];

    $stmt = $conn->prepare("UPDATE usuarios 
                            SET tipo_usuario=?, tipo_documento=?, identificacion=?, 
                                nombre_completo=?, correo=?, celular=? 
                            WHERE id=?");
    $stmt->bind_param("ssssssi", $tipo_usuario, $tipo_documento, $identificacion, $nombre, $correo, $celular, $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "✅ Usuario actualizado correctamente.";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "❌ Error al actualizar el usuario.";
        $_SESSION['tipo_mensaje'] = "danger";
    }

    $stmt->close();
    header("Location: usuarios.php");
    exit;
}
?>
