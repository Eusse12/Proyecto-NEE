<?php
session_start();

// Si el usuario no ha iniciado sesión, redirige al login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "traspasemos";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Consulta para obtener los datos del usuario actual
$nombre_usuario = $_SESSION['usuario'];
$sql = "SELECT nombre_completo, correo, foto FROM usuarios WHERE nombre_completo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombre_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Perfil - TRASPASEMOS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container mt-5">
    <div class="card shadow-lg border-0">
      <div class="card-body text-center p-5">
        <h2 class="mb-4">👤 Mi Perfil</h2>

        <img src="<?= $usuario['foto'] ?: 'img/Foto.png' ?>" alt="Foto de perfil" class="rounded-circle mb-3" width="150" height="150">

        <h4 class="mb-3"><?= htmlspecialchars($usuario['nombre_completo']) ?></h4>
        <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?></p>

        <hr>
        <a href="index.php" class="btn btn-primary mt-3">⬅ Volver al inicio</a>
      </div>
    </div>
  </div>

</body>
</html>
