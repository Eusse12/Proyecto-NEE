<?php
include 'db.php';
header('Content-Type: application/json');

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

switch ($accion) {
    case 'listar':
        listarGrupos($conn);
        break;
    case 'agregar':
        agregarGrupo($conn);
        break;
    case 'editar':
        editarGrupo($conn);
        break;
    case 'eliminar':
        eliminarGrupo($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

$conn->close();

function listarGrupos($conn) {
    $sql = "SELECT Idgrupo as id, Descripcion as nombre FROM grupo ORDER BY Idgrupo ASC";
    $result = $conn->query($sql);
    
    $grupos = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $grupos[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'data' => $grupos]);
}

function agregarGrupo($conn) {
    $id = isset($_POST['grupoId']) ? intval($_POST['grupoId']) : 0;
    $nombre = isset($_POST['nombreGrupo']) ? trim($_POST['nombreGrupo']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'El ID debe ser un número mayor a 0']);
        return;
    }
    
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre del grupo es obligatorio']);
        return;
    }
    
    $stmt_check = $conn->prepare("SELECT Idgrupo FROM grupo WHERE Idgrupo = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un grupo con ese ID']);
        $stmt_check->close();
        return;
    }
    $stmt_check->close();
    
    $stmt = $conn->prepare("INSERT INTO grupo (Idgrupo, Descripcion) VALUES (?, ?)");
    $stmt->bind_param("is", $id, $nombre);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '✅ Grupo agregado correctamente', 'id' => $id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function editarGrupo($conn) {
    $id = isset($_POST['grupoId']) ? intval($_POST['grupoId']) : 0;
    $nombre = isset($_POST['nombreGrupo']) ? trim($_POST['nombreGrupo']) : '';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre del grupo es obligatorio']);
        return;
    }
    
    $stmt = $conn->prepare("UPDATE grupo SET Descripcion = ? WHERE Idgrupo = ?");
    $stmt->bind_param("si", $nombre, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Grupo actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el grupo o no hubo cambios']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
    }
    
    $stmt->close();
}

function eliminarGrupo($conn) {
    $id = isset($_POST['grupoId']) ? intval($_POST['grupoId']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID no válido']);
        return;
    }
    
    $stmt = $conn->prepare("DELETE FROM grupo WHERE Idgrupo = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Grupo eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el grupo']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $conn->error]);
    }
    
    $stmt->close();
}
?>