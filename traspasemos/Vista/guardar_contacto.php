<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "traspasemos";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Capturar datos del formulario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$mensaje = $_POST['mensaje'];

// Insertar en la base de datos
$sql = "INSERT INTO contacto (nombre, email, telefono, mensaje)
        VALUES ('$nombre', '$email', '$telefono', '$mensaje')";

if ($conn->query($sql) === TRUE) {
    echo "
    <script>
      alert('✅ Tu mensaje ha sido enviado correctamente. ¡Gracias por contactarnos!');
      window.location.href = 'index.html';
    </script>";
} else {
    echo "
    <script>
      alert('❌ Error al enviar el mensaje: " . $conn->error . "');
      window.history.back();
    </script>";
}

$conn->close();
?>
