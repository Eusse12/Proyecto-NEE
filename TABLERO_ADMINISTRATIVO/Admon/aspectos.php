<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Aspectos Académicos y Complementarios</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.css" rel="stylesheet">
</head>
<body id="page-top">

<div class="container mt-4 mb-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white text-center">
      <h4><i class="fas fa-book-open"></i> Registro de Aspectos Académicos y Complementarios</h4>
    </div>
    <div class="card-body">

      <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-success text-center">
          <?= htmlspecialchars($_GET['mensaje']) ?>
        </div>
      <?php endif; ?>

      <form action="procesar_aspectos.php" method="POST">
        <h5 class="text-primary mt-3 mb-3"><i class="fas fa-graduation-cap"></i> Aspecto Académico</h5>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Nombre del Aspecto Académico</label>
            <input type="text" name="nombre_academico" class="form-control" required placeholder="Ej: Matemáticas, Ciencias Naturales...">
          </div>
          <div class="form-group col-md-6">
            <label>Área o Campo</label>
            <input type="text" name="area_academica" class="form-control" placeholder="Ej: Ciencias exactas, Lenguaje...">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-3">
            <label>Intensidad Horaria (Horas/Semana)</label>
            <input type="number" name="horas_academicas" class="form-control" min="1" max="40">
          </div>
          <div class="form-group col-md-5">
            <label>Grado Asignado</label>
            <input type="text" name="grado_academico" class="form-control" placeholder="Ej: 6°, 7°, 8°...">
          </div>
          <div class="form-group col-md-4">
            <label>Docente Responsable</label>
            <input type="text" name="responsable_academico" class="form-control" placeholder="Ej: Juan Pérez">
          </div>
        </div>

        <div class="form-group">
          <label>Descripción del Aspecto Académico</label>
          <textarea name="descripcion_academico" class="form-control" rows="3" placeholder="Breve descripción o enfoque del aspecto..."></textarea>
        </div>

        <div class="form-group col-md-4">
          <label>Estado</label>
          <select name="estado_academico" class="form-control">
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
          </select>
        </div>

        <hr class="mt-4 mb-4">

        <h5 class="text-success mt-3 mb-3"><i class="fas fa-user-check"></i> Aspecto Complementario</h5>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Nombre del Aspecto Complementario</label>
            <input type="text" name="nombre_complementario" class="form-control" required placeholder="Ej: Respeto, Puntualidad, Convivencia...">
          </div>
          <div class="form-group col-md-6">
            <label>Categoría</label>
            <select name="categoria_complementaria" class="form-control">
              <option value="Convivencia">Convivencia</option>
              <option value="Valores">Valores</option>
              <option value="Participación">Participación</option>
              <option value="Presentación">Presentación</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-5">
            <label>Grado Asignado</label>
            <input type="text" name="grado_complementario" class="form-control" placeholder="Ej: 9°, 10°, 11°...">
          </div>
          <div class="form-group col-md-4">
            <label>Responsable</label>
            <input type="text" name="responsable_complementario" class="form-control" placeholder="Ej: Coordinador de convivencia">
          </div>
          <div class="form-group col-md-3">
            <label>Estado</label>
            <select name="estado_complementario" class="form-control">
              <option value="Activo">Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Descripción del Aspecto Complementario</label>
          <textarea name="descripcion_complementario" class="form-control" rows="3" placeholder="Ej: Evalúa el respeto hacia compañeros y docentes..."></textarea>
        </div>

        <div class="form-group">
          <label>Observaciones</label>
          <textarea name="observaciones_complementario" class="form-control" rows="3" placeholder="Observaciones adicionales o recomendaciones..."></textarea>
        </div>

        <div class="text-center mt-4">
          <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> Guardar Aspectos</button>
          <a href="index.html" class="btn btn-secondary btn-lg"><i class="fas fa-home"></i> Volver</a>
        </div>

      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
