<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : "";

// Obtener todos los registros
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

    <li class="nav-item active">
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

    <li class="nav-item">
        <a class="nav-link" href="Tipo_Estudiante.php">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span>Tipos de Estudiantes</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
</ul>
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

        <!-- TABLA con botones -->
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
                    <th>Acciones</th>
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
                        <td class="text-nowrap">
                          <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $fila['id'] ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-danger btn-sm ml-1" onclick="confirmarEliminar(<?= $fila['id'] ?>, '<?= htmlspecialchars($fila['nombre'], ENT_QUOTES) ?>')">
                            <i class="fas fa-trash"></i>
                          </button>
                        </td>
                      </tr>

                      <!-- Modal Editar -->
                      <div class="modal fade" id="editModal<?= $fila['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Editar Aspecto Complementario</h5>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form action="procesar_complementario.php" method="POST">
                              <input type="hidden" name="accion" value="editar">
                              <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                              <div class="modal-body">
                                <div class="form-row">
                                  <div class="form-group col-md-6">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($fila['nombre']) ?>" required>
                                  </div>
                                  <div class="form-group col-md-6">
                                    <label>Categoría</label>
                                    <select name="categoria" class="form-control" required>
                                      <option value="Convivencia" <?= $fila['categoria'] == 'Convivencia' ? 'selected' : '' ?>>Convivencia</option>
                                      <option value="Valores" <?= $fila['categoria'] == 'Valores' ? 'selected' : '' ?>>Valores</option>
                                      <option value="Participación" <?= $fila['categoria'] == 'Participación' ? 'selected' : '' ?>>Participación</option>
                                      <option value="Presentación" <?= $fila['categoria'] == 'Presentación' ? 'selected' : '' ?>>Presentación</option>
                                      <option value="Otro" <?= $fila['categoria'] == 'Otro' ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-row">
                                  <div class="form-group col-md-5">
                                    <label>Grado</label>
                                    <input type="text" name="grado" class="form-control" value="<?= htmlspecialchars($fila['grado']) ?>">
                                  </div>
                                  <div class="form-group col-md-4">
                                    <label>Responsable</label>
                                    <input type="text" name="responsable" class="form-control" value="<?= htmlspecialchars($fila['responsable']) ?>">
                                  </div>
                                  <div class="form-group col-md-3">
                                    <label>Estado</label>
                                    <select name="estado" class="form-control">
                                      <option value="Activo" <?= $fila['estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                                      <option value="Inactivo" <?= $fila['estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label>Descripción</label>
                                  <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($fila['descripcion']) ?></textarea>
                                </div>
                                <div class="form-group">
                                  <label>Observaciones</label>
                                  <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($fila['observaciones']) ?></textarea>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="9" class="text-center text-muted">No hay registros</td></tr>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmarEliminar(id, nombre) {
  if (confirm('¿Eliminar "' + nombre + '"?')) {
    window.location.href = 'procesar_complementario.php?accion=eliminar&id=' + id;
  }
}
</script>

</body>
</html>
<?php $conn->close(); ?>