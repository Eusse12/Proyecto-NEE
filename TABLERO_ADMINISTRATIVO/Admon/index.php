<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../inicio.php");
    exit;
}

// 🔹 Cargar foto si no está en sesión
if (!isset($_SESSION['foto'])) {
    $conn = new mysqli("localhost", "root", "", "traspasemos");
    if (!$conn->connect_error) {
        $conn->set_charset("utf8mb4");
        $sql = "SELECT foto FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            $_SESSION['foto'] = $usuario['foto'] ?? 'img/default.png';
        }
        $stmt->close();
        $conn->close();
    }
}

$nombre = $_SESSION['usuario'];
$foto = $_SESSION['foto'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>TRASPASEMOS</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.css" rel="stylesheet">
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
            <li class="nav-item active">
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

                    <div class="row mt-5 mb-5">
                        <div class="col-md-12">
                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="text-center my-auto py-5">
                                    <img src="img/logo2.png" alt="Logo" height="300" width="300">
                                    <h3 style="font-family: 'Times New Roman', serif;">BIENVENIDOS</h3>
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

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Cerrar Sesión?</h5>
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

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>
    <script src="js/usuario.js"></script>

</body>

</html>