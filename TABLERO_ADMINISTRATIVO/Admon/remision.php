<?php
// remision.php
// Usa PDO para insertar y mostrar remisiones (tabla 'remision').

// ---- CONFIG ----
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

// ---- PROCESO FORMULARIO (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // recoger campos (usar null coalescing)
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

    // checkboxes -> boolean 0/1
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

    // Validación mínima (puedes ampliar)
    if ($nombre_estudiante === '' || $docente_remitente === '' || $motivo_remision === '') {
        $mensaje = '⚠ Complete al menos: nombre del estudiante, docente remitente y motivo de remisión.';
        $tipo = 'warning';
    } else {
        // Insert usando PDO con named params
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

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':fecha_remision' => $fecha_remision ?: null,
                ':nombre_estudiante' => $nombre_estudiante,
                ':documento_id' => $documento_id ?: null,
                ':fecha_nacimiento' => $fecha_nacimiento ?: null,
                ':edad' => $edad ?: null,
                ':grado' => $grado ?: null,
                ':jornada' => $jornada ?: null,
                ':sede' => $sede ?: null,
                ':direccion' => $direccion ?: null,
                ':eps' => $eps ?: null,
                ':nombre_acudiente' => $nombre_acudiente ?: null,
                ':telefono_acudiente' => $telefono_acudiente ?: null,
                ':docente_remitente' => $docente_remitente,
                ':motivo_remision' => $motivo_remision,
                ':disp_percepcion' => $disp_percepcion,
                ':disp_atencion' => $disp_atencion,
                ':disp_memoria' => $disp_memoria,
                ':disp_lenguaje' => $disp_lenguaje,
                ':disp_ritmo' => $disp_ritmo,
                ':incump_normas' => $incump_normas,
                ':impulsivo' => $impulsivo,
                ':agresividad' => $agresividad,
                ':retraimiento' => $retraimiento,
                ':llanto' => $llanto,
                ':otro_comportamiento' => $otro_comportamiento ?: null,
                ':comportamientos_problema' => $comportamientos_problema ?: null,
                ':estrategias_pedagogicas' => $estrategias_pedagogicas ?: null,
                ':observaciones_adicionales' => $observaciones_adicionales ?: null
            ]);
            $mensaje = '✅ Remisión guardada correctamente.';
            $tipo = 'success';
            // limpiar POST para que el formulario quede vacío:
            $_POST = [];
        } catch (Exception $e) {
            $mensaje = '❌ Error al guardar: ' . $e->getMessage();
            $tipo = 'danger';
        }
    }
}

