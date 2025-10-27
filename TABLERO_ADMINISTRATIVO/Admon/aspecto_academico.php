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
            $mensaje = "⚠️ Todos los campos son obligatorios.";
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
            $mensaje = "✏️ Aspecto académico actualizado correctamente.";
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
            $mensaje = "🗑️ Registro eliminado correctamente.";
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
    <title>Aspectos Académicos</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
    <!-- SIDEBAR -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon">
                <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-width: 100px;">
            </div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link" href="index.html">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>TRASPASEMOS</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <li class="nav-item">
            <a class="nav-link" href="Usuarios.php">
                <i class="fas fa-fw fa-user"></i>
                <span>Usuarios</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">

        <!-- Menú Configuración corregido -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfig" 
               aria-expanded="false" aria-controls="collapseConfig">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Configuración</span>
            </a>
            <div id="collapseConfig" class="collapse" aria-labelledby="headingConfig" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Opciones:</h6>
                    <a class="collapse-item" href="TipoDocumento.html">Tipo Documento</a>
                    <a class="collapse-item" href="grado.php">Grado</a>
                    <a class="collapse-item" href="Sede.php">Sede</a>
                    <a class="collapse-item" href="Grupo.php">Grupos</a>
                    <a class="collapse-item" href="aspecto_complementario.php">Aspectos Complementarios</a>
                    <a class="collapse-item active" href="aspecto_academico.php">Aspectos Académicos</a>
                    <a class="collapse-item" href="Tipo_usuario.php">Tipos de Usuarios</a>
                    <a class="collapse-item" href="Tipo_Estudiante.php">Tipos de Estudiantes</a>
                </div>
            </div>
        </li>

        <hr class="sidebar-divider">

        
    </ul>

    <!-- CONTENIDO -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Usuario</span>
                            <i class="fas fa-user-circle fa-2x"></i>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Contenido Principal -->
            <div class="container-fluid">

                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipo ?> alert-dismissible fade show">
                        <?= $mensaje ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <!-- FORMULARIO -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-<?= $editar ? 'warning' : 'success' ?> text-white">
                        <h5 class="m-0 font-weight-bold text-center">
                            <?= $editar ? '✏️ Editar Aspecto Académico' : '➕ Registrar Aspecto Académico' ?>
                        </h5>
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

                            <div class="text-center">
                                <button type="submit" class="btn btn-<?= $editar ? 'warning' : 'success' ?> btn-lg">
                                    <i class="fas fa-<?= $editar ? 'save' : 'plus' ?>"></i>
                                    <?= $editar ? 'Actualizar' : 'Guardar' ?>
                                </button>
                                <?php if ($editar): ?>
                                    <a href="aspecto_academico.php" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TABLA -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="m-0 font-weight-bold text-center">📋 Lista de Aspectos Académicos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Área</th>
                                        <th>Ponderación</th>
                                        <th>Descripción</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
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
                                                <td>
                                                    <a href="?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" style="display:inline-block;" 
                                                          onsubmit="return confirm('¿Está seguro de eliminar este registro?');">
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
                                        <tr><td colspan="7" class="text-muted">No hay registros disponibles</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>© TRASPASEMOS 2025</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle del sidebar
    $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
    });

    // Prevenir que el sidebar se cierre al hacer scroll en dispositivos pequeños
    $(window).resize(function() {
        if ($(window).width() < 768) {
            $('.sidebar .collapse').collapse('hide');
        }
    });
</script>

</body>
</html>

<?php $conn->close(); ?>