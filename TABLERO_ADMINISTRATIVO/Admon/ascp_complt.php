<?php
include 'db.php';
header('Content-Type: application/json');

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

switch ($accion) {
    case 'listar':
        listarAspectos($conn);
        break;
    case 'agregar':
        agregarAspecto($conn);
        break;
    case 'editar':
        editarAspecto($conn);
        break;
    case 'eliminar':
        eliminarAspecto($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

$conn->close();

function listarAspectos($conn) {
    $sql = "SELECT IdAspectoCompo as id, NombreAsp as nombre FROM aspectocomport ORDER BY IdAspectoCompo ASC";
    $result = $conn->query($sql);
    
    $aspectos = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $aspectos[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'data' => $aspectos]);
}

function agregarAspecto($conn) {
    $id = isset($_POST['aspectoId']) ? intval($_POST['aspectoId']) : 0;
    $nombre = isset($_POST['nombreAspecto']) ? trim($_POST['nombreAspecto']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'El ID debe ser un número mayor a 0']);
        return;
    }
    
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre del aspecto es obligatorio']);
        return;
    }
    
    $stmt_check = $conn->prepare("SELECT IdAspectoCompo FROM aspectocomport WHERE IdAspectoCompo = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un aspecto con ese ID']);
        $stmt_check->close();
        return;
    }
    $stmt_check->close();
    
    $stmt = $conn->prepare("INSERT INTO aspectocomport (IdAspectoCompo, NombreAsp) VALUES (?, ?)");
    $stmt->bind_param("is", $id, $nombre);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '✅ Aspecto agregado correctamente', 'id' => $id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function editarAspecto($conn) {
    $id = isset($_POST['aspectoId']) ? intval($_POST['aspectoId']) : 0;
    $nombre = isset($_POST['nombreAspecto']) ? trim($_POST['nombreAspecto']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre del aspecto es obligatorio']);
        return;
    }
    
    $stmt = $conn->prepare("UPDATE aspectocomport SET NombreAsp = ? WHERE IdAspectoCompo = ?");
    $stmt->bind_param("si", $nombre, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Aspecto actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el aspecto o no hubo cambios']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function eliminarAspecto($conn) {
    $id = isset($_POST['aspectoId']) ? intval($_POST['aspectoId']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    $stmt = $conn->prepare("DELETE FROM aspectocomport WHERE IdAspectoCompo = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Aspecto eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el aspecto']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $conn->error]);
    }
    
    $stmt->close();
}
?>