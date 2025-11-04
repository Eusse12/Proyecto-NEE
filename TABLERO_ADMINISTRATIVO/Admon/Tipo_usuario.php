<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /traspasemos_git/Proyecto-NEE/traspasemos/Vista/inicio.php");
    exit;
}

$nombre = $_SESSION['usuario'];
$foto = isset($_SESSION['foto']) ? $_SESSION['foto'] : 'img/default.png';

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipoMensaje = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? '';
    $id = intval($_POST['estudianteId'] ?? 0);
    
    $nombre_completo = trim($_POST["nombre_completo"] ?? '');
    $tipo_documento = trim($_POST["tipo_documento"] ?? '');
    $numero_documento = trim($_POST["numero_documento"] ?? '');
    $fecha_nacimiento = $_POST["fecha_nacimiento"] ?? null;
    
    // Calcular edad automáticamente desde la fecha de nacimiento
    $edad = 0;
    if (!empty($fecha_nacimiento)) {
        $fechaNac = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fechaNac)->y;
    }
    
    $direccion = trim($_POST["direccion"] ?? '');
    $barrio = trim($_POST["barrio"] ?? '');
    $id_ciudad = intval($_POST["id_ciudad"] ?? 0);
    $id_departamento = intval($_POST["id_departamento"] ?? 0);
    $eps = trim($_POST["eps"] ?? '');
    $id_grupo = intval($_POST["id_grupo"] ?? 0);
    $id_acudiente = intval($_POST["id_acudiente"] ?? 0);

    if ($accion === 'agregar') {
        if (!empty($nombre_completo) && !empty($numero_documento)) {
            $stmt = $conn->prepare("INSERT INTO datosestud (nombre_completo, tipo_documento, numero_documento, fecha_nacimiento, edad, direccion, barrio, id_ciudad, id_departamento, eps, id_grupo, id_acudiente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssissiisii", $nombre_completo, $tipo_documento, $numero_documento, $fecha_nacimiento, $edad, $direccion, $barrio, $id_ciudad, $id_departamento, $eps, $id_grupo, $id_acudiente);
            if ($stmt->execute()) {
                $mensaje = "✅ Estudiante agregado correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "❌ Error al guardar: " . $conn->error;
                $tipoMensaje = "danger";
            }
            $stmt->close();
        } else {
            $mensaje = "⚠ El nombre completo y número de documento son obligatorios.";
            $tipoMensaje = "warning";
        }
    }

    if ($accion === 'editar') {
        if ($id > 0 && !empty($nombre_completo) && !empty($numero_documento)) {
            $stmt = $conn->prepare("UPDATE datosestud SET nombre_completo=?, tipo_documento=?, numero_documento=?, fecha_nacimiento=?, edad=?, direccion=?, barrio=?, id_ciudad=?, id_departamento=?, eps=?, id_grupo=?, id_acudiente=? WHERE id=?");
            $stmt->bind_param("ssssissiisiii", $nombre_completo, $tipo_documento, $numero_documento, $fecha_nacimiento, $edad, $direccion, $barrio, $id_ciudad, $id_departamento, $eps, $id_grupo, $id_acudiente, $id);
            if ($stmt->execute()) {
                $mensaje = "✏️ Estudiante actualizado correctamente.";
                $tipoMensaje = "success";
            } else {
                $mensaje = "❌ Error al actualizar: " . $conn->error;
                $tipoMensaje = "danger";
            }
            $stmt->close();
        } else {
            $mensaje = "⚠ Datos inválidos para editar.";
            $tipoMensaje = "warning";
        }
    }

    if ($accion === 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM datosestud WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "🗑️ Estudiante eliminado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al eliminar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }
}

// Obtener datos para editar
$editarEstudiante = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conn->prepare("SELECT * FROM datosestud WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $editarEstudiante = $result->fetch_assoc();
        $stmt->close();
    }
}

// Consultar estudiantes
$estudiantes = $conn->query("SELECT * FROM datosestud ORDER BY nombre_completo ASC");

