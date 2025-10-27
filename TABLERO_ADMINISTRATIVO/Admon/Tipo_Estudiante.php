<?php
// Configuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Variables iniciales
$nombre = $parentesco = $telefono = $correo = "";
$accion = $_POST['accion'] ?? '';
$id = $_POST['id'] ?? '';

// Procesar acciones CRUD
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($accion === "agregar") {
        $stmt = $conn->prepare("INSERT INTO acudiente (nombre_completo, parentesco, telefono, correo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $_POST['nombre_completo'], $_POST['parentesco'], $_POST['telefono'], $_POST['correo']);
        $stmt->execute();
        $mensaje = "✅ Acudiente agregado correctamente.";
    } elseif ($accion === "editar") {
        $stmt = $conn->prepare("UPDATE acudiente SET nombre_completo=?, parentesco=?, telefono=?, correo=? WHERE id=?");
        $stmt->bind_param("ssssi", $_POST['nombre_completo'], $_POST['parentesco'], $_POST['telefono'], $_POST['correo'], $_POST['id']);
        $stmt->execute();
        $mensaje = "✏️ Acudiente actualizado correctamente.";
    } elseif ($accion === "eliminar") {
        $stmt = $conn->prepare("DELETE FROM acudiente WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $mensaje = "🗑️ Acudiente eliminado correctamente.";
    }
}

// Cargar todos los acudientes
$result = $conn->query("SELECT * FROM acudiente ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Acudientes - TRASPASEMOS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar .nav-link, .sidebar .collapse-item {
            color: white !important;
        }
        .card-header {
            background: linear-gradient(90deg, #007bff, #00c6ff);
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .sidebar {
            min-height: 100vh;
        }
    </style>
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
        <li class="nav-item active">
            <a class="nav-link" href="Acudiente.php"><i class="fas fa-user-friends"></i> <span>Acudientes</span></a>
        </li>
        <hr class="sidebar-divider">
    </ul>

    <!-- Contenido principal -->
    <div id="content-wrapper" class="d-flex flex-column" style="width:100%">
        <div id="content" class="p-4">
            <div class="container-fluid">
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $mensaje ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-plus"></i> Agregar Nuevo Acudiente
                    </div>
                    <div class="card-body">
                        <form method="POST" action="tipo_estudiante.php">
                            <input type="hidden" name="accion" value="agregar">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Nombre Completo</label>
                                    <input type="text" name="nombre_completo" class="form-control" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Parentesco</label>
                                    <input type="text" name="parentesco" class="form-control">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Teléfono</label>
                                    <input type="text" name="telefono" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Correo</label>
                                    <input type="email" name="correo" class="form-control">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                        </form>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="card shadow">
                    <div class="card-header">
                        <i class="fas fa-table"></i> Acudientes Registrados
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered text-center">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Parentesco</th>
                                        <th>Teléfono</th>
                                        <th>Correo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($a = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $a['id'] ?></td>
                                                <td><?= htmlspecialchars($a['nombre_completo']) ?></td>
                                                <td><?= htmlspecialchars($a['parentesco']) ?></td>
                                                <td><?= htmlspecialchars($a['telefono']) ?></td>
                                                <td><?= htmlspecialchars($a['correo']) ?></td>
                                                <td>
                                                    <!-- Botón editar -->
                                                    <form method="POST" action="tipo_estudiante.php" style="display:inline-block;">
                                                        <input type="hidden" name="accion" value="editar">
                                                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                                        <input type="hidden" name="nombre_completo" value="<?= $a['nombre_completo'] ?>">
                                                        <input type="hidden" name="parentesco" value="<?= $a['parentesco'] ?>">
                                                        <input type="hidden" name="telefono" value="<?= $a['telefono'] ?>">
                                                        <input type="hidden" name="correo" value="<?= $a['correo'] ?>">
                                                        <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                                                    </form>

                                                    <!-- Botón eliminar -->
                                                    <form method="POST" action="tipo_estudiante.php" style="display:inline-block;" onsubmit="return confirm('¿Seguro que deseas eliminar este acudiente?');">
                                                        <input type="hidden" name="accion" value="eliminar">
                                                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6">No hay acudientes registrados.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <footer class="sticky-footer bg-light text-center py-3">
            <span>© TRASPASEMOS 2025</span>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
