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

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        $sql = "SELECT * FROM grados ORDER BY id ASC";
        $result = $conn->query($sql);
        $grados = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $grados[] = [
                    "id" => $row["id"],
                    "nombre" => $row["nombre"]
                ];
            }
        }

        echo json_encode(["success" => true, "data" => $grados]);
        break;

    case 'agregar':
        $nombre = trim($_POST['nombreGrado'] ?? '');
        if ($nombre === '') {
            echo json_encode(["success" => false, "message" => "El nombre del grado no puede estar vacío."]);
            break;
        }

        $sql = "INSERT INTO grados (nombre) VALUES ('$nombre')";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "✅ Grado agregado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al agregar el grado: " . $conn->error]);
        }
        break;

    case 'editar':
        $id = intval($_POST['gradoId']);
        $nombre = trim($_POST['nombreGrado'] ?? '');

        if ($id <= 0 || $nombre === '') {
            echo json_encode(["success" => false, "message" => "Datos inválidos para editar."]);
            break;
        }

        $sql = "UPDATE grados SET nombre='$nombre' WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "✏️ Grado actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar: " . $conn->error]);
        }
        break;

    case 'eliminar':
        $id = intval($_POST['gradoId']);
        if ($id <= 0) {
            echo json_encode(["success" => false, "message" => "ID inválido para eliminar."]);
            break;
        }

        $sql = "DELETE FROM grados WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "🗑️ Grado eliminado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar: " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Acción no válida."]);
}

$conn->close();
?>
