<?php
include 'db.php';
header('Content-Type: application/json');

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

switch ($accion) {
    case 'listar':
        listarAcademicos($conn);
        break;
    case 'agregar':
        agregarAcademico($conn);
        break;
    case 'editar':
        editarAcademico($conn);
        break;
    case 'eliminar':
        eliminarAcademico($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

$conn->close();

function listarAcademicos($conn) {
    $sql = "SELECT IDAspectoAcad as id, NombreAsp as nombre FROM aspacadem ORDER BY IDAspectoAcad ASC";
    $result = $conn->query($sql);
    
    $academicos = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $academicos[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'data' => $academicos]);
}

function agregarAcademico($conn) {
    $id = isset($_POST['academicoId']) ? intval($_POST['academicoId']) : 0;
    $nombre = isset($_POST['nombreAcademico']) ? trim($_POST['nombreAcademico']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'El ID debe ser un número mayor a 0']);
        return;
    }
    
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre del aspecto es obligatorio']);
        return;
    }
    
    // Verificar si el ID ya existe
    $stmt_check = $conn->prepare("SELECT IDAspectoAcad FROM aspacadem WHERE IDAspectoAcad = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un aspecto académico con ese ID']);
        $stmt_check->close();
        return;
    }
    $stmt_check->close();
    
    // Insertar el nuevo aspecto
    $stmt = $conn->prepare("INSERT INTO aspacadem (IDAspectoAcad, NombreAsp) VALUES (?, ?)");
    $stmt->bind_param("is", $id, $nombre);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '✅ Aspecto académico agregado correctamente', 'id' => $id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function editarAcademico($conn) {
    $id = isset($_POST['academicoId']) ? intval($_POST['academicoId']) : 0;
    $nombre = isset($_POST['nombreAcademico']) ? trim($_POST['nombreAcademico']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre del aspecto es obligatorio']);
        return;
    }
    
    // Actualizar el aspecto
    $stmt = $conn->prepare("UPDATE aspacadem SET NombreAsp = ? WHERE IDAspectoAcad = ?");
    $stmt->bind_param("si", $nombre, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Aspecto académico actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el aspecto o no hubo cambios']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function eliminarAcademico($conn) {
    $id = isset($_POST['academicoId']) ? intval($_POST['academicoId']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    // Eliminar el aspecto
    $stmt = $conn->prepare("DELETE FROM aspacadem WHERE IDAspectoAcad = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Aspecto académico eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el aspecto académico']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $conn->error]);
    }
    
    $stmt->close();
}
?>