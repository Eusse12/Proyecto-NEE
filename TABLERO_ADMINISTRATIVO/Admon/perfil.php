<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../inicio.php");
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
            $redirigir = true; // ✅ Activar redirección
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
                        $redirigir = true; // ✅ Activar redirección
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
        
        // ✅ Redirigir al index después de 2 segundos si todo salió bien
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
        .profile-img-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #4e73df;
        }
        .img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
            border-radius: 50%;
            cursor: pointer;
        }
        .profile-img-container:hover .img-overlay {
            opacity: 1;
        }
        .img-overlay i {
            color: white;
            font-size: 2rem;
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
                        <h1 class="h3 mb-0 text-gray-800">Mi Perfil</h1>
                    </div>

                    <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <strong><?php echo $mensaje; ?></strong>
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
                            <div class="card shadow mb-4">
                                <div class="card-body text-center">
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
                                    <p class="text-muted mb-1"><?php echo htmlspecialchars($tipo_usuario); ?></p>
                                    <p class="text-muted mb-4"><?php echo htmlspecialchars($correo); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Editar Perfil -->
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Editar Información</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="nombre_completo">Nombre Completo</label>
                                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                                                   value="<?php echo htmlspecialchars($nombre); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="identificacion">Identificación</label>
                                            <input type="text" class="form-control" id="identificacion" name="identificacion" 
                                                   value="<?php echo htmlspecialchars($identificacion); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="correo">Correo Electrónico</label>
                                            <input type="email" class="form-control" id="correo" name="correo" 
                                                   value="<?php echo htmlspecialchars($correo); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="foto-input">Foto de Perfil</label>
                                            <input type="file" class="form-control-file" id="foto-input" name="foto" accept="image/*">
                                            <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG, GIF</small>
                                        </div>
                                        
                                        <hr>
                                        
                                        <h6 class="font-weight-bold text-primary mb-3">Cambiar Contraseña (Opcional)</h6>
                                        
                                        <div class="form-group">
                                            <label for="clave_actual">Contraseña Actual</label>
                                            <input type="password" class="form-control" id="clave_actual" name="clave_actual" 
                                                   placeholder="Dejar en blanco si no desea cambiarla">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="clave_nueva">Nueva Contraseña</label>
                                            <input type="password" class="form-control" id="clave_nueva" name="clave_nueva">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="clave_confirmar">Confirmar Nueva Contraseña</label>
                                            <input type="password" class="form-control" id="clave_confirmar" name="clave_confirmar">
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                        <a href="index.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
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
    // Vista previa de la imagen
    document.getElementById('foto-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
    </script>

</body>

</html>