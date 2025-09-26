<?php
include("db.php"); // conexión a la BD

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST["correo"];
    $clave  = $_POST["clave"];

    // buscar usuario por correo
    $sql = "SELECT * FROM usuarios WHERE correo = '$correo' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // verificar contraseña
        if (password_verify($clave, $user["clave"])) {
            echo "<div class='alert alert-success'>✅ Sesión iniciada correctamente. ¡Bienvenido, " . $user["nombre"] . "!</div>";
            // Aquí podrías iniciar sesión con $_SESSION si quieres
            // session_start();
            // $_SESSION["usuario"] = $user["nombre"];
            // header("Location: index.html");
        } else {
            echo "<div class='alert alert-danger'>❌ Contraseña incorrecta.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>❌ No existe una cuenta con el correo <b>$correo</b>.</div>";
    }
}

$conn->close();
?>
