<?php
// ============================
// CONFIGURACIÓN DE CONEXIÓN
// ============================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "traspasemos";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]));
}

// ============================
// OBTENER ACCIÓN
// ============================
$accion = $_POST['accion'] ?? '';

switch ($accion) {

    // ============================
    // LISTAR SEDES
    // ============================
    case 'listar':
        $sql = "SELECT id, nombre, direccion FROM sede ORDER BY id ASC";
        $result = $conn->query($sql);

        $sedes = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sedes[] = $row;
            }
        }

        echo json_encode(["success" => true, "data" => $sedes]);
        break;

    // ============================
    // AGREGAR SEDE
    // ============================
    case 'agregar':
        $nombre = $_POST['nombresede'] ?? '';
        $direccion = $_POST['direccion'] ?? '';

        if (empty($nombre) || empty($direccion)) {
            echo json_encode(["success" => false, "message" => "⚠ Faltan datos para registrar la sede."]);
            exit;
        }

        $sql = "INSERT INTO sede (nombre, direccion) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nombre, $direccion);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Sede agregada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "❌ Error al agregar la sede: " . $conn->error]);
        }
        $stmt->close();
        break;

    // ============================
    // EDITAR SEDE
    // ============================
    case 'editar':
        $id = $_POST['sedeId'] ?? '';
        $nombre = $_POST['nombresede'] ?? '';
        $direccion = $_POST['direccion'] ?? '';

        if (empty($id) || empty($nombre) || empty($direccion)) {
            echo json_encode(["success" => false, "message" => "⚠ Faltan datos para editar la sede."]);
            exit;
        }

        $sql = "UPDATE sede SET nombre = ?, direccion = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nombre, $direccion, $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Sede actualizada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "❌ Error al actualizar la sede: " . $conn->error]);
        }
        $stmt->close();
        break;

    // ============================
    // ELIMINAR SEDE
    // ============================
    case 'eliminar':
        $id = $_POST['sedeId'] ?? '';

        if (empty($id)) {
            echo json_encode(["success" => false, "message" => "⚠ Falta el ID de la sede a eliminar."]);
            exit;
        }

        $sql = "DELETE FROM sede WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Sede eliminada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "❌ Error al eliminar la sede: " . $conn->error]);
        }
        $stmt->close();
        break;

    default:
        echo json_encode(["success" => false, "message" => "⚠ Acción no válida."]);
        break;
}

$conn->close();
?>
