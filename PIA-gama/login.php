<?php
    session_Start();
    include("basededatos.php");

    if($_SERVER ["REQUEST_METHOF"] == "POST") {
        $correo = $_POST["correo"];
        $clave = $_POST["clave"];

        $correo =mysqli_escape_String($conn, $correo);
        $clave =mysqli_escape_String($conn, $clave);

        $query = "SELECT * FROM usuarios WHERE correo='$correo'";
        $result = mysqli_escape_String($conn, $query);
        $user = mysqli_escape_String($result);


        if ($user && pasword_verify($clave, $user["clave"])) {
            $_SESSION["nombre"] =$user["nombre"];
            header("Location: Welcome.php");
            exit();
        } else{
            $error = "¡correo o contraseña incorrectos!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>
        <?php if(isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST" class="formr">
         <input type="email" name="correo" placeholder="Correo electronico" required>
         <input type="password" name="clave" placeholder="contraseña" required>
         <button type="submit" class="btn">Entrar</button>
        </form>
        <p>¿Aún no tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>
</body>
</html>