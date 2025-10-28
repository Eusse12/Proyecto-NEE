<?php
session_start();

// Configuración de la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$database = "traspasemos";

$conn = new mysqli($host, $user, $pass, $database);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipoMensaje = "";

// Procesar acciones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'agregar':
            $id_estudiante = intval($_POST['id_estudiante'] ?? 0);
            $necesidad_especial = trim($_POST['necesidad_especial'] ?? '');
            $tratamiento = trim($_POST['tratamiento'] ?? '');

            if ($id_estudiante > 0 && $necesidad_especial !== '') {
                $stmt = $conn->prepare("INSERT INTO NEE (id_estudiante, necesidad_especial, tratamiento) VALUES (?, ?, ?)");
                if ($stmt === false) {
                    $mensaje = "❌ Error al preparar la consulta: " . $conn->error;
                    $tipoMensaje = "danger";
                } else {
                    $stmt->bind_param("iss", $id_estudiante, $necesidad_especial, $tratamiento);
                    if ($stmt->execute()) {
                        $mensaje = "✅ Registro agregado correctamente.";
                        $tipoMensaje = "success";
                    } else {
                        $mensaje = "❌ Error al agregar: " . $stmt->error;
                        $tipoMensaje = "danger";
                    }
                    $stmt->close();
                }
            } else {
                $mensaje = "⚠ Todos los campos marcados con * son obligatorios.";
                $tipoMensaje = "warning";
            }
            break;

        case 'editar':
            $id = intval($_POST['neeId'] ?? 0);
            $id_estudiante = intval($_POST['id_estudiante'] ?? 0);
            $necesidad_especial = trim($_POST['necesidad_especial'] ?? '');
            $tratamiento = trim($_POST['tratamiento'] ?? '');

            if ($id > 0 && $id_estudiante > 0 && $necesidad_especial !== '') {
                $stmt = $conn->prepare("UPDATE NEE SET id_estudiante = ?, necesidad_especial = ?, tratamiento = ? WHERE id = ?");
                if ($stmt === false) {
                    $mensaje = "❌ Error al preparar la actualización: " . $conn->error;
                    $tipoMensaje = "danger";
                } else {
                    $stmt->bind_param("issi", $id_estudiante, $necesidad_especial, $tratamiento, $id);
                    if ($stmt->execute()) {
                        $mensaje = "✏️ Registro actualizado correctamente.";
                        $tipoMensaje = "success";
                    } else {
                        $mensaje = "❌ Error al actualizar: " . $stmt->error;
                        $tipoMensaje = "danger";
                    }
                    $stmt->close();
                }
            } else {
                $mensaje = "⚠ Datos inválidos para editar.";
                $tipoMensaje = "warning";
            }
            break;

        case 'eliminar':
            $id = intval($_POST['neeId'] ?? 0);
            if ($id > 0) {
                $stmt = $conn->prepare("DELETE FROM NEE WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        $mensaje = "🗑️ Registro eliminado correctamente.";
                        $tipoMensaje = "success";
                    } else {
                        $mensaje = "❌ Error al eliminar: " . $stmt->error;
                        $tipoMensaje = "danger";
                    }
                    $stmt->close();
                }
            }
            break;
    }
}

// Obtener datos para editar si se pasa un ID
$editarNEE = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM NEE WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editarNEE = $result->fetch_assoc();
        $stmt->close();
    }
}

// Listar todos los registros
$sql = "SELECT * FROM NEE ORDER BY id ASC";
$result = $conn->query($sql);
if ($result === false) {
    die("Error al obtener registros: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Necesidades Educativas Especiales - TRASPASEMOS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div class="sidebar-brand-text mx-3">TRASPASEMOS</div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">Gestión</div>

        <li class="nav-item active">
            <a class="nav-link" href="nee.php">
                <i class="fas fa-fw fa-brain"></i>
                <span>Necesidades Especiales</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="estudiantes.php">
                <i class="fas fa-fw fa-users"></i>
                <span>Estudiantes</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>
    <!-- End of Sidebar -->

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
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Administrador</span>
                            <i class="fas fa-user-circle fa-2x"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Cerrar Sesión
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">
                    <i class="fas fa-brain"></i> Gestión de Necesidades Educativas Especiales
                </h1>

                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                        <?= $mensaje ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: <?= $editarNEE ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editarNEE ? 'edit' : 'plus' ?>"></i>
                            <?= $editarNEE ? 'Editar Registro NEE' : 'Agregar Necesidad Educativa Especial' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="<?= $editarNEE ? 'editar' : 'agregar' ?>">
                            <?php if ($editarNEE): ?>
                                <input type="hidden" name="neeId" value="<?= $editarNEE['id'] ?>">
                            <?php endif; ?>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="id_estudiante">ID Estudiante <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="id_estudiante" name="id_estudiante"
                                           value="<?= $editarNEE ? htmlspecialchars($editarNEE['id_estudiante']) : '' ?>" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="necesidad_especial">Necesidad Especial <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="necesidad_especial" name="necesidad_especial"
                                           value="<?= $editarNEE ? htmlspecialchars($editarNEE['necesidad_especial']) : '' ?>" 
                                           placeholder="Ej: TDAH, Ansiedad, TOC" required>
                                </div>

                                <div class="form-group col-md-5">
                                    <label for="tratamiento">Tratamiento / Estrategia</label>
                                    <textarea class="form-control" id="tratamiento" name="tratamiento" rows="2" 
                                              placeholder="Describe el tratamiento o estrategia de apoyo"><?= $editarNEE ? htmlspecialchars($editarNEE['tratamiento']) : '' ?></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-<?= $editarNEE ? 'warning' : 'success' ?>">
                                <i class="fas fa-<?= $editarNEE ? 'save' : 'plus' ?>"></i>
                                <?= $editarNEE ? 'Actualizar' : 'Agregar' ?>
                            </button>
                            <?php if ($editarNEE): ?>
                                <a href="nee.php" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla de registros -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-table"></i> Tabla - Necesidades Educativas Especiales
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>ID</th>
                                        <th>ID Estudiante</th>
                                        <th>Necesidad Especial</th>
                                        <th>Tratamiento</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while ($nee = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $nee['id'] ?></td>
                                                <td><?= htmlspecialchars($nee['id_estudiante']) ?></td>
                                                <td><strong><?= htmlspecialchars($nee['necesidad_especial']) ?></strong></td>
                                                <td><?= htmlspecialchars($nee['tratamiento']) ?></td>
                                                <td class="text-center">
                                                    <a href="?editar=<?= $nee['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" style="display:inline-block;" 
                                                          onsubmit="return confirm('¿Está seguro que desea eliminar este registro?');">
                                                        <input type="hidden" name="accion" value="eliminar">
                                                        <input type="hidden" name="neeId" value="<?= $nee['id'] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay registros de NEE registrados
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

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; TRASPASEMOS 2025</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>

<?php 
$conn->close(); 
?>