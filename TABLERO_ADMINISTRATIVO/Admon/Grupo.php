<?php
include("conexion.php");

$mensaje = "";
$tipoMensaje = "";

// ============================
// PROCESAR FORMULARIO
// ============================
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
    }
}

// ============================
// OBTENER DATOS
// ============================

// Listas para selects
$sedes = $conn->query("SELECT id, nombre FROM sede ORDER BY id ASC");
$jornadas = $conn->query("SELECT id, nombre FROM jornada ORDER BY id ASC");
$grados = $conn->query("SELECT id, nombre FROM grado ORDER BY id ASC");

// Listado principal
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
    <title>Gestión de Grupos</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3 class="text-center text-primary mb-4">Gestión de Grupos</h3>

    <?php if ($mensaje): ?>
    <div class="alert alert-<?= $tipoMensaje ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Agregar Grupo</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="accion" value="agregar">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" class="form-control" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Sede</label>
                        <select name="id_sede" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($s = $sedes->fetch_assoc()): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Jornada</label>
                        <select name="id_jornada" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($j = $jornadas->fetch_assoc()): ?>
                                <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Director de Grupo</label>
                        <input type="text" name="director_grupo" class="form-control" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Grado</label>
                        <select name="id_grado" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <?php while ($g = $grados->fetch_assoc()): ?>
                                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
            </form>
        </div>
    </div>

    <!-- TABLA -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0">Tabla de Grupos</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped text-center">
                <thead class="bg-info text-white">
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Sede</th>
                        <th>Jornada</th>
                        <th>Director</th>
                        <th>Grado</th>
                        <th>Acciones</th>
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
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="grupoId" value="<?= $grupo['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este grupo?');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-muted">No hay grupos registrados</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>
