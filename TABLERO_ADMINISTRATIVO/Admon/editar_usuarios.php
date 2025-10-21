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

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $_SESSION['mensaje'] = "Error: Método no permitido";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: usuarios.php");
    exit();
}

// Obtener y limpiar datos del formulario
$tipo_usuario = trim($_POST['tipo_usuario']);
$tipo_documento = trim($_POST['tipo_documento']);
$identificacion = trim($_POST['identificacion']);
$nombre_completo = trim($_POST['nombre_completo']);
$correo = trim($_POST['correo']);
$celular = trim($_POST['celular']);
$password = trim($_POST['password']);

// Validaciones básicas
if (empty($tipo_usuario) || empty($tipo_documento) || empty($identificacion) || 
    empty($nombre_completo) || empty($correo) || empty($password)) {
    $_SESSION['mensaje'] = "✗ Error: Todos los campos obligatorios deben estar completos";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: usuarios.php");
    exit();
}

// Validar formato de email
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['mensaje'] = "✗ Error: Formato de correo electrónico inválido";
    $_SESSION['tipo_mensaje'] = "danger";
    header("Location: usuarios.php");
    exit();
}

// Verificar si la identificación ya existe
$sql_check = "SELECT id FROM usuarios WHERE identificacion = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $identificacion);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $_SESSION['mensaje'] = "✗ Error: Ya existe un usuario con esa identificación";
    $_SESSION['tipo_mensaje'] = "warning";
    $stmt_check->close();
    $conn->close();
    header("Location: usuarios.php");
    exit();
}
$stmt_check->close();

// Verificar si el correo ya existe
$sql_check_email = "SELECT id FROM usuarios WHERE correo = ?";
$stmt_check_email = $conn->prepare($sql_check_email);
$stmt_check_email->bind_param("s", $correo);
$stmt_check_email->execute();
$result_check_email = $stmt_check_email->get_result();

if ($result_check_email->num_rows > 0) {
    $_SESSION['mensaje'] = "✗ Error: Ya existe un usuario con ese correo electrónico";
    $_SESSION['tipo_mensaje'] = "warning";
    $stmt_check_email->close();
    $conn->close();
    header("Location: usuarios.php");
    exit();
}
$stmt_check_email->close();

// Encriptar contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar nuevo usuario con prepared statement
$sql = "INSERT INTO usuarios (tipo_usuario, tipo_documento, identificacion, nombre_completo, correo, celular, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $tipo_usuario, $tipo_documento, $identificacion, $nombre_completo, $correo, $celular, $password_hash);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = "✓ Usuario '$nombre_completo' registrado exitosamente";
    $_SESSION['tipo_mensaje'] = "success";
} else {
    $_SESSION['mensaje'] = "✗ Error al registrar usuario: " . $conn->error;
    $_SESSION['tipo_mensaje'] = "danger";
}

$stmt->close();
$conn->close();

// Redirigir de vuelta a la página de usuarios
header("Location: usuarios.php");
exit();
?>