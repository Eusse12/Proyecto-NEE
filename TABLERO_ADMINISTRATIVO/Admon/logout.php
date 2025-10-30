<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, borre también la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Obtener la ruta base del proyecto dinámicamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// Construir la URL completa hacia inicio.php
// Desde TABLERO_ADMINISTRATIVO/Admon/ necesitamos subir 2 niveles
$base_url = $protocol . "://" . $host . dirname(dirname(dirname($_SERVER['PHP_SELF']))) . "/inicio.php";

// Redirigir
header("Location: " . $base_url);
exit();
?>