<?php
// 🔹 Conexión directa a la base de datos
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : "";

// 🔹 Obtener todos los registros
$sql = "SELECT * FROM aspectos_complementarios ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Aspectos Complementarios</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
  <!-- Sidebar -->
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
          <div class="sidebar-brand-icon">
              <img src="img/logo.png" alt="">
          </div>
      </a>

      <hr class="sidebar-divider my-0">
      <li class="nav-item active">
          <a class="nav-link" href="index.html">
              <i class="fas fa-fw fa-tachometer-alt"></i>
              <span>TRASPASEMOS</span></a>
      </li>
      <hr class="sidebar-divider">

      <li class="nav-item">
          <a class="nav-link" href="Usuarios.php">
              <i class="fas fa-fw fa-table"></i>
              <span>Usuarios</span></a>
      </li>

      <li class="nav-item">
          <a class="nav-link" href="charts.html">
              <i class="fas fa-fw fa-chart-area"></i>
              <span>Charts</span></a>
      </li>            
      <li class="nav-item">
          <a class="nav-link" href="usuarios.php">
              <i class="fa-solid fa-user"></i>
              <span>Usuarios</span></a>
      </li>
      <li class="nav-item">
          <a class="nav-link" href="usuarios.php">
              <i class="fa-solid fa-flag-checkered"></i>
              <span>Reportes</span></a>
      </li>
      <hr class="sidebar-divider d-none d-md-block">

      <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
              aria-expanded="true" aria-controls="collapseUtilities">
              <i class="fas fa-fw fa-wrench"></i>
              <span>Configuración</span>
          </a>
          <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
              data-parent="#accordionSidebar">
              <div class="bg-white py-2 collapse-inner rounded">
                  <a class="collapse-item" href="TipoDocumento.html">Tipo Documento</a>
                  <a class="collapse-item" href="TipoIdentifica.html">Tipo Usuario</a>
                  <a class="collapse-item" href="grado.php">Grado</a>
                  <a class="collapse-item" href="Sedes.html">Sede</a>
                  <a class="collapse-item" href="Grupo.php">Grupos</a>
                  <a class="collapse-item" href="ascp_complt.php">Aspectos complementarios</a>
                  <a class="collapse-item" href="ascp_aca.php">Aspectos académicos</a>
                  <a class="collapse-item" href="Tipo_usuario.html">Tipos de usuarios</a>
                  <a class="collapse-item" href="Tipo_Estudiante.html">Tipos de estudiantes</a>
              </div>
          </div>
      </li>

      <hr class="sidebar-divider">
  </ul>
  <!-- End Sidebar -->

  <!-- Content -->
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content" class="p-4">

      <div class="container-fluid">
        <div class="card shadow">
          <div class="card-header bg-success text-white text-center">
            <h4><i class="fas fa-user-check"></i> Registro de Aspectos Complementarios</h4>
          </div>
          <div class="card-body">

            <?php if ($mensaje): ?>
              <div class="alert alert-success text-center"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <!-- FORMULARIO -->
            <form action="procesar_complementario.php" method="POST">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Nombre del Aspecto Complementario</label>
                  <input type="text" name="nombre" class="form-control" required placeholder="Ej: Respeto, Puntualidad, Convivencia...">
                </div>
                <div class="form-group col-md-6">
                  <label>Categoría</label>
                  <select name="categoria" class="form-control" required>
                    <option value="">Seleccione...</option>
                    <option value="Convivencia">Convivencia</option>
                    <option value="Valores">Valores</option>
                    <option value="Participación">Participación</option>
                    <option value="Presentación">Presentación</option>
                    <option value="Otro">Otro</option>
                  </select>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-5">
                  <label>Grado Asignado</label>
                  <input type="text" name="grado" class="form-control" placeholder="Ej: 9°, 10°, 11°...">
                </div>
                <div class="form-group col-md-4">
                  <label>Responsable</label>
                  <input type="text" name="responsable" class="form-control" placeholder="Ej: Coordinador de convivencia">
                </div>
                <div class="form-group col-md-3">
                  <label>Estado</label>
                  <select name="estado" class="form-control">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" placeholder="Ej: Evalúa el respeto hacia compañeros y docentes..."></textarea>
              </div>

              <div class="form-group">
                <label>Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="3" placeholder="Observaciones adicionales o recomendaciones..."></textarea>
              </div>

              <div class="text-center mt-4">
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> Guardar</button>
              </div>
            </form>
          </div>
        </div>

        <!-- TABLA -->
        <div class="card shadow mt-4">
          <div class="card-header bg-primary text-white text-center">
            <h5><i class="fas fa-list"></i> Aspectos Complementarios Registrados</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead class="bg-info text-white">
                  <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Grado</th>
                    <th>Responsable</th>
                    <th>Estado</th>
                    <th>Descripción</th>
                    <th>Observaciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($fila = $result->fetch_assoc()): ?>
                      <tr>
                        <td><?= $fila['id'] ?></td>
                        <td><?= htmlspecialchars($fila['nombre']) ?></td>
                        <td><?= htmlspecialchars($fila['categoria']) ?></td>
                        <td><?= htmlspecialchars($fila['grado']) ?></td>
                        <td><?= htmlspecialchars($fila['responsable']) ?></td>
                        <td><?= htmlspecialchars($fila['estado']) ?></td>
                        <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                        <td><?= htmlspecialchars($fila['observaciones']) ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted">No hay registros</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
