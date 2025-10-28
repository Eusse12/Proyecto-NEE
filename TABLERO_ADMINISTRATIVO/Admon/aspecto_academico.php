<?php
// =============================
// CONFIGURACIÓN DE CONEXIÓN
// =============================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "traspasemos";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipo = "";
$editar = false;
$aspectoEditar = null;

// =============================
// PROCESAR ACCIONES
// =============================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    $id = intval($_POST["id"] ?? 0);
    $nombre = trim($_POST["nombre_aspecto"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $area = trim($_POST["area"] ?? "");
    $ponderacion = floatval($_POST["ponderacion"] ?? 0);

    // AGREGAR
    if ($accion === "agregar") {
        if ($nombre === "" || $descripcion === "" || $area === "" || $ponderacion <= 0) {
            $mensaje = "⚠ Todos los campos son obligatorios.";
            $tipo = "warning";
        } else {
            $stmt = $conn->prepare("INSERT INTO aspectos_academicos (nombre_aspecto, descripcion, area, ponderacion) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssd", $nombre, $descripcion, $area, $ponderacion);
            if ($stmt->execute()) {
                $mensaje = "✅ Aspecto académico agregado correctamente.";
                $tipo = "success";
            } else {
                $mensaje = "❌ Error al agregar: " . $stmt->error;
                $tipo = "danger";
            }
            $stmt->close();
        }
    }

    // EDITAR
    elseif ($accion === "editar" && $id > 0) {
        $stmt = $conn->prepare("UPDATE aspectos_academicos SET nombre_aspecto=?, descripcion=?, area=?, ponderacion=? WHERE id=?");
        $stmt->bind_param("sssdi", $nombre, $descripcion, $area, $ponderacion, $id);
        if ($stmt->execute()) {
            $mensaje = "✏ Aspecto académico actualizado correctamente.";
            $tipo = "success";
        } else {
            $mensaje = "❌ Error al actualizar: " . $stmt->error;
            $tipo = "danger";
        }
        $stmt->close();
    }

    // ELIMINAR
    elseif ($accion === "eliminar" && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM aspectos_academicos WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "🗑 Registro eliminado correctamente.";
            $tipo = "success";
        } else {
            $mensaje = "❌ Error al eliminar: " . $stmt->error;
            $tipo = "danger";
        }
        $stmt->close();
    }
}

// CARGAR PARA EDICIÓN
if (isset($_GET["editar"])) {
    $editar = true;
    $idEditar = intval($_GET["editar"]);
    $resultEdit = $conn->query("SELECT * FROM aspectos_academicos WHERE id = $idEditar");
    $aspectoEditar = $resultEdit->fetch_assoc();
}

// CONSULTAR REGISTROS
$result = $conn->query("SELECT * FROM aspectos_academicos ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Aspectos Académicos</title>
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
        <li class="nav-item active">
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

    <!-- CONTENIDO -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid mt-4">

                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
                        <?= $mensaje ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- FORMULARIO -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: <?= $editar ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editar ? 'edit' : 'plus' ?>"></i>
                            <?= $editar ? 'Editar Aspecto Académico' : 'Agregar Nuevo Aspecto Académico' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="<?= $editar ? 'editar' : 'agregar' ?>">
                            <?php if ($editar): ?>
                                <input type="hidden" name="id" value="<?= $aspectoEditar['id'] ?>">
                            <?php endif; ?>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del Aspecto <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre_aspecto" class="form-control" 
                                           value="<?= $editar ? htmlspecialchars($aspectoEditar['nombre_aspecto']) : '' ?>" 
                                           placeholder="Ej: Desempeño en Matemáticas" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Área <span class="text-danger">*</span></label>
                                    <input type="text" name="area" class="form-control" 
                                           value="<?= $editar ? htmlspecialchars($aspectoEditar['area']) : '' ?>" 
                                           placeholder="Ej: Ciencias Naturales, Lenguaje..." required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Ponderación (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="ponderacion" class="form-control" step="0.01" min="0" max="100"
                                           value="<?= $editar ? htmlspecialchars($aspectoEditar['ponderacion']) : '' ?>" 
                                           placeholder="Ej: 25.00" required>
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Descripción <span class="text-danger">*</span></label>
                                    <textarea name="descripcion" class="form-control" rows="3" 
                                              placeholder="Ej: Evaluación del rendimiento en matemáticas" required><?= $editar ? htmlspecialchars($aspectoEditar['descripcion']) : '' ?></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-<?= $editar ? 'warning' : 'success' ?>">
                                <i class="fas fa-<?= $editar ? 'save' : 'plus' ?>"></i>
                                <?= $editar ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            <?php if ($editar): ?>
                                <a href="aspecto_academico.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- TABLA -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Aspectos Académicos Registrados</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="8%">ID</th>
                                        <th width="20%">Nombre</th>
                                        <th width="15%">Área</th>
                                        <th width="10%">Ponderación</th>
                                        <th width="27%">Descripción</th>
                                        <th width="10%">Fecha</th>
                                        <th width="10%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['id'] ?></td>
                                                <td><?= htmlspecialchars($row['nombre_aspecto']) ?></td>
                                                <td><?= htmlspecialchars($row['area']) ?></td>
                                                <td><?= htmlspecialchars($row['ponderacion']) ?>%</td>
                                                <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                                <td><?= date("d/m/Y", strtotime($row['fecha_creacion'])) ?></td>
                                                <td class="text-center">
                                            <a href="?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                             </a>
                                            <form method="POST" style="display:inline-block;" 
                                             onsubmit="return confirm('⚠ ¿Estás seguro de eliminar este registro?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
        
                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
            
                                            <i class="fas fa-trash"></i>
        
                                        </button>
    
                                    </form>
</td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay aspectos académicos registrados
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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

</body>
</html>

<?php $conn->close();?>
