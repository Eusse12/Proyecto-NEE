<?php
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
    $nombre   = $_POST['nombre'];
    $correo   = $_POST['correo'];
    $celular  = $_POST['celular'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $tipo_documento = $_POST['tipo_documento'];
    $identificacion = $_POST['identificacion'];
    $clave    = $_POST['clave'];
    $clave2   = $_POST['clave2'];

    // Validar contraseñas
    if ($clave !== $clave2) {
        $mensaje = "⚠️ Las contraseñas no coinciden.";
    } else {
        $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

        // Validar que no exista correo
        $check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $check->bind_param("s", $correo);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $mensaje = "⚠️ Este correo ya está registrado.";
        } else {
            $sql = "INSERT INTO usuarios (tipo_usuario, tipo_documento, identificacion, nombre_completo, correo, celular, clave)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $tipo_usuario, $tipo_documento, $identificacion, $nombre, $correo, $celular, $clave_hash);

            if ($stmt->execute()) {
                // ✅ Redirigir al login después del registro
                header("Location: login.php");
                exit;
            } else {
                $mensaje = "❌ Error al registrar: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Registrar Cuenta</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Crear Cuenta</h1>
                            </div>

                            <!-- Mostrar mensaje -->
                            <?php if ($mensaje): ?>
                                <div class="alert alert-info text-center"><?= $mensaje ?></div>
                            <?php endif; ?>

                            <form class="user" method="POST" action="">
                                <div class="form-group">
                                    <input type="text" name="nombre" class="form-control form-control-user"
                                        placeholder="Nombre completo" required>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="correo" class="form-control form-control-user"
                                        placeholder="Correo electrónico" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="celular" class="form-control form-control-user"
                                        placeholder="Celular" required>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3">
                                        <input type="text" name="tipo_usuario" class="form-control form-control-user"
                                            placeholder="Tipo de usuario (Ej: Administrador)" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="tipo_documento" class="form-control form-control-user"
                                            placeholder="Tipo de documento (Ej: Cédula)" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="identificacion" class="form-control form-control-user"
                                        placeholder="Número de documento" required>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3">
                                        <input type="password" name="clave" class="form-control form-control-user"
                                            placeholder="Contraseña" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" name="clave2" class="form-control form-control-user"
                                            placeholder="Repite la contraseña" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Registrar Cuenta
                                </button>
                            </form>

                            <hr>
                            <div class="text-center">
                                <a class="small" href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>
</html>
