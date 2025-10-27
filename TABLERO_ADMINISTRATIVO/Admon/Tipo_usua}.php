<?php
/**
 * Gestión de Tipos de Usuarios - Archivo Unificado
 * Maneja tanto la vista como las operaciones CRUD
 */

// Configuración de errores (comentar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$database = "traspasemos";

// Si es una petición AJAX, procesar y devolver JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    // Conectar a la base de datos
    $conn = new mysqli($host, $user, $pass, $database);
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
        exit;
    }
    $conn->set_charset("utf8mb4");
    
    // Función para limpiar datos
    function limpiarDato($dato) {
        return trim(htmlspecialchars($dato, ENT_QUOTES, 'UTF-8'));
    }
    
    // Función para enviar respuestas JSON
    function enviarRespuesta($success, $message, $data = null) {
        $response = ['success' => $success, 'message' => $message];
        if ($data !== null) $response['data'] = $data;
        echo json_encode($response);
    }
    
    $accion = limpiarDato($_POST['accion']);
    
    // Procesar según la acción
    switch ($accion) {
        case 'listar':
            $sql = "SELECT IdTipoUsuario as id, Descripcion as descripcion FROM tipousuario ORDER BY IdTipoUsuario ASC";
            $result = $conn->query($sql);
            
            $tipos = [];
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $tipos[] = $row;
                }
            }
            enviarRespuesta(true, "Tipos de usuarios cargados correctamente.", $tipos);
            break;
        
        case 'agregar':
            $id = isset($_POST['tipoUsuarioId']) ? intval($_POST['tipoUsuarioId']) : 0;
            $descripcion = isset($_POST['descripcionTipoUsuario']) ? limpiarDato($_POST['descripcionTipoUsuario']) : '';
            
            if ($id <= 0) {
                enviarRespuesta(false, 'El ID debe ser un número mayor a 0');
                break;
            }
            
            if (empty($descripcion)) {
                enviarRespuesta(false, 'La descripción es obligatoria');
                break;
            }
            
            if (strlen($descripcion) > 100) {
                enviarRespuesta(false, 'La descripción no puede exceder los 100 caracteres');
                break;
            }
            
            // Verificar si el ID ya existe
            $stmt_check = $conn->prepare("SELECT IdTipoUsuario FROM tipousuario WHERE IdTipoUsuario = ?");
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                enviarRespuesta(false, 'Ya existe un tipo de usuario con ese ID');
                $stmt_check->close();
                break;
            }
            $stmt_check->close();
            
            // Insertar
            $stmt = $conn->prepare("INSERT INTO tipousuario (IdTipoUsuario, Descripcion) VALUES (?, ?)");
            $stmt->bind_param("is", $id, $descripcion);
            
            if ($stmt->execute()) {
                enviarRespuesta(true, 'Tipo de usuario agregado correctamente', ['id' => $id]);
            } else {
                enviarRespuesta(false, 'Error al agregar: ' . $conn->error);
            }
            $stmt->close();
            break;
        
        case 'editar':
            $id = isset($_POST['tipoUsuarioId']) ? intval($_POST['tipoUsuarioId']) : 0;
            $descripcion = isset($_POST['descripcionTipoUsuario']) ? limpiarDato($_POST['descripcionTipoUsuario']) : '';
            
            if ($id <= 0) {
                enviarRespuesta(false, 'ID no válido');
                break;
            }
            
            if (empty($descripcion)) {
                enviarRespuesta(false, 'La descripción es obligatoria');
                break;
            }
            
            if (strlen($descripcion) > 100) {
                enviarRespuesta(false, 'La descripción no puede exceder los 100 caracteres');
                break;
            }
            
            // Actualizar
            $stmt = $conn->prepare("UPDATE tipousuario SET Descripcion = ? WHERE IdTipoUsuario = ?");
            $stmt->bind_param("si", $descripcion, $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    enviarRespuesta(true, 'Tipo de usuario actualizado correctamente');
                } else {
                    enviarRespuesta(true, 'No se realizaron cambios (los datos son iguales)');
                }
            } else {
                enviarRespuesta(false, 'Error al actualizar: ' . $conn->error);
            }
            $stmt->close();
            break;
        
        case 'eliminar':
            $id = isset($_POST['tipoUsuarioId']) ? intval($_POST['tipoUsuarioId']) : 0;
            
            if ($id <= 0) {
                enviarRespuesta(false, 'ID no válido');
                break;
            }
            
            // Verificar si hay usuarios asociados
            $stmt_check = $conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE id_tipo_usuario = ?");
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $row_check = $result_check->fetch_assoc();
            $stmt_check->close();
            
            if ($row_check['total'] > 0) {
                enviarRespuesta(false, 'No se puede eliminar porque hay ' . $row_check['total'] . ' usuario(s) asociado(s) a este tipo');
                break;
            }
            
            // Eliminar
            $stmt = $conn->prepare("DELETE FROM tipousuario WHERE IdTipoUsuario = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    enviarRespuesta(true, 'Tipo de usuario eliminado correctamente');
                } else {
                    enviarRespuesta(false, 'No se encontró el tipo de usuario');
                }
            } else {
                enviarRespuesta(false, 'Error al eliminar: ' . $conn->error);
            }
            $stmt->close();
            break;
        
        default:
            enviarRespuesta(false, 'Acción no válida');
            break;
    }
    
    $conn->close();
    exit;
}

