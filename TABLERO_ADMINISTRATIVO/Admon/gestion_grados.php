<?php
header('Content-Type: application/json');
$host = "localhost";
$user = "root";
$pass = "";
$database = "traspasemos";

// Conectar a la base de datos
$conn = new mysqli($host, $user, $pass, $database);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Error en la conexión: " . $conn->connect_error]));
}

// Configurar charset para evitar problemas con caracteres especiales
$conn->set_charset("utf8mb4");

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        $sql = "SELECT * FROM grado ORDER BY id ASC";
        $result = $conn->query($sql);
        $grado = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $grado[] = [
                    "id" => $row["id"],
                    "nombre" => $row["nombre"]
                ];
            }
        }

        echo json_encode(["success" => true, "data" => $grado]);
        break;

    case 'agregar':
        $nombre = trim($_POST['nombreGrado'] ?? '');
        
        if ($nombre === '') {
            echo json_encode(["success" => false, "message" => "El nombre del grado no puede estar vacío."]);
            break;
        }

        // Usar consultas preparadas para evitar inyección SQL
        $stmt = $conn->prepare("INSERT INTO grado (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Grado agregado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al agregar el grado: " . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'editar':
        $id = intval($_POST['gradoId'] ?? 0);
        $nombre = trim($_POST['nombreGrado'] ?? '');

        if ($id <= 0 || $nombre === '') {
            echo json_encode(["success" => false, "message" => "Datos inválidos para editar."]);
            break;
        }

        // Usar consultas preparadas
        $stmt = $conn->prepare("UPDATE grado SET nombre = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre, $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["success" => true, "message" => "✏️ Grado actualizado correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "No se encontró el grado con ese ID."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar: " . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'eliminar':
        $id = intval($_POST['gradoId'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(["success" => false, "message" => "ID inválido para eliminar."]);
            break;
        }

        // Usar consultas preparadas
        $stmt = $conn->prepare("DELETE FROM grado WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["success" => true, "message" => "🗑️ Grado eliminado correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "No se encontró el grado con ese ID."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar: " . $stmt->error]);
        }
        $stmt->close();
        break;

    default:
        echo json_encode(["success" => false, "message" => "Acción no válida."]);
}

$conn->close();
?>