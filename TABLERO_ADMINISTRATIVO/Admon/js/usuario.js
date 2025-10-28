document.addEventListener("DOMContentLoaded", function() {
  // Solo intentar cargar si viene desde login o si ya hay sesión activa
  fetch("perfil_usuario.php")
    .then(res => res.json())
    .then(data => {
      const userSpan = document.querySelector(".nombre-usuario");
      const userImg = document.querySelector(".img-profile");

      if (data.logueado) {
        userSpan.textContent = data.nombre;
        if (data.foto) {
          userImg.src = data.foto;
        }
      } else {
        userSpan.textContent = "Invitado";
      }
    })
    .catch(err => console.error("Error al obtener usuario:", err));
});
