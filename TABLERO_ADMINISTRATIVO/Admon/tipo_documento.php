<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Consultar estudiantes desde la tabla datosestud
$query = "
    SELECT id, nombre_completo, tipo_documento, numero_documento
    FROM datosestud
    ORDER BY nombre_completo ASC
";
$estudiantes = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Estudiantes</title>
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

<!-- SIDEBAR -->
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
        <li class="nav-item">
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
        <li class="nav-item ">
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
        <h2 class="mb-4 text-primary"><i class="fas fa-eye"></i> Ver Datos de Estudiantes</h2>

        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <i class="fas fa-list"></i> Seleccione un Estudiante
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label>Estudiante</label>
                        <select id="id_estudiante" class="form-control">
                            <option value="">-- Seleccione --</option>
                            <?php while($est = $estudiantes->fetch_assoc()): ?>
                                <option 
                                    value="<?= $est['id'] ?>" 
                                    data-documento="<?= htmlspecialchars($est['tipo_documento']) ?>" 
                                    data-identificacion="<?= htmlspecialchars($est['numero_documento']) ?>" 
                                    data-nombre="<?= htmlspecialchars($est['nombre_completo']) ?>">
                                    <?= htmlspecialchars($est['nombre_completo']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Tipo de Documento</label>
                        <input type="text" id="tipo_documento" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Número de Documento</label>
                        <input type="text" id="numero_documento" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Nombre Completo</label>
                        <input type="text" id="nombre_estudiante" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <span>© TRASPASEMOS 2025 - Panel Administrativo</span>
        </footer>
    </div>
</div>

<script>
document.getElementById('id_estudiante').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    document.getElementById('tipo_documento').value = option.getAttribute('data-documento') || '';
    document.getElementById('numero_documento').value = option.getAttribute('data-identificacion') || '';
    document.getElementById('nombre_estudiante').value = option.getAttribute('data-nombre') || '';
});
</script>

</body>
</html>
<?php $conn->close(); ?>
