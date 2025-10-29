<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../inicio.php");
    exit;
}

$nombre = $_SESSION['usuario'];
$foto = isset($_SESSION['foto']) ? $_SESSION['foto'] : 'img/default.png';

// remision.php
$host = 'localhost';
$db   = 'traspasemos';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

$mensaje = '';
$tipo = 'info';
$editando = false;
$remision_editar = null;

// Obtener lista de estudiantes
try {
    $stmt_estudiantes = $pdo->query("
        SELECT 
            d.id, 
            d.nombre_completo, 
            d.numero_documento, 
            d.tipo_documento,
            d.fecha_nacimiento,
            d.edad,
            d.direccion,
            d.barrio,
            d.eps,
            g.descripcion as grupo,
            gr.nombre as grado,
            s.nombre as sede,
            j.nombre as jornada,
            a.nombre_completo as acudiente,
            a.telefono as telefono_acudiente
        FROM datosestud d
        LEFT JOIN grupo g ON d.id_grupo = g.id
        LEFT JOIN grado gr ON g.id_grado = gr.id
        LEFT JOIN sede s ON g.id_sede = s.id
        LEFT JOIN jornada j ON g.id_jornada = j.id
        LEFT JOIN acudiente a ON d.id_acudiente = a.id
        ORDER BY d.nombre_completo ASC
    ");
    $estudiantes = $stmt_estudiantes->fetchAll();
} catch (Exception $e) {
    $estudiantes = [];
    $mensaje = "Error al cargar estudiantes: " . $e->getMessage();
    $tipo = "danger";
}

// PROCESO ELIMINAR
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        $stmt = $pdo->prepare("DELETE FROM remision WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $mensaje = '✅ Remisión eliminada correctamente.';
        $tipo = 'success';
        header("Location: remision.php?mensaje=" . urlencode($mensaje) . "&tipo=" . $tipo);
        exit;
    } catch (Exception $e) {
        $mensaje = '❌ Error al eliminar: ' . $e->getMessage();
        $tipo = 'danger';
    }
}

// CARGAR DATOS PARA EDITAR
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM remision WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $remision_editar = $stmt->fetch();
        if ($remision_editar) {
            $editando = true;
        } else {
            $mensaje = '⚠ No se encontró la remisión.';
            $tipo = 'warning';
        }
    } catch (Exception $e) {
        $mensaje = '❌ Error al cargar remisión: ' . $e->getMessage();
        $tipo = 'danger';
    }
}

