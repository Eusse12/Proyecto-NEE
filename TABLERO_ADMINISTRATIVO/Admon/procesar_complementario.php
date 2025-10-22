<?php
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$nombre = $_POST['nombre'];
$categoria = $_POST['categoria'];
$grado = $_POST['grado'];
$responsable = $_POST['responsable'];
$estado = $_POST['estado'];
$descripcion = $_POST['descripcion'];
$observaciones = $_POST['observaciones'];

$sql = "INSERT INTO aspectos_complementarios 
(nombre, categoria, grado, responsable, estado, descripcion, observaciones)
VALUES ('$nombre', '$categoria', '$grado', '$responsable', '$estado', '$descripcion', '$observaciones')";

if ($conn->query($sql) === TRUE) {
    header("Location: ascp_complt.php?mensaje=✅ Registro guardado exitosamente");
    exit();
} else {
    echo "Error al guardar: " . $conn->error;
}

$conn->close();
?>