// Si no es AJAX, mostrar la interfaz HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Usuarios - TRASPASEMOS</title>
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

    <li class="nav-item active">
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

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid mt-4">
                
                <!-- Alertas dinámicas -->
                <div id="alertContainer"></div>
                
                <!-- Formulario Agregar/Editar -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" id="cardHeaderForm" style="background-color: #1cc88a;">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-plus"></i>
                            <span id="tituloForm">Agregar Nuevo Tipo de Usuario</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="formTipoUsuario">
                            <input type="hidden" id="accion" value="agregar">
                            <input type="hidden" id="idOriginal" value="">
                            
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="tipoUsuarioId">ID <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="tipoUsuarioId" 
                                           name="tipoUsuarioId"
                                           placeholder="Ej: 1" 
                                           min="1" 
                                           required>
                                </div>
                                <div class="form-group col-md-5">
                                    <label for="descripcionTipoUsuario">Descripción <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="descripcionTipoUsuario" 
                                           name="descripcionTipoUsuario"
                                           placeholder="Ej: Administrador, Docente, Coordinador" 
                                           maxlength="100" 
                                           required>
                                </div>
                                <div class="form-group col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success btn-block" id="btnSubmit">
                                        <i class="fas fa-plus"></i>
                                        <span id="textoBoton">Agregar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ml-2" id="btnCancelar" style="display: none;">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Tipos de Usuarios -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #1fbeac;">
                        <h6 class="m-0 font-weight-bold text-white text-center">Tabla - Tipos de Usuarios Registrados</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tablaTiposUsuarios" width="100%" cellspacing="0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th width="60%">Descripción</th>
                                        <th width="30%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <i class="fas fa-spinner fa-spin"></i> Cargando datos...
                                        </td>
                                    </tr>
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

