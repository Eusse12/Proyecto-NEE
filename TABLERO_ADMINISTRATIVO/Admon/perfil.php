<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /traspasemos_git/Proyecto-NEE/traspasemos/Vista/inicio.php");
    exit;
}
// Procesar actualización de perfil
$mensaje = '';
$tipo_mensaje = '';
$redirigir = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "traspasemos";

    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        $mensaje = "Error en la conexión a la base de datos";
        $tipo_mensaje = "danger";
    } else {
        $conn->set_charset("utf8mb4");
        
        $usuario_id = $_SESSION['usuario_id'];
        $nombre_completo = trim($_POST['nombre_completo']);
        $correo = trim($_POST['correo']);
        $identificacion = trim($_POST['identificacion']);
        
        // Manejar la subida de foto
        $foto_nueva = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $directorio = "imagenes_perfil/";
            
            // Crear el directorio si no existe
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = "perfil_" . $usuario_id . "_" . time() . "." . $extension;
            $ruta_completa = $directorio . $nombre_archivo;
            
            // Validar que sea una imagen
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($extension), $extensiones_permitidas)) {
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_completa)) {
                    $foto_nueva = $ruta_completa;
                }
            }
        }
        
        // Actualizar datos del usuario
        if ($foto_nueva) {
            $sql = "UPDATE usuarios SET nombre_completo = ?, correo = ?, identificacion = ?, foto = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $nombre_completo, $correo, $identificacion, $foto_nueva, $usuario_id);
        } else {
            $sql = "UPDATE usuarios SET nombre_completo = ?, correo = ?, identificacion = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nombre_completo, $correo, $identificacion, $usuario_id);
        }
        
        if ($stmt->execute()) {
            // Actualizar la sesión
            $_SESSION['usuario'] = $nombre_completo;
            $_SESSION['correo'] = $correo;
            $_SESSION['identificacion'] = $identificacion;
            if ($foto_nueva) {
                $_SESSION['foto'] = $foto_nueva;
            }
            
            $mensaje = "Perfil actualizado exitosamente";
            $tipo_mensaje = "success";
            $redirigir = true;
        } else {
            $mensaje = "Error al actualizar el perfil";
            $tipo_mensaje = "danger";
        }
        
        $stmt->close();
        
        // Si se solicitó cambio de contraseña
        if (!empty($_POST['clave_actual']) && !empty($_POST['clave_nueva'])) {
            $clave_actual = $_POST['clave_actual'];
            $clave_nueva = $_POST['clave_nueva'];
            $clave_confirmar = $_POST['clave_confirmar'];
            
            // Verificar contraseña actual
            $sql = "SELECT clave FROM usuarios WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $usuario = $result->fetch_assoc();
            
            if (password_verify($clave_actual, $usuario['clave'])) {
                if ($clave_nueva === $clave_confirmar) {
                    $clave_hash = password_hash($clave_nueva, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET clave = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $clave_hash, $usuario_id);
                    
                    if ($stmt->execute()) {
                        $mensaje .= " | Contraseña actualizada exitosamente";
                        $tipo_mensaje = "success";
                        $redirigir = true;
                    } else {
                        $mensaje .= " | Error al actualizar la contraseña";
                        $tipo_mensaje = "warning";
                    }
                } else {
                    $mensaje .= " | Las contraseñas nuevas no coinciden";
                    $tipo_mensaje = "warning";
                }
            } else {
                $mensaje .= " | La contraseña actual es incorrecta";
                $tipo_mensaje = "warning";
            }
            
            $stmt->close();
        }
        
        $conn->close();
        
        if ($redirigir && $tipo_mensaje === 'success') {
            header("refresh:2;url=index.php");
        }
    }
}

