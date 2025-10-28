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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Estudiantes</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
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

        <!-- Acudiente -->
        <li class="nav-item">
            <a class="nav-link" href="acudiente.php">
                <i class="fas fa-user-tie"></i>
                <span>Acudiente</span>
            </a>
        </li>

        <!-- seguimiento -->

        <li class="nav-item">
            <a class="nav-link" href="seguimiento.php">
                <i class="fas fa-clipboard-check"></i>
                <span>Seguimiento</span>
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

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid mt-4">

                <!-- Formulario Consulta -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1cc88a;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-eye"></i> Consultar Datos de Estudiante
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Seleccione un Estudiante <span class="text-danger">*</span></label>
                                <select id="id_estudiante" class="form-control">
                                    <option value="">-- Seleccione un estudiante --</option>
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

                        <a href="index.html" class="btn btn-secondary mt-3">
                            <i class="fas fa-home"></i> Volver al Menú Principal
                        </a>
                    </div>
                </div>

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