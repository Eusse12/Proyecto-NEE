<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /traspasemos_git/Proyecto-NEE/traspasemos/Vista/inicio.php");
    exit;
}

$nombre = $_SESSION['usuario'];
$foto = isset($_SESSION['foto']) ? $_SESSION['foto'] : 'img/default.png';

$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Consultar estudiantes con información relacionada
$query = "
SELECT 
    e.id AS id_estudiante,
    e.nombre_completo,
    e.tipo_documento,
    e.numero_documento,
    e.edad,
    e.eps,
    e.direccion,
    e.barrio,
    c.nombre AS ciudad,
    d.nombre AS departamento,
    g.descripcion AS grupo,
    gr.nombre AS grado,
    j.nombre AS jornada,
    s.nombre AS sede,
    a.nombre_completo AS acudiente,
    a.telefono AS telefono_acudiente,
    a.parentesco
FROM datosestud e
LEFT JOIN grupo g ON e.id_grupo = g.id
LEFT JOIN grado gr ON g.id_grado = gr.id
LEFT JOIN jornada j ON g.id_jornada = j.id
LEFT JOIN sede s ON g.id_sede = s.id
LEFT JOIN ciudad c ON e.id_ciudad = c.id
LEFT JOIN departamento d ON e.id_departamento = d.id
LEFT JOIN acudiente a ON e.id_acudiente = a.id
ORDER BY e.nombre_completo ASC
";
$estudiantes = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Estudiantes y Remisiones</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <style>
        .info-box { 
            background: #f1fdf8; 
            border-left: 5px solid #1cc88a; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 15px; 
        }
        .info-box h5 {
            color: #1cc88a;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .info-box p {
            margin-bottom: 8px;
        }
        .card-estudiante {
            border-left: 4px solid #1cc88a;
        }
        .card-estudiante .card-header {
            background: linear-gradient(90deg, #1cc88a, #13855c);
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        .card-estudiante .card-header:hover {
            background: linear-gradient(90deg, #13855c, #1cc88a);
        }
        .badge-activa {
            background-color: #1cc88a;
        }
        .badge-finalizada {
            background-color: #858796;
        }
    </style>
</head>
<body id="page-top">

<div id="wrapper">
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Logo -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-width: 100px;">
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Inicio -->
    <li class="nav-item">
        <a class="nav-link" href="index.php">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Gestión de Usuarios</div>

    <!-- Usuarios -->
    <li class="nav-item">
        <a class="nav-link" href="Usuarios.php">
            <i class="fas fa-users"></i>
            <span>Usuarios</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Estudiantes</div>

    <!-- Agregar Estudiante -->
    <li class="nav-item">
        <a class="nav-link" href="tipo_usuario.php">
            <i class="fas fa-user-plus"></i>
            <span>Agregar Estudiante</span>
        </a>
    </li>

    <!-- Acudiente -->
    <li class="nav-item">
        <a class="nav-link" href="acudiente.php">
            <i class="fas fa-user-tie"></i>
            <span>Acudientes</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Estructura Académica</div>

    <!-- Sede -->
    <li class="nav-item">
        <a class="nav-link" href="Sede.php">
            <i class="fas fa-school"></i>
            <span>Sedes</span>
        </a>
    </li>

    <!-- Grado -->
    <li class="nav-item">
        <a class="nav-link" href="grado.php">
            <i class="fas fa-graduation-cap"></i>
            <span>Grados</span>
        </a>
    </li>

    <!-- Grupo -->
    <li class="nav-item ">
        <a class="nav-link" href="Grupo.php">
            <i class="fas fa-users-cog"></i>
            <span>Grupos</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">NEE y Seguimiento</div>

    <!-- NEE -->
    <li class="nav-item">
        <a class="nav-link" href="nee.php">
            <i class="fas fa-brain"></i>
            <span>NEE</span>
        </a>
    </li>

    <!-- Remisión -->
    <li class="nav-item">
        <a class="nav-link" href="remision.php">
            <i class="fas fa-file-medical"></i>
            <span>Remisiones</span>
        </a>
    </li>

    <!-- Seguimiento -->
    <li class="nav-item active">
        <a class="nav-link" href="seguimiento.php">
            <i class="fas fa-clipboard-check"></i>
            <span>Seguimientos</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Aspectos Educativos</div>

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

                    <!-- Botón menú responsive -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Sección derecha del topbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Usuario con imagen -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <!-- Foto y nombre -->
                                <div class="d-flex align-items-center">
                                    <img class="img-profile rounded-circle mr-2"
                                         src="<?php echo htmlspecialchars($foto); ?>"
                                         alt="Foto de perfil"
                                         style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #ddd;">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                        <?php echo htmlspecialchars($nombre); ?>
                                    </span>
                                </div>
                            </a>

                            <!-- Menú desplegable -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="perfil.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Mi Perfil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->
            <div class="container-fluid mt-4">

                <!-- Título -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-users text-success"></i> Lista de Estudiantes y Remisiones
                    </h1>
                </div>

                <!-- Acordeón de Estudiantes -->
                <div class="accordion" id="accordionEstudiantes">
                    <?php if ($estudiantes && $estudiantes->num_rows > 0): ?>
                        <?php while ($row = $estudiantes->fetch_assoc()): ?>
                            <?php
                            // Consultar remisiones asociadas por nombre exacto
                            $rem = $conn->prepare("
                                SELECT fecha_remision, motivo_remision, docente_remitente,
                                    CASE 
                                        WHEN fecha_remision >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 'Activa'
                                        ELSE 'Finalizada'
                                    END AS estado
                                FROM remision
                                WHERE nombre_estudiante = ?
                                ORDER BY fecha_remision DESC
                            ");
                                                    
                            $rem->bind_param("s", $row['nombre_completo']);
                            $rem->execute();
                            $resRem = $rem->get_result();
                            $remisiones = $resRem->fetch_all(MYSQLI_ASSOC);
                            $rem->close();
                            ?>

                            <div class="card shadow mb-3 card-estudiante">
                                <div class="card-header" id="heading<?= $row['id_estudiante'] ?>" data-toggle="collapse" 
                                     data-target="#collapse<?= $row['id_estudiante'] ?>" aria-expanded="false">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($row['nombre_completo']) ?> — 
                                    <?= htmlspecialchars($row['tipo_documento'] ?? '') ?> <?= htmlspecialchars($row['numero_documento'] ?? '') ?>
                                    <i class="fas fa-chevron-down float-right"></i>
                                </div>

                                <div id="collapse<?= $row['id_estudiante'] ?>" class="collapse" 
                                     aria-labelledby="heading<?= $row['id_estudiante'] ?>" data-parent="#accordionEstudiantes">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Columna Izquierda -->
                                            <div class="col-md-6">
                                                <!-- Información Personal -->
                                                <div class="info-box">
                                                    <h5><i class="fas fa-id-card"></i> Información Personal</h5>
                                                    <p><strong>Edad:</strong> <?= $row['edad'] ?? 'No registrada' ?> años</p>
                                                    <p><strong>EPS:</strong> <?= htmlspecialchars($row['eps'] ?? 'No registrada') ?></p>
                                                    <p><strong>Dirección:</strong> <?= htmlspecialchars($row['direccion'] ?? 'No registrada') ?></p>
                                                    <p><strong>Barrio:</strong> <?= htmlspecialchars($row['barrio'] ?? 'No registrado') ?></p>
                                                    <p><strong>Ciudad:</strong> <?= htmlspecialchars($row['ciudad'] ?? 'N/A') ?></p>
                                                    <p><strong>Departamento:</strong> <?= htmlspecialchars($row['departamento'] ?? 'N/A') ?></p>
                                                </div>

                                                <!-- Información Académica -->
                                                <div class="info-box">
                                                    <h5><i class="fas fa-school"></i> Información Académica</h5>
                                                    <p><strong>Grado:</strong> <?= htmlspecialchars($row['grado'] ?? 'N/A') ?></p>
                                                    <p><strong>Grupo:</strong> <?= htmlspecialchars($row['grupo'] ?? 'N/A') ?></p>
                                                    <p><strong>Jornada:</strong> <?= htmlspecialchars($row['jornada'] ?? 'N/A') ?></p>
                                                    <p><strong>Sede:</strong> <?= htmlspecialchars($row['sede'] ?? 'N/A') ?></p>
                                                </div>
                                            </div>

                                            <!-- Columna Derecha -->
                                            <div class="col-md-6">
                                                <!-- Acudiente -->
                                                <div class="info-box">
                                                    <h5><i class="fas fa-user-friends"></i> Acudiente</h5>
                                                    <p><strong>Nombre:</strong> <?= htmlspecialchars($row['acudiente'] ?? 'No registrado') ?></p>
                                                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($row['telefono_acudiente'] ?? '---') ?></p>
                                                    <p><strong>Parentesco:</strong> <?= htmlspecialchars($row['parentesco'] ?? '---') ?></p>
                                                </div>

                                                <!-- Remisiones -->
                                                <div class="info-box">
                                                    <h5><i class="fas fa-file-alt"></i> Remisiones</h5>
                                                    <?php if (count($remisiones) > 0): ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Fecha</th>
                                                                        <th>Docente</th>
                                                                        <th>Motivo</th>
                                                                        <th>Estado</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($remisiones as $r): ?>
                                                                        <tr>
                                                                            <td><?= htmlspecialchars($r['fecha_remision']) ?></td>
                                                                            <td><?= htmlspecialchars($r['docente_remitente'] ?? 'Sin docente') ?></td>
                                                                            <td>
                                                                                <small><?= htmlspecialchars(substr($r['motivo_remision'] ?? 'Sin motivo', 0, 30)) ?>...</small>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge <?= $r['estado'] === 'Activa' ? 'badge-activa' : 'badge-finalizada' ?>">
                                                                                    <?= $r['estado'] ?>
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <?php else: ?>
                                                        <p class="text-muted"><i class="fas fa-info-circle"></i> No tiene remisiones registradas.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> No hay estudiantes registrados en el sistema.
                        </div>
                    <?php endif; ?>
                </div>

                <a href="index.html" class="btn btn-secondary mt-3">
                    <i class="fas fa-home"></i> Volver al Menú Principal
                </a>

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