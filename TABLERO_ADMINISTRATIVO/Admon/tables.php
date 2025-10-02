<?php
session_start();

// 🚨 Verificar sesión y tipo de usuario
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo"] != "Administrador") {
    die("Acceso denegado. Solo administradores pueden entrar.");
}

// 📌 Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$database = "traspasemos";

$conn = new mysqli($host, $user, $pass, $database);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// 📌 Consultar usuarios
$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>TRASPASEMOS</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page-->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">TRASPASEMOS</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">Administración</div>

            <!-- Nav Item - Usuarios -->
            <li class="nav-item active">
                <a class="nav-link" href="tables.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Usuarios</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                Bienvenido, <?= $_SESSION["usuario"]; ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-danger btn-sm" href="logout.php">Cerrar sesión</a>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2" style="color: blue;">
                        <i class="fa fa-users" aria-hidden="true"></i> Usuarios
                    </h1>

                    <!-- DataTable -->
                    <div class="card shadow mb-4">
                        <div class="d-flex justify-content-end m-3">
                            <button type="button" class="btn btn-primary align-content-end">
                                Agregar <i class="fa fa-plus-circle" aria-hidden="true"></i>
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
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row["id"] ?></td>
                                            <td><?= $row["tipo_usuario"] ?></td>
                                            <td><?= $row["tipo_documento"] ?></td>
                                            <td><?= $row["identificacion"] ?></td>
                                            <td><?= $row["nombre_completo"] ?></td>
                                            <td><?= $row["correo"] ?></td>
                                            <td><?= $row["celular"] ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-info">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Traspasemos 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>
