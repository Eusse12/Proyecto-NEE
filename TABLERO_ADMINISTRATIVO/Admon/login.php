<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db   = "traspasemos";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Procesar login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo   = $_POST['correo'];
    $password = $_POST['password'];

    // Buscar usuario
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar contraseña
        if (password_verify($password, $user['password'])) {
            // Guardar sesión
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_name'] = $user['nombre_completo'];
            $_SESSION['tipo']       = $user['tipo_usuario'];

            // Redirigir al tablero
            header("Location: usuarios.php");
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "El correo no está registrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - TRASPASEMOS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">

<div class="container">

    <!-- Card Login -->
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-5">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Iniciar Sesión</h1>
                    </div>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger text-center">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="form-group">
                            <input type="email" name="correo" class="form-control form-control-user"
                                   placeholder="Correo electrónico" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control form-control-user"
                                   placeholder="Contraseña" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-user btn-block">
                            Ingresar
                        </button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a class="small" href="registro.php">¿No tienes cuenta? ¡Regístrate!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
