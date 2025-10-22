<?php
// Configuración de la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$database = "traspasemos";

// Conectar a la base de datos
$conn = new mysqli($host, $user, $pass, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Crear tabla si no existe
$crearTabla = "CREATE TABLE IF NOT EXISTS grado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!$conn->query($crearTabla)) {
    die("Error al crear la tabla: " . $conn->error);
}

$mensaje = "";
$tipoMensaje = "";

// Procesar acciones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    switch ($accion) {
        case 'agregar':
            $nombre = trim($_POST['nombreGrado'] ?? '');
            
            if ($nombre !== '') {
                $stmt = $conn->prepare("INSERT INTO grado (nombre) VALUES (?)");
                
                if ($stmt === false) {
                    $mensaje = "❌ Error al preparar consulta: " . $conn->error;
                    $tipoMensaje = "danger";
                } else {
                    $stmt->bind_param("s", $nombre);
                    
                    if ($stmt->execute()) {
                        $mensaje = "✅ Grado agregado correctamente.";
                        $tipoMensaje = "success";
                    } else {
                        $mensaje = "❌ Error al agregar el grado: " . $stmt->error;
                        $tipoMensaje = "danger";
                    }
                    $stmt->close();
                }
            } else {
                $mensaje = "⚠ El nombre del grado no puede estar vacío.";
                $tipoMensaje = "warning";
            }
            break;
            
        case 'editar':
            $id = intval($_POST['gradoId'] ?? 0);
            $nombre = trim($_POST['nombreGrado'] ?? '');
            
            if ($id > 0 && $nombre !== '') {
                $stmt = $conn->prepare("UPDATE grado SET nombre = ? WHERE id = ?");
                
                if ($stmt === false) {
                    $mensaje = "❌ Error al preparar consulta: " . $conn->error;
                    $tipoMensaje = "danger";
                } else {
                    $stmt->bind_param("si", $nombre, $id);
                    
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            $mensaje = "✏️ Grado actualizado correctamente.";
                            $tipoMensaje = "success";
                        } else {
                            $mensaje = "⚠ No se encontró el grado con ese ID.";
                            $tipoMensaje = "warning";
                        }
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
            $id = intval($_POST['gradoId'] ?? 0);
            
            if ($id > 0) {
                $stmt = $conn->prepare("DELETE FROM grado WHERE id = ?");
                
                if ($stmt === false) {
                    $mensaje = "❌ Error al preparar consulta: " . $conn->error;
                    $tipoMensaje = "danger";
                } else {
                    $stmt->bind_param("i", $id);
                    
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            $mensaje = "🗑️ Grado eliminado correctamente.";
                            $tipoMensaje = "success";
                        } else {
                            $mensaje = "⚠ No se encontró el grado con ese ID.";
                            $tipoMensaje = "warning";
                        }
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
$editarGrado = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM grado WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editarGrado = $result->fetch_assoc();
        $stmt->close();
    }
}

// Listar todos los grado
$sql = "SELECT * FROM grado ORDER BY id ASC";
$result = $conn->query($sql);

if ($result === false) {
    die("Error al obtener los grado: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grado</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
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
                <span>Menú Principal</span></a>
        </li>
        <hr class="sidebar-divider">
        <li class="nav-item active">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Grado</span></a>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid mt-4">
                
                <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>
                
                <!-- Formulario Agregar/Editar -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: <?= $editarGrado ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editarGrado ? 'edit' : 'plus' ?>"></i>
                            <?= $editarGrado ? 'Editar Grado' : 'Agregar Nuevo Grado' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="<?= $editarGrado ? 'editar' : 'agregar' ?>">
                            <?php if ($editarGrado): ?>
                            <input type="hidden" name="gradoId" value="<?= $editarGrado['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label for="nombreGrado">Nombre del Grado <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombreGrado" name="nombreGrado" 
                                           placeholder="Ej: Grado 11" 
                                           value="<?= $editarGrado ? htmlspecialchars($editarGrado['nombre']) : '' ?>" 
                                           required>
                                </div>
                                <div class="form-group col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-<?= $editarGrado ? 'warning' : 'success' ?> btn-block">
                                        <i class="fas fa-<?= $editarGrado ? 'save' : 'plus' ?>"></i>
                                        <?= $editarGrado ? 'Actualizar' : 'Agregar' ?>
                                    </button>
                                    <?php if ($editarGrado): ?>
                                    <a href="grado.php" class="btn btn-secondary ml-2">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de grado -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - grado Registrados</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th width="60%">Nombre del Grado</th>
                                        <th width="30%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($grado = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $grado['id'] ?></td>
                                            <td><?= htmlspecialchars($grado['nombre']) ?></td>
                                            <td class="text-center">
                                                <a href="?editar=<?= $grado['id'] ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de que deseas eliminar este grado?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="gradoId" value="<?= $grado['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay grado registrados
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
        
        <footer class="sticky-footer bg-light">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Tu Proyecto 2025</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php $conn->close(); ?>