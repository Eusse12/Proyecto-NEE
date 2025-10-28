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
<div class="sidebar">
    <div class="text-center py-4 border-bottom border-light">
        <i class="fas fa-user-graduate fa-2x"></i>
        <h6 class="mt-2">TRASPASEMOS</h6>
    </div>
    <div class="sidebar-heading">Gestión</div>
    <a href="index.html"><i class="fas fa-home"></i> Inicio</a>
    <a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
    <a href="tipo_usuario.php"><i class="fas fa-user-shield"></i> Tipos de Usuario</a>
    <a href="datosestud.php" class="bg-white text-dark rounded"><i class="fas fa-user-graduate"></i> Estudiantes</a>
    <a href="remision.php"><i class="fas fa-file-alt"></i> Remisiones</a>
</div>

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
