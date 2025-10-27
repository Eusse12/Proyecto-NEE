<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "traspasemos";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Consultar los datos desde la vista
$sql = "SELECT * FROM vista_seguimiento_general ORDER BY estudiante ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento General - TRASPASEMOS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon">
                <img src="img/logo.png" alt="Logo" style="max-width:80px;">
            </div>
        </a>

        <hr class="sidebar-divider my-0">
        <li class="nav-item active">
            <a class="nav-link" href="index.html">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>TRASPASEMOS</span></a>
        </li>
        <hr class="sidebar-divider">

        <li class="nav-item"><a class="nav-link" href="Usuarios.php"><i class="fas fa-fw fa-user"></i> Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="aspecto_academico.php"><i class="fas fa-fw fa-book"></i> Aspectos Académicos</a></li>
        <li class="nav-item"><a class="nav-link" href="aspecto_complementario.php"><i class="fas fa-fw fa-check-circle"></i> Aspectos Complementarios</a></li>
        <li class="nav-item"><a class="nav-link" href="seguimiento_general.php"><i class="fas fa-fw fa-table"></i> Seguimiento General</a></li>
        <hr class="sidebar-divider">
    </ul>

    <!-- Contenido -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content" class="p-4">

            <div class="container-fluid">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><i class="fas fa-users"></i> Seguimiento General de Estudiantes</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-primary text-white text-center">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Estudiante</th>
                                        <th>Tipo Documento</th>
                                        <th>Identificación</th>
                                        <th>Grado</th>
                                        <th>Grupo</th>
                                        <th>Sede</th>
                                        <th>Fecha Nacimiento</th>
                                        <th>Dirección</th>
                                        <th>Barrio</th>
                                        <th>Ciudad</th>
                                        <th>Departamento</th>
                                        <th>EPS</th>
                                        <th>Acudiente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-center"><?= $row['id_usuario'] ?></td>
                                                <td><?= htmlspecialchars($row['estudiante'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['tipo_documento'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['identificacion'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['grado'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['grupo'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['sede'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['fecha_nacimiento'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['direccion'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['barrio'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['ciudad'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['departamento'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['eps'] ?? '—') ?></td>
                                                <td><?= htmlspecialchars($row['acudiente'] ?? '—') ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="14" class="text-center text-muted">No hay datos registrados.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
