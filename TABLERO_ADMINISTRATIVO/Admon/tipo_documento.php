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
    
    // Campos de usuario
    $id_tipo_usuario = intval($_POST["id_tipo_usuario"] ?? 0);
    $id_tipo_documento = intval($_POST["id_tipo_documento"] ?? 0);
    $identificacion = trim($_POST["identificacion"] ?? '');
    $nombre_completo = trim($_POST["nombre_completo"] ?? '');
    $correo = trim($_POST["correo"] ?? '');
    $celular = trim($_POST["celular"] ?? '');
    // NOTA: La gestión de contraseñas se omite para mantener la simplicidad del CRUD
    // como en el ejemplo de Sede.php.

    $camposVacios = empty($id_tipo_usuario) || empty($id_tipo_documento) || empty($identificacion) || empty($nombre_completo) || empty($correo);

    if ($accion === 'agregar') {
        if (!$camposVacios) {
            $stmt = $conn->prepare("INSERT INTO usuarios (id_tipo_usuario, id_tipo_documento, identificacion, nombre_completo, correo, celular) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $id_tipo_usuario, $id_tipo_documento, $identificacion, $nombre_completo, $correo, $celular);
            
            if ($stmt->execute()) {
                $mensaje = "✅ Usuario agregado correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "❌ Error al guardar: " . $conn->error;
                $tipoMensaje = "danger";
            }
            $stmt->close();
        } else {
            $mensaje = "⚠ Todos los campos obligatorios (*) deben ser completados.";
            $tipoMensaje = "warning";
        }
    }

    if ($accion === 'editar') {
        if ($id > 0 && !$camposVacios) {
            $stmt = $conn->prepare("UPDATE usuarios SET id_tipo_usuario=?, id_tipo_documento=?, identificacion=?, nombre_completo=?, correo=?, celular=? WHERE id=?");
            $stmt->bind_param("iissssi", $id_tipo_usuario, $id_tipo_documento, $identificacion, $nombre_completo, $correo, $celular, $id);
            
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

// Consultar datos para los <select> del formulario
$tiposUsuario = $conn->query("SELECT id, nombre FROM tipo_usuario ORDER BY nombre ASC");
$tiposDocumento = $conn->query("SELECT id, nombre FROM tipo_documento ORDER BY nombre ASC");

// Consultar usuarios (con JOINs para mostrar nombres en la tabla)
$queryUsuarios = "
    SELECT 
        u.id, 
        u.identificacion, 
        u.nombre_completo, 
        u.correo, 
        u.celular,
        tu.nombre AS tipo_usuario,
        td.nombre AS tipo_documento
    FROM usuarios u
    LEFT JOIN tipo_usuario tu ON u.id_tipo_usuario = tu.id
    LEFT JOIN tipo_documento td ON u.id_tipo_documento = td.id
    ORDER BY u.id ASC
";
$usuarios = $conn->query($queryUsuarios);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon">
                <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-width: 100px;">
            </div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link" href="index.html">
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <li class="nav-item active">
            <a class="nav-link" href="Usuarios.php">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <li class="nav-item">
            <a class="nav-link" href="grado.php">
                <i class="fas fa-graduation-cap"></i>
                <span>Grado</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="Grupo.php">
                <i class="fas fa-users-cog"></i>
                <span>Grupos</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="Sede.php">
                <i class="fas fa-school"></i>
                <span>Sede</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="aspecto_academico.php">
                <i class="fas fa-book-open"></i>
                <span>Aspectos Académicos</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="aspecto_complementario.php">
                <i class="fas fa-puzzle-piece"></i>
                <span>Aspectos Complementarios</span>
            </a>
        </li>

        <li class="nav-item ">
            <a class="nav-link" href="tipo_estudiante.php">
                <i class="fas fa-user-graduate"></i>
                <span>Tipos de Estudiantes</span>
            </a>
        </li>

        <hr class="sidebar-divider">

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

                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: <?= $editarUsuario ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editarUsuario ? 'edit' : 'plus' ?>"></i>
                            <?= $editarUsuario ? 'Editar Usuario' : 'Agregar Nuevo Usuario' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="Usuarios.php">
                            <input type="hidden" name="accion" value="<?= $editarUsuario ? 'editar' : 'agregar' ?>">
                            <?php if ($editarUsuario): ?>
                            <input type="hidden" name="usuarioId" value="<?= $editarUsuario['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tipo de Usuario <span class="text-danger">*</span></label>
                                    <select name="id_tipo_usuario" class="form-control" required>
                                        <option value="">-- Seleccione --</option>
                                        <?php while($tipo = $tiposUsuario->fetch_assoc()): ?>
                                        <option value="<?= $tipo['id'] ?>" <?= ($editarUsuario && $editarUsuario['id_tipo_usuario'] == $tipo['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tipo['nombre']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tipo de Documento <span class="text-danger">*</span></label>
                                    <select name="id_tipo_documento" class="form-control" required>
                                        <option value="">-- Seleccione --</option>
                                        <?php while($tipo = $tiposDocumento->fetch_assoc()): ?>
                                        <option value="<?= $tipo['id'] ?>" <?= ($editarUsuario && $editarUsuario['id_tipo_documento'] == $tipo['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tipo['nombre']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre_completo" class="form-control" 
                                           placeholder="Ej: Ana María Pérez"
                                           value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['nombre_completo']) : '' ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Identificación <span class="text-danger">*</span></label>
                                    <input type="text" name="identificacion" class="form-control" 
                                           placeholder="Ej: 1020304050"
                                           value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['identificacion']) : '' ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Celular</label>
                                    <input type="text" name="celular" class="form-control" 
                                           placeholder="Ej: 3001234567"
                                           value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['celular']) : '' ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" name="correo" class="form-control" 
                                           placeholder="Ej: usuario@correo.com"
                                           value="<?= $editarUsuario ? htmlspecialchars($editarUsuario['correo']) : '' ?>" required>
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

                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Usuarios Registrados</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo Usuario</th>
                                        <th>Tipo Documento</th>
                                        <th>Identificación</th>
                                        <th>Nombre Completo</th>
                                        <th>Correo</th>
                                        <th>Celular</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($usuarios && $usuarios->num_rows > 0): ?>
                                        <?php while ($row = $usuarios->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['tipo_usuario']) ?></td>
                                            <td><?= htmlspecialchars($row['tipo_documento']) ?></td>
                                            <td><?= htmlspecialchars($row['identificacion']) ?></td>
                                            <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($row['correo']) ?></td>
                                            <td><?= htmlspecialchars($row['celular']) ?></td>
                                            <td class="text-center">
                                                <a href="?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar este usuario?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="usuarioId" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Eliminar
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
<?php $conn->close(); ?>