<?php
session_start();

// Verificar autenticación (descomenta cuando tengas sistema de login)
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Configuración de base de datos (mover a config.php en producción)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "traspasemos";

// Conexión con manejo de errores
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4"); // Asegurar codificación correcta

// Consulta usuarios con prepared statement
$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$result = $conn->query($sql);

// Mensajes de feedback
$mensaje = "";
$tipo_mensaje = "success";
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'success';
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de gestión TRASPASEMOS">
    <meta name="author" content="">

    <title>Usuarios - TRASPASEMOS</title>

    <!-- Custom fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles -->
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <img src="img/Logo.png" alt="Logo TRASPASEMOS">
                </div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <li class="nav-item active">
                <a class="nav-link" href="usuarios.php">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="reportes.php">
                    <i class="fas fa-flag-checkered"></i>
                    <span>Reportes</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfig">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Configuración</span>
                </a>
                <div id="collapseConfig" class="collapse" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="TipoDocumento.html">Tipo Documento</a>
                        <a class="collapse-item" href="TipoUsuario.html">Tipo Usuario</a>
                        <a class="collapse-item" href="TipoEstudiante.html">Tipo Estudiante</a>
                        <a class="collapse-item" href="grado.php">Grado</a>
                        <a class="collapse-item" href="sede.php">Sede</a>
                        <a class="collapse-item" href="Grupo.php">Grupos</a>
                        <a class="collapse-item" href="aspecto_complementario.php">Aspectos Complementarios</a>
                        <a class="collapse-item" href="aspecto_academico.php">Aspectos Académicos</a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?= isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Usuario' ?>
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg" alt="Profile">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End Topbar -->

                <!-- Page Content -->
                <div class="container-fluid">
                    
                    <h1 class="h3 mb-2" style="color: blue;">
                        <i class="fa fa-users"></i> Usuarios
                    </h1>

                    <?php if ($mensaje): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($mensaje) ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="d-flex justify-content-end m-3">
                            <button type="button" class="btn btn-primary align-content-end" data-toggle="modal" data-target="#modalAgregarUsuario">
                                Agregar <i class="fa fa-plus-circle"></i>
                            </button>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead style="background-color: rgb(211, 248, 248);">
                                        <tr>
                                            <th class="text-center">Cód. Usuario</th>
                                            <th class="text-center">Tipo Usuario</th>
                                            <th class="text-center">Tipo Documento</th>
                                            <th class="text-center">Identificación</th>
                                            <th class="text-center">Nombre Completo</th>
                                            <th class="text-center">Correo</th>
                                            <th class="text-center">Celular</th>
                                            <th class="text-center">Modificar</th>
                                            <th class="text-center">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                                    <td><?= htmlspecialchars($row['tipo_usuario']) ?></td>
                                                    <td><?= htmlspecialchars($row['tipo_documento']) ?></td>
                                                    <td><?= htmlspecialchars($row['identificacion']) ?></td>
                                                    <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                                    <td><?= htmlspecialchars($row['correo']) ?></td>
                                                    <td><?= htmlspecialchars($row['celular']) ?></td>
                                                    <td class="text-center">
                                                        <a class="btn btn-info" href="editar_usuario.php?id=<?= urlencode($row['id']) ?>">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-danger" href="eliminar_usuario.php?id=<?= urlencode($row['id']) ?>" 
                                                           onclick="return confirm('¿Eliminar usuario?');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="text-center">⚠️ No hay usuarios registrados</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Page Content -->

            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; TRASPASEMOS <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- Scroll to Top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿Cerrar Sesión?</h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">Seleccione "Salir" para cerrar su sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="logout.php">Salir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Usuario -->
    <div class="modal fade" id="modalAgregarUsuario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="guardar_usuario.php" method="post" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-user-plus"></i> Agregar Nuevo Usuario</h5>
                    <button class="close text-white" type="button" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo Usuario <span class="text-danger">*</span></label>
                                <select name="tipo_usuario" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Docente">Docente</option>
                                    <option value="Estudiante">Estudiante</option>
                                    <option value="Acudiente">Acudiente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="TI">Tarjeta de Identidad</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                    <option value="RC">Registro Civil</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Identificación <span class="text-danger">*</span></label>
                                <input type="text" name="identificacion" class="form-control" required 
                                       pattern="[0-9]+" title="Solo números">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Celular</label>
                                <input type="tel" name="celular" class="form-control" 
                                       pattern="[0-9]{10}" title="10 dígitos">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_completo" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Contraseña <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6"
                               title="Mínimo 6 caracteres">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-save"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>

</body>
</html>
<?php $conn->close(); ?>