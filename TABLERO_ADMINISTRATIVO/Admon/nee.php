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
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tratamiento = trim($_POST['tratamiento'] ?? '');
            $fecha_diagnostico = $_POST['fecha_diagnostico'] ?? null;
            $profesional_diagnostico = trim($_POST['profesional_diagnostico'] ?? '');
            $estado = $_POST['estado'] ?? 'Activo';

            if ($id_estudiante > 0 && $necesidad_especial !== '') {
                $stmt = $conn->prepare("INSERT INTO NEE (id_estudiante, necesidad_especial, descripcion, tratamiento, fecha_diagnostico, profesional_diagnostico, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssss", $id_estudiante, $necesidad_especial, $descripcion, $tratamiento, $fecha_diagnostico, $profesional_diagnostico, $estado);
                if ($stmt->execute()) {
                    $mensaje = "✅ NEE agregada correctamente.";
                    $tipoMensaje = "success";
                } else {
                    $mensaje = "❌ Error al guardar: " . $conn->error;
                    $tipoMensaje = "danger";
                }
                $stmt->close();
            } else {
                $mensaje = "⚠ Todos los campos obligatorios deben estar completos.";
                $tipoMensaje = "warning";
            }
            break;

        case 'editar':
            $id = intval($_POST['neeId'] ?? 0);
            $id_estudiante = intval($_POST['id_estudiante'] ?? 0);
            $necesidad_especial = trim($_POST['necesidad_especial'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tratamiento = trim($_POST['tratamiento'] ?? '');
            $fecha_diagnostico = $_POST['fecha_diagnostico'] ?? null;
            $profesional_diagnostico = trim($_POST['profesional_diagnostico'] ?? '');
            $estado = $_POST['estado'] ?? 'Activo';

            if ($id > 0 && $id_estudiante > 0 && $necesidad_especial !== '') {
                $stmt = $conn->prepare("UPDATE NEE SET id_estudiante=?, necesidad_especial=?, descripcion=?, tratamiento=?, fecha_diagnostico=?, profesional_diagnostico=?, estado=? WHERE id=?");
                $stmt->bind_param("issssssi", $id_estudiante, $necesidad_especial, $descripcion, $tratamiento, $fecha_diagnostico, $profesional_diagnostico, $estado, $id);
                if ($stmt->execute()) {
                    $mensaje = "✏️ NEE actualizada correctamente.";
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
            break;

        case 'eliminar':
            $id = intval($_POST['neeId'] ?? 0);
            if ($id > 0) {
                $stmt = $conn->prepare("DELETE FROM NEE WHERE id = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $mensaje = "🗑️ NEE eliminada correctamente.";
                    $tipoMensaje = "success";
                } else {
                    $mensaje = "❌ Error al eliminar: " . $stmt->error;
                    $tipoMensaje = "danger";
                }
                $stmt->close();
            }
            break;
    }
}

// Obtener datos para editar
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

// Consultar registros con información del estudiante
$query = "
    SELECT 
        n.id,
        n.id_estudiante,
        e.nombre_completo,
        e.numero_documento,
        n.necesidad_especial,
        n.descripcion,
        n.tratamiento,
        n.fecha_diagnostico,
        n.profesional_diagnostico,
        n.estado,
        n.fecha_registro
    FROM NEE n
    LEFT JOIN datosestud e ON n.id_estudiante = e.id
    ORDER BY n.id DESC
";
$registros = $conn->query($query);

// Obtener lista de estudiantes para el select
$estudiantes = $conn->query("SELECT id, nombre_completo, numero_documento FROM datosestud ORDER BY nombre_completo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de NEE</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

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
    <div class="sidebar-heading">Gestión de Usuarios</div>

    <!-- Usuarios -->
    <li class="nav-item">
        <a class="nav-link" href="Usuarios.php">
            <i class="fas fa-users"></i>
            <span>Usuarios</span>
        </a>
    </li>

    <!-- Tipo de Usuario -->
    <li class="nav-item">
        <a class="nav-link" href="tipo_usuario.php">
            <i class="fas fa-user-shield"></i>
            <span>Tipo de Usuario</span>
        </a>
    </li>

    <!-- Acudiente -->
    <li class="nav-item">
        <a class="nav-link" href="acudiente.php">
            <i class="fas fa-user-tie"></i>
            <span>Acudientes</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Estructura Académica</div>

    <!-- Sede -->
    <li class="nav-item">
        <a class="nav-link" href="Sede.php">
            <i class="fas fa-school"></i>
            <span>Sedes</span>
        </a>
    </li>

    <!-- Grado -->
    <li class="nav-item">
        <a class="nav-link" href="grado.php">
            <i class="fas fa-graduation-cap"></i>
            <span>Grados</span>
        </a>
    </li>

    <!-- Grupo -->
    <li class="nav-item">
        <a class="nav-link" href="Grupo.php">
            <i class="fas fa-users-cog"></i>
            <span>Grupos</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">NEE y Seguimiento</div>

    <!-- NEE -->
    <li class="nav-item active">
        <a class="nav-link" href="nee.php">
            <i class="fas fa-brain"></i>
            <span>NEE</span>
        </a>
    </li>

    <!-- Remisión -->
    <li class="nav-item">
        <a class="nav-link" href="remision.php">
            <i class="fas fa-file-medical"></i>
            <span>Remisiones</span>
        </a>
    </li>

    <!-- Seguimiento -->
    <li class="nav-item">
        <a class="nav-link" href="seguimiento.php">
            <i class="fas fa-clipboard-check"></i>
            <span>Seguimientos</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Aspectos Educativos</div>

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
            </div>
        </div>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
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
                    <div class="card-header py-3" style="background-color: <?= $editarNEE ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editarNEE ? 'edit' : 'plus' ?>"></i>
                            <?= $editarNEE ? 'Editar Necesidad Educativa Especial' : 'Agregar Nueva NEE' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="<?= $editarNEE ? 'editar' : 'agregar' ?>">
                            <?php if ($editarNEE): ?>
                            <input type="hidden" name="neeId" value="<?= $editarNEE['id'] ?>">
                            <?php endif; ?>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Estudiante <span class="text-danger">*</span></label>
                                    <select name="id_estudiante" class="form-control" required>
                                        <option value="">-- Seleccione un estudiante --</option>
                                        <?php 
                                        $estudiantes->data_seek(0);
                                        while($est = $estudiantes->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $est['id'] ?>" <?= ($editarNEE && $editarNEE['id_estudiante'] == $est['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($est['nombre_completo']) ?> - <?= htmlspecialchars($est['numero_documento']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Necesidad Especial <span class="text-danger">*</span></label>
                                    <input type="text" name="necesidad_especial" class="form-control" 
                                           placeholder="Ej: TDAH, Dislexia, Autismo"
                                           value="<?= $editarNEE ? htmlspecialchars($editarNEE['necesidad_especial']) : '' ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="2" 
                                              placeholder="Descripción detallada de la necesidad"><?= $editarNEE ? htmlspecialchars($editarNEE['descripcion']) : '' ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Tratamiento / Estrategia</label>
                                    <textarea name="tratamiento" class="form-control" rows="2" 
                                              placeholder="Tratamiento o estrategias de apoyo"><?= $editarNEE ? htmlspecialchars($editarNEE['tratamiento']) : '' ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Fecha de Diagnóstico</label>
                                    <input type="date" name="fecha_diagnostico" class="form-control" 
                                           value="<?= $editarNEE ? $editarNEE['fecha_diagnostico'] : '' ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Profesional que Diagnosticó</label>
                                    <input type="text" name="profesional_diagnostico" class="form-control" 
                                           placeholder="Ej: Dr. Juan Pérez"
                                           value="<?= $editarNEE ? htmlspecialchars($editarNEE['profesional_diagnostico']) : '' ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Estado</label>
                                    <select name="estado" class="form-control">
                                        <option value="Activo" <?= ($editarNEE && $editarNEE['estado'] == 'Activo') ? 'selected' : '' ?>>Activo</option>
                                        <option value="Inactivo" <?= ($editarNEE && $editarNEE['estado'] == 'Inactivo') ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-<?= $editarNEE ? 'warning' : 'success' ?>">
                                <i class="fas fa-<?= $editarNEE ? 'save' : 'plus' ?>"></i>
                                <?= $editarNEE ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            <?php if ($editarNEE): ?>
                            <a href="nee.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla de NEE -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Necesidades Educativas Especiales</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="20%">Estudiante</th>
                                        <th width="15%">NEE</th>
                                        <th width="20%">Descripción</th>
                                        <th width="15%">Tratamiento</th>
                                        <th width="10%">Estado</th>
                                        <th width="15%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($registros && $registros->num_rows > 0): ?>
                                        <?php while ($row = $registros->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($row['nombre_completo']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($row['numero_documento']) ?></small>
                                            </td>
                                            <td><span class="badge badge-info"><?= htmlspecialchars($row['necesidad_especial']) ?></span></td>
                                            <td><small><?= htmlspecialchars(substr($row['descripcion'] ?? 'Sin descripción', 0, 50)) ?>...</small></td>
                                            <td><small><?= htmlspecialchars(substr($row['tratamiento'] ?? 'Sin tratamiento', 0, 50)) ?>...</small></td>
                                            <td>
                                                <span class="badge badge-<?= $row['estado'] == 'Activo' ? 'success' : 'secondary' ?>">
                                                    <?= $row['estado'] ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar esta NEE?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="neeId" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay necesidades educativas especiales registradas
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="index.php" class="btn btn-secondary mt-3">
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