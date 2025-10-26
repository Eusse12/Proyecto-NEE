<?php
/******************************************************
 * CONFIGURACIÓN DE CONEXIÓN A LA BASE DE DATOS
 ******************************************************/
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "traspasemos"; // <-- cambia esto

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

/******************************************************
 * FUNCIONES CRUD
 ******************************************************/
// Crear nuevo registro
if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
    $stmt = $conn->prepare("INSERT INTO datosestud (id_usuario, fecha_nacimiento, id_grupo, direccion, barrio, id_ciudad, id_departamento, eps, id_acudiente)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isississi", $_POST['id_usuario'], $_POST['fecha_nacimiento'], $_POST['id_grupo'], $_POST['direccion'],
        $_POST['barrio'], $_POST['id_ciudad'], $_POST['id_departamento'], $_POST['eps'], $_POST['id_acudiente']);
    $stmt->execute();
    echo "<p style='color:green;'>✅ Registro agregado correctamente.</p>";
}

// Actualizar registro existente
if (isset($_POST['accion']) && $_POST['accion'] == 'editar') {
    $stmt = $conn->prepare("UPDATE datosestud SET id_usuario=?, fecha_nacimiento=?, id_grupo=?, direccion=?, barrio=?, id_ciudad=?, id_departamento=?, eps=?, id_acudiente=? WHERE id=?");
    $stmt->bind_param("isississii", $_POST['id_usuario'], $_POST['fecha_nacimiento'], $_POST['id_grupo'], $_POST['direccion'],
        $_POST['barrio'], $_POST['id_ciudad'], $_POST['id_departamento'], $_POST['eps'], $_POST['id_acudiente'], $_POST['id']);
    $stmt->execute();
    echo "<p style='color:blue;'>✏️ Registro actualizado correctamente.</p>";
}

// Eliminar registro
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM datosestud WHERE id=$id");
    echo "<p style='color:red;'>🗑️ Registro eliminado correctamente.</p>";
}

/******************************************************
 * OBTENER REGISTROS
 ******************************************************/
$result = $conn->query("SELECT * FROM datosestud ORDER BY id DESC");

// Si se va a editar, obtener los datos del registro
$registroEditar = null;
if (isset($_GET['editar'])) {
    $idEditar = intval($_GET['editar']);
    $registroEditar = $conn->query("SELECT * FROM datosestud WHERE id=$idEditar")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor de Datos Estudiantiles</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f7f7f7; }
        h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: white; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #e6e6e6; }
        form { background: white; padding: 15px; margin-bottom: 25px; border: 1px solid #ccc; }
        input, button { margin: 5px 0; padding: 6px; }
        button { background: #333; color: white; border: none; cursor: pointer; }
        button:hover { background: #555; }
        a { text-decoration: none; color: #007BFF; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h2>Gestor de Datos Estudiantiles</h2>

<!-- FORMULARIO DE AGREGAR O EDITAR -->
<form method="POST">
    <h3><?= $registroEditar ? "✏️ Editar Estudiante (ID {$registroEditar['id']})" : "➕ Agregar Nuevo Estudiante" ?></h3>

    <input type="hidden" name="accion" value="<?= $registroEditar ? 'editar' : 'agregar' ?>">
    <?php if ($registroEditar): ?>
        <input type="hidden" name="id" value="<?= $registroEditar['id'] ?>">
    <?php endif; ?>

    ID Usuario: <input type="number" name="id_usuario" value="<?= $registroEditar['id_usuario'] ?? '' ?>" required><br>
    Fecha Nacimiento: <input type="date" name="fecha_nacimiento" value="<?= $registroEditar['fecha_nacimiento'] ?? '' ?>"><br>
    Grupo: <input type="number" name="id_grupo" value="<?= $registroEditar['id_grupo'] ?? '' ?>"><br>
    Dirección: <input type="text" name="direccion" value="<?= $registroEditar['direccion'] ?? '' ?>"><br>
    Barrio: <input type="text" name="barrio" value="<?= $registroEditar['barrio'] ?? '' ?>"><br>
    Ciudad: <input type="number" name="id_ciudad" value="<?= $registroEditar['id_ciudad'] ?? '' ?>"><br>
    Departamento: <input type="number" name="id_departamento" value="<?= $registroEditar['id_departamento'] ?? '' ?>"><br>
    EPS: <input type="text" name="eps" value="<?= $registroEditar['eps'] ?? '' ?>"><br>
    Acudiente: <input type="number" name="id_acudiente" value="<?= $registroEditar['id_acudiente'] ?? '' ?>"><br>

    <button type="submit"><?= $registroEditar ? "Actualizar" : "Guardar" ?></button>
    <?php if ($registroEditar): ?>
        <a href="gestor_datosestud.php">Cancelar edición</a>
    <?php endif; ?>
</form>

<!-- TABLA DE REGISTROS -->
<table>
    <tr>
        <th>ID</th>
        <th>ID Usuario</th>
        <th>Fecha Nacimiento</th>
        <th>Grupo</th>
        <th>Dirección</th>
        <th>Barrio</th>
        <th>Ciudad</th>
        <th>Departamento</th>
        <th>EPS</th>
        <th>Acudiente</th>
        <th>Acciones</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['id_usuario'] ?></td>
            <td><?= $row['fecha_nacimiento'] ?></td>
            <td><?= $row['id_grupo'] ?></td>
            <td><?= $row['direccion'] ?></td>
            <td><?= $row['barrio'] ?></td>
            <td><?= $row['id_ciudad'] ?></td>
            <td><?= $row['id_departamento'] ?></td>
            <td><?= $row['eps'] ?></td>
            <td><?= $row['id_acudiente'] ?></td>
            <td>
                <a href="?editar=<?= $row['id'] ?>">✏️ Editar</a> |
                <a href="?eliminar=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este registro?')">🗑️ Eliminar</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
