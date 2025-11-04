<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: /traspasemos_git/Proyecto-NEE/traspasemos/Vista/inicio.php");
    exit;
}

$nombre = $_SESSION['usuario'];
$foto = isset($_SESSION['foto']) ? $_SESSION['foto'] : 'img/default.png';

// Configuración de base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db   = "traspasemos";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensaje = "";
$tipoMensaje = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = intval($_POST['usuarioId'] ?? 0);

    // AGREGAR NUEVO USUARIO
    if ($accion === 'agregar') {
        // Validar que todos los campos requeridos existan
        $campos_requeridos = ['tipo_usuario', 'tipo_documento', 'identificacion', 'nombre_completo', 'correo', 'password'];
        $campos_faltantes = [];
        
        foreach ($campos_requeridos as $campo) {
            if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                $campos_faltantes[] = $campo;
            }
        }
        
        if (!empty($campos_faltantes)) {
            $mensaje = "❌ Error: Faltan campos requeridos: " . implode(', ', $campos_faltantes);
            $tipoMensaje = "danger";
        } else {
            // Capturar y sanitizar datos del formulario
            $tipo_usuario = trim($_POST['tipo_usuario']);
            $tipo_documento = trim($_POST['tipo_documento']);
            $identificacion = trim($_POST['identificacion']);
            $nombre_completo = trim($_POST['nombre_completo']);
            $correo = trim($_POST['correo']);
            $celular = trim($_POST['celular'] ?? '');
            $clave = $_POST['password'];
            
            // Validaciones adicionales
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $mensaje = "❌ Error: El correo electrónico no es válido";
                $tipoMensaje = "danger";
            } elseif (strlen($clave) < 6) {
                $mensaje = "❌ Error: La contraseña debe tener al menos 6 caracteres";
                $tipoMensaje = "danger";
            } else {
                // Verificar si el correo ya existe
                $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
                $stmt_check->bind_param("s", $correo);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                
                if ($result_check->num_rows > 0) {
                    $mensaje = "❌ Error: El correo electrónico ya está registrado";
                    $tipoMensaje = "danger";
                    $stmt_check->close();
                } else {
                    $stmt_check->close();
                    
                    // Verificar si la identificación ya existe
                    $stmt_check_id = $conn->prepare("SELECT id FROM usuarios WHERE identificacion = ?");
                    $stmt_check_id->bind_param("s", $identificacion);
                    $stmt_check_id->execute();
                    $result_check_id = $stmt_check_id->get_result();
                    
                    if ($result_check_id->num_rows > 0) {
                        $mensaje = "❌ Error: El número de identificación ya está registrado";
                        $tipoMensaje = "danger";
                        $stmt_check_id->close();
                    } else {
                        $stmt_check_id->close();
                        
                        // Encriptar la contraseña antes de guardarla
                        $clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);
                        
                        // Usar prepared statements para prevenir SQL Injection
                        $stmt = $conn->prepare("INSERT INTO usuarios (tipo_usuario, tipo_documento, identificacion, nombre_completo, correo, celular, clave) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        
                        if ($stmt === false) {
                            $mensaje = "❌ Error al preparar la consulta: " . $conn->error;
                            $tipoMensaje = "danger";
                        } else {
                            $stmt->bind_param("sssssss", $tipo_usuario, $tipo_documento, $identificacion, $nombre_completo, $correo, $celular, $clave_encriptada);
                            
                            if ($stmt->execute()) {
                                $usuario_id = $stmt->insert_id;
                                $mensaje = "✅ Usuario agregado correctamente (ID: $usuario_id)";
                                $tipoMensaje = "success";
                            } else {
                                $mensaje = "❌ Error al agregar el usuario: " . $stmt->error;
                                $tipoMensaje = "danger";
                            }
                            
                            $stmt->close();
                        }
                    }
                }
            }
        }
    }
    
    // ELIMINAR USUARIO
    elseif ($accion === 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "🗑️ Usuario eliminado correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "❌ Error al eliminar: " . $conn->error;
            $tipoMensaje = "danger";
        }
        $stmt->close();
    }
}

// Mensajes de sesión
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipoMensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'success';
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}

// Consultar usuarios
$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Usuarios - TRASPASEMOS</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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
    <li class="nav-item active">
        <a class="nav-link" href="Usuarios.php">
            <i class="fas fa-users"></i>
            <span>Usuarios</span>
        </a>
    </li>

    <!-- Tipo de Usuario -->
    <li class="nav-item">
        <a class="nav-link" href="tipo_usuario.php">
            <i class="fas fa-user-shield"></i>
            <span>Tipo de Usuario</span>
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
                         src="<?php echo isset($_SESSION['foto']) && $_SESSION['foto'] != '' 
                                  ? htmlspecialchars($_SESSION['foto']) 
                                  : 'img/default.png'; ?>"
                         alt="Foto de perfil"
                         style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #ddd;">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                        <?php echo isset($_SESSION['usuario']) 
                                ? htmlspecialchars($_SESSION['usuario']) 
                                : 'Usuario'; ?>
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
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Cerrar Sesión
                </a>
            </div>
        </li>
    </ul>
