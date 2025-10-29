<?php
session_start();

// Procesar login si se envió el formulario
$loginMessage = '';
$mostrarModal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['correo']) && isset($_POST['clave'])) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "traspasemos";

    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        $loginMessage = "<div class='alert alert-danger w-100 text-center'>⚠️ Error en la conexión a la base de datos</div>";
        $mostrarModal = true;
    } else {
        $conn->set_charset("utf8mb4");
        
        $correo = trim($_POST['correo'] ?? '');
        $clave  = $_POST['clave'] ?? '';

        $sql = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            
            if (password_verify($clave, $usuario['clave'])) {
                // Guardar toda la información del usuario en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario'] = $usuario['nombre_completo'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['identificacion'] = $usuario['identificacion'];
                $_SESSION['foto'] = isset($usuario['foto']) && !empty($usuario['foto']) ? $usuario['foto'] : 'img/default.png';

                // Redirigir según tipo de usuario
                if ($usuario['tipo_usuario'] === 'Administrador') {
                    header("Location: TABLERO_ADMINISTRATIVO/Admon/index.php");
                    exit();
                } elseif ($usuario['tipo_usuario'] === 'Docente') {
                    header("Location: TABLERO_DOCENTE/index.php");
                    exit();
                } elseif ($usuario['tipo_usuario'] === 'Estudiante') {
                    header("Location: TABLERO_ESTUDIANTE/index.php");
                    exit();
                } else {
                    // Tipo de usuario desconocido, mostrar mensaje
                    $loginMessage = "<div class='alert alert-warning w-100 text-center'>⚠️ Tipo de usuario no configurado</div>";
                    $mostrarModal = true;
                }
            } else {
                $loginMessage = "<div class='alert alert-danger w-100 text-center'>❌ Contraseña incorrecta</div>";
                $mostrarModal = true;
            }
        } else {
            $loginMessage = "<div class='alert alert-danger w-100 text-center'>❌ El usuario no existe</div>";
            $mostrarModal = true;
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="stylesheet" href="css/bootstrap.css">
    <script src="https://kit.fontawesome.com/aa77aa11e4.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .nav-link{
            transition: all 0.3s ease;
        }
        .nav-link:hover{
            transform: translateY(-3px);
            color: #1fbeac !important;
            text-shadow: 0 2px 8px rgba(255,255,255,0.3);
        }
        .btn:hover {
            transform: translateY(-2px) scale(1.05);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .navbar-brand:hover {
            transform: scale(1.05);
            transition: all 0.3s ease;
        }
    </style>    

    <title>Traspasemos - Necesidades Educativas Especiales</title>
    <link rel="icon" type="image/png" href="img/Logo.png">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="inicio.php">
                    <img src="img/Logo.png" alt="Logo" style="height: 150px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="inicio.php">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="inicio.php#Nosotros">Nosotros</a></li>
                        <li class="nav-item"><a class="nav-link" href="inicio.php#Servicios">Servicios</a></li>
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="Necesidades.php">Necesidades Educativas Especiales</a></li>
                        <li class="nav-item"><a class="nav-link" href="inicio.php#Contacto">Contáctenos</a></li>
                    </ul>

                    <form class="d-flex" role="search">
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <!-- Usuario logueado -->
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <?php if ($_SESSION['tipo_usuario'] === 'Administrador'): ?>
                                        <li><a class="dropdown-item" href="TABLERO_ADMINISTRATIVO/Admon/index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                    <?php elseif ($_SESSION['tipo_usuario'] === 'Docente'): ?>
                                        <li><a class="dropdown-item" href="TABLERO_DOCENTE/index.php"><i class="fas fa-chalkboard-teacher"></i> Mi Panel</a></li>
                                    <?php elseif ($_SESSION['tipo_usuario'] === 'Estudiante'): ?>
                                        <li><a class="dropdown-item" href="TABLERO_ESTUDIANTE/index.php"><i class="fas fa-graduation-cap"></i> Mi Panel</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <!-- Usuario no logueado -->
                            <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">INGRESAR</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Modal de Login -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="Necesidades.php">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ingresar</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="emailInput" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" name="correo" id="emailInput" placeholder="name@example.com" required autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label for="passwordInput" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="clave" id="passwordInput" placeholder="Contraseña" required autocomplete="current-password">
                            </div>
                            <div id="loginMessage">
                                <?php echo $loginMessage; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Ingresar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- SECCIONES -->
    <section class="container mt-5 mb-5" style="text-align: justify;">
        <div class="accordion" id="accordionExample">

            <!-- ITEM#1 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        ¿Qué es una Necesidad Educativa Especial?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="media/aa.png" alt="Imagen NEE" class="img-fluid">
                            </div>
                            <div class="col-md-6">
                                <p><strong>Las necesidades Educativas Especiales (NEE)</strong> hacen referencia a aquellos apoyos adicionales que algunos estudiantes requieren para acceder a una educación en igualdad de condiciones.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ITEM#2 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Tipos de Necesidades Educativas Especiales
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Síndrome de Asperger:</strong> Parte del espectro autista, con inteligencia promedio o superior y dificultades sociales.</p>
                                <p><strong>TDAH:</strong> Problemas de concentración, impulsividad e hiperactividad que afectan el rendimiento.</p>
                                <p><strong>TOC:</strong> Pensamientos recurrentes y compulsiones que buscan reducir la ansiedad.</p>
                                <p><strong>Ansiedad:</strong> Trastorno emocional que interfiere en la participación académica y social.</p>
                                <p><strong>Dislexia:</strong> Dificultad para leer y escribir debido al procesamiento del lenguaje.</p>
                                <p><strong>Discalculia:</strong> Dificultad para comprender números, operaciones y conceptos matemáticos.</p>
                                <p><strong>TEA:</strong> Condición del neurodesarrollo que afecta comunicación y relaciones sociales.</p>
                                <p><strong>Dispraxia:</strong> Trastorno del desarrollo motor que dificulta la coordinación de movimientos.</p>
                                <p><strong>Hipoacusia:</strong> Disminución parcial de la capacidad auditiva.</p>
                                <p><strong>Trastorno del lenguaje expresivo:</strong> Dificultad para expresar ideas verbalmente.</p>
                            </div>
                            <div class="col-md-6 text-center">
                                <img src="media/eee.jpg" alt="Imagen educativa" class="img-fluid mb-3">
                                <img src="media/iiii.jpg" alt="Imagen educativa 2" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ITEM#3 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        ¿Qué es una discapacidad?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="media/Captura de pantalla 2025-04-23 100920.png" alt="Imagen discapacidad" class="img-fluid">
                            </div>
                            <div class="col-md-6">
                                <p>Es una <b>condición</b> que afecta la capacidad de una persona para realizar actividades o funciones, ya sea físicas, mentales, sensoriales o intelectuales.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item #4 -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Tipos de discapacidad
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="img/Discapacidad_fisica.png" alt="Imagen discapacidad" class="img-fluid">
                            </div>
                            <div class="col-md-6">
                                <p><strong>Paraplejia:</strong> Afecta las piernas y parte del cuerpo por lesiones en la médula espinal.</p>
                                <p><strong>Tetraplejia:</strong> Afecta las cuatro extremidades y el torso por lesiones en la médula espinal alta.</p>
                                <p><strong>Lesiones medulares:</strong> Causa pérdida de movilidad y sensibilidad según el nivel afectado.</p>
                                <p><strong>Hemiplejia:</strong> Parálisis de un lado del cuerpo, generalmente tras un accidente cerebrovascular.</p>
                                <p><strong>Espina Bífida:</strong> Malformación de la columna vertebral que limita la movilidad.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-primary text-light pt-5 pb-4" style="text-align: justify;">
        <div class="container">
            <div class="row text-center text-md-start">
                <div class="col-md-3 col-sm-12 mb-4">
                    <a href="inicio.php">
                        <img src="img/Logo.png" alt="Logo Traspasemos" style="height: 200px;">
                    </a>
                </div>
                <div class="col-md-3 col-sm-12 mb-4">
                    <h5 class="text-uppercase mb-3">Nuestra Empresa</h5>
                    <p>Somos una plataforma dedicada a mejorar la educación inclusiva y apoyar a estudiantes con Necesidades Educativas Especiales (NEE).</p>
                    <p>Contáctanos para más información o asesoría profesional.</p>
                </div>
                <div class="col-md-3 col-sm-12 mb-4">
                    <h5 class="text-uppercase mb-3">Contáctanos</h5>
                    <ul class="list-unstyled">
                        <li>
                            <a href="mailto:traspasemosinfo@gmail.com" class="text-light">
                                <i class="fas fa-envelope"></i> traspasemosinfo@gmail.com
                            </a>
                        </li>
                        <li>
                            <a href="https://goo.gl/maps/XXXX" target="_blank" class="text-light">
                                <i class="fas fa-map-marker-alt"></i> Cll 84 #74 - 60
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-12 mb-4">
                    <h5 class="text-uppercase mb-3">Síguenos</h5>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <a href="https://whatsapp.com/channel/0029Vb7OrhCEAKWITy7xk80w" target="_blank" class="social-icon">
                            <img src="img/WhatsApp.svg.webp" alt="WhatsApp" class="img-fluid" style="width: 50px; height: 50px;">
                        </a>
                        <a href="https://www.facebook.com/ieluislopezdemesamedellin" target="_blank" class="social-icon">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" alt="Facebook" class="img-fluid" style="width: 45px; height: 45px;">
                        </a>
                    </div>
                    <a href="#" class="text-light d-block">Política de Tratamiento de Datos</a>
                    <a href="#" class="text-light d-block">Términos y Servicios</a>
                </div>
            </div>
            <hr class="bg-light" />
            <p class="text-center mb-0">&copy; 2025 Traspasemos. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="js/bootstrap.bundle.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <?php if ($mostrarModal): ?>
    <script>
        // Mostrar el modal automáticamente si hay un mensaje de login
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('exampleModal'));
            modal.show();
        });
    </script>
    <?php endif; ?>
    
</body>
</html>