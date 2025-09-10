<?php
include("basededatos.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $clave = password_hash($_POST["clave"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, correo, clave) VALUES ('$nombre', '$correo', '$clave')";

    if ($conn ->query($sql) === TRUE){
        header("Location: login.php");
        exit();
    } else{
        echo "Error: " .$sql . "<br>" .$conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Crear cuenta</h1>
    <form method="POST" class="form">
      <input type="text" name="nombre" placeholder="Nombre completo" required>
      <input type="email" name="correo" placeholder="Correo electrónico" required>
      <input type="password" name="clave" placeholder="Contraseña" required>
      <button type="submit" class="btn">Registrarse</button>
    </form>
    <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
  </div>
</body>
</html>
