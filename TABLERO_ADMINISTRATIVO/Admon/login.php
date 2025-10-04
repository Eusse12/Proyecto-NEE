<?php
session_start();

$host = "localhost";
$user = "root";   
$pass = "";       
$dbname = "traspasemos";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("❌ Error en la conexión: " . $conn->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['correo'];
    $clave  = $_POST['clave'];

    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($clave, $usuario['clave'])) {
            $_SESSION['usuario'] = $usuario['nombre_completo'];
            // ✅ Redirigir al index
            header("Location: index.html");
            exit;
        } else {
            $mensaje = "❌ Contraseña incorrecta.";
        }
    } else {
        $mensaje = "⚠️ No existe una cuenta con ese correo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow-lg border-0">
          <div class="card-body p-4">
            <h2 class="text-center mb-4">🔑 Iniciar Sesión</h2>
            
            <!-- Mostrar mensaje si existe -->
            <?php if ($mensaje): ?>
              <div class="alert alert-info text-center"><?= $mensaje ?></div>
            <?php endif; ?>

            <!-- Formulario de login -->
            <form method="POST" action="">
              <div class="mb-3">
                <label for="correo" class="form-label">Correo electrónico</label>
                <input type="email" name="correo" id="correo" class="form-control" placeholder="ejemplo@correo.com" required>
              </div>

              <div class="mb-3">
                <label for="clave" class="form-label">Contraseña</label>
                <input type="password" name="clave" id="clave" class="form-control" placeholder="********" required>
              </div>

              <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>

            <div class="text-center mt-3">
              <a href="register.php">¿No tienes cuenta? Regístrate</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
