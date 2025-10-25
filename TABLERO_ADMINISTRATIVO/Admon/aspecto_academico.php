<?php
// Configuración de conexión
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "traspasemos";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipo = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    $id = intval($_POST["id"] ?? 0);
    $nombre = trim($_POST["nombre_aspecto"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $grado = trim($_POST["grado_asociado"] ?? "");

    if ($accion === "agregar") {
        if ($nombre === "" || $descripcion === "") {
            $mensaje = "Todos los campos obligatorios deben llenarse.";
            $tipo = "warning";
        } else {
            $stmt = $conn->prepare("INSERT INTO aspectos_academicos (nombre_aspecto, descripcion, grado_asociado) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $descripcion, $grado);
            if ($stmt->execute()) {
                $mensaje = "Aspecto académico agregado correctamente.";
                $tipo = "success";
            } else {
                $mensaje = "Error al guardar: " . $stmt->error;
                $tipo = "danger";
            }
            $stmt->close();
        }
    } elseif ($accion === "eliminar" && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM aspectos_academicos WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "Registro eliminado correctamente.";
            $tipo = "success";
        } else {
            $mensaje = "Error al eliminar.";
            $tipo = "danger";
        }
        $stmt->close();
    }
}

// Consulta de registros
$result = $conn->query("SELECT * FROM aspectos_academicos ORDER BY id DESC");
if (!$result) {
    die("Error al obtener los aspectos académicos: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspectos Académicos - TRASPASEMOS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
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

        <li class="nav-item">
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
                    <a class="collapse-item" href="Sedes.html">Sede</a>
                    <a class="collapse-item" href="Grupo.php">Grupos</a>
                    <a class="collapse-item" href="aspecto_complementario.php">Aspectos Complementarios</a>
                    <a class="collapse-item active" href="aspecto_academico.php">Aspectos Académicos</a>
                    <a class="collapse-item" href="Tipo_usuario.html">Tipos de Usuarios</a>
                    <a class="collapse-item" href="Tipo_Estudiante.html">Tipos de Estudiantes</a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content" class="p-4">

            <!-- Mensajes -->
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?= $tipo === 'success' ? 'check-circle' : ($tipo === 'warning' ? 'exclamation-triangle' : 'times-circle') ?>"></i>
                    <?= $mensaje ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Formulario de Registro -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold text-center">Registrar Aspecto Académico</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="accion" value="agregar">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Nombre del Aspecto <span class="text-danger">*</span></label>
                                <input type="text" name="nombre_aspecto" class="form-control" 
                                       placeholder="Ej: Desempeño en matemáticas" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Grado Asociado</label>
                                <input type="text" name="grado_asociado" class="form-control" 
                                       placeholder="Ej: 10° o 11°">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <textarea name="descripcion" class="form-control" rows="3" 
                                      placeholder="Descripción del aspecto académico..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <a href="index.html" class="btn btn-secondary">
                            <i class="fas fa-home"></i> Volver al Inicio
                        </a>
                    </form>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold text-center">Listado de Aspectos Académicos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary text-white text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Grado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['nombre_aspecto']) ?></td>
                                            <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['grado_asociado'] ?: 'N/A') ?></td>
                                            <td class="text-center"><?= date('d/m/Y', strtotime($row['fecha_creacion'])) ?></td>
                                            <td class="text-center">
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('¿Está seguro de eliminar este registro?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No hay registros disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-light mt-4">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; TRASPASEMOS 2025</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
<?php $conn->close(); ?>