<script>
$(document).ready(function(){
    // Cargar datos al iniciar
    cargarTiposUsuarios();

    /**
     * Mostrar alerta
     */
    function mostrarAlerta(tipo, mensaje) {
        const alertHTML = `
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        $('#alertContainer').html(alertHTML);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
        
        // Scroll hacia arriba
        $('html, body').animate({ scrollTop: 0 }, 300);
    }

    /**
     * Cargar todos los tipos de usuarios
     */
    function cargarTiposUsuarios() {
        $.ajax({
            url: 'Tipo_usuario.php',
            type: 'POST',
            data: { accion: 'listar' },
            dataType: 'json',
            beforeSend: function() {
                $('#tablaTiposUsuarios tbody').html(`
                    <tr>
                        <td colspan="3" class="text-center">
                            <i class="fas fa-spinner fa-spin"></i> Cargando datos...
                        </td>
                    </tr>
                `);
            },
            success: function(response){
                if(response.success) {
                    if(response.data && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(function(tipo){
                            html += `
                                <tr>
                                    <td>${escapeHtml(tipo.id)}</td>
                                    <td>${escapeHtml(tipo.descripcion)}</td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm btnEditar" 
                                                data-id="${escapeHtml(tipo.id)}" 
                                                data-descripcion="${escapeHtml(tipo.descripcion)}">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button class="btn btn-danger btn-sm btnEliminar" 
                                                data-id="${escapeHtml(tipo.id)}">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#tablaTiposUsuarios tbody').html(html);
                    } else {
                        $('#tablaTiposUsuarios tbody').html(`
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    <i class="fas fa-info-circle"></i> No hay tipos de usuarios registrados
                                </td>
                            </tr>
                        `);
                    }
                } else {
                    mostrarAlerta('danger', '❌ ' + response.message);
                }
            },
            error: function(xhr, status, error){
                console.error('Error AJAX:', error);
                console.error('Respuesta:', xhr.responseText);
                mostrarAlerta('danger', '❌ Error al cargar los tipos de usuarios.');
            }
        });
    }

    /**
     * Enviar formulario
     */
    $('#formTipoUsuario').submit(function(e){
        e.preventDefault();
        
        const id = $('#tipoUsuarioId').val().trim();
        const descripcion = $('#descripcionTipoUsuario').val().trim();
        const accion = $('#accion').val();

        // Validaciones
        if(id === '' || parseInt(id) < 1){
            mostrarAlerta('warning', '⚠ Por favor, ingresa un ID válido (mayor a 0).');
            $('#tipoUsuarioId').focus();
            return;
        }

        if(descripcion === ''){
            mostrarAlerta('warning', '⚠ Por favor, ingresa una descripción.');
            $('#descripcionTipoUsuario').focus();
            return;
        }

        if(descripcion.length > 100){
            mostrarAlerta('warning', '⚠ La descripción no puede exceder los 100 caracteres.');
            $('#descripcionTipoUsuario').focus();
            return;
        }

        // Deshabilitar botón
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

        $.ajax({
            url: 'Tipo_usuario.php',
            type: 'POST',
            data: { 
                accion: accion,
                tipoUsuarioId: id,
                descripcionTipoUsuario: descripcion
            },
            dataType: 'json',
            success: function(response){
                if(response.success) {
                    mostrarAlerta('success', '✅ ' + response.message);
                    resetearFormulario();
                    cargarTiposUsuarios();
                } else {
                    mostrarAlerta('danger', '❌ ' + response.message);
                }
            },
            error: function(xhr, status, error){
                console.error('Error AJAX:', error);
                console.error('Respuesta:', xhr.responseText);
                mostrarAlerta('danger', '❌ Error al procesar la solicitud.');
            },
            complete: function(){
                $('#btnSubmit').prop('disabled', false);
                actualizarBotonSubmit();
            }
        });
    });

    /**
     * Editar tipo de usuario
     */
    $(document).on('click', '.btnEditar', function(){
        const id = $(this).data('id');
        const descripcion = $(this).data('descripcion');
        
        $('#tipoUsuarioId').val(id).prop('readonly', true);
        $('#descripcionTipoUsuario').val(descripcion);
        $('#idOriginal').val(id);
        $('#accion').val('editar');
        
        $('#cardHeaderForm').css('background-color', '#f6c23e');
        $('#tituloForm').html('<i class="fas fa-edit"></i> Editar Tipo de Usuario');
        $('#btnSubmit').removeClass('btn-success').addClass('btn-warning');
        $('#textoBoton').text('Actualizar');
        $('#btnSubmit i').removeClass('fa-plus').addClass('fa-save');
        $('#btnCancelar').show();
        
        $('html, body').animate({
            scrollTop: $('#formTipoUsuario').offset().top - 100
        }, 500);
    });

    /**
     * Eliminar tipo de usuario
     */
    $(document).on('click', '.btnEliminar', function(){
        const id = $(this).data('id');
        
        if(confirm('⚠️ ¿Estás seguro de que deseas eliminar este tipo de usuario?\n\nEsta acción no se puede deshacer.')) {
            $.ajax({
                url: 'Tipo_usuario.php',
                type: 'POST',
                data: { 
                    accion: 'eliminar',
                    tipoUsuarioId: id
                },
                dataType: 'json',
                success: function(response){
                    if(response.success) {
                        mostrarAlerta('success', '🗑️ ' + response.message);
                        cargarTiposUsuarios();
                    } else {
                        mostrarAlerta('danger', '❌ ' + response.message);
                    }
                },
                error: function(xhr, status, error){
                    console.error('Error AJAX:', error);
                    mostrarAlerta('danger', '❌ Error al eliminar el tipo de usuario.');
                }
            });
        }
    });

    /**
     * Cancelar edición
     */
    $('#btnCancelar').click(function(){
        resetearFormulario();
    });

    /**
     * Resetear formulario
     */
    function resetearFormulario() {
        $('#formTipoUsuario')[0].reset();
        $('#tipoUsuarioId').prop('readonly', false);
        $('#idOriginal').val('');
        $('#accion').val('agregar');
        
        $('#cardHeaderForm').css('background-color', '#1cc88a');
        $('#tituloForm').html('<i class="fas fa-plus"></i> Agregar Nuevo Tipo de Usuario');
        $('#btnSubmit').removeClass('btn-warning').addClass('btn-success');
        $('#textoBoton').text('Agregar');
        $('#btnSubmit i').removeClass('fa-save').addClass('fa-plus');
        $('#btnCancelar').hide();
    }

    /**
     * Actualizar texto del botón
     */
    function actualizarBotonSubmit() {
        const accion = $('#accion').val();
        if(accion === 'agregar') {
            $('#btnSubmit').html('<i class="fas fa-plus"></i> <span id="textoBoton">Agregar</span>');
        } else {
            $('#btnSubmit').html('<i class="fas fa-save"></i> <span id="textoBoton">Actualizar</span>');
        }
    }

    /**
     * Escapar HTML
     */
    function escapeHtml(text) {
        if(text === null || text === undefined) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>

</body>
</html>