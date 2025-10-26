<?php
// 🔹 Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 🔹 Determinar acción (guardar, editar o eliminar)
$accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : '');

switch ($accion) {

    // 🟢 GUARDAR NUEVO REGISTRO
    case '':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $conn->real_escape_string($_POST['nombre']);
            $categoria = $conn->real_escape_string($_POST['categoria']);
            $grado = $conn->real_escape_string($_POST['grado']);
            $responsable = $conn->real_escape_string($_POST['responsable']);
            $estado = $conn->real_escape_string($_POST['estado']);
            $descripcion = $conn->real_escape_string($_POST['descripcion']);
            $observaciones = $conn->real_escape_string($_POST['observaciones']);

            $sql = "INSERT INTO aspectos_complementarios 
                    (nombre, categoria, grado, responsable, estado, descripcion, observaciones)
                    VALUES ('$nombre', '$categoria', '$grado', '$responsable', '$estado', '$descripcion', '$observaciones')";

            if ($conn->query($sql)) {
                header("Location: aspecto_complementario.php?mensaje=" . urlencode("Registro guardado correctamente."));
            } else {
                echo "Error al guardar: " . $conn->error;
            }
        }
        break;

    // 🟡 EDITAR REGISTRO
    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $nombre = $conn->real_escape_string($_POST['nombre']);
            $categoria = $conn->real_escape_string($_POST['categoria']);
            $grado = $conn->real_escape_string($_POST['grado']);
            $responsable = $conn->real_escape_string($_POST['responsable']);
            $estado = $conn->real_escape_string($_POST['estado']);
            $descripcion = $conn->real_escape_string($_POST['descripcion']);
            $observaciones = $conn->real_escape_string($_POST['observaciones']);

            $sql = "UPDATE aspectos_complementarios 
                    SET nombre='$nombre', categoria='$categoria', grado='$grado', responsable='$responsable',
                        estado='$estado', descripcion='$descripcion', observaciones='$observaciones'
                    WHERE id=$id";

            if ($conn->query($sql)) {
                header("Location: aspecto_complementario.php?mensaje=" . urlencode("Registro actualizado correctamente."));
            } else {
                echo "Error al actualizar: " . $conn->error;
            }
        }
        break;

    // 🔴 ELIMINAR REGISTRO
    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "DELETE FROM aspectos_complementarios WHERE id = $id";
            if ($conn->query($sql)) {
                header("Location: aspecto_complementario.php?mensaje=" . urlencode("Registro eliminado correctamente."));
            } else {
                echo "Error al eliminar: " . $conn->error;
            }
        }
        break;

    default:
        echo "Acción no válida.";
        break;
}

$conn->close();
?>
