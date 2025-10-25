<?php
include("conexion.php");

$mensaje = "";
$tipoMensaje = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = intval($_POST['grupoId'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $idSede = intval($_POST['id_sede'] ?? 0);
    $idJornada = intval($_POST['id_jornada'] ?? 0);
    $directorGrupo = trim($_POST['director_grupo'] ?? '');
    $idGrado = intval($_POST['id_grado'] ?? 0);

    if ($accion === 'agregar') {
        if ($descripcion === '' || $idSede <= 0 || $idJornada <= 0 || $directorGrupo === '' || $idGrado <= 0) {
            $mensaje = "⚠ Todos los campos son obligatorios.";
            $tipoMensaje = "warning";
        } else {
            $stmt = $conn->prepare("INSERT INTO grupo (descripcion, id_sede, id_jornada, director_grupo, id_grado) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("siisi", $descripcion, $idSede, $idJornada, $directorGrupo, $idGrado);
            if ($stmt->execute()) {
                $mensaje = "✅ Grupo agregado correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "❌ Error al agregar: " . $stmt->error;
                $tipoMensaje = "danger";
            }
            $stmt->close();
        }
    }

    if ($accion === 'editar') {
        if ($id > 0 && $descripcion !== '' && $idSede > 0 && $idJornada > 0 && $directorGrupo !== '' && $idGrado > 0) {
            $stmt = $conn->prepare("UPDATE grupo SET descripcion=?, id_sede=?, id_jornada=?, director_grupo=?, id_grado=? WHERE id=?");
            $stmt->bind_param("siisii", $descripcion, $idSede, $idJornada, $directorGrupo, $idGrado, $id);
            if ($stmt->execute()) {
                $mensaje = "✏️ Grupo actualizado correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "❌ Error al actualizar: " . $stmt->error;
                $tipoMensaje = "danger";
            }
            $stmt->close();
        } else {
            $mensaje = "⚠ Datos inválidos para editar.";
            $tipoMensaje = "warning";
        }
    }

    if ($accion === 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM grupo WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "🗑️ Grupo eliminado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al eliminar: " . $stmt->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }
}

// Obtener datos para editar
$editarGrupo = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM grupo WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editarGrupo = $result->fetch_assoc();
        $stmt->close();
    }
}

// Obtener listas para selects
$sedes = $conn->query("SELECT id, nombre FROM sede ORDER BY id ASC");
$jornadas = $conn->query("SELECT id, nombre FROM jornada ORDER BY id ASC");
$grados = $conn->query("SELECT id, nombre FROM grado ORDER BY id ASC");

// Consultar grupos
$sql = "SELECT g.id, g.descripcion, 
               s.nombre AS sede, 
               j.nombre AS jornada, 
               g.director_grupo, 
               gr.nombre AS grado
        FROM grupo g
        LEFT JOIN sede s ON g.id_sede = s.id
        LEFT JOIN jornada j ON g.id_jornada = j.id
        LEFT JOIN grado gr ON g.id_grado = gr.id
        ORDER BY g.id ASC";

$grupos = $conn->query($sql);
if (!$grupos) {
    die("Error al obtener los grupos: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grupos</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
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
                    <a class="collapse-item active" href="Grupo.php">Grupos</a>
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
                    <div class="card-header py-3" style="background-color: <?= $editarGrupo ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editarGrupo ? 'edit' : 'plus' ?>"></i>
                            <?= $editarGrupo ? 'Editar Grupo' : 'Agregar Nuevo Grupo' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="<?= $editarGrupo ? 'editar' : 'agregar' ?>">
                            <?php if ($editarGrupo): ?>
                            <input type="hidden" name="grupoId" value="<?= $editarGrupo['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Descripción <span class="text-danger">*</span></label>
                                    <input type="text" name="descripcion" class="form-control" 
                                           placeholder="Ej: Grupo A"
                                           value="<?= $editarGrupo ? htmlspecialchars($editarGrupo['descripcion']) : '' ?>" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Sede <span class="text-danger">*</span></label>
                                    <select name="id_sede" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <?php 
                                        $sedes->data_seek(0);
                                        while ($s = $sedes->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $s['id'] ?>" <?= ($editarGrupo && $editarGrupo['id_sede'] == $s['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($s['nombre']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Jornada <span class="text-danger">*</span></label>
                                    <select name="id_jornada" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <?php 
                                        $jornadas->data_seek(0);
                                        while ($j = $jornadas->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $j['id'] ?>" <?= ($editarGrupo && $editarGrupo['id_jornada'] == $j['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($j['nombre']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Director de Grupo <span class="text-danger">*</span></label>
                                    <input type="text" name="director_grupo" class="form-control" 
                                           placeholder="Ej: Juan Pérez"
                                           value="<?= $editarGrupo ? htmlspecialchars($editarGrupo['director_grupo']) : '' ?>" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Grado <span class="text-danger">*</span></label>
                                    <select name="id_grado" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <?php 
                                        $grados->data_seek(0);
                                        while ($g = $grados->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $g['id'] ?>" <?= ($editarGrupo && $editarGrupo['id_grado'] == $g['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($g['nombre']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-<?= $editarGrupo ? 'warning' : 'success' ?>">
                                <i class="fas fa-<?= $editarGrupo ? 'save' : 'plus' ?>"></i>
                                <?= $editarGrupo ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            <?php if ($editarGrupo): ?>
                            <a href="Grupo.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Grupos -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Grupos Registrados</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="15%">Descripción</th>
                                        <th width="15%">Sede</th>
                                        <th width="12%">Jornada</th>
                                        <th width="20%">Director</th>
                                        <th width="10%">Grado</th>
                                        <th width="23%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($grupos->num_rows > 0): ?>
                                        <?php while ($grupo = $grupos->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $grupo['id'] ?></td>
                                            <td><?= htmlspecialchars($grupo['descripcion']) ?></td>
                                            <td><?= htmlspecialchars($grupo['sede']) ?></td>
                                            <td><?= htmlspecialchars($grupo['jornada']) ?></td>
                                            <td><?= htmlspecialchars($grupo['director_grupo']) ?></td>
                                            <td><?= htmlspecialchars($grupo['grado']) ?></td>
                                            <td class="text-center">
                                                <a href="?editar=<?= $grupo['id'] ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar este grupo?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="grupoId" value="<?= $grupo['id'] ?>">
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
                                                <i class="fas fa-info-circle"></i> No hay grupos registrados
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