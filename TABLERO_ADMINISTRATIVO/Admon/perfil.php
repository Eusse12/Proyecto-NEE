<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    // Redirigir dinámicamente a inicio.php
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $base_url = $protocol . "://" . $host . dirname(dirname(dirname($_SERVER['PHP_SELF']))) . "/inicio.php";
    header("Location: " . $base_url);
    exit;
}

// Procesar actualización de perfil
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actualizar_perfil'])) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "traspasemos";

    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        $mensaje = "Error en la conexión a la base de datos";
        $tipoMensaje = "danger";
    } else {
        $conn->set_charset("utf8mb4");
        
        $usuario_id = $_SESSION['usuario_id'];
        $nombre_completo = trim($_POST['nombre_completo'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $identificacion = trim($_POST['identificacion'] ?? '');
        
        // Procesar foto de perfil si se subió
        $foto_ruta = $_SESSION['foto'];
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $directorio_destino = "imagenes_perfil/";
            
            // Crear directorio si no existe
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }
            
            $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($extension, $extensiones_permitidas)) {
                $nombre_archivo = "perfil_" . $usuario_id . "_" . time() . "." . $extension;
                $ruta_completa = $directorio_destino . $nombre_archivo;
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_completa)) {
                    // Eliminar foto anterior si existe y no es la default
                    if ($foto_ruta != 'img/default.png' && file_exists($foto_ruta)) {
                        unlink($foto_ruta);
                    }
                    $foto_ruta = $ruta_completa;
                }
            }
        }
        
        // Actualizar datos en la base de datos - usando 'foto_perfil' según la estructura de la BD
        $sql = "UPDATE usuarios SET nombre_completo = ?, correo = ?, identificacion = ?, foto_perfil = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre_completo, $correo, $identificacion, $foto_ruta, $usuario_id);
        
        if ($stmt->execute()) {
            // Actualizar variables de sesión
            $_SESSION['usuario'] = $nombre_completo;
            $_SESSION['correo'] = $correo;
            $_SESSION['identificacion'] = $identificacion;
            $_SESSION['foto'] = $foto_ruta;
            
            $mensaje = "Perfil actualizado correctamente";
            $tipoMensaje = "success";
        } else {
            $mensaje = "Error al actualizar el perfil";
            $tipoMensaje = "danger";
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Procesar cambio de contraseña
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cambiar_password'])) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "traspasemos";

    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        $mensaje = "Error en la conexión a la base de datos";
        $tipoMensaje = "danger";
    } else {
        $conn->set_charset("utf8mb4");
        
        $usuario_id = $_SESSION['usuario_id'];
        $password_actual = $_POST['password_actual'] ?? '';
        $password_nueva = $_POST['password_nueva'] ?? '';
        $password_confirmar = $_POST['password_confirmar'] ?? '';
        
        // Obtener contraseña actual de la base de datos
        $sql = "SELECT clave FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            
            // Verificar si la contraseña está en MD5 o en password_hash
            $password_valida = false;
            if (password_verify($password_actual, $usuario['clave'])) {
                $password_valida = true;
            } elseif (md5($password_actual) === $usuario['clave']) {
                $password_valida = true;
            }
            
            if ($password_valida) {
                if ($password_nueva === $password_confirmar) {
                    if (strlen($password_nueva) >= 6) {
                        $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                        
                        $sql_update = "UPDATE usuarios SET clave = ? WHERE id = ?";
                        $stmt_update = $conn->prepare($sql_update);
                        $stmt_update->bind_param("si", $password_hash, $usuario_id);
                        
                        if ($stmt_update->execute()) {
                            $mensaje = "Contraseña actualizada correctamente";
                            $tipoMensaje = "success";
                        } else {
                            $mensaje = "Error al actualizar la contraseña";
                            $tipoMensaje = "danger";
                        }
                        
                        $stmt_update->close();
                    } else {
                        $mensaje = "La nueva contraseña debe tener al menos 6 caracteres";
                        $tipoMensaje = "warning";
                    }
                } else {
                    $mensaje = "Las contraseñas nuevas no coinciden";
                    $tipoMensaje = "warning";
                }
            } else {
                $mensaje = "La contraseña actual es incorrecta";
                $tipoMensaje = "danger";
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}

$nombre = $_SESSION['usuario'];
$foto = isset($_SESSION['foto']) ? $_SESSION['foto'] : 'img/default.png';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Mi Perfil - TRASPASEMOS</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.css" rel="stylesheet">
    
    <style>
        .profile-img-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #4e73df;
            margin: 20px auto;
            display: block;
        }
        .custom-file-upload {
            display: inline-block;
            padding: 6px 12px;
            cursor: pointer;
            background-color: #4e73df;
            color: white;
            border-radius: 5px;
            text-align: center;
        }
        .custom-file-upload:hover {
            background-color: #2e59d9;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Logo -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-width: 100px;">
                </div>
            </a>

            <hr class="sidebar-divider my-0">

            <!-- Inicio -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <!-- Usuarios -->
            <li class="nav-item">
                <a class="nav-link" href="Usuarios.php">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <!-- Grado -->
            <li class="nav-item">
                <a class="nav-link" href="grado.php">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Grado</span>
                </a>
            </li>

            <!-- Grupo -->
            <li class="nav-item">
                <a class="nav-link" href="Grupo.php">
                    <i class="fas fa-users-cog"></i>
                    <span>Grupos</span>
                </a>
            </li>

            <!-- Sede -->
            <li class="nav-item">
                <a class="nav-link" href="Sede.php">
                    <i class="fas fa-school"></i>
                    <span>Sede</span>
                </a>
            </li>

            <!-- Remisión -->
            <li class="nav-item">
                <a class="nav-link" href="remision.php">
                    <i class="fas fa-file-medical"></i>
                    <span>Remisiones</span>
                </a>
            </li>

            <!-- Académico -->
            <li class="nav-item">
                <a class="nav-link" href="aspecto_academico.php">
                    <i class="fas fa-book-open"></i>
                    <span>Aspectos Académicos</span>
                </a>
            </li>

            <!-- Aspectos Complementarios -->
            <li class="nav-item">
                <a class="nav-link" href="aspecto_complementario.php">
                    <i class="fas fa-puzzle-piece"></i>
                    <span>Aspectos Complementarios</span>
                </a>
            </li>

            <!-- Acudiente -->
            <li class="nav-item">
                <a class="nav-link" href="acudiente.php">
                    <i class="fas fa-user-tie"></i>
                    <span>Acudiente</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <!-- Configuración -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfig"
                   aria-expanded="true" aria-controls="collapseConfig">
                    <i class="fas fa-cogs"></i>
                    <span>Configuración</span>
                </a>
                <div id="collapseConfig" class="collapse" aria-labelledby="headingConfig" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Catálogos:</h6>
                        <a class="collapse-item" href="tipo_documento.php">
                            <i class="fas fa-id-card"></i> Tipo de Documento
                        </a>
                        <a class="collapse-item" href="tipo_usuario.php">
                            <i class="fas fa-user-shield"></i> Tipo de Usuario
                        </a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider d-none d-md-block">
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Botón menú responsive -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Sección derecha del topbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Usuario con imagen -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <!-- Foto y nombre -->
                                <div class="d-flex align-items-center">
                                    <img class="img-profile rounded-circle mr-2"
                                         src="<?php echo isset($_SESSION['foto']) && $_SESSION['foto'] != '' 
                                                  ? htmlspecialchars($_SESSION['foto']) 
                                                  : 'img/default.png'; ?>"
                                         alt="Foto de perfil"
                                         style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #ddd;">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                        <?php echo isset($_SESSION['usuario']) 
                                                ? htmlspecialchars($_SESSION['usuario']) 
                                                : 'Usuario'; ?>
                                    </span>
                                </div>
                            </a>

                            <!-- Menú desplegable -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="perfil.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Mi Perfil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Imagen de Bienvenida -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card shadow">
                                <div class="text-center my-auto py-4">
                                    <img src="img/logo2.png" alt="Logo Traspasemos" height="200" width="200">
                                    <h3 style="font-family: 'Times New Roman', serif;">BIENVENIDOS</h3>
                                    <p class="text-muted">Gestiona tu perfil y configura tu cuenta</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Mi Perfil</h1>
                    </div>

                    <?php if ($mensaje != ''): ?>
                    <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> <?php echo $mensaje; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Información del Perfil -->
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-user-edit"></i> Información Personal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="perfil.php" enctype="multipart/form-data">
                                        <!-- Foto de perfil -->
                                        <div class="text-center mb-4">
                                            <img id="preview-foto" 
                                                 src="<?php echo htmlspecialchars($foto); ?>" 
                                                 alt="Foto de perfil" 
                                                 class="profile-img-preview">
                                            <div class="mt-3">
                                                <label for="foto" class="custom-file-upload">
                                                    <i class="fas fa-camera"></i> Cambiar Foto
                                                </label>
                                                <input type="file" 
                                                       id="foto" 
                                                       name="foto" 
                                                       accept="image/*" 
                                                       style="display: none;" 
                                                       onchange="previewImage(this)">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nombre_completo">Nombre Completo</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="nombre_completo" 
                                                   name="nombre_completo" 
                                                   value="<?php echo htmlspecialchars($_SESSION['usuario']); ?>" 
                                                   required>
                                        </div>

                                        <div class="form-group">
                                            <label for="correo">Correo Electrónico</label>
                                            <input type="email" 
                                                   class="form-control" 
                                                   id="correo" 
                                                   name="correo" 
                                                   value="<?php echo htmlspecialchars($_SESSION['correo']); ?>" 
                                                   required>
                                        </div>

                                        <div class="form-group">
                                            <label for="identificacion">Identificación</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="identificacion" 
                                                   name="identificacion" 
                                                   value="<?php echo htmlspecialchars($_SESSION['identificacion']); ?>" 
                                                   required>
                                        </div>

                                        <div class="form-group">
                                            <label>Tipo de Usuario</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   value="<?php echo htmlspecialchars($_SESSION['tipo_usuario']); ?>" 
                                                   disabled>
                                        </div>

                                        <button type="submit" name="actualizar_perfil" class="btn btn-primary btn-block">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Cambiar Contraseña -->
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-key"></i> Cambiar Contraseña
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="perfil.php">
                                        <div class="form-group">
                                            <label for="password_actual">Contraseña Actual</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_actual" 
                                                   name="password_actual" 
                                                   required>
                                        </div>

                                        <div class="form-group">
                                            <label for="password_nueva">Nueva Contraseña</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_nueva" 
                                                   name="password_nueva" 
                                                   minlength="6"
                                                   required>
                                            <small class="form-text text-muted">
                                                Mínimo 6 caracteres
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label for="password_confirmar">Confirmar Nueva Contraseña</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmar" 
                                                   name="password_confirmar" 
                                                   minlength="6"
                                                   required>
                                        </div>

                                        <button type="submit" name="cambiar_password" class="btn btn-warning btn-block">
                                            <i class="fas fa-lock"></i> Cambiar Contraseña
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-info-circle"></i> Información de la Cuenta
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>ID de Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario_id']); ?></p>
                                    <p><strong>Tipo de Usuario:</strong> <?php echo htmlspecialchars($_SESSION['tipo_usuario']); ?></p>
                                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($_SESSION['correo']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End of Page Content -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-light">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; TRASPASEMOS 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script>
        // Previsualizar imagen antes de subir
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('preview-foto').src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Validar que las contraseñas coincidan
        document.querySelector('form[action="perfil.php"]').addEventListener('submit', function(e) {
            var passwordNueva = document.getElementById('password_nueva');
            var passwordConfirmar = document.getElementById('password_confirmar');
            
            if (passwordNueva && passwordConfirmar) {
                if (passwordNueva.value !== passwordConfirmar.value) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                }
            }
        });
    </script>

</body>

</html>