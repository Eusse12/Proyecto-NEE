<?php
// 🚀 SIN LOGIN: conexión directa a la BD
$host = "localhost";
$user = "root";
$pass = "";
$db   = "traspasemos";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Consulta usuarios
$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>TRASPASEMOS</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
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
                <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
            </a>
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Interface</div>

            <!-- Nav Item - Tables -->
            <li class="nav-item active">
                <a class="nav-link" href="usuarios.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Usuarios</span>
                </a>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <span class="navbar-brand">Panel de Administración</span>
                </nav>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-primary">
                        <i class="fa fa-users"></i> Usuarios
                    </h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="d-flex justify-content-end m-3">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarUsuario">
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
                                                    <td><?= htmlspecialchars($row['id']); ?></td>
                                                    <td><?= htmlspecialchars($row['tipo_usuario']); ?></td>
                                                    <td><?= htmlspecialchars($row['tipo_documento']); ?></td>
                                                    <td><?= htmlspecialchars($row['identificacion']); ?></td>
                                                    <td><?= htmlspecialchars($row['nombre_completo']); ?></td>
                                                    <td><?= htmlspecialchars($row['correo']); ?></td>
                                                    <td><?= htmlspecialchars($row['celular']); ?></td>
                                                    <td class="text-center">
                                                        <a class="btn btn-info btn-sm" href="editar_usuario.php?id=<?= urlencode($row['id']); ?>">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a class="btn btn-danger btn-sm" href="eliminar_usuario.php?id=<?= urlencode($row['id']); ?>" onclick="return confirm('¿Eliminar usuario?');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="text-center">No hay usuarios registrados</td>
                                            </tr>
                                        <?php endif; ?>
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
                        <span>Copyright &copy; TRASPASEMOS 2025</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- End of Page Wrapper -->

    <!-- Modal Agregar Usuario -->
    <div class="modal fade" id="modalAgregarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="guardar_usuario.php" method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Usuario</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group"><label>Tipo Usuario</label><input type="text" name="tipo_usuario" class="form-control" required></div>
                    <div class="form-group"><label>Tipo Documento</label><input type="text" name="tipo_documento" class="form-control" required></div>
                    <div class="form-group"><label>Identificación</label><input type="text" name="identificacion" class="form-control" required></div>
                    <div class="form-group"><label>Nombre Completo</label><input type="text" name="nombre_completo" class="form-control" required></div>
                    <div class="form-group"><label>Correo</label><input type="email" name="correo" class="form-control" required></div>
                    <div class="form-group"><label>Celular</label><input type="text" name="celular" class="form-control"></div>
                    <div class="form-group"><label>Contraseña</label><input type="password" name="password" class="form-control" required></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
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
