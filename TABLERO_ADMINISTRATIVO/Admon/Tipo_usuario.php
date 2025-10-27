<?php
// =========================================
// CONFIGURACIÓN DE CONEXIÓN
// =========================================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "traspasemos";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipo = "";

// =========================================
// PROCESAR FORMULARIO (INSERTAR / ELIMINAR)
// =========================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    $id = intval($_POST["id"] ?? 0);

    // Datos comunes
    $nombre = trim($_POST["nombre_completo"] ?? "");
    $fecha = $_POST["fecha_nacimiento"] ?? null;
    $id_grupo = intval($_POST["id_grupo"] ?? 0);
    $direccion = trim($_POST["direccion"] ?? "");
    $barrio = trim($_POST["barrio"] ?? "");
    $id_ciudad = intval($_POST["id_ciudad"] ?? 0);
    $id_departamento = intval($_POST["id_departamento"] ?? 0);
    $eps = trim($_POST["eps"] ?? "");
    $id_acudiente = !empty($_POST['id_acudiente']) ? $_POST['id_acudiente'] : null;


    // AGREGAR NUEVO
    if ($accion === "agregar") {
        $stmt = $conn->prepare("INSERT INTO datosestud 
            (id, fecha_nacimiento, id_grupo, direccion, barrio, id_ciudad, id_departamento, eps, id_acudiente)
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sisssisi", $fecha, $id_grupo, $direccion, $barrio, $id_ciudad, $id_departamento, $eps, $id_acudiente);
            if ($stmt->execute()) {
                $mensaje = "✅ Estudiante registrado correctamente.";
                $tipo = "success";
            } else {
                $mensaje = "❌ Error al guardar: " . $stmt->error;
                $tipo = "danger";
            }
            $stmt->close();
        }
    }

    // ELIMINAR
    elseif ($accion === "eliminar" && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM datosestud WHERE id = ?");
        if ($stmt) {
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
}

// =========================================
// CONSULTA DE ESTUDIANTES
// =========================================
$sql = "SELECT d.*, 
            g.descripcion AS grupo, 
            a.nombre_completo AS acudiente,
            c.nombre AS ciudad,
            dept.nombre AS departamento
        FROM datosestud d
        LEFT JOIN grupo g ON d.id_grupo = g.id
        LEFT JOIN acudiente a ON d.id_acudiente = a.id
        LEFT JOIN ciudad c ON d.id_ciudad = c.id
        LEFT JOIN departamento dept ON d.id_departamento = dept.id
        ORDER BY d.id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Estudiantes</title>
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

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="TipoDocumento.html">
            <i class="fas fa-fw fa-id-card"></i>
            <span>Tipo Documento</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="grado.php">
            <i class="fas fa-fw fa-graduation-cap"></i>
            <span>Grado</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="Sede.php">
            <i class="fas fa-fw fa-building"></i>
            <span>Sede</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="Grupo.php">
            <i class="fas fa-fw fa-users"></i>
            <span>Grupos</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="aspecto_complementario.php">
            <i class="fas fa-fw fa-puzzle-piece"></i>
            <span>Aspectos Complementarios</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="aspecto_academico.php">
            <i class="fas fa-fw fa-book"></i>
            <span>Aspectos Académicos</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="Tipo_usuario.php">
            <i class="fas fa-fw fa-user-tag"></i>
            <span>Tipos de Usuarios</span>
        </a>
    </li>

    <li class="nav-item active">
        <a class="nav-link" href="Tipo_Estudiante.php">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span>Tipos de Estudiantes</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
</ul>


    <!-- Content Wrapper -->
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

                <!-- Formulario Agregar -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1cc88a;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-plus"></i> Agregar Nuevo Estudiante
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="accion" value="agregar">
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre_completo" class="form-control" placeholder="Nombre completo del estudiante" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Fecha de nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Grupo</label>
                                    <select name="id_grupo" class="form-control">
                                        <option value="">Seleccione...</option>
                                        <?php
                                        $grupos = $conn->query("SELECT id, descripcion FROM grupo");
                                        while ($g = $grupos->fetch_assoc()) {
                                            echo "<option value='{$g['id']}'>{$g['descripcion']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Dirección</label>
                                    <input type="text" name="direccion" class="form-control" placeholder="Dirección de residencia">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Barrio</label>
                                    <input type="text" name="barrio" class="form-control" placeholder="Barrio">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>EPS</label>
                                    <input type="text" name="eps" class="form-control" placeholder="Nombre de la EPS">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Ciudad</label>
                                    <select name="id_ciudad" class="form-control">
                                        <option value="">Seleccione...</option>
                                        <?php
                                        $ciudades = $conn->query("SELECT id, nombre FROM ciudad");
                                        while ($c = $ciudades->fetch_assoc()) {
                                            echo "<option value='{$c['id']}'>{$c['nombre']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Departamento</label>
                                    <select name="id_departamento" class="form-control">
                                        <option value="">Seleccione...</option>
                                        <option value="">Amazonia</option>
                                        <option value="">Antioquia</option>
                                        <option value="">Arauca</option>
                                        <option value="">Atlantico</option>
                                        <option value="">Bolivar</option>
                                        <option value="">Boyacá</option>
                                        <option value="">Caldas</option>
                                        <option value="">Caquetá</option>
                                        <option value="">Casanare</option>
                                        <option value="">Cauca</option>
                                        <option value="">Cesar</option>
                                        <option value="">Chocó</option>
                                        <option value="">Córdoba</option>
                                        <option value="">Cundinamarca</option>
                                        <option value="">Guainía</option>
                                        <option value="">Guviare</option>
                                        <option value="">Huila</option>
                                        <option value="">La Guajira</option>
                                        <option value="">Magdalena</option>
                                        <option value="">Meta</option>
                                        <option value="">Nariño</option>
                                        <option value="">Norte de Santander</option>
                                        <option value="">Putamayo</option>
                                        <option value="">Quindío</option>
                                        <option value="">Risaralda</option>
                                        <option value="">San Andrés y Provivencia</option>
                                        <option value="">Santander</option>
                                        <option value="">Sucre</option>
                                        <option value="">Tolima</option>
                                        <option value="">Valle del cauca</option>
                                        <option value="">Vaupés</option>
                                        <option value="">Vichada</option>

                                        <?php
                                        $deps = $conn->query("SELECT id, nombre FROM departamento");
                                        while ($d = $deps->fetch_assoc()) {
                                            echo "<option value='{$d['id']}'>{$d['nombre']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <button class="btn btn-success" type="submit">
                                <i class="fas fa-save"></i> Agregar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tabla de estudiantes -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Estudiantes Registrados</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="15%">Dirección</th>
                                        <th width="10%">Barrio</th>
                                        <th width="10%">Ciudad</th>
                                        <th width="10%">Departamento</th>
                                        <th width="10%">Grupo</th>
                                        <th width="10%">EPS</th>
                                        <th width="15%">Acudiente</th>
                                        <th width="15%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row["id"] ?></td>
                                            <td><?= htmlspecialchars($row["direccion"]) ?></td>
                                            <td><?= htmlspecialchars($row["barrio"]) ?></td>
                                            <td><?= htmlspecialchars($row["ciudad"]) ?></td>
                                            <td><?= htmlspecialchars($row["departamento"]) ?></td>
                                            <td><?= htmlspecialchars($row["grupo"]) ?></td>
                                            <td><?= htmlspecialchars($row["eps"]) ?></td>
                                            <td><?= htmlspecialchars($row["acudiente"]) ?></td>
                                            <td class="text-center text-nowrap">
                                                <a href="?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar este registro?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay estudiantes registrados
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
                    <span>Copyright &copy; TRASPASEMOS 2025</span>
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
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
<?php $conn->close(); ?>