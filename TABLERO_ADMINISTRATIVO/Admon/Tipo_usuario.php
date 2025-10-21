<?php
include 'db.php';
header('Content-Type: application/json');

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

switch ($accion) {
    case 'listar':
        listarTiposUsuarios($conn);
        break;
    case 'agregar':
        agregarTipoUsuario($conn);
        break;
    case 'editar':
        editarTipoUsuario($conn);
        break;
    case 'eliminar':
        eliminarTipoUsuario($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

$conn->close();

function listarTiposUsuarios($conn) {
    $sql = "SELECT IdTipoUsuario as id, Descripcion as descripcion FROM tipousuario ORDER BY IdTipoUsuario ASC";
    $result = $conn->query($sql);
    
    $tipos = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $tipos[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'data' => $tipos]);
}

function agregarTipoUsuario($conn) {
    $id = isset($_POST['tipoUsuarioId']) ? intval($_POST['tipoUsuarioId']) : 0;
    $descripcion = isset($_POST['descripcionTipoUsuario']) ? trim($_POST['descripcionTipoUsuario']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'El ID debe ser un número mayor a 0']);
        return;
    }
    
    if (empty($descripcion)) {
        echo json_encode(['success' => false, 'message' => 'La descripción es obligatoria']);
        return;
    }
    
    $stmt_check = $conn->prepare("SELECT IdTipoUsuario FROM tipousuario WHERE IdTipoUsuario = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un tipo de usuario con ese ID']);
        $stmt_check->close();
        return;
    }
    $stmt_check->close();
    
    $stmt = $conn->prepare("INSERT INTO tipousuario (IdTipoUsuario, Descripcion) VALUES (?, ?)");
    $stmt->bind_param("is", $id, $descripcion);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '✅ Tipo de usuario agregado correctamente', 'id' => $id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function editarTipoUsuario($conn) {
    $id = isset($_POST['tipoUsuarioId']) ? intval($_POST['tipoUsuarioId']) : 0;
    $descripcion = isset($_POST['descripcionTipoUsuario']) ? trim($_POST['descripcionTipoUsuario']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    if (empty($descripcion)) {
        echo json_encode(['success' => false, 'message' => 'La descripción es obligatoria']);
        return;
    }
    
    $stmt = $conn->prepare("UPDATE tipousuario SET Descripcion = ? WHERE IdTipoUsuario = ?");
    $stmt->bind_param("si", $descripcion, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Tipo de usuario actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el tipo de usuario o no hubo cambios']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function eliminarTipoUsuario($conn) {
    $id = isset($_POST['tipoUsuarioId']) ? intval($_POST['tipoUsuarioId']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    $stmt = $conn->prepare("DELETE FROM tipousuario WHERE IdTipoUsuario = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Tipo de usuario eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el tipo de usuario']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $conn->error]);
    }
    
    $stmt->close();
}
?>