<?php
session_start();

// Procesar logout si se solicita
if (isset($_GET['logout'])) {
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
    
    // Redirigir a la página de inicio
    header("Location: inicio.php");
    exit();
}

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
            
            // Verificar contraseña (MD5 o password_hash)
            $password_valida = false;
            if (password_verify($clave, $usuario['clave'])) {
                $password_valida = true;
            } elseif (md5($clave) === $usuario['clave']) {
                $password_valida = true;
            }
            
            if ($password_valida) {
                // Guardar toda la información del usuario en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario'] = $usuario['nombre_completo'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['identificacion'] = $usuario['identificacion'];
                
                // Cargar foto de perfil correctamente
                if (isset($usuario['foto_perfil']) && !empty($usuario['foto_perfil']) && file_exists('TABLERO_ADMINISTRATIVO/Admon/' . $usuario['foto_perfil'])) {
                    $_SESSION['foto'] = 'TABLERO_ADMINISTRATIVO/Admon/' . $usuario['foto_perfil'];
                } elseif (isset($usuario['foto']) && !empty($usuario['foto'])) {
                    $_SESSION['foto'] = $usuario['foto'];
                } else {
                    $_SESSION['foto'] = 'img/default.png';
                }

                // Redirigir según tipo de usuario
                if ($usuario['tipo_usuario'] === 'Administrador') {
                    header("Location: /traspasemos-git/Proyecto-NEE/TABLERO_ADMINISTRATIVO/Admon/index.php");
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
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="stylesheet" href="css/bootstrap.css">

    <script src="https://kit.fontawesome.com/aa77aa11e4.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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
        .Mititulo {
            text-align: center;
        }
        .card-img-top {
            height: 250px;
            object-fit: cover;    
        }
        .user-profile-img {
            width: 35px;
            height: 35px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
            margin-right: 8px;
        }
        .dropdown-menu {
            min-width: 200px;
        }
    </style>
    
    <title>Traspasemos - Inicio</title>
    <link rel="icon" type="image/png" href="img/Logo.png">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="inicio.php"><img src="img/Logo.png" alt="" style="height: 150px;"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="inicio.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inicio.php#Nosotros">Nosotros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inicio.php#Servicios">Servicios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Necesidades.php">Necesidades Educativas Especiales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inicio.php#Contacto">Contáctenos</a>
                        </li>
                    </ul>
                    <form class="d-flex" role="search">
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <!-- Usuario logueado -->
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" type="button" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo isset($_SESSION['foto']) && $_SESSION['foto'] != '' ? htmlspecialchars($_SESSION['foto']) : 'img/default.png'; ?>" 
                                         alt="Foto de perfil" 
                                         class="user-profile-img">
                                    <span><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                                    <li>
                                        <div class="px-3 py-2">
                                            <small class="text-muted d-block">Conectado como:</small>
                                            <strong><?php echo htmlspecialchars($_SESSION['tipo_usuario']); ?></strong>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php if ($_SESSION['tipo_usuario'] === 'Administrador'): ?>
                                        <li><a class="dropdown-item" href="TABLERO_ADMINISTRATIVO/Admon/index.php">
                                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                        </a></li>
                                    <?php elseif ($_SESSION['tipo_usuario'] === 'Docente'): ?>
                                        <li><a class="dropdown-item" href="TABLERO_DOCENTE/index.php">
                                            <i class="fas fa-chalkboard-teacher me-2"></i> Mi Panel
                                        </a></li>
                                    <?php elseif ($_SESSION['tipo_usuario'] === 'Estudiante'): ?>
                                        <li><a class="dropdown-item" href="TABLERO_ESTUDIANTE/index.php">
                                            <i class="fas fa-graduation-cap me-2"></i> Mi Panel
                                        </a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="inicio.php?logout=true">
                                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                    </a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <!-- Usuario no logueado -->
                            <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <i class="fas fa-sign-in-alt me-1"></i> INGRESAR
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Modal de Login -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="inicio.php">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ingresar</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" name="correo" required placeholder="name@example.com" autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="clave" required placeholder="Contraseña" autocomplete="current-password">
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

    <section>
        <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-pause="false">
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="3000">
                    <img src="img/autismo.png" class="d-block w-100" alt="Imagen carrusel">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 style="color: rgb(87, 81, 81);">TRASPASEMOS</h5>
                        <p style="color: rgb(87, 81, 81);">Las barreras de la educación</p>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="3000">
                    <img src="img/imagen-carrusel3.png" class="d-block w-100" alt="Imagen carrusel">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 style="color: rgb(87, 81, 81);">ESTUDIA SIN LIMITES</h5>
                        <p style="color: rgb(87, 81, 81);">No le tengas miedo a ser tu mismo</p>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="3000">
                    <img src="img/imagen-carrusel2.jpg" class="d-block w-100" alt="Imagen carrusel">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 style="color: rgb(87, 81, 81);">ERES PODEROS@</h5>
                        <p style="color: rgb(87, 81, 81);">NO A LA DISCRIMINACIÓN</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5" style="text-align: justify;">
            <div class="row mt-5">
                <h2 class="mt-5 Mititulo poppins-black">Nuestro Proyecto</h2>
                <div class="col-md-6 col-sm-12" id="Nosotros">
                    <h3>Misión</h3>
                    <p><i>Nuestra plataforma tiene como propósito proporcionar a las instituciones educativas una herramienta integral de apoyo psicológico, facilitando la identificación, comprensión y orientación de estudiantes con dificultades cognitivas, emocionales y mentales. A través de información especializada, recursos educativos y asesoramiento profesional, buscamos capacitar a los docentes para que puedan ofrecer un acompañamiento efectivo, promoviendo el bienestar emocional y el desarrollo integral de los estudiantes en un entorno escolar inclusivo y empático.</i></p>
                </div>
                <div class="col-md-6 col-sm-12">
                    <h3>Visión</h3>
                    <p><i>Ser la plataforma líder en el ámbito educativo para la detección y abordaje de dificultades cognitivas, emocionales y mentales en estudiantes, fomentando una cultura de concienciación y apoyo psicológico en las instituciones. Aspiramos a transformar la educación mediante la integración de estrategias de salud mental en el proceso formativo, reduciendo el estigma asociado a los trastornos mentales y asegurando que cada estudiante reciba la orientación y el apoyo necesario para alcanzar su máximo potencial.</i></p>
                </div>
            </div>

            <h2 class="mt-5 Mititulo poppins-black">Equipo de Trabajo</h2>
            <div class="row mt-5 justify-content-center">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card">
                        <img src="img/yo.jpg" class="card-img-top" alt="Foto Nicolás">
                        <div class="card-body text-center">
                            <p class="card-text">Nicolás Eusse Gaviria</p>
                            <p>Desarrollador de software</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card">
                        <img src="img/Sebas.jpg" class="card-img-top" alt="Foto Sebastián">
                        <div class="card-body text-center">
                            <p class="card-text">Sebastián Quiceno Giraldo</p>
                            <p>Desarrollador de software</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card">
                        <img src="img/Kris.jpg" class="card-img-top" alt="Foto Kristhian">
                        <div class="card-body text-center">
                            <p class="card-text">Kristhian Zaaid Rivas</p>
                            <p>Desarrollador de software</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card">
                        <img src="img/Diego.png" class="card-img-top" alt="Foto Diego">
                        <div class="card-body text-center">
                            <p class="card-text">Diego Fernando Cortes</p>
                            <p>Desarrollador de software</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card">
                        <img src="img/jesus.jpg" class="card-img-top" alt="Foto Jesús">
                        <div class="card-body text-center">
                            <p class="card-text">Jesus Orlando Mas</p>
                            <p>Desarrollador de software</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid" style="background-color: #c3f5fa;" id="Servicios">
            <div class="container mt-5">
                <h2 class="mt-5 Mititulo poppins-black text-center">Nuestros Servicios</h2>
                <p class="text-center">Nuestro aplicativo <b>TRASPASEMOS</b> permite a las instituciones educativas y colegios:</p>
                
                <div class="row text-center mt-4">
                    <div class="col-md-4 col-lg-2 mb-5">
                        <i class="fa-regular fa-id-card fa-2xl" style="color: #1fbeac;"></i>
                        <p class="mt-3">Registrar y gestionar información sobre estudiantes con Necesidades Educativas Especiales y/o discapacidades.</p>
                    </div>
                    <div class="col-md-4 col-lg-2 mb-5">
                        <i class="fa-solid fa-hospital fa-2xl" style="color: #1fbeac;"></i>
                        <p class="mt-3">Mantener actualizadas las recomendaciones de especialistas médicos, accesibles a docentes y directivos.</p>
                    </div>
                    <div class="col-md-4 col-lg-2 mb-5">
                        <i class="fa-solid fa-street-view fa-2xl" style="color: #1fbeac;"></i>
                        <p class="mt-3">Reportar estudiantes con posibles NEE para su seguimiento por parte de la institución.</p>
                    </div>
                    <div class="col-md-4 col-lg-2 mb-5">
                        <i class="fa-solid fa-download fa-2xl" style="color: #1fbeac;"></i>
                        <p class="mt-3">Descargar reportes estadísticos detallados para análisis y toma de decisiones.</p>
                    </div>
                    <div class="col-md-4 col-lg-2 mb-5">
                        <i class="fa-regular fa-calendar-days fa-2xl" style="color: #1fbeac;"></i>
                        <p class="mt-3">Agendar citas psicológicas con el especialista de la institución educativa.</p>
                    </div>
                    <div class="col-md-4 col-lg-2 mb-5">
                        <i class="fa-solid fa-person-breastfeeding fa-2xl" style="color: #1fbeac;"></i>
                        <p class="mt-3">Capacitar a padres y comunidad educativa en el manejo de estudiantes con NEE mediante eventos y materiales.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5" id="Contacto">
            <h2 class="Mititulo poppins-black">Contáctenos</h2>
            <div class="row mt-5">
                <div class="col-md-6 col-sm-12">
                    <h3 class="text-center mt-4 mb-4">Datos de Contacto</h3>
                    <a href="mailto:traspasemosinfo@gmail.com" target="_blank" class="d-flex align-items-center mb-3">
                        <img src="https://cdn-icons-png.flaticon.com/128/732/732200.png" alt="Gmail" height="50" class="me-2">
                        <span>traspasemosinfo@gmail.com</span>
                    </a>
                    <a href="https://www.facebook.com/ieluislopezdemesamedellin" target="_blank" class="d-flex align-items-center mb-3">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" alt="Facebook" height="50" class="me-2">
                        <span>Facebook</span>
                    </a>
                    <a href="https://whatsapp.com/channel/0029Vb7OrhCEAKWITy7xk80w" target="_blank" class="d-flex align-items-center">
                        <img src="img/WhatsApp.svg.webp" alt="WhatsApp" height="50" class="me-2">
                        <span>WhatsApp</span>
                    </a>
                </div>

                <div class="col-md-6 col-sm-12">
                    <div class="card mb-4 p-3">
                        <h3 class="text-center mt-3 mb-4">Formulario de Contacto</h3>
                        <form action="guardar_contacto.php" method="POST">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Tu nombre completo" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Tu correo electrónico" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Tu número de teléfono">
                            </div>
                            <div class="mb-3">
                                <label for="mensaje" class="form-label">Mensaje</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="4" placeholder="Escribe tu mensaje" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.8478070817464!2d-75.58278392414576!3d6.283728475901458!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e44292eb929fc61%3A0x33d78c3c6ef01884!2sI.E.%20Luis%20L%C3%B3pez%20de%20Mesa!5e0!3m2!1ses!2sco!4v1739999378797!5m2!1ses!2sco" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

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
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/2021_Facebook_icon.svg/2048px-2021_Facebook_icon.svg.png" alt="Facebook" class="img-fluid" style="width: 45px; height: 45px;">
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