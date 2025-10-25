<?php
session_start();

// Verificar sesión y tipo de usuario
if (!isset($_SESSION["usuario"]) || $_SESSION["tipo"] != "Administrador") {
    die("Acceso denegado. Solo administradores pueden entrar.");
}

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$database = "traspasemos";

$conn = new mysqli($host, $user, $pass, $database);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipoMensaje = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = intval($_POST['usuarioId'] ?? 0);

    if ($accion === 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "🗑️ Usuario eliminado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al eliminar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }
}

// Consultar usuarios
$sql = "SELECT * FROM usuarios ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Usuarios - TRASPASEMOS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon">
                <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-width: 100px;">
            </div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link" href="index.html">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>TRASPASEMOS</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <li class="nav-item active">
            <a class="nav-link" href="Usuarios.php">
                <i class="fas fa-fw fa-user"></i>
                <span>Usuarios</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="charts.html">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Reportes</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfig">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Configuración</span>
            </a>
            <div id="collapseConfig" class="collapse" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="TipoDocumento.html">Tipo Documento</a>
                    <a class="collapse-item" href="grado.php">Grado</a>
                    <a class="collapse-item" href="sede.php">Sede</a>
                    <a class="collapse-item" href="Grupo.php">Grupos</a>
                    <a class="collapse-item" href="aspecto_complementario.php">Aspectos Complementarios</a>
                    <a class="collapse-item" href="aspecto_academico.php">Aspectos Académicos</a>
                    <a class="collapse-item" href="Tipo_usuario.html">Tipos de Usuarios</a>
                    <a class="collapse-item" href="Tipo_Estudiante.html">Tipos de Estudiantes</a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            Bienvenido, <?= htmlspecialchars($_SESSION["usuario"]) ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger btn-sm" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid">
                
                <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <h1 class="h3 mb-4 text-primary">
                    <i class="fas fa-users"></i> Gestión de Usuarios
                </h1>

                <!-- Tabla de Usuarios -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white">Lista de Usuarios</h6>
                        <button type="button" class="btn btn-success btn-sm" onclick="window.location.href='agregar_usuario.php'">
                            <i class="fas fa-plus"></i> Agregar Usuario
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="text-center">Cód.</th>
                                        <th class="text-center">Tipo Usuario</th>
                                        <th class="text-center">Tipo Doc.</th>
                                        <th class="text-center">Identificación</th>
                                        <th class="text-center">Nombre Completo</th>
                                        <th class="text-center">Correo</th>
                                        <th class="text-center">Celular</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= $row["id"] ?></td>
                                            <td><?= htmlspecialchars($row["tipo_usuario"]) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row["tipo_documento"]) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row["identificacion"]) ?></td>
                                            <td><?= htmlspecialchars($row["nombre_completo"]) ?></td>
                                            <td><?= htmlspecialchars($row["correo"]) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row["celular"]) ?></td>
                                            <td class="text-center">
                                                <a href="editar_usuario.php?id=<?= $row["id"] ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar este usuario?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="usuarioId" value="<?= $row["id"] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay usuarios registrados
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-light">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; TRASPASEMOS 2025</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

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