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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aspectos Complementarios</title>
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
        <li class="nav-item">
            <a class="nav-link" href="aspecto_academico.php">
                <i class="fas fa-book-open"></i>
                <span>Aspectos Académicos</span>
            </a>
        </li>

        <!-- Aspectos Complementarios -->
        <li class="nav-item active">
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

        <li class="nav-item ">
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

  <!-- Content Wrapper -->
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
      <div class="container-fluid mt-4">
        
        <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>

        <!-- Formulario Agregar -->
        <div class="card shadow mb-4">
          <div class="card-header py-3" style="background-color: #1cc88a;">
            <h6 class="m-0 font-weight-bold text-white">
              <i class="fas fa-plus"></i> Agregar Nuevo Aspecto Complementario
            </h6>
          </div>
          <div class="card-body">
            <form action="procesar_complementario.php" method="POST">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Nombre del Aspecto Complementario <span class="text-danger">*</span></label>
                  <input type="text" name="nombre" class="form-control" required placeholder="Ej: Respeto, Puntualidad, Convivencia...">
                </div>
                <div class="form-group col-md-6">
                  <label>Categoría <span class="text-danger">*</span></label>
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

              <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Agregar
              </button>
            </form>
          </div>
        </div>

        <!-- Tabla -->
        <div class="card shadow mb-4">
          <div class="card-header py-3" style="background-color: #1fbeac;">
            <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Aspectos Complementarios Registrados</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                  <tr>
                    <th width="5%">ID</th>
                    <th width="15%">Nombre</th>
                    <th width="10%">Categoría</th>
                    <th width="8%">Grado</th>
                    <th width="12%">Responsable</th>
                    <th width="8%">Estado</th>
                    <th width="17%">Descripción</th>
                    <th width="17%">Observaciones</th>
                    <th width="8%" class="text-center">Acciones</th>
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
                        <td class="text-center text-nowrap">
                          <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $fila['id'] ?>">
                            <i class="fas fa-edit"></i> Editar
                          </button>
                          <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?= $fila['id'] ?>, '<?= htmlspecialchars($fila['nombre'], ENT_QUOTES) ?>')">
                            <i class="fas fa-trash"></i> Eliminar
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
                    <tr>
                      <td colspan="9" class="text-center text-muted">
                        <i class="fas fa-info-circle"></i> No hay registros
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
function confirmarEliminar(id, nombre) {
  if (confirm('⚠ ¿Estás seguro de eliminar "' + nombre + '"?')) {
    window.location.href = 'procesar_complementario.php?accion=eliminar&id=' + id;
  }
}
</script>

</body>
</html>
<?php $conn->close(); ?>