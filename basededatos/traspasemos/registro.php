<?php
include("db.php"); // incluye solo la conexión

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $clave  = password_hash($_POST["clave"], PASSWORD_DEFAULT);

    // comprobar si existe el correo
    $check = "SELECT id FROM usuarios WHERE correo='$correo' LIMIT 1";
    $result = $conn->query($check);

    if ($result && $result->num_rows > 0) {
        echo "<div class='alert alert-warning'>⚠️ El correo <b>$correo</b> ya está registrado.</div>";
    } else {
        $sql = "INSERT INTO usuarios (nombre, correo, clave) 
                VALUES ('$nombre', '$correo', '$clave')";

        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>✅ Usuario registrado correctamente.</div>";
            // opcional: redirigir después de unos segundos
            // header("Refresh:2; url=index.html");
        } else {
            echo "<div class='alert alert-danger'>❌ Error al registrar: " . $conn->error . "</div>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <button type="button" onclick="location.href='Necesidades.html'">Regresar</button>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body p-4">
            <h2 class="card-title text-center mb-4">Registro</h2>

            <!-- FORMULARIO YA CORREGIDO -->
            <form class="needs-validation" method="POST" action="registro.php" novalidate>
              <div class="mb-3">
                <label for="fullName" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="fullName" name="nombre" required>
                <div class="invalid-feedback">
                  Por favor ingresa tu nombre completo.
                </div>
              </div>
              
              <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="correo" required>
                <div class="invalid-feedback">
                  Por favor ingresa un correo válido.
                </div>
              </div>
              
              <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="clave" required>
                <div class="invalid-feedback">
                  Por favor ingresa una contraseña.
                </div>
              </div>
              
              <div class="mb-4">
                <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirmPassword" required>
                <div class="invalid-feedback">
                  Las contraseñas deben coincidir.
                </div>
              </div>
              
              <button type="submit" class="btn btn-primary w-100">Registrar</button>
            </form>
            <!-- FIN FORMULARIO -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Validación del formulario + verificación de contraseña -->
  <script>
    (function () {
      'use strict';
      var forms = document.querySelectorAll('.needs-validation');
      Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
          if (!form.checkValidity() ||
              document.getElementById('password').value !== document.getElementById('confirmPassword').value) {
            event.preventDefault();
            event.stopPropagation();
            if (document.getElementById('password').value !== document.getElementById('confirmPassword').value) {
              document.getElementById('confirmPassword').classList.add('is-invalid');
            }
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();
  </script>
</body>
</html>