// ---- OBTENER LISTADO DE REMISIONES ----
try {
    $stmt = $pdo->query("SELECT * FROM remision ORDER BY fecha_registro DESC LIMIT 200");
    $remisiones = $stmt->fetchAll();
} catch (Exception $e) {
    $remisiones = [];
    $mensaje = '❌ Error al obtener remisiones: ' . $e->getMessage();
    $tipo = 'danger';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Remisión - TRASPASEMOS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">
  <!-- Sidebar -->
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
          <div class="sidebar-brand-icon">
              <img src="img/logo.png" alt="" style="max-width:100px;">
          </div>
      </a>
      <hr class="sidebar-divider my-0">
      <li class="nav-item active">
          <a class="nav-link" href="index.html">
              <i class="fas fa-fw fa-tachometer-alt"></i>
              <span>TRASPASEMOS</span></a>
      </li>
      <hr class="sidebar-divider">
      <li class="nav-item"><a class="nav-link" href="Usuarios.php"><i class="fas fa-fw fa-table"></i><span>Usuarios</span></a></li>
      <li class="nav-item"><a class="nav-link" href="charts.html"><i class="fas fa-fw fa-chart-area"></i><span>Charts</span></a></li>
      <li class="nav-item"><a class="nav-link" href="usuarios.php"><i class="fa-solid fa-user"></i><span>Usuarios</span></a></li>
      <li class="nav-item"><a class="nav-link" href="usuarios.php"><i class="fa-solid fa-flag-checkered"></i><span>Reportes</span></a></li>
      <hr class="sidebar-divider d-none d-md-block">
      <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
              <i class="fas fa-fw fa-wrench"></i>
              <span>Configuración</span>
          </a>
          <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
              <div class="bg-white py-2 collapse-inner rounded">
                  <a class="collapse-item" href="TipoDocumento.html">Tipo Documento</a>
                  <a class="collapse-item" href="TipoIdentifica.html">Tipo Usuario</a>
                  <a class="collapse-item" href="grado.php">Grado</a>
                  <a class="collapse-item" href="Sede.php">Sede</a>
                  <a class="collapse-item" href="Grupo.php">Grupos</a>
                  <a class="collapse-item" href="aspecto_complementario.php">Aspectos complementarios</a>
                  <a class="collapse-item" href="aspecto_academico.php">Aspectos académicos</a>
                  <a class="collapse-item" href="Tipo_usuario.php">Tipos de usuarios</a>
                  <a class="collapse-item" href="Tipo_Estudiante.php">Tipos de estudiantes</a>
              </div>
          </div>
      </li>
      <hr class="sidebar-divider">
  </ul>
  <!-- End Sidebar -->

  <!-- Content Wrapper -->
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content" class="p-4">

      <?php if ($mensaje): ?>
        <div class="alert alert-<?= htmlspecialchars($tipo) ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($mensaje) ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php endif; ?>

      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="m-0">Formulario de Remisión</h5>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <div class="form-row">
              <div class="form-group col-md-3">
                <label>Fecha de remisión</label>
                <input type="date" name="fecha_remision" class="form-control" value="<?= htmlspecialchars($_POST['fecha_remision'] ?? date('Y-m-d')) ?>">
              </div>
              <div class="form-group col-md-5">
                <label>Nombre del estudiante *</label>
                <input type="text" name="nombre_estudiante" class="form-control" value="<?= htmlspecialchars($_POST['nombre_estudiante'] ?? '') ?>" required>
              </div>
              <div class="form-group col-md-4">
                <label>Documento</label>
                <input type="text" name="documento_id" class="form-control" value="<?= htmlspecialchars($_POST['documento_id'] ?? '') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-3">
                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($_POST['fecha_nacimiento'] ?? '') ?>">
              </div>
              <div class="form-group col-md-1">
                <label>Edad</label>
                <input type="number" name="edad" class="form-control" min="0" value="<?= htmlspecialchars($_POST['edad'] ?? '') ?>">
              </div>
              <div class="form-group col-md-2">
                <label>Grado</label>
                <input type="text" name="grado" class="form-control" value="<?= htmlspecialchars($_POST['grado'] ?? '') ?>">
              </div>
              <div class="form-group col-md-2">
                <label>Jornada</label>
                <input type="text" name="jornada" class="form-control" value="<?= htmlspecialchars($_POST['jornada'] ?? '') ?>">
              </div>
              <div class="form-group col-md-4">
                <label>Sede</label>
                <input type="text" name="sede" class="form-control" value="<?= htmlspecialchars($_POST['sede'] ?? '') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Dirección</label>
                <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
              </div>
              <div class="form-group col-md-3">
                <label>EPS</label>
                <input type="text" name="eps" class="form-control" value="<?= htmlspecialchars($_POST['eps'] ?? '') ?>">
              </div>
              <div class="form-group col-md-3">
                <label>Nombre acudiente</label>
                <input type="text" name="nombre_acudiente" class="form-control" value="<?= htmlspecialchars($_POST['nombre_acudiente'] ?? '') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Teléfono acudiente</label>
                <input type="text" name="telefono_acudiente" class="form-control" value="<?= htmlspecialchars($_POST['telefono_acudiente'] ?? '') ?>">
              </div>
              <div class="form-group col-md-6">
                <label>Docente remitente *</label>
                <input type="text" name="docente_remitente" class="form-control" value="<?= htmlspecialchars($_POST['docente_remitente'] ?? '') ?>" required>
              </div>
              <div class="form-group col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-success btn-block">Guardar</button>
              </div>
            </div>

            <div class="form-group">
              <label>Motivo de la remisión *</label>
              <textarea name="motivo_remision" class="form-control" rows="3" required><?= htmlspecialchars($_POST['motivo_remision'] ?? '') ?></textarea>
            </div>

            <hr>
            <h6>Posibles discapacidades / dificultades (marque si aplica)</h6>
            <div class="form-row">
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="disp_percepcion" id="disp_percepcion" <?= isset($_POST['disp_percepcion']) ? 'checked' : '' ?>><label class="form-check-label" for="disp_percepcion">Percepción</label></div></div>
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="disp_atencion" id="disp_atencion" <?= isset($_POST['disp_atencion']) ? 'checked' : '' ?>><label class="form-check-label" for="disp_atencion">Atención</label></div></div>
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="disp_memoria" id="disp_memoria" <?= isset($_POST['disp_memoria']) ? 'checked' : '' ?>><label class="form-check-label" for="disp_memoria">Memoria</label></div></div>
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="disp_lenguaje" id="disp_lenguaje" <?= isset($_POST['disp_lenguaje']) ? 'checked' : '' ?>><label class="form-check-label" for="disp_lenguaje">Lenguaje</label></div></div>
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="disp_ritmo" id="disp_ritmo" <?= isset($_POST['disp_ritmo']) ? 'checked' : '' ?>><label class="form-check-label" for="disp_ritmo">Ritmo</label></div></div>
            </div>

            <hr>
            <h6>Comportamientos observados (marque si aplica)</h6>
            <div class="form-row">
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="incump_normas" id="incump_normas" <?= isset($_POST['incump_normas']) ? 'checked' : '' ?>><label class="form-check-label" for="incump_normas">Incumple normas</label></div></div>
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="impulsivo" id="impulsivo" <?= isset($_POST['impulsivo']) ? 'checked' : '' ?>><label class="form-check-label" for="impulsivo">Impulsivo</label></div></div>
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="agresividad" id="agresividad" <?= isset($_POST['agresividad']) ? 'checked' : '' ?>><label class="form-check-label" for="agresividad">Agresividad</label></div></div>
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="retraimiento" id="retraimiento" <?= isset($_POST['retraimiento']) ? 'checked' : '' ?>><label class="form-check-label" for="retraimiento">Retraimiento</label></div></div>
              <div class="form-group col-md-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="llanto" id="llanto" <?= isset($_POST['llanto']) ? 'checked' : '' ?>><label class="form-check-label" for="llanto">Llanto</label></div></div>
            </div>

            <div class="form-group">
              <label>Otro comportamiento observado</label>
              <input type="text" name="otro_comportamiento" class="form-control" value="<?= htmlspecialchars($_POST['otro_comportamiento'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label>Comportamientos problema (detalle)</label>
              <textarea name="comportamientos_problema" class="form-control" rows="2"><?= htmlspecialchars($_POST['comportamientos_problema'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
              <label>Estrategias pedagógicas sugeridas</label>
              <textarea name="estrategias_pedagogicas" class="form-control" rows="2"><?= htmlspecialchars($_POST['estrategias_pedagogicas'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
              <label>Observaciones adicionales</label>
              <textarea name="observaciones_adicionales" class="form-control" rows="2"><?= htmlspecialchars($_POST['observaciones_adicionales'] ?? '') ?></textarea>
            </div>

          </form>
        </div>
      </div>

      <!-- Tabla con remisiones registradas -->
      <div class="card shadow">
        <div class="card-header bg-info text-white">
          <h6 class="m-0">Remisiones Registradas</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead class="bg-primary text-white text-center">
                <tr>
                  <th>ID</th>
                  <th>Fecha</th>
                  <th>Estudiante</th>
                  <th>Documento</th>
                  <th>Edad</th>
                  <th>Grado</th>
                  <th>Jornada</th>
                  <th>Sede</th>
                  <th>Docente</th>
                  <th>Motivo</th>
                  <th>Fecha registro</th>
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
                    <td><?= htmlspecialchars($r['fecha_registro']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="11" class="text-center text-muted">No hay remisiones registradas.</td></tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

    <footer class="sticky-footer bg-light mt-4">
      <div class="container my-auto">
        <div class="copyright text-center my-auto">
          <span>Copyright &copy; TRASPASEMOS 2025</span>
        </div>
      </div>
    </footer>

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
