<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipoMensaje = "";

// === Consultar datos para selects ===
$grupos = $conn->query("SELECT id, descripcion FROM grupo ORDER BY descripcion ASC");
$acudientes = $conn->query("SELECT id, nombre_completo FROM acudiente ORDER BY nombre_completo ASC");
$ciudades = $conn->query("SELECT id, nombre FROM ciudad ORDER BY nombre ASC");
$departamentos = $conn->query("SELECT id, nombre FROM departamento ORDER BY nombre ASC");

// === Procesar formulario ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? '';
    $id = intval($_POST['estudianteId'] ?? 0);

    $nombre_completo = trim($_POST["nombre_completo"] ?? '');
    $tipo_documento = trim($_POST["tipo_documento"] ?? '');
    $numero_documento = trim($_POST["numero_documento"] ?? '');
    $fecha_nacimiento = $_POST["fecha_nacimiento"] ?? null;
    $grado = trim($_POST["grado"] ?? '');
    $jornada = trim($_POST["jornada"] ?? '');
    $sede = trim($_POST["sede"] ?? '');
    $direccion = trim($_POST["direccion"] ?? '');
    $barrio = trim($_POST["barrio"] ?? '');
    $eps = trim($_POST["eps"] ?? '');
    $nombre_acudiente = trim($_POST["nombre_acudiente"] ?? '');
    $telefono_acudiente = trim($_POST["telefono_acudiente"] ?? '');
    $id_acudiente = !empty($_POST["id_acudiente"]) ? intval($_POST["id_acudiente"]) : null;
    $id_grupo = !empty($_POST["id_grupo"]) ? intval($_POST["id_grupo"]) : null;
    $id_ciudad = !empty($_POST["id_ciudad"]) ? intval($_POST["id_ciudad"]) : null;
    $id_departamento = !empty($_POST["id_departamento"]) ? intval($_POST["id_departamento"]) : null;

    // Calcular edad automáticamente
    $edad = null;
    if ($fecha_nacimiento) {
        $diff = date_diff(date_create($fecha_nacimiento), date_create('today'));
        $edad = $diff->y;
    }

    // AGREGAR
    if ($accion === 'agregar') {
        $stmt = $conn->prepare("INSERT INTO datosestud 
        (nombre_completo, tipo_documento, numero_documento, fecha_nacimiento, edad, grado, jornada, sede, direccion, barrio, eps, nombre_acudiente, telefono_acudiente, id_grupo, id_acudiente, id_ciudad, id_departamento)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssissssssssiiii",
            $nombre_completo, $tipo_documento, $numero_documento, $fecha_nacimiento, $edad,
            $grado, $jornada, $sede, $direccion, $barrio, $eps, $nombre_acudiente,
            $telefono_acudiente, $id_grupo, $id_acudiente, $id_ciudad, $id_departamento
        );

        if ($stmt->execute()) {
            $mensaje = "✅ Estudiante registrado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al guardar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }

    // EDITAR
    if ($accion === 'editar' && $id > 0) {
        $stmt = $conn->prepare("UPDATE datosestud SET 
        nombre_completo=?, tipo_documento=?, numero_documento=?, fecha_nacimiento=?, edad=?, grado=?, jornada=?, sede=?, direccion=?, barrio=?, eps=?, nombre_acudiente=?, telefono_acudiente=?, id_grupo=?, id_acudiente=?, id_ciudad=?, id_departamento=? WHERE id=?");

        $stmt->bind_param("ssssissssssssiiiiii",
            $nombre_completo, $tipo_documento, $numero_documento, $fecha_nacimiento, $edad,
            $grado, $jornada, $sede, $direccion, $barrio, $eps, $nombre_acudiente,
            $telefono_acudiente, $id_grupo, $id_acudiente, $id_ciudad, $id_departamento, $id
        );

        if ($stmt->execute()) {
            $mensaje = "✏️ Estudiante actualizado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al actualizar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }

    // ELIMINAR
    if ($accion === 'eliminar' && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM datosestud WHERE id=?");
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
    $stmt = $conn->prepare("SELECT * FROM datosestud WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editarEstudiante = $result->fetch_assoc();
    $stmt->close();
}

// Consultar todos los estudiantes
$estudiantes = $conn->query("SELECT d.*, g.descripcion AS grupo FROM datosestud d 
LEFT JOIN grupo g ON d.id_grupo = g.id
ORDER BY d.id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1cc88a 10%, #13855c 100%);
            color: white;
            position: fixed;
        }
        .sidebar a { color: white; display: block; padding: 12px 20px; text-decoration: none; }
        .sidebar a:hover { background-color: rgba(255,255,255,0.2); }
        .sidebar .sidebar-heading { font-size: 0.9rem; text-transform: uppercase; margin: 10px 15px; opacity: 0.8; }
        #content-wrapper { margin-left: 250px; padding: 20px; }
        footer { background: #f8f9fc; padding: 15px; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

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
        <li class="nav-item active">
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

<!-- CONTENIDO -->
<div id="content-wrapper">
    <div class="container-fluid">
        <h2 class="mb-4 text-primary"><i class="fas fa-user-graduate"></i> Gestión de Estudiantes</h2>

        <?php if ($mensaje): ?>
        <div class="alert alert-<?= $tipoMensaje ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <?= $editarEstudiante ? "✏️ Editar Estudiante" : "➕ Registrar Estudiante" ?>
            </div>
            <div class="card-body">
                <form method="POST" action="tipo_usuario.php">
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
                                <option value="Cédula" <?= isset($editarEstudiante) && $editarEstudiante['tipo_documento']=='Cédula'?'selected':'' ?>>Cédula</option>
                                <option value="TI" <?= isset($editarEstudiante) && $editarEstudiante['tipo_documento']=='TI'?'selected':'' ?>>Tarjeta de Identidad</option>
                                <option value="RC" <?= isset($editarEstudiante) && $editarEstudiante['tipo_documento']=='RC'?'selected':'' ?>>Registro Civil</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Número documento</label>
                            <input type="text" name="numero_documento" class="form-control" required value="<?= $editarEstudiante['numero_documento'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Fecha nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" value="<?= $editarEstudiante['fecha_nacimiento'] ?? '' ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Grado</label>
                            <input type="text" name="grado" class="form-control" value="<?= $editarEstudiante['grado'] ?? '' ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Jornada</label>
                            <select name="jornada" class="form-control">
                                <option value="">-- Seleccione --</option>
                                <option value="Mañana" <?= isset($editarEstudiante) && $editarEstudiante['jornada']=='Mañana'?'selected':'' ?>>Mañana</option>
                                <option value="Tarde" <?= isset($editarEstudiante) && $editarEstudiante['jornada']=='Tarde'?'selected':'' ?>>Tarde</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Grupo</label>
                            <select name="id_grupo" class="form-control">
                                <option value="">-- Seleccione grupo --</option>
                                <?php mysqli_data_seek($grupos, 0);
                                while ($g = $grupos->fetch_assoc()): ?>
                                    <option value="<?= $g['id'] ?>" <?= isset($editarEstudiante) && $editarEstudiante['id_grupo']==$g['id']?'selected':'' ?>>
                                        <?= htmlspecialchars($g['descripcion']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control" value="<?= $editarEstudiante['direccion'] ?? '' ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Barrio</label>
                            <input type="text" name="barrio" class="form-control" value="<?= $editarEstudiante['barrio'] ?? '' ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label>EPS</label>
                            <input type="text" name="eps" class="form-control" value="<?= $editarEstudiante['eps'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Ciudad</label>
                            <select name="id_ciudad" class="form-control">
                                <option value="">-- Seleccione ciudad --</option>
                                <?php mysqli_data_seek($ciudades, 0);
                                while ($c = $ciudades->fetch_assoc()): ?>
                                    <option value="<?= $c['id'] ?>" <?= isset($editarEstudiante) && $editarEstudiante['id_ciudad']==$c['id']?'selected':'' ?>>
                                        <?= htmlspecialchars($c['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Departamento</label>
                            <select name="id_departamento" class="form-control">
                                <option value="">-- Seleccione departamento --</option>
                                <?php mysqli_data_seek($departamentos, 0);
                                while ($d = $departamentos->fetch_assoc()): ?>
                                    <option value="<?= $d['id'] ?>" <?= isset($editarEstudiante) && $editarEstudiante['id_departamento']==$d['id']?'selected':'' ?>>
                                        <?= htmlspecialchars($d['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Acudiente</label>
                            <select name="id_acudiente" class="form-control">
                                <option value="">-- Seleccione acudiente --</option>
                                <?php mysqli_data_seek($acudientes, 0);
                                while ($a = $acudientes->fetch_assoc()): ?>
                                    <option value="<?= $a['id'] ?>" <?= isset($editarEstudiante) && $editarEstudiante['id_acudiente']==$a['id']?'selected':'' ?>>
                                        <?= htmlspecialchars($a['nombre_completo']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nombre acudiente</label>
                            <input type="text" name="nombre_acudiente" class="form-control" value="<?= $editarEstudiante['nombre_acudiente'] ?? '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Teléfono acudiente</label>
                            <input type="text" name="telefono_acudiente" class="form-control" value="<?= $editarEstudiante['telefono_acudiente'] ?? '' ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-<?= $editarEstudiante ? 'warning' : 'success' ?>">
                        <i class="fas fa-<?= $editarEstudiante ? 'save' : 'plus' ?>"></i>
                        <?= $editarEstudiante ? 'Actualizar' : 'Guardar' ?>
                    </button>
                    <?php if ($editarEstudiante): ?>
                        <a href="tipo_usuario.php" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Tabla -->
        <div class="card shadow">
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
                            <th>Grupo</th>
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
                                    <td><?= htmlspecialchars($row['grupo'] ?? 'N/A') ?></td>
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

        <footer>
            <span>© TRASPASEMOS 2025 - Panel Administrativo</span>
        </footer>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
