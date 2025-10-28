<?php
// Con<?php
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
    <title>Necesidades Educativas Especiales</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">

    <!-- Sidebar (idéntico al que ya tienes, puedes copiarlo del archivo grado.php) -->

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid mt-4">

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
                        <form method="POST">
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
                                           value="<?= $editarNEE ? htmlspecialchars($editarNEE['necesidad_especial']) : '' ?>" required>
                                </div>

                                <div class="form-group col-md-5">
                                    <label for="tratamiento">Tratamiento / Estrategia</label>
                                    <textarea class="form-control" id="tratamiento" name="tratamiento" rows="2"><?= $editarNEE ? htmlspecialchars($editarNEE['tratamiento']) : '' ?></textarea>
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
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Necesidades Educativas Especiales</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
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
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($nee = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $nee['id'] ?></td>
                                                <td><?= htmlspecialchars($nee['id_estudiante']) ?></td>
                                                <td><?= htmlspecialchars($nee['necesidad_especial']) ?></td>
                                                <td><?= htmlspecialchars($nee['tratamiento']) ?></td>
                                                <td class="text-center">
                                                    <a href="?editar=<?= $nee['id'] ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" style="display:inline-block;" 
                                                          onsubmit="return confirm('¿Eliminar este registro?');">
                                                        <input type="hidden" name="accion" value="eliminar">
                                                        <input type="hidden" name="neeId" value="<?= $nee['id'] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay registros NEE
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <a href="index.php" class="btn btn-secondary mt-3">
                    <i class="fas fa-home"></i> Volver al Menú Principal
                </a>

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
