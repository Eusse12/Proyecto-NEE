<?php
include 'db.php';
header('Content-Type: application/json');

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

switch ($accion) {
    case 'listar':
        listarTiposEstudiantes($conn);
        break;
    case 'agregar':
        agregarTipoEstudiante($conn);
        break;
    case 'editar':
        editarTipoEstudiante($conn);
        break;
    case 'eliminar':
        eliminarTipoEstudiante($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

$conn->close();

function listarTiposEstudiantes($conn) {
    $sql = "SELECT IdTipoEstudiante as id, Descripcion as descripcion FROM tipoestudiante ORDER BY IdTipoEstudiante ASC";
    $result = $conn->query($sql);
    
    $tipos = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $tipos[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'data' => $tipos]);
}

function agregarTipoEstudiante($conn) {
    $id = isset($_POST['tipoEstudianteId']) ? intval($_POST['tipoEstudianteId']) : 0;
    $descripcion = isset($_POST['descripcionTipoEstudiante']) ? trim($_POST['descripcionTipoEstudiante']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'El ID debe ser un número mayor a 0']);
        return;
    }
    
    if (empty($descripcion)) {
        echo json_encode(['success' => false, 'message' => 'La descripción es obligatoria']);
        return;
    }
    
    // Verificar si el ID ya existe
    $stmt_check = $conn->prepare("SELECT IdTipoEstudiante FROM tipoestudiante WHERE IdTipoEstudiante = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un tipo de estudiante con ese ID']);
        $stmt_check->close();
        return;
    }
    $stmt_check->close();
    
    // Insertar nuevo tipo de estudiante
    $stmt = $conn->prepare("INSERT INTO tipoestudiante (IdTipoEstudiante, Descripcion) VALUES (?, ?)");
    $stmt->bind_param("is", $id, $descripcion);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '✅ Tipo de estudiante agregado correctamente', 'id' => $id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function editarTipoEstudiante($conn) {
    $id = isset($_POST['tipoEstudianteId']) ? intval($_POST['tipoEstudianteId']) : 0;
    $descripcion = isset($_POST['descripcionTipoEstudiante']) ? trim($_POST['descripcionTipoEstudiante']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    if (empty($descripcion)) {
        echo json_encode(['success' => false, 'message' => 'La descripción es obligatoria']);
        return;
    }
    
    // Actualizar tipo de estudiante
    $stmt = $conn->prepare("UPDATE tipoestudiante SET Descripcion = ? WHERE IdTipoEstudiante = ?");
    $stmt->bind_param("si", $descripcion, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Tipo de estudiante actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el tipo de estudiante o no hubo cambios']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function eliminarTipoEstudiante($conn) {
    $id = isset($_POST['tipoEstudianteId']) ? intval($_POST['tipoEstudianteId']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    // Eliminar tipo de estudiante
    $stmt = $conn->prepare("DELETE FROM tipoestudiante WHERE IdTipoEstudiante = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Tipo de estudiante eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el tipo de estudiante']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $conn->error]);
    }
    
    $stmt->close();
}
?>