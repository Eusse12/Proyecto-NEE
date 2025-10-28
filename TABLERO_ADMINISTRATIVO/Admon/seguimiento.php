<?php
$conn = new mysqli("localhost", "root", "", "traspasemos");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Consultar estudiantes y su información relacionada
$query = "
SELECT 
    e.id AS id_estudiante,
    e.nombre_completo,
    e.tipo_documento,
    e.numero_documento,
    e.edad,
    e.eps,
    e.direccion,
    e.barrio,
    c.nombre AS ciudad,
    d.nombre AS departamento,
    g.descripcion AS grupo,
    gr.nombre AS grado,
    j.nombre AS jornada,
    s.nombre AS sede,
    a.nombre_completo AS acudiente,
    a.telefono AS telefono_acudiente,
    a.parentesco
FROM datosestud e
LEFT JOIN grupo g ON e.id_grupo = g.id
LEFT JOIN grado gr ON g.id_grado = gr.id
LEFT JOIN jornada j ON g.id_jornada = j.id
LEFT JOIN sede s ON g.id_sede = s.id
LEFT JOIN ciudad c ON e.id_ciudad = c.id
LEFT JOIN departamento d ON e.id_departamento = d.id
LEFT JOIN acudiente a ON e.id_acudiente = a.id
ORDER BY e.nombre_completo ASC
";
$estudiantes = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estudiantes y Remisiones</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fc; }
.card-header { background: linear-gradient(90deg, #1cc88a, #13855c); color: white; cursor: pointer; }
.accordion-button:not(.collapsed) { background-color: #20c997; color: #fff; }
.info-box { background: #f1fdf8; border-left: 5px solid #1cc88a; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
</style>
</head>
<body class="p-4">

<div class="container">
    <h2 class="text-primary mb-4"><i class="fas fa-users"></i> Lista de Estudiantes</h2>

    <div class="accordion" id="accordionEstudiantes">
        <?php if ($estudiantes && $estudiantes->num_rows > 0): ?>
            <?php while ($row = $estudiantes->fetch_assoc()): ?>
                <?php
                // Consultar remisiones asociadas
                $rem = $conn->prepare("
                    SELECT fecha_remision, motivo_remision, docente_remitente,
                        CASE 
                            WHEN fecha_remision >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 'Activa'
                            ELSE 'Finalizada'
                        END AS estado
                    FROM remision
                    WHERE nombre_estudiante = ?
                    ORDER BY fecha_remision DESC
                ");
                $rem->bind_param("s", $row['nombre_completo']);
                $rem->execute();
                $resRem = $rem->get_result();
                $remisiones = $resRem->fetch_all(MYSQLI_ASSOC);
                $rem->close();
                ?>

                <div class="accordion-item mb-3 shadow-sm">
                    <h2 class="accordion-header" id="heading<?= $row['id_estudiante'] ?>">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse<?= $row['id_estudiante'] ?>" aria-expanded="false"
                            aria-controls="collapse<?= $row['id_estudiante'] ?>">
                            👤 <?= htmlspecialchars($row['nombre_completo']) ?> — <?= $row['tipo_documento'] ?> <?= $row['numero_documento'] ?>
                        </button>
                    </h2>

                    <div id="collapse<?= $row['id_estudiante'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordionEstudiantes">
                        <div class="accordion-body">
                            <div class="row">
                                <!-- Información personal -->
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <h5 class="text-success"><i class="fas fa-id-card"></i> Información Personal</h5>
                                        <p><strong>Edad:</strong> <?= $row['edad'] ?? 'No registrada' ?></p>
                                        <p><strong>EPS:</strong> <?= htmlspecialchars($row['eps'] ?? 'No registrada') ?></p>
                                        <p><strong>Dirección:</strong> <?= htmlspecialchars($row['direccion'] ?? 'No registrada') ?></p>
                                        <p><strong>Barrio:</strong> <?= htmlspecialchars($row['barrio'] ?? 'No registrado') ?></p>
                                        <p><strong>Ciudad:</strong> <?= htmlspecialchars($row['ciudad'] ?? 'N/A') ?></p>
                                        <p><strong>Departamento:</strong> <?= htmlspecialchars($row['departamento'] ?? 'N/A') ?></p>
                                    </div>

                                    <div class="info-box">
                                        <h5 class="text-success"><i class="fas fa-school"></i> Información Académica</h5>
                                        <p><strong>Grado:</strong> <?= htmlspecialchars($row['grado'] ?? 'N/A') ?></p>
                                        <p><strong>Grupo:</strong> <?= htmlspecialchars($row['grupo'] ?? 'N/A') ?></p>
                                        <p><strong>Jornada:</strong> <?= htmlspecialchars($row['jornada'] ?? 'N/A') ?></p>
                                        <p><strong>Sede:</strong> <?= htmlspecialchars($row['sede'] ?? 'N/A') ?></p>
                                    </div>
                                </div>

                                <!-- Información del acudiente -->
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <h5 class="text-success"><i class="fas fa-user-friends"></i> Acudiente</h5>
                                        <p><strong>Nombre:</strong> <?= htmlspecialchars($row['acudiente'] ?? 'No registrado') ?></p>
                                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($row['telefono_acudiente'] ?? '---') ?></p>
                                        <p><strong>Parentesco:</strong> <?= htmlspecialchars($row['parentesco'] ?? '---') ?></p>
                                    </div>

                                    <!-- Remisiones -->
                                    <div class="info-box">
                                        <h5 class="text-success"><i class="fas fa-file-alt"></i> Remisiones</h5>
                                        <?php if (count($remisiones) > 0): ?>
                                            <ul class="list-group">
                                                <?php foreach ($remisiones as $r): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong><?= htmlspecialchars($r['fecha_remision']) ?></strong> — <?= htmlspecialchars($r['docente_remitente'] ?? 'Sin docente') ?><br>
                                                            <small><?= htmlspecialchars($r['motivo_remision'] ?? 'Sin motivo') ?></small>
                                                        </div>
                                                        <span class="badge <?= $r['estado'] === 'Activa' ? 'bg-success' : 'bg-secondary' ?>">
                                                            <?= $r['estado'] ?>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-muted">❌ No tiene remisiones registradas.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">No hay estudiantes registrados en el sistema.</div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
