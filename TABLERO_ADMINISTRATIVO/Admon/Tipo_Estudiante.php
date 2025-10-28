<?php
// Configuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipoMensaje = "";

// Procesar acciones CRUD
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $parentesco = trim($_POST['parentesco'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    if ($accion === "agregar") {
        if (!empty($nombre_completo)) {
            $stmt = $conn->prepare("INSERT INTO acudiente (nombre_completo, parentesco, telefono, correo) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre_completo, $parentesco, $telefono, $correo);
            if ($stmt->execute()) {
                $mensaje = "✅ Acudiente agregado correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "❌ Error al guardar: " . $conn->error;
                $tipoMensaje = "danger";
            }
            $stmt->close();
        } else {
            $mensaje = "⚠ El nombre completo es obligatorio.";
            $tipoMensaje = "warning";
        }
    }

    if ($accion === "editar") {
        if ($id > 0 && !empty($nombre_completo)) {
            $stmt = $conn->prepare("UPDATE acudiente SET nombre_completo=?, parentesco=?, telefono=?, correo=? WHERE id=?");
            $stmt->bind_param("ssssi", $nombre_completo, $parentesco, $telefono, $correo, $id);
            if ($stmt->execute()) {
                $mensaje = "✏️ Acudiente actualizado correctamente.";
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

    if ($accion === "eliminar") {
        $stmt = $conn->prepare("DELETE FROM acudiente WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "🗑️ Acudiente eliminado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al eliminar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }
}

// Obtener datos para editar
$editarAcudiente = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM acudiente WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editarAcudiente = $result->fetch_assoc();
        $stmt->close();
    }
}

// Cargar todos los acudientes
$result = $conn->query("SELECT * FROM acudiente ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Acudientes - TRASPASEMOS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
      <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <!-- Logo -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon">
                <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-width: 100px;">
            </div>
        </a>

        <hr class="sidebar-divider my-0">

        <!-- Inicio -->
        <li class="nav-item">
            <a class="nav-link" href="index.html">
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

        <!-- Tipos de Estudiantes -->
        <li class="nav-item active">
            <a class="nav-link" href="tipo_estudiante.php">
                <i class="fas fa-user-graduate"></i>
                <span>Tipos de Estudiantes</span>
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
                    <div class="card-header py-3" style="background-color: <?= $editarAcudiente ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editarAcudiente ? 'edit' : 'user-plus' ?>"></i>
                            <?= $editarAcudiente ? 'Editar Acudiente' : 'Agregar Nuevo Acudiente' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="tipo_estudiante.php">
                            <input type="hidden" name="accion" value="<?= $editarAcudiente ? 'editar' : 'agregar' ?>">
                            <?php if ($editarAcudiente): ?>
                            <input type="hidden" name="id" value="<?= $editarAcudiente['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre_completo" class="form-control" 
                                           placeholder="Ej: María González Pérez"
                                           value="<?= $editarAcudiente ? htmlspecialchars($editarAcudiente['nombre_completo']) : '' ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Parentesco</label>
                                    <input type="text" name="parentesco" class="form-control" 
                                           placeholder="Ej: Madre, Padre, Tío"
                                           value="<?= $editarAcudiente ? htmlspecialchars($editarAcudiente['parentesco']) : '' ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" 
                                           placeholder="Ej: 3001234567"
                                           value="<?= $editarAcudiente ? htmlspecialchars($editarAcudiente['telefono']) : '' ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Correo Electrónico</label>
                                    <input type="email" name="correo" class="form-control" 
                                           placeholder="Ej: acudiente@ejemplo.com"
                                           value="<?= $editarAcudiente ? htmlspecialchars($editarAcudiente['correo']) : '' ?>">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-<?= $editarAcudiente ? 'warning' : 'success' ?>">
                                <i class="fas fa-<?= $editarAcudiente ? 'save' : 'plus' ?>"></i>
                                <?= $editarAcudiente ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            <?php if ($editarAcudiente): ?>
                            <a href="tipo_estudiante.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Acudientes -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">
                            <i class="fas fa-table"></i> Acudientes Registrados
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="8%" class="text-center">ID</th>
                                        <th width="28%">Nombre Completo</th>
                                        <th width="15%">Parentesco</th>
                                        <th width="15%">Teléfono</th>
                                        <th width="20%">Correo</th>
                                        <th width="14%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while ($a = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= $a['id'] ?></td>
                                            <td><?= htmlspecialchars($a['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($a['parentesco']) ?></td>
                                            <td><?= htmlspecialchars($a['telefono']) ?></td>
                                            <td><?= htmlspecialchars($a['correo']) ?></td>
                                            <td class="text-center">
                                                <a href="?editar=<?= $a['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="tipo_estudiante.php" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar este acudiente?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay acudientes registrados
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