// PROCESO FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $fecha_remision = $_POST['fecha_remision'] ?? null;
    $nombre_estudiante = trim($_POST['nombre_estudiante'] ?? '');
    $documento_id = trim($_POST['documento_id'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $edad = intval($_POST['edad'] ?? 0);
    $grado = trim($_POST['grado'] ?? '');
    $jornada = trim($_POST['jornada'] ?? '');
    $sede = trim($_POST['sede'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $eps = trim($_POST['eps'] ?? '');
    $nombre_acudiente = trim($_POST['nombre_acudiente'] ?? '');
    $telefono_acudiente = trim($_POST['telefono_acudiente'] ?? '');
    $docente_remitente = trim($_POST['docente_remitente'] ?? '');
    $motivo_remision = trim($_POST['motivo_remision'] ?? '');

    $disp_percepcion = isset($_POST['disp_percepcion']) ? 1 : 0;
    $disp_atencion = isset($_POST['disp_atencion']) ? 1 : 0;
    $disp_memoria = isset($_POST['disp_memoria']) ? 1 : 0;
    $disp_lenguaje = isset($_POST['disp_lenguaje']) ? 1 : 0;
    $disp_ritmo = isset($_POST['disp_ritmo']) ? 1 : 0;
    $incump_normas = isset($_POST['incump_normas']) ? 1 : 0;
    $impulsivo = isset($_POST['impulsivo']) ? 1 : 0;
    $agresividad = isset($_POST['agresividad']) ? 1 : 0;
    $retraimiento = isset($_POST['retraimiento']) ? 1 : 0;
    $llanto = isset($_POST['llanto']) ? 1 : 0;

    $otro_comportamiento = trim($_POST['otro_comportamiento'] ?? '');
    $comportamientos_problema = trim($_POST['comportamientos_problema'] ?? '');
    $estrategias_pedagogicas = trim($_POST['estrategias_pedagogicas'] ?? '');
    $observaciones_adicionales = trim($_POST['observaciones_adicionales'] ?? '');

    if ($nombre_estudiante === '' || $docente_remitente === '' || $motivo_remision === '') {
        $mensaje = '⚠ Complete: nombre del estudiante, docente remitente y motivo de remisión.';
        $tipo = 'warning';
    } else {
        try {
            if ($id > 0) {
                // ACTUALIZAR
                $sql = "UPDATE remision SET
                    fecha_remision = :fecha_remision, nombre_estudiante = :nombre_estudiante,
                    documento_id = :documento_id, fecha_nacimiento = :fecha_nacimiento, edad = :edad,
                    grado = :grado, jornada = :jornada, sede = :sede, direccion = :direccion, eps = :eps,
                    nombre_acudiente = :nombre_acudiente, telefono_acudiente = :telefono_acudiente,
                    docente_remitente = :docente_remitente, motivo_remision = :motivo_remision,
                    disp_percepcion = :disp_percepcion, disp_atencion = :disp_atencion,
                    disp_memoria = :disp_memoria, disp_lenguaje = :disp_lenguaje, disp_ritmo = :disp_ritmo,
                    incump_normas = :incump_normas, impulsivo = :impulsivo, agresividad = :agresividad,
                    retraimiento = :retraimiento, llanto = :llanto,
                    otro_comportamiento = :otro_comportamiento, comportamientos_problema = :comportamientos_problema,
                    estrategias_pedagogicas = :estrategias_pedagogicas, observaciones_adicionales = :observaciones_adicionales
                WHERE id = :id";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':id' => $id, ':fecha_remision' => $fecha_remision ?: null,
                    ':nombre_estudiante' => $nombre_estudiante, ':documento_id' => $documento_id ?: null,
                    ':fecha_nacimiento' => $fecha_nacimiento ?: null, ':edad' => $edad ?: null,
                    ':grado' => $grado ?: null, ':jornada' => $jornada ?: null, ':sede' => $sede ?: null,
                    ':direccion' => $direccion ?: null, ':eps' => $eps ?: null,
                    ':nombre_acudiente' => $nombre_acudiente ?: null, ':telefono_acudiente' => $telefono_acudiente ?: null,
                    ':docente_remitente' => $docente_remitente, ':motivo_remision' => $motivo_remision,
                    ':disp_percepcion' => $disp_percepcion, ':disp_atencion' => $disp_atencion,
                    ':disp_memoria' => $disp_memoria, ':disp_lenguaje' => $disp_lenguaje, ':disp_ritmo' => $disp_ritmo,
                    ':incump_normas' => $incump_normas, ':impulsivo' => $impulsivo, ':agresividad' => $agresividad,
                    ':retraimiento' => $retraimiento, ':llanto' => $llanto,
                    ':otro_comportamiento' => $otro_comportamiento ?: null,
                    ':comportamientos_problema' => $comportamientos_problema ?: null,
                    ':estrategias_pedagogicas' => $estrategias_pedagogicas ?: null,
                    ':observaciones_adicionales' => $observaciones_adicionales ?: null
                ]);
                $mensaje = '✅ Remisión actualizada correctamente.';
                $tipo = 'success';
            } else {
                // INSERTAR
                $sql = "INSERT INTO remision (
                    fecha_remision, nombre_estudiante, documento_id, fecha_nacimiento, edad, grado, jornada, sede, direccion, eps,
                    nombre_acudiente, telefono_acudiente, docente_remitente, motivo_remision,
                    disp_percepcion, disp_atencion, disp_memoria, disp_lenguaje, disp_ritmo,
                    incump_normas, impulsivo, agresividad, retraimiento, llanto,
                    otro_comportamiento, comportamientos_problema, estrategias_pedagogicas, observaciones_adicionales
                ) VALUES (
                    :fecha_remision, :nombre_estudiante, :documento_id, :fecha_nacimiento, :edad, :grado, :jornada, :sede, :direccion, :eps,
                    :nombre_acudiente, :telefono_acudiente, :docente_remitente, :motivo_remision,
                    :disp_percepcion, :disp_atencion, :disp_memoria, :disp_lenguaje, :disp_ritmo,
                    :incump_normas, :impulsivo, :agresividad, :retraimiento, :llanto,
                    :otro_comportamiento, :comportamientos_problema, :estrategias_pedagogicas, :observaciones_adicionales
                )";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':fecha_remision' => $fecha_remision ?: null, ':nombre_estudiante' => $nombre_estudiante,
                    ':documento_id' => $documento_id ?: null, ':fecha_nacimiento' => $fecha_nacimiento ?: null,
                    ':edad' => $edad ?: null, ':grado' => $grado ?: null, ':jornada' => $jornada ?: null,
                    ':sede' => $sede ?: null, ':direccion' => $direccion ?: null, ':eps' => $eps ?: null,
                    ':nombre_acudiente' => $nombre_acudiente ?: null, ':telefono_acudiente' => $telefono_acudiente ?: null,
                    ':docente_remitente' => $docente_remitente, ':motivo_remision' => $motivo_remision,
                    ':disp_percepcion' => $disp_percepcion, ':disp_atencion' => $disp_atencion,
                    ':disp_memoria' => $disp_memoria, ':disp_lenguaje' => $disp_lenguaje, ':disp_ritmo' => $disp_ritmo,
                    ':incump_normas' => $incump_normas, ':impulsivo' => $impulsivo, ':agresividad' => $agresividad,
                    ':retraimiento' => $retraimiento, ':llanto' => $llanto,
                    ':otro_comportamiento' => $otro_comportamiento ?: null,
                    ':comportamientos_problema' => $comportamientos_problema ?: null,
                    ':estrategias_pedagogicas' => $estrategias_pedagogicas ?: null,
                    ':observaciones_adicionales' => $observaciones_adicionales ?: null
                ]);
                $mensaje = '✅ Remisión guardada correctamente.';
                $tipo = 'success';
            }
            
            header("Location: remision.php?mensaje=" . urlencode($mensaje) . "&tipo=" . $tipo);
            exit;
        } catch (Exception $e) {
            $mensaje = '❌ Error al guardar: ' . $e->getMessage();
            $tipo = 'danger';
        }
    }
}

