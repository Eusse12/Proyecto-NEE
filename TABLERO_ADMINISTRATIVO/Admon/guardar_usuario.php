<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "traspasemos";

// Conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Capturar datos del formulario
$tipo_usuario = $_POST['tipo_usuario'];
$tipo_documento = $_POST['tipo_documento'];
$identificacion = $_POST['identificacion'];
$nombre_completo = $_POST['nombre_completo'];
$correo = $_POST['correo'];
$celular = $_POST['celular'];
$clave = $_POST['password']; // Se guarda como 'clave' en la BD

// Encriptar la contraseña antes de guardarla
$clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

// Insertar datos
$sql = "INSERT INTO usuarios (tipo_usuario, tipo_documento, identificacion, nombre_completo, correo, celular, clave)
        VALUES ('$tipo_usuario', '$tipo_documento', '$identificacion', '$nombre_completo', '$correo', '$celular', '$clave_encriptada')";

if ($conn->query($sql) === TRUE) {
    echo "
    <script>
        alert('✅ Usuario agregado correctamente');
        window.location.href = 'usuarios.php';
    </script>";
} else {
    echo "
    <script>
        alert('❌ Error al agregar el usuario: " . $conn->error . "');
        window.history.back();
    </script>";
}

$conn->close();
?>
