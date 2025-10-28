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
    $id = intval($_POST['estudianteId'] ?? 0);

    // Campos del formulario
    $nombre_completo = trim($_POST["nombre_completo"] ?? '');
    $tipo_documento = trim($_POST["tipo_documento"] ?? '');
    $numero_documento = trim($_POST["numero_documento"] ?? '');
    $fecha_nacimiento = $_POST["fecha_nacimiento"] ?? null;
    $grado = trim($_POST["grado"] ?? '');
    $jornada = trim($_POST["jornada"] ?? '');
    $sede = trim($_POST["sede"] ?? '');
    $eps = trim($_POST["eps"] ?? '');
    $nombre_acudiente = trim($_POST["nombre_acudiente"] ?? '');
    $telefono_acudiente = trim($_POST["telefono_acudiente"] ?? '');

    $camposVacios = empty($nombre_completo) || empty($tipo_documento) || empty($numero_documento);

    // Agregar nuevo estudiante
    if ($accion === 'agregar' && !$camposVacios) {
        $stmt = $conn->prepare("INSERT INTO estudiantes (nombre_completo, tipo_documento, numero_documento, fecha_nacimiento, grado, jornada, sede, eps, nombre_acudiente, telefono_acudiente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $nombre_completo, $tipo_documento, $numero_documento, $fecha_nacimiento, $grado, $jornada, $sede, $eps, $nombre_acudiente, $telefono_acudiente);
        if ($stmt->execute()) {
            $mensaje = "✅ Estudiante registrado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al guardar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }

    // Editar estudiante
    if ($accion === 'editar' && $id > 0) {
        $stmt = $conn->prepare("UPDATE estudiantes SET nombre_completo=?, tipo_documento=?, numero_documento=?, fecha_nacimiento=?, grado=?, jornada=?, sede=?, eps=?, nombre_acudiente=?, telefono_acudiente=? WHERE id=?");
        $stmt->bind_param("ssssssssssi", $nombre_completo, $tipo_documento, $numero_documento, $fecha_nacimiento, $grado, $jornada, $sede, $eps, $nombre_acudiente, $telefono_acudiente, $id);
        if ($stmt->execute()) {
            $mensaje = "✏️ Estudiante actualizado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al actualizar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }

    // Eliminar estudiante
    if ($accion === 'eliminar' && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM estudiantes WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "🗑️ Estudiante eliminado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al eliminar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }
}

// Cargar estudiante para edición
$editarEstudiante = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM estudiantes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editarEstudiante = $result->fetch_assoc();
    $stmt->close();
}

// Consultar todos los estudiantes
$estudiantes = $conn->query("SELECT * FROM estudiantes ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">
    <!-- SIDEBAR INTEGRADO -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon">
                <i class="fas fa-graduation-cap fa-2x text-white"></i>
            </div>
            <div class="sidebar-brand-text mx-3">TRASPASEMOS</div>
        </a>
        <hr class="sidebar-divider">
        <li class="nav-item active">
            <a class="nav-link" href="estudiantes.php">
                <i class="fas fa-user-graduate"></i>
                <span>Estudiantes</span>
            </a>
        </li>
        <li class="nav-item"><a class="nav-link" href="remision.php"><i class="fas fa-file-alt"></i><span>Remisiones</span></a></li>
        <li class="nav-item"><a class="nav-link" href="aspecto_academico.php"><i class="fas fa-book"></i><span>Aspectos Académicos</span></a></li>
        <li class="nav-item"><a class="nav-link" href="aspecto_complementario.php"><i class="fas fa-puzzle-piece"></i><span>Aspectos Complementarios</span></a></li>
        <hr class="sidebar-divider">
    </ul>

    <!-- CONTENIDO PRINCIPAL -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content" class="p-4">
            <div class="container-fluid">

                <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipoMensaje ?>"><?= $mensaje ?></div>
                <?php endif; ?>

                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <?= $editarEstudiante ? '✏️ Editar Estudiante' : '➕ Registrar Estudiante' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="estudiantes.php">
                            <input type="hidden" name="accion" value="<?= $editarEstudiante ? 'editar' : 'agregar' ?>">
                            <?php if ($editarEstudiante): ?>
                                <input type="hidden" name="estudianteId" value="<?= $editarEstudiante['id'] ?>">
                            <?php endif; ?>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre completo</label>
                                    <input type="text" name="nombre_completo" class="form-control" required value="<?= $editarEstudiante['nombre_completo'] ?? '' ?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Tipo de documento</label>
                                    <select name="tipo_documento" class="form-control">
                                        <option value="">-- Seleccione --</option>
                                        <option value="Cédula" <?= isset($editarEstudiante) && $editarEstudiante['tipo_documento'] == 'Cédula' ? 'selected' : '' ?>>Cédula</option>
                                        <option value="Tarjeta de Identidad" <?= isset($editarEstudiante) && $editarEstudiante['tipo_documento'] == 'Tarjeta de Identidad' ? 'selected' : '' ?>>Tarjeta de Identidad</option>
                                        <option value="Registro Civil" <?= isset($editarEstudiante) && $editarEstudiante['tipo_documento'] == 'Registro Civil' ? 'selected' : '' ?>>Registro Civil</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Número documento</label>
                                    <input type="text" name="numero_documento" class="form-control" required value="<?= $editarEstudiante['numero_documento'] ?? '' ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Fecha de nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" class="form-control" value="<?= $editarEstudiante['fecha_nacimiento'] ?? '' ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Grado</label>
                                    <input type="text" name="grado" class="form-control" value="<?= $editarEstudiante['grado'] ?? '' ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Jornada</label>
                                    <select name="jornada" class="form-control">
                                        <option value="">-- Seleccione --</option>
                                        <option value="Mañana" <?= isset($editarEstudiante) && $editarEstudiante['jornada'] == 'Mañana' ? 'selected' : '' ?>>Mañana</option>
                                        <option value="Tarde" <?= isset($editarEstudiante) && $editarEstudiante['jornada'] == 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                                        <option value="Noche" <?= isset($editarEstudiante) && $editarEstudiante['jornada'] == 'Noche' ? 'selected' : '' ?>>Noche</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Sede</label>
                                    <input type="text" name="sede" class="form-control" value="<?= $editarEstudiante['sede'] ?? '' ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>EPS</label>
                                    <input type="text" name="eps" class="form-control" value="<?= $editarEstudiante['eps'] ?? '' ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del acudiente</label>
                                    <input type="text" name="nombre_acudiente" class="form-control" value="<?= $editarEstudiante['nombre_acudiente'] ?? '' ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Teléfono del acudiente</label>
                                    <input type="text" name="telefono_acudiente" class="form-control" value="<?= $editarEstudiante['telefono_acudiente'] ?? '' ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-<?= $editarEstudiante ? 'warning' : 'success' ?>">
                                <i class="fas fa-<?= $editarEstudiante ? 'save' : 'plus' ?>"></i>
                                <?= $editarEstudiante ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            <?php if ($editarEstudiante): ?>
                                <a href="estudiantes.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla de estudiantes -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white text-center">
                        <h6 class="m-0 font-weight-bold">Listado de Estudiantes</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Grado</th>
                                    <th>Jornada</th>
                                    <th>Acudiente</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($estudiantes && $estudiantes->num_rows > 0): ?>
                                    <?php while ($row = $estudiantes->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($row['tipo_documento'] . " " . $row['numero_documento']) ?></td>
                                            <td><?= htmlspecialchars($row['grado']) ?></td>
                                            <td><?= htmlspecialchars($row['jornada']) ?></td>
                                            <td><?= htmlspecialchars($row['nombre_acudiente']) ?></td>
                                            <td>
                                                <a href="?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                <form method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar estudiante?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="estudianteId" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted">No hay estudiantes registrados</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <footer class="sticky-footer bg-light text-center py-3">
            <span>© TRASPASEMOS 2025</span>
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