$nombre = $_SESSION['usuario'];
$correo = $_SESSION['correo'];
$identificacion = $_SESSION['identificacion'];
$tipo_usuario = $_SESSION['tipo_usuario'];
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
        /* Mejorar el card de perfil */
        .profile-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .profile-img-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 20px;
        }
        
        .profile-img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .profile-img-container:hover .img-overlay {
            opacity: 1;
        }
        
        .img-overlay i {
            color: white;
            font-size: 2.5rem;
        }
        
        .profile-card h5 {
            font-weight: 700;
            font-size: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .profile-card .badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            font-size: 0.9rem;
            border-radius: 20px;
        }
        
        /* Mejorar el formulario */
        .edit-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .edit-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        
        .form-control {
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        /* Botón personalizado para subir foto */
        .custom-file-upload {
            display: inline-block;
            padding: 12px 25px;
            cursor: pointer;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
            border: none;
        }
        
        .custom-file-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .custom-file-upload i {
            margin-right: 8px;
        }
        
        input[type="file"] {
            display: none;
        }
        
        .file-name {
            display: inline-block;
            margin-left: 15px;
            color: #6c757d;
            font-style: italic;
        }
        
        /* Mejorar botones */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        /* Separador de secciones */
        .section-divider {
            border: 0;
            height: 2px;
            background: linear-gradient(to right, transparent, #667eea, transparent);
            margin: 30px 0;
        }
        
        .password-section-title {
            color: #667eea;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Animación para alertas */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert {
            animation: slideDown 0.3s ease;
            border-radius: 10px;
            border: none;
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
                                         src="<?php echo htmlspecialchars($foto); ?>"
                                         alt="Foto de perfil"
                                         style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #ddd;">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                        <?php echo htmlspecialchars($nombre); ?>
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
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
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

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-user-circle mr-2"></i>Mi Perfil
                        </h1>
                    </div>

                    <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <strong>
                            <?php if ($tipo_mensaje === 'success'): ?>
                                <i class="fas fa-check-circle mr-2"></i>
                            <?php else: ?>
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                            <?php endif; ?>
                            <?php echo $mensaje; ?>
                        </strong>
                        <?php if ($redirigir && $tipo_mensaje === 'success'): ?>
                            <br><small>Redirigiendo al inicio en 2 segundos...</small>
                        <?php endif; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Información del Perfil -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4 profile-card">
                                <div class="card-body text-center py-5">
                                    <div class="profile-img-container">
                                        <img src="<?php echo htmlspecialchars($foto); ?>" 
                                             alt="Foto de perfil" 
                                             class="rounded-circle profile-img"
                                             id="preview-img">
                                        <div class="img-overlay" onclick="document.getElementById('foto-input').click()">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-3"><?php echo htmlspecialchars($nombre); ?></h5>
                                    <span class="badge mb-3"><?php echo htmlspecialchars($tipo_usuario); ?></span>
                                    <p class="mb-2">
                                        <i class="fas fa-envelope mr-2"></i>
                                        <?php echo htmlspecialchars($correo); ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-id-card mr-2"></i>
                                        <?php echo htmlspecialchars($identificacion); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Editar Perfil -->
                        <div class="col-lg-8">
                            <div class="card shadow mb-4 edit-card">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fas fa-edit mr-2"></i>Editar Información
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="nombre_completo">
                                                <i class="fas fa-user mr-2 text-primary"></i>Nombre Completo
                                            </label>
                                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                                                   value="<?php echo htmlspecialchars($nombre); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="identificacion">
                                                <i class="fas fa-id-card mr-2 text-primary"></i>Identificación
                                            </label>
                                            <input type="text" class="form-control" id="identificacion" name="identificacion" 
                                                   value="<?php echo htmlspecialchars($identificacion); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="correo">
                                                <i class="fas fa-envelope mr-2 text-primary"></i>Correo Electrónico
                                            </label>
                                            <input type="email" class="form-control" id="correo" name="correo" 
                                                   value="<?php echo htmlspecialchars($correo); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>
                                                <i class="fas fa-image mr-2 text-primary"></i>Foto de Perfil
                                            </label>
                                            <div class="d-flex align-items-center">
                                                <label for="foto-input" class="custom-file-upload mb-0">
                                                    <i class="fas fa-camera"></i>Elegir Foto de Perfil
                                                </label>
                                                <span class="file-name" id="file-name">Ningún archivo seleccionado</span>
                                            </div>
                                            <input type="file" id="foto-input" name="foto" accept="image/*">
                                            <small class="form-text text-muted mt-2">
                                                <i class="fas fa-info-circle mr-1"></i>Formatos permitidos: JPG, JPEG, PNG, GIF
                                            </small>
                                        </div>
                                        
                                        <hr class="section-divider">
                                        
                                        <h6 class="password-section-title mb-3">
                                            <i class="fas fa-lock"></i>
                                            Cambiar Contraseña (Opcional)
                                        </h6>
                                        
                                        <div class="form-group">
                                            <label for="clave_actual">
                                                <i class="fas fa-key mr-2 text-primary"></i>Contraseña Actual
                                            </label>
                                            <input type="password" class="form-control" id="clave_actual" name="clave_actual" 
                                                   placeholder="Dejar en blanco si no desea cambiarla">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="clave_nueva">
                                                <i class="fas fa-lock mr-2 text-primary"></i>Nueva Contraseña
                                            </label>
                                            <input type="password" class="form-control" id="clave_nueva" name="clave_nueva">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="clave_confirmar">
                                                <i class="fas fa-lock mr-2 text-primary"></i>Confirmar Nueva Contraseña
                                            </label>
                                            <input type="password" class="form-control" id="clave_confirmar" name="clave_confirmar">
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save mr-2"></i>Guardar Cambios
                                            </button>
                                            <a href="index.php" class="btn btn-secondary">
                                                <i class="fas fa-times mr-2"></i>Cancelar
                                            </a>
                                        </div>
                                    </form>
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

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">¿Cerrar Sesión?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">¿Está seguro que desea cerrar sesión?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="logout.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script>
    // Vista previa de la imagen y mostrar nombre del archivo
    document.getElementById('foto-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileNameSpan = document.getElementById('file-name');
        
        if (file) {
            // Actualizar nombre del archivo
            fileNameSpan.textContent = file.name;
            fileNameSpan.style.color = '#667eea';
            fileNameSpan.style.fontStyle = 'normal';
            
            // Vista previa de la imagen
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            fileNameSpan.textContent = 'Ningún archivo seleccionado';
            fileNameSpan.style.color = '#6c757d';
            fileNameSpan.style.fontStyle = 'italic';
        }
    });
    </script>

</body>

</html>