// Consultar datos para los select
$departamentos = $conn->query("SELECT id, nombre FROM departamento ORDER BY nombre ASC");
$grupos = $conn->query("SELECT id, descripcion FROM grupo ORDER BY descripcion ASC");
$acudientes = $conn->query("SELECT id, nombre_completo FROM acudiente ORDER BY nombre_completo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
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
    <li class="nav-item active">
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
    <li class="nav-item">
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
    <li class="nav-item">
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
                    <div class="card-header py-3" style="background-color: <?= $editarEstudiante ? '#f6c23e' : '#1cc88a' ?>;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-<?= $editarEstudiante ? 'edit' : 'plus' ?>"></i>
                            <?= $editarEstudiante ? 'Editar Estudiante' : 'Agregar Nuevo Estudiante' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="<?= $editarEstudiante ? 'editar' : 'agregar' ?>">
                            <?php if ($editarEstudiante): ?>
                            <input type="hidden" name="estudianteId" value="<?= $editarEstudiante['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre_completo" class="form-control" 
                                           placeholder="Ej: Juan Pérez García"
                                           value="<?= $editarEstudiante ? htmlspecialchars($editarEstudiante['nombre_completo']) : '' ?>" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Tipo de Documento</label>
                                    <select name="tipo_documento" class="form-control">
                                        <option value="">-- Seleccione --</option>
                                        <option value="TI" <?= ($editarEstudiante && $editarEstudiante['tipo_documento'] == 'TI') ? 'selected' : '' ?>>TI</option>
                                        <option value="CC" <?= ($editarEstudiante && $editarEstudiante['tipo_documento'] == 'CC') ? 'selected' : '' ?>>CC</option>
                                        <option value="CE" <?= ($editarEstudiante && $editarEstudiante['tipo_documento'] == 'CE') ? 'selected' : '' ?>>CE</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Número de Documento <span class="text-danger">*</span></label>
                                    <input type="text" name="numero_documento" class="form-control" 
                                           placeholder="Ej: 1234567890"
                                           value="<?= $editarEstudiante ? htmlspecialchars($editarEstudiante['numero_documento']) : '' ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Fecha de Nacimiento</label>
                                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" 
                                           value="<?= $editarEstudiante ? $editarEstudiante['fecha_nacimiento'] : '' ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Edad</label>
                                    <input type="number" id="edad" name="edad" class="form-control" 
                                           placeholder="Auto"
                                           value="<?= $editarEstudiante ? $editarEstudiante['edad'] : '' ?>" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>EPS</label>
                                    <input type="text" name="eps" class="form-control" 
                                           placeholder="Ej: Sura"
                                           value="<?= $editarEstudiante ? htmlspecialchars($editarEstudiante['eps']) : '' ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Dirección</label>
                                    <input type="text" name="direccion" class="form-control" 
                                           placeholder="Ej: Calle 123 #45-67"
                                           value="<?= $editarEstudiante ? htmlspecialchars($editarEstudiante['direccion']) : '' ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Barrio</label>
                                    <input type="text" name="barrio" class="form-control" 
                                           placeholder="Ej: El Poblado"
                                           value="<?= $editarEstudiante ? htmlspecialchars($editarEstudiante['barrio']) : '' ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Departamento</label>
                                    <select name="id_departamento" id="select_departamento" class="form-control">
                                        <option value="0">-- Seleccione --</option>
                                        <?php 
                                        $departamentos->data_seek(0);
                                        while($dep = $departamentos->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $dep['id'] ?>" <?= ($editarEstudiante && $editarEstudiante['id_departamento'] == $dep['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($dep['nombre']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Ciudad</label>
                                    <select name="id_ciudad" id="select_ciudad" class="form-control">
                                        <option value="0">-- Primero seleccione departamento --</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Grupo</label>
                                    <select name="id_grupo" class="form-control">
                                        <option value="0">-- Seleccione --</option>
                                        <?php 
                                        $grupos->data_seek(0);
                                        while($grp = $grupos->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $grp['id'] ?>" <?= ($editarEstudiante && $editarEstudiante['id_grupo'] == $grp['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($grp['descripcion']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Acudiente</label>
                                    <select name="id_acudiente" class="form-control">
                                        <option value="0">-- Seleccione --</option>
                                        <?php 
                                        $acudientes->data_seek(0);
                                        while($acu = $acudientes->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $acu['id'] ?>" <?= ($editarEstudiante && $editarEstudiante['id_acudiente'] == $acu['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($acu['nombre_completo']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-<?= $editarEstudiante ? 'warning' : 'success' ?>">
                                <i class="fas fa-<?= $editarEstudiante ? 'save' : 'plus' ?>"></i>
                                <?= $editarEstudiante ? 'Actualizar' : 'Guardar' ?>
                            </button>
                            <?php if ($editarEstudiante): ?>
                            <a href="tipo_estudiante.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Estudiantes -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Estudiantes Registrados</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Documento</th>
                                        <th>Edad</th>
                                        <th>Ciudad</th>
                                        <th>Grupo</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($estudiantes && $estudiantes->num_rows > 0): ?>
                                        <?php while ($row = $estudiantes->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($row['tipo_documento']) ?> <?= htmlspecialchars($row['numero_documento']) ?></td>
                                            <td><?= $row['edad'] ?></td>
                                            <td><?= $row['id_ciudad'] ?></td>
                                            <td><?= $row['id_grupo'] ?></td>
                                            <td class="text-center">
                                                <a href="?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar este estudiante?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="estudianteId" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay estudiantes registrados
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="index.php" class="btn btn-secondary mt-3">
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
// Calcular edad automáticamente al cambiar la fecha de nacimiento
document.getElementById('fecha_nacimiento').addEventListener('change', function() {
    const fechaNac = new Date(this.value);
    const hoy = new Date();
    
    if (this.value) {
        let edad = hoy.getFullYear() - fechaNac.getFullYear();
        const mes = hoy.getMonth() - fechaNac.getMonth();
        
        // Ajustar si aún no ha cumplido años este año
        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
            edad--;
        }
        
        document.getElementById('edad').value = edad;
    } else {
        document.getElementById('edad').value = '';
    }
});

// Calcular edad al cargar la página si hay fecha de nacimiento
window.addEventListener('DOMContentLoaded', function() {
    const fechaNacInput = document.getElementById('fecha_nacimiento');
    if (fechaNacInput.value) {
        fechaNacInput.dispatchEvent(new Event('change'));
    }
});

// ============================================
// FILTRO DE CIUDADES POR DEPARTAMENTO
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const selectDepartamento = document.getElementById('select_departamento');
    const selectCiudad = document.getElementById('select_ciudad');
    
    // Función para cargar ciudades según departamento
    function cargarCiudades(idDepartamento, idCiudadSeleccionada = null) {
        if (!idDepartamento || idDepartamento == '0') {
            selectCiudad.innerHTML = '<option value="0">-- Primero seleccione departamento --</option>';
            return;
        }
        
        // Mostrar mensaje de carga
        selectCiudad.innerHTML = '<option value="0">Cargando ciudades...</option>';
        selectCiudad.disabled = true;
        
        // Realizar petición AJAX
        fetch(`get_ciudades.php?id_departamento=${idDepartamento}`)
            .then(response => response.json())
            .then(ciudades => {
                selectCiudad.innerHTML = '<option value="0">-- Seleccione --</option>';
                
                if (ciudades.length > 0) {
                    ciudades.forEach(ciudad => {
                        const option = document.createElement('option');
                        option.value = ciudad.id;
                        option.textContent = ciudad.nombre;
                        
                        // Si hay una ciudad que debe estar seleccionada
                        if (idCiudadSeleccionada && ciudad.id == idCiudadSeleccionada) {
                            option.selected = true;
                        }
                        
                        selectCiudad.appendChild(option);
                    });
                } else {
                    selectCiudad.innerHTML = '<option value="0">No hay ciudades disponibles</option>';
                }
                
                selectCiudad.disabled = false;
            })
            .catch(error => {
                console.error('Error al cargar ciudades:', error);
                selectCiudad.innerHTML = '<option value="0">Error al cargar ciudades</option>';
                selectCiudad.disabled = false;
                alert('Error al cargar las ciudades. Por favor, intente nuevamente.');
            });
    }
    
    // Evento cuando cambia el departamento
    selectDepartamento.addEventListener('change', function() {
        cargarCiudades(this.value);
    });
    
    // Si estamos editando un estudiante, cargar sus ciudades
    <?php if ($editarEstudiante && $editarEstudiante['id_departamento']): ?>
    const idDepartamentoActual = '<?= $editarEstudiante['id_departamento'] ?>';
    const idCiudadActual = '<?= $editarEstudiante['id_ciudad'] ?>';
    
    if (idDepartamentoActual && idDepartamentoActual != '0') {
        cargarCiudades(idDepartamentoActual, idCiudadActual);
    }
    <?php endif; ?>
});
</script>

</body>
</html>
<?php $conn->close(); ?>