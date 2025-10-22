<?php
// ==============================
// CONEXIÓN A LA BASE DE DATOS
// ==============================
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// ==============================
// GUARDAR SEDE
// ==============================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombresede"]);
    $direccion = trim($_POST["direccion"]);

    if (!empty($nombre) && !empty($direccion)) {
        $stmt = $conn->prepare("INSERT INTO sede (nombre, direccion) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $direccion);
        if ($stmt->execute()) {
            $mensaje = "✅ Sede agregada correctamente.";
        } else {
            $mensaje = "❌ Error al guardar: " . $conn->error;
        }
        $stmt->close();
    } else {
        $mensaje = "⚠ Debes llenar todos los campos.";
    }
}

// ==============================
// CONSULTAR SEDES
// ==============================
$sedes = $conn->query("SELECT id, nombre, direccion FROM sede ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Sedes</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon">
                <img src="img/Logo.png" alt="Logo" class="img-fluid" style="max-width: 130px; height: auto;">
            </div>
        </a>

        <hr class="sidebar-divider my-0">
        <li class="nav-item active">
            <a class="nav-link" href="index.html">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Menú Principal</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <li class="nav-item active">
            <a class="nav-link" href="sede.php">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Sede</span>
            </a>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
    </ul>

    <!-- Contenido principal -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid mt-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 text-white text-center" style="background-color:#1fbeac;">
                        <h5 class="m-0 font-weight-bold">Tabla - Sede</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($mensaje)): ?>
                            <div class="alert alert-info"><?= $mensaje ?></div>
                        <?php endif; ?>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white text-center">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre de la sede</th>
                                        <th>Dirección</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($sedes && $sedes->num_rows > 0): ?>
                                        <?php while ($row = $sedes->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['id'] ?></td>
                                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                                <td><?= htmlspecialchars($row['direccion']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted">No hay sedes registradas.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Botón para abrir el modal -->
                        <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarSede">
                            <i class="fas fa-plus"></i> Agregar Sede
                        </button>
                        <a href="index.html" class="btn btn-secondary ml-2">
                            <i class="fas fa-home"></i> Volver al Menú Principal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-light">
            <div class="container my-auto">
                <div class="text-center my-auto">
                    <span>Copyright &copy; Tu Proyecto 2025</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Modal Agregar Sede -->
<div class="modal fade" id="modalAgregarSede" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Agregar Sede</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="sede.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre de la sede</label>
                        <input type="text" name="nombresede" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