if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    $tipo = $_GET['tipo'] ?? 'info';
}

// OBTENER REMISIONES
try {
    $stmt = $pdo->query("SELECT * FROM remision ORDER BY fecha_registro DESC LIMIT 200");
    $remisiones = $stmt->fetchAll();
} catch (Exception $e) {
    $remisiones = [];
    $mensaje = '❌ Error al obtener remisiones: ' . $e->getMessage();
    $tipo = 'danger';
}

function obtenerValor($campo, $remision_editar, $post_data = null) {
    if ($remision_editar && isset($remision_editar[$campo])) {
        return $remision_editar[$campo];
    }
    if ($post_data && isset($post_data[$campo])) {
        return $post_data[$campo];
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remisión - TRASPASEMOS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <style>
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body id="page-top">

<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <div class="sidebar-brand-icon">
                <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-width: 100px;">
            </div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i><span>Inicio</span></a></li>
        <hr class="sidebar-divider">
        <li class="nav-item"><a class="nav-link" href="Usuarios.php"><i class="fas fa-users"></i><span>Usuarios</span></a></li>
        <hr class="sidebar-divider">
        <li class="nav-item"><a class="nav-link" href="grado.php"><i class="fas fa-graduation-cap"></i><span>Grado</span></a></li>
        <li class="nav-item"><a class="nav-link" href="Grupo.php"><i class="fas fa-users-cog"></i><span>Grupos</span></a></li>
        <li class="nav-item"><a class="nav-link" href="Sede.php"><i class="fas fa-school"></i><span>Sede</span></a></li>
        <li class="nav-item active"><a class="nav-link" href="remision.php"><i class="fas fa-file-medical"></i><span>Remisiones</span></a></li>
        <li class="nav-item"><a class="nav-link" href="aspecto_academico.php"><i class="fas fa-book-open"></i><span>Aspectos Académicos</span></a></li>
        <li class="nav-item"><a class="nav-link" href="aspecto_complementario.php"><i class="fas fa-puzzle-piece"></i><span>Aspectos Complementarios</span></a></li>
        <li class="nav-item"><a class="nav-link" href="acudiente.php"><i class="fas fa-user-tie"></i><span>Acudiente</span></a></li>
        <li class="nav-item"><a class="nav-link" href="seguimiento.php"><i class="fas fa-clipboard-check"></i><span>Seguimiento</span></a></li>
        <hr class="sidebar-divider">
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfig" aria-expanded="true" aria-controls="collapseConfig">
                <i class="fas fa-cogs"></i><span>Configuración</span>
            </a>
            <div id="collapseConfig" class="collapse" aria-labelledby="headingConfig" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Catálogos:</h6>
                    <a class="collapse-item" href="tipo_documento.php"><i class="fas fa-id-card"></i> Tipo de Documento</a>
                    <a class="collapse-item" href="tipo_usuario.php"><i class="fas fa-user-shield"></i> Tipo de Usuario</a>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider d-none d-md-block">
    </ul>

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
                <div class="alert alert-<?= htmlspecialchars($tipo) ?> alert-dismissible fade show">
                    <?= $mensaje ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php endif; ?>

                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1cc88a;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-file-medical"></i> <?= $editando ? 'Editar Remisión' : 'Formulario de Remisión' ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if ($editando): ?>
                            <input type="hidden" name="id" value="<?= $remision_editar['id'] ?>">
                            <?php endif; ?>

                            <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Datos del Estudiante</h6>
                            
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Fecha de remisión <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_remision" class="form-control" 
                                           value="<?= htmlspecialchars(obtenerValor('fecha_remision', $remision_editar, $_POST) ?: date('Y-m-d')) ?>" required>
                                </div>
                                <div class="form-group col-md-9">
                                    <label>Nombre del estudiante <span class="text-danger">*</span></label>
                                    <select id="select_estudiante" name="nombre_estudiante" class="form-control" required>
                                        <option value="">Seleccione un estudiante...</option>
                                        <?php foreach ($estudiantes as $est): ?>
                                            <option value="<?= htmlspecialchars($est['nombre_completo']) ?>" 
                                                data-documento="<?= htmlspecialchars($est['numero_documento'] ?? '') ?>"
                                                data-tipo-doc="<?= htmlspecialchars($est['tipo_documento'] ?? '') ?>"
                                                data-fecha-nac="<?= htmlspecialchars($est['fecha_nacimiento'] ?? '') ?>"
                                                data-edad="<?= htmlspecialchars($est['edad'] ?? '') ?>"
                                                data-direccion="<?= htmlspecialchars($est['direccion'] ?? '') ?>"
                                                data-eps="<?= htmlspecialchars($est['eps'] ?? '') ?>"
                                                data-grado="<?= htmlspecialchars($est['grado'] ?? '') ?>"
                                                data-sede="<?= htmlspecialchars($est['sede'] ?? '') ?>"
                                                data-jornada="<?= htmlspecialchars($est['jornada'] ?? '') ?>"
                                                data-acudiente="<?= htmlspecialchars($est['acudiente'] ?? '') ?>"
                                                data-tel-acudiente="<?= htmlspecialchars($est['telefono_acudiente'] ?? '') ?>"
                                                <?= ($editando && $remision_editar['nombre_estudiante'] == $est['nombre_completo']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($est['nombre_completo']) ?> - <?= htmlspecialchars($est['tipo_documento'] ?? '') ?> <?= htmlspecialchars($est['numero_documento'] ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Documento</label>
                                    <input type="text" name="documento_id" id="documento_id" class="form-control" 
                                           value="<?= htmlspecialchars(obtenerValor('documento_id', $remision_editar, $_POST)) ?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Fecha de nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" 
                                           value="<?= htmlspecialchars(obtenerValor('fecha_nacimiento', $remision_editar, $_POST)) ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Edad</label>
                                    <input type="number" name="edad" id="edad" class="form-control" placeholder="Auto"
                                           value="<?= htmlspecialchars(obtenerValor('edad', $remision_editar, $_POST)) ?>" readonly>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Grado</label>
                                    <input type="text" name="grado" id="grado" class="form-control" placeholder="Ej: 5°"
                                           value="<?= htmlspecialchars(obtenerValor('grado', $remision_editar, $_POST)) ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label>Jornada</label>
                                    <input type="text" name="jornada" id="jornada" class="form-control" placeholder="Mañana/Tarde"
                                           value="<?= htmlspecialchars(obtenerValor('jornada', $remision_editar, $_POST)) ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Sede</label>
                                    <input type="text" name="sede" id="sede" class="form-control" placeholder="Nombre de la sede"
                                           value="<?= htmlspecialchars(obtenerValor('sede', $remision_editar, $_POST)) ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Dirección</label>
                                    <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Dirección de residencia"
                                           value="<?= htmlspecialchars(obtenerValor('direccion', $remision_editar, $_POST)) ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>EPS</label>
                                    <input type="text" name="eps" id="eps" class="form-control" placeholder="Nombre de la EPS"
                                           value="<?= htmlspecialchars(obtenerValor('eps', $remision_editar, $_POST)) ?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Nombre acudiente</label>
                                    <input type="text" name="nombre_acudiente" id="nombre_acudiente" class="form-control" placeholder="Nombre del acudiente"
                                           value="<?= htmlspecialchars(obtenerValor('nombre_acudiente', $remision_editar, $_POST)) ?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Teléfono acudiente</label>
                                    <input type="text" name="telefono_acudiente" id="telefono_acudiente" class="form-control" placeholder="Número de contacto"
                                           value="<?= htmlspecialchars(obtenerValor('telefono_acudiente', $remision_editar, $_POST)) ?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Docente remitente <span class="text-danger">*</span></label>
                                    <input type="text" name="docente_remitente" class="form-control" placeholder="Nombre del docente"
                                           value="<?= htmlspecialchars(obtenerValor('docente_remitente', $remision_editar, $_POST)) ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Motivo de la remisión <span class="text-danger">*</span></label>
                                <textarea name="motivo_remision" class="form-control" rows="3" 
                                          placeholder="Describa el motivo de la remisión" required><?= htmlspecialchars(obtenerValor('motivo_remision', $remision_editar, $_POST)) ?></textarea>
                            </div>

                            <hr class="my-4">
                            <!-- Posibles Discapacidades / Dificultades -->
                            <h6 class="text-primary mb-3">
                              <i class="fas fa-diagnoses"></i> Posibles discapacidades / dificultades
                            </h6>
                                                                    
                            <div class="form-row">
                              <div class="form-group col-md-2">
                                <div class="custom-control custom-checkbox">
                                  <input class="custom-control-input" type="checkbox" name="disp_percepcion" id="disp_percepcion"
                                         <?= ($editando && $remision_editar['disp_percepcion']) || isset($_POST['disp_percepcion']) ? 'checked' : '' ?>>
                                  <label class="custom-control-label" for="disp_percepcion">Percepción</label>
                                </div>
                              </div>
                                                                    
                              <div class="form-group col-md-2">
                                <div class="custom-control custom-checkbox">
                                  <input class="custom-control-input" type="checkbox" name="disp_atencion" id="disp_atencion"
                                         <?= ($editando && $remision_editar['disp_atencion']) || isset($_POST['disp_atencion']) ? 'checked' : '' ?>>
                                  <label class="custom-control-label" for="disp_atencion">Atención</label>
                                </div>
                              </div>
                                                                    
                              <div class="form-group col-md-2">
                                <div class="custom-control custom-checkbox">
                                  <input class="custom-control-input" type="checkbox" name="disp_memoria" id="disp_memoria"
                                         <?= ($editando && $remision_editar['disp_memoria']) || isset($_POST['disp_memoria']) ? 'checked' : '' ?>>
                                  <label class="custom-control-label" for="disp_memoria">Memoria</label>
                                </div>
                              </div>
                                                                    
                              <div class="form-group col-md-2">
                                <div class="custom-control custom-checkbox">
                                  <input class="custom-control-input" type="checkbox" name="disp_lenguaje" id="disp_lenguaje"
                                         <?= ($editando && $remision_editar['disp_lenguaje']) || isset($_POST['disp_lenguaje']) ? 'checked' : '' ?>>
                                  <label class="custom-control-label" for="disp_lenguaje">Lenguaje</label>
                                </div>
                              </div>
                                                                    
                              <div class="form-group col-md-2">
                                <div class="custom-control custom-checkbox">
                                  <input class="custom-control-input" type="checkbox" name="disp_ritmo" id="disp_ritmo"
                                         <?= ($editando && $remision_editar['disp_ritmo']) || isset($_POST['disp_ritmo']) ? 'checked' : '' ?>>
                                  <label class="custom-control-label" for="disp_ritmo">Ritmo</label>
                                </div>
                              </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-clipboard-list"></i> Comportamientos observados</h6>
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" name="incump_normas" id="incump_normas" 
                                               <?= ($editando && $remision_editar['incump_normas']) || isset($_POST['incump_normas']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="incump_normas">Incumple normas</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" name="impulsivo" id="impulsivo" 
                                               <?= ($editando && $remision_editar['impulsivo']) || isset($_POST['impulsivo']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="impulsivo">Impulsivo</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" name="agresividad" id="agresividad" 
                                               <?= ($editando && $remision_editar['agresividad']) || isset($_POST['agresividad']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="agresividad">Agresividad</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" name="retraimiento" id="retraimiento" 
                                               <?= ($editando && $remision_editar['retraimiento']) || isset($_POST['retraimiento']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="retraimiento">Retraimiento</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" name="llanto" id="llanto" 
                                               <?= ($editando && $remision_editar['llanto']) || isset($_POST['llanto']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="llanto">Llanto</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Otro comportamiento observado</label>
                                <input type="text" name="otro_comportamiento" class="form-control" 
                                       placeholder="Especifique otros comportamientos"
                                       value="<?= htmlspecialchars(obtenerValor('otro_comportamiento', $remision_editar, $_POST)) ?>">
                            </div>

                            <div class="form-group">
                                <label>Comportamientos problema (detalle)</label>
                                <textarea name="comportamientos_problema" class="form-control" rows="2" 
                                          placeholder="Describa detalladamente los comportamientos problemáticos"><?= htmlspecialchars(obtenerValor('comportamientos_problema', $remision_editar, $_POST)) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Estrategias pedagógicas sugeridas</label>
                                <textarea name="estrategias_pedagogicas" class="form-control" rows="2" 
                                          placeholder="Indique las estrategias que se han implementado o se sugieren"><?= htmlspecialchars(obtenerValor('estrategias_pedagogicas', $remision_editar, $_POST)) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Observaciones adicionales</label>
                                <textarea name="observaciones_adicionales" class="form-control" rows="2" 
                                          placeholder="Observaciones o comentarios adicionales"><?= htmlspecialchars(obtenerValor('observaciones_adicionales', $remision_editar, $_POST)) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-plus"></i> <?= $editando ? 'Actualizar Remisión' : 'Guardar Remisión' ?>
                                    </button>
                                    <?php if ($editando): ?>
                                    <a href="remision.php" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de remisiones -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">
                            <i class="fas fa-table"></i> Remisiones Registradas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-primary text-white text-center">
                                    <tr>
                                        <th width="4%">ID</th>
                                        <th width="8%">Fecha</th>
                                        <th width="13%">Estudiante</th>
                                        <th width="9%">Documento</th>
                                        <th width="4%">Edad</th>
                                        <th width="6%">Grado</th>
                                        <th width="7%">Jornada</th>
                                        <th width="9%">Sede</th>
                                        <th width="11%">Docente</th>
                                        <th width="16%">Motivo</th>
                                        <th width="13%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($remisiones)): ?>
                                    <?php foreach ($remisiones as $r): ?>
                                    <tr>
                                        <td class="text-center"><?= htmlspecialchars($r['id']) ?></td>
                                        <td><?= htmlspecialchars($r['fecha_remision']) ?></td>
                                        <td><?= htmlspecialchars($r['nombre_estudiante']) ?></td>
                                        <td><?= htmlspecialchars($r['documento_id']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($r['edad']) ?></td>
                                        <td><?= htmlspecialchars($r['grado']) ?></td>
                                        <td><?= htmlspecialchars($r['jornada']) ?></td>
                                        <td><?= htmlspecialchars($r['sede']) ?></td>
                                        <td><?= htmlspecialchars($r['docente_remitente']) ?></td>
                                        <td><?= nl2br(htmlspecialchars(substr($r['motivo_remision'],0,80))) ?><?= strlen($r['motivo_remision'])>80? '...':'' ?></td>
                                        <td class="text-center">
                                            <a href="remision.php?editar=<?= $r['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="remision.php?eliminar=<?= $r['id'] ?>" class="btn btn-danger btn-sm" title="Eliminar"
                                               onclick="return confirm('¿Está seguro de eliminar esta remisión?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay remisiones registradas
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

<script>
// Autocompletar campos cuando se selecciona un estudiante
document.getElementById('select_estudiante').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    
    if (option.value) {
        document.getElementById('documento_id').value = option.dataset.documento || '';
        document.getElementById('fecha_nacimiento').value = option.dataset.fechaNac || '';
        document.getElementById('edad').value = option.dataset.edad || '';
        document.getElementById('grado').value = option.dataset.grado || '';
        document.getElementById('jornada').value = option.dataset.jornada || '';
        document.getElementById('sede').value = option.dataset.sede || '';
        document.getElementById('direccion').value = option.dataset.direccion || '';
        document.getElementById('eps').value = option.dataset.eps || '';
        document.getElementById('nombre_acudiente').value = option.dataset.acudiente || '';
        document.getElementById('telefono_acudiente').value = option.dataset.telAcudiente || '';
    } else {
        // Limpiar campos
        document.getElementById('documento_id').value = '';
        document.getElementById('fecha_nacimiento').value = '';
        document.getElementById('edad').value = '';
        document.getElementById('grado').value = '';
        document.getElementById('jornada').value = '';
        document.getElementById('sede').value = '';
        document.getElementById('direccion').value = '';
        document.getElementById('eps').value = '';
        document.getElementById('nombre_acudiente').value = '';
        document.getElementById('telefono_acudiente').value = '';
    }
});

// Calcular edad automáticamente cuando se cambia la fecha de nacimiento
document.addEventListener('DOMContentLoaded', function() {
    const fechaNacimientoInput = document.querySelector('input[name="fecha_nacimiento"]');
    const edadInput = document.querySelector('input[name="edad"]');
    
    if (fechaNacimientoInput && edadInput) {
        fechaNacimientoInput.addEventListener('change', function() {
            const fechaNacimiento = new Date(this.value);
            const hoy = new Date();
            
            if (this.value && !isNaN(fechaNacimiento)) {
                let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
                const mes = hoy.getMonth() - fechaNacimiento.getMonth();
                
                if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                    edad--;
                }
                
                edadInput.value = edad >= 0 ? edad : 0;
            } else {
                edadInput.value = '';
            }
        });
    }
});
</script>

</body>
</html>