</nav>

            <!-- Page Content -->
            <div class="container-fluid">
                
                <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>

                <h1 class="h3 mb-4 text-primary">
                    <i class="fas fa-users"></i> Gestión de Usuarios
                </h1>

                <!-- Tabla de Usuarios -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white">Lista de Usuarios</h6>
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalAgregarUsuario">
                            <i class="fas fa-plus"></i> Agregar Usuario
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Tipo Usuario</th>
                                        <th class="text-center">Tipo Doc.</th>
                                        <th class="text-center">Identificación</th>
                                        <th class="text-center">Nombre Completo</th>
                                        <th class="text-center">Correo</th>
                                        <th class="text-center">Celular</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= htmlspecialchars($row['id']) ?></td>
                                            <td><?= htmlspecialchars($row['tipo_usuario']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['tipo_documento']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['identificacion']) ?></td>
                                            <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($row['correo']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['celular']) ?></td>
                                            <td class="text-center">
                                               <button 
                                                   type="button"
                                                    class="btn btn-warning btn-sm btnEditarUsuario"
                                                      data-id="<?= $row['id'] ?>"
                                                         data-tipo_usuario="<?= htmlspecialchars($row['tipo_usuario']) ?>"
                                                          data-tipo_documento="<?= htmlspecialchars($row['tipo_documento']) ?>"
                                                            data-identificacion="<?= htmlspecialchars($row['identificacion']) ?>"
                                                           data-nombre="<?= htmlspecialchars($row['nombre_completo']) ?>"
                                                          data-correo="<?= htmlspecialchars($row['correo']) ?>"
                                                          data-celular="<?= htmlspecialchars($row['celular']) ?>"
                                                              >
                                                          <i class="fas fa-edit"></i>
                                                          </button>

                                                <form method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('⚠ ¿Estás seguro de eliminar este usuario?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="usuarioId" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No hay usuarios registrados
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-light">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; TRASPASEMOS <?= date('Y') ?></span>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Scroll to Top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">¿Cerrar Sesión?</h5>
                <button class="close" type="button" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">Seleccione "Salir" para cerrar su sesión actual.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" href="logout.php">Salir</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Usuario -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <input type="hidden" name="accion" value="agregar">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Agregar Nuevo Usuario</h5>
                <button class="close text-white" type="button" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo Usuario <span class="text-danger">*</span></label>
                            <select name="tipo_usuario" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Docente">Docente</option>
                                <option value="Estudiante">Estudiante</option>
                                <option value="Acudiente">Acudiente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo Documento <span class="text-danger">*</span></label>
                            <select name="tipo_documento" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="TI">Tarjeta de Identidad</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="RC">Registro Civil</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Identificación <span class="text-danger">*</span></label>
                            <input type="text" name="identificacion" class="form-control" required 
                                   pattern="[0-9]+" title="Solo números" placeholder="Ej: 1234567890">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Celular</label>
                            <input type="tel" name="celular" class="form-control" 
                                   pattern="[0-9]{10}" title="10 dígitos" placeholder="Ej: 3001234567">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nombre Completo <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_completo" class="form-control" required 
                           placeholder="Ej: Juan Pérez García">
                </div>

                <div class="form-group">
                    <label>Correo Electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="correo" class="form-control" required 
                           placeholder="Ej: usuario@example.com">
                </div>

                <div class="form-group">
                    <label>Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="6"
                           title="Mínimo 6 caracteres" placeholder="Mínimo 6 caracteres">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="btn btn-success" type="submit">
                    <i class="fas fa-save"></i> Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form action="editar_usuario.php" method="POST" class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title"><i class="fas fa-user-edit"></i> Editar Usuario</h5>
        <button class="close text-white" type="button" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="editId">

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Tipo Usuario</label>
              <select name="tipo_usuario" id="editTipoUsuario" class="form-control" required>
                <option value="">Seleccione...</option>
                <option value="Administrador">Administrador</option>
                <option value="Docente">Docente</option>
                <option value="Estudiante">Estudiante</option>
                <option value="Acudiente">Acudiente</option>
              </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Tipo Documento</label>
              <select name="tipo_documento" id="editTipoDocumento" class="form-control" required>
                <option value="">Seleccione...</option>
                <option value="CC">Cédula de Ciudadanía</option>
                <option value="TI">Tarjeta de Identidad</option>
                <option value="CE">Cédula de Extranjería</option>
                <option value="RC">Registro Civil</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Identificación</label>
              <input type="text" name="identificacion" id="editIdentificacion" class="form-control" required>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Celular</label>
              <input type="text" name="celular" id="editCelular" class="form-control">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Nombre Completo</label>
          <input type="text" name="nombre_completo" id="editNombreCompleto" class="form-control" required>
        </div>

        <div class="form-group">
          <label>Correo Electrónico</label>
          <input type="email" name="correo" id="editCorreo" class="form-control" required>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-warning" type="submit"><i class="fas fa-save"></i> Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="js/demo/datatables-demo.js"></script>
<script>
$(document).ready(function() {
  $('.btnEditarUsuario').on('click', function() {
    const usuario = $(this).data();

    // Llenar los campos del formulario
    $('#editId').val(usuario.id);
    $('#editTipoUsuario').val(usuario.tipo_usuario);
    $('#editTipoDocumento').val(usuario.tipo_documento);
    $('#editIdentificacion').val(usuario.identificacion);
    $('#editNombreCompleto').val(usuario.nombre);
    $('#editCorreo').val(usuario.correo);
    $('#editCelular').val(usuario.celular);

    // Mostrar el modal
    $('#modalEditarUsuario').modal('show');
  });
});
</script>

</body>
</html>

<?php $conn->close(); ?>