<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipoMensaje = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? '';
    $id = intval($_POST['sedeId'] ?? 0);
    $nombre = trim($_POST["nombresede"] ?? '');
    $direccion = trim($_POST["direccion"] ?? '');

    if ($accion === 'agregar') {
        if (!empty($nombre) && !empty($direccion)) {
            $stmt = $conn->prepare("INSERT INTO sede (nombre, direccion) VALUES (?, ?)");
            $stmt->bind_param("ss", $nombre, $direccion);
            if ($stmt->execute()) {
                $mensaje = "✅ Sede agregada correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "❌ Error al guardar: " . $conn->error;
                $tipoMensaje = "danger";
            }
            $stmt->close();
        } else {
            $mensaje = "⚠ Todos los campos son obligatorios.";
            $tipoMensaje = "warning";
        }
    }

    if ($accion === 'editar') {
        if ($id > 0 && !empty($nombre) && !empty($direccion)) {
            $stmt = $conn->prepare("UPDATE sede SET nombre=?, direccion=? WHERE id=?");
            $stmt->bind_param("ssi", $nombre, $direccion, $id);
            if ($stmt->execute()) {
                $mensaje = "✏️ Sede actualizada correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "❌ Error al actualizar: " . $conn->error;
                $tipoMensaje = "danger";
            }
            $stmt->close();
        } else {
            $mensaje = "⚠ Datos inválidos para editar.";
            $tipoMensaje = "warning";
        }
    }

    if ($accion === 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM sede WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "🗑️ Sede eliminada correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al eliminar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }
}

// Obtener datos para editar
$editarSede = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM sede WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editarSede = $result->fetch_assoc();
        $stmt->close();
    }
}

// Consultar sedes
$sedes = $conn->query("SELECT id, nombre, direccion FROM sede ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Sedes</title>
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
                    <a class="collapse-item active" href="sede.php">Sede</a>
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
            <div class="container-fluid mt-4">
                
                <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <!-- Formulario Agregar/Editar -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: <?= $editarSede ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editarSede ? 'edit' : 'plus' ?>"></i>
                            <?= $editarSede ? 'Editar Sede' : 'Agregar Nueva Sede' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="<?= $editarSede ? 'editar' : 'agregar' ?>">
                            <?php if ($editarSede): ?>
                            <input type="hidden" name="sedeId" value="<?= $editarSede['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre de la Sede <span class="text-danger">*</span></label>
                                    <input type="text" name="nombresede" class="form-control" 
                                           placeholder="Ej: Sede Principal"
                                           value="<?= $editarSede ? htmlspecialchars($editarSede['nombre']) : '' ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Dirección <span class="text-danger">*</span></label>
                                    <input type="text" name="direccion" class="form-control" 
                                           placeholder="Ej: Calle 123 #45-67"
                                           value="<?= $editarSede ? htmlspecialchars($editarSede['direccion']) : '' ?>" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-<?= $editarSede ? 'warning' : 'success' ?>">
                                <i class="fas fa-<?= $editarSede ? 'save' : 'plus' ?>"></i>
                                <?= $editarSede ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            <?php if ($editarSede): ?>
                            <a href="sede.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Sedes -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Sedes Registradas</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th width="35%">Nombre de la Sede</th>
                                        <th width="35%">Dirección</th>
                                        <th width="20%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($sedes && $sedes->num_rows > 0): ?>
                                        <?php while ($row = $sedes->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                                            <td><?= htmlspecialchars($row['direccion']) ?></td>
                                            <td class="text-center">
                                                <a href="?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar esta sede?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="sedeId" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay sedes registradas
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="index.html" class="btn btn-secondary mt-3">
                            <i class="fas fa-home"></i> Volver al Menú Principal
                        </a>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
<?php $conn->close(); ?>