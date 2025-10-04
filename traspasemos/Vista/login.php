<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "traspasemos";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['correo'] ?? '';
    $clave  = $_POST['clave'] ?? '';

    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($clave, $usuario['clave'])) {
            $_SESSION['usuario'] = $usuario['nombre_completo'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            // Mensaje de éxito
            echo "<div class='alert alert-success w-100 text-center'>
                    ✅ Bienvenido, {$usuario['nombre_completo']}
                  </div>";

            // Si es administrador, mostrar botón de Dashboard
            if ($usuario['tipo_usuario'] === 'Administrador') {
                echo "<div class='text-center mt-2'>
                        <a href='/traspasemos_git/Proyecto-NEE/TABLERO_ADMINISTRATIVO/Admon/index.html' class='btn btn-success'>Ir al Dashboard</a>
                      </div>";
            }
        } else {
            echo "<div class='alert alert-danger w-100 text-center'>❌ Contraseña incorrecta</div>";
        }
    } else {
        echo "<div class='alert alert-danger w-100 text-center'>❌ El usuario no existe</div>";
    }
}
