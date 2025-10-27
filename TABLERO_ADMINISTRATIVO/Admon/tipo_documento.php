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
    $id = intval($_POST['usuarioId'] ?? 0);
    $tipoUsuario = intval($_POST["tipoUsuario"] ?? 0);
    $tipoDocumento = intval($_POST["tipoDocumento"] ?? 0);
    $identificacion = trim($_POST["identificacion"] ?? '');
    $nombreCompleto = trim($_POST["nombreCompleto"] ?? '');
    $correo = trim($_POST["correo"] ?? '');
    $celular = trim($_POST["celular"] ?? '');

    if ($accion === 'agregar') {
        if (!empty($tipoUsuario) && !empty($tipoDocumento) && !empty($identificacion) && !empty($nombreCompleto) && !empty($correo) && !empty($celular)) {
            $stmt = $conn->prepare("INSERT INTO usuarios (tipo_usuario_id, tipo_documento_id, identificacion, nombre_completo, correo, celular) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $tipoUsuario, $tipoDocumento, $identificacion, $nombreCompleto, $correo, $celular);
            if ($stmt->execute()) {
                $mensaje = "✅ Usuario agregado correctamente.";
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
        if ($id > 0 && !empty($tipoUsuario) && !empty($tipoDocumento) && !empty($identificacion) && !empty($nombreCompleto) && !empty($correo) && !empty($celular)) {
            $stmt = $conn->prepare("UPDATE usuarios SET tipo_usuario_id=?, tipo_documento_id=?, identificacion=?, nombre_completo=?, correo=?, celular=? WHERE id=?");
            $stmt->bind_param("iissssi", $tipoUsuario, $tipoDocumento, $identificacion, $nombreCompleto, $correo, $celular, $id);
            if ($stmt->execute()) {
                $mensaje = "✏️ Usuario actualizado correctamente.";
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

// Obtener datos para editar
$editarUsuario = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editarUsuario = $result->fetch_assoc();
        $stmt->close();
    }
}

// Consultar usuarios con sus relaciones
$usuarios = $conn->query("
    SELECT u.id, tu.nombre as tipo_usuario, td.nombre as tipo_documento, 
           u.identificacion, u.nombre_completo, u.correo, u.celular 
    FROM usuarios u
    LEFT JOIN tipo_usuario tu ON u.tipo_usuario_id = tu.id
    LEFT JOIN tipo_documento td ON u.tipo_documento_id = td.id
    ORDER BY u.id ASC
");

// Obtener tipos de usuario y tipos de documento para los selects
$tiposUsuario = $conn->query("SELECT id, nombre FROM tipo_usuario ORDER BY nombre ASC");
$tiposDocumento = $conn->query("SELECT id, nombre FROM tipo_documento ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - TRASPASEMOS</title>
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
    <li class="nav-item">
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
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Administrador</span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Perfil
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i> Configuración
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="login.php">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Cerrar Sesión
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>

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
                    <div class="card-header py-3" style="background-color: <?= $editarUsuario ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editarUsuario ? 'edit' : 'user-plus' ?>"></i>
                            <?= $editarUsuario ? 'Editar Usuario' : 'Agregar Nuevo Usuario' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="<?= $editarUsuario ? 'editar' : 'agregar' ?>">
                            <?php if ($editarUsuario): ?>
                            <input type="hidden" name="usuarioId" value="<?= $editarUsuario['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tipo de Usuario <span class="text-danger">*</span></label>
                                    <select name="tipoUsuario" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <?php 
                                        $tiposUsuario->data_seek(0);
                                        while ($tipo = $tiposUsuario->fetch_assoc()): 
                                        ?>
                                        <option value="<?= $tipo['id'] ?>" 
                                            <?= ($editarUsuario && $editarUsuario['tipo_usuario_id'] == $tipo['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tipo['nombre']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tipo de Documento <span class="text-danger">*</span></label>
                                    <select name="tipoDocumento" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <?php 
                                        $tiposDocumento->data_seek(0);
                                        while ($tipo = $tiposDocumento->fetch_assoc()): 
                                        ?>
                                        <option value="<?= $tipo['id'] ?>"
                                            <?= ($editarUsuario && $editarUsuario['tipo_documento_id'] == $tipo['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tipo['nombre']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Identificación <span class="text-danger">*</span></label>
                                    <input type="text" name="identificacion" class="form-control" 
                                           placeholder="Ej: 1234567890"
                                           value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['identificacion']) : '' ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" name="nombreCompleto" class="form-control" 
                                           placeholder="Ej: Juan Pérez García"
                                           value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['nombre_completo']) : '' ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" name="correo" class="form-control" 
                                           placeholder="Ej: usuario@ejemplo.com"
                                           value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['correo']) : '' ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Celular <span class="text-danger">*</span></label>
                                    <input type="text" name="celular" class="form-control" 
                                           placeholder="Ej: 3001234567"
                                           value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['celular']) : '' ?>" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-<?= $editarUsuario ? 'warning' : 'success' ?>">
                                <i class="fas fa-<?= $editarUsuario ? 'save' : 'plus' ?>"></i>
                                <?= $editarUsuario ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            <?php if ($editarUsuario): ?>
                            <a href="Usuarios.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Usuarios -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">
                            <i class="fa fa-users" aria-hidden="true"></i> Usuarios Registrados
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead style="background-color: rgb(211, 248, 248);">
                                    <tr>
                                        <th class="text-center">Cód. Usuario</th>
                                        <th class="text-center">Tipo Usuario</th>
                                        <th class="text-center">Tipo Documento</th>
                                        <th class="text-center">Identificación</th>
                                        <th class="text-center">Nombre Completo</th>
                                        <th class="text-center">Correo</th>
                                        <th class="text-center">Celular</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($usuarios && $usuarios->num_rows > 0): ?>
                                        <?php while ($row = $usuarios->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['tipo_usuario']) ?></td>
                                            <td><?= htmlspecialchars($row['tipo_documento']) ?></td>
                                            <td><?= htmlspecialchars($row['identificacion']) ?></td>
                                            <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($row['correo']) ?></td>
                                            <td><?= htmlspecialchars($row['celular']) ?></td>
                                            <td class="text-center">
                                                <a href="?editar=<?= $row['id'] ?>" class="btn btn-info btn-sm" title="Modificar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar este usuario?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="usuarioId" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fa fa-trash"></i>
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