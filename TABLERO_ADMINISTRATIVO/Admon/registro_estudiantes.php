<?php
// =========================================
// CONFIGURACIÓN DE CONEXIÓN
// =========================================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "traspasemos";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipo = "";

// =========================================
// PROCESAR FORMULARIO (INSERTAR / ELIMINAR)
// =========================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    $id = intval($_POST["id"] ?? 0);
    $nombre = trim($_POST["nombre_completo"] ?? "");
    $fecha = $_POST["fecha_nacimiento"] ?? null;
    $id_grupo = intval($_POST["id_grupo"] ?? 0);
    $direccion = trim($_POST["direccion"] ?? "");
    $barrio = trim($_POST["barrio"] ?? "");
    $id_ciudad = intval($_POST["id_ciudad"] ?? 0);
    $id_departamento = intval($_POST["id_departamento"] ?? 0);
    $eps = trim($_POST["eps"] ?? "");
    $discapacidad = trim($_POST["discapacidad"] ?? "");
    $areas_dificultad = trim($_POST["areas_dificultad"] ?? "");
    $id_acudiente = intval($_POST["id_acudiente"] ?? 0);

    if ($accion === "agregar") {
        $stmt = $conn->prepare("INSERT INTO datosestud 
            (id_usuario, fecha_nacimiento, id_grupo, direccion, barrio, id_ciudad, id_departamento, eps, id_acudiente)
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            $mensaje = "❌ Error en prepare(): " . $conn->error;
            $tipo = "danger";
        } else {
            $stmt->bind_param("sisssisi", $fecha, $id_grupo, $direccion, $barrio, $id_ciudad, $id_departamento, $eps, $id_acudiente);
            if ($stmt->execute()) {
                $mensaje = "✅ Estudiante registrado correctamente.";
                $tipo = "success";
            } else {
                $mensaje = "❌ Error al guardar: " . $stmt->error;
                $tipo = "danger";
            }
            $stmt->close();
        }
    } elseif ($accion === "eliminar" && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM datosestud WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $mensaje = "🗑️ Registro eliminado correctamente.";
                $tipo = "success";
            } else {
                $mensaje = "❌ Error al eliminar: " . $stmt->error;
                $tipo = "danger";
            }
            $stmt->close();
        }
    }
}

// =========================================
// CONSULTA DE ESTUDIANTES
// =========================================
$sql = "SELECT d.*, 
            g.descripcion AS grupo, 
            a.nombre_completo AS acudiente,
            c.nombre AS ciudad,
            dept.nombre AS departamento
        FROM datosestud d
        LEFT JOIN grupo g ON d.id_grupo = g.id
        LEFT JOIN acudiente a ON d.id_acudiente = a.id
        LEFT JOIN ciudad c ON d.id_ciudad = c.id
        LEFT JOIN departamento dept ON d.id_departamento = dept.id
        ORDER BY d.id DESC";

$result = $conn->query($sql);
if (!$result) {
    die("❌ Error en la consulta SQL: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Estudiantes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">

<div class="container">
    <h2 class="text-center mb-4 text-primary">Registro de Estudiantes</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= $tipo ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <form method="POST" class="card p-4 shadow mb-4">
        <input type="hidden" name="accion" value="agregar">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Nombre completo</label>
                <input type="text" name="nombre_completo" class="form-control" required>
            </div>
            <div class="form-group col-md-3">
                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control">
            </div>
            <div class="form-group col-md-3">
                <label>Grupo</label>
                <select name="id_grupo" class="form-control">
                    <option value="">Seleccione...</option>
                    <?php
                    $grupos = $conn->query("SELECT id, descripcion FROM grupo");
                    while ($g = $grupos->fetch_assoc()) {
                        echo "<option value='{$g['id']}'>{$g['descripcion']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Dirección</label>
                <input type="text" name="direccion" class="form-control">
            </div>
            <div class="form-group col-md-4">
                <label>Barrio</label>
                <input type="text" name="barrio" class="form-control">
            </div>
            <div class="form-group col-md-4">
                <label>EPS</label>
                <input type="text" name="eps" class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Ciudad</label>
                <select name="id_ciudad" class="form-control">
                    <option value="">Seleccione...</option>
                    <?php
                    $ciudades = $conn->query("SELECT id, nombre FROM ciudad");
                    while ($c = $ciudades->fetch_assoc()) {
                        echo "<option value='{$c['id']}'>{$c['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>Departamento</label>
                <select name="id_departamento" class="form-control">
                    <option value="">Seleccione...</option>
                    <?php
                    $deps = $conn->query("SELECT id, nombre FROM departamento");
                    while ($d = $deps->fetch_assoc()) {
                        echo "<option value='{$d['id']}'>{$d['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <button class="btn btn-success" type="submit">Guardar</button>
    </form>

    <!-- TABLA -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">Estudiantes Registrados</div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="bg-info text-white text-center">
                    <tr>
                        <th>ID</th>
                        <th>Dirección</th>
                        <th>Barrio</th>
                        <th>Ciudad</th>
                        <th>Departamento</th>
                        <th>Grupo</th>
                        <th>EPS</th>
                        <th>Acudiente</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row["id"] ?></td>
                                <td><?= htmlspecialchars($row["direccion"]) ?></td>
                                <td><?= htmlspecialchars($row["barrio"]) ?></td>
                                <td><?= htmlspecialchars($row["ciudad"]) ?></td>
                                <td><?= htmlspecialchars($row["departamento"]) ?></td>
                                <td><?= htmlspecialchars($row["grupo"]) ?></td>
                                <td><?= htmlspecialchars($row["eps"]) ?></td>
                                <td><?= htmlspecialchars($row["acudiente"]) ?></td>
                                <td class="text-center">
                                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('¿Eliminar registro?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted">No hay estudiantes registrados</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>
