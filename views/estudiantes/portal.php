<?php
$titulo = 'Mi Portal Academico - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="content-medium">
    <nav class="breadcrumb">
        <a href="<?= $base ?>/">Inicio</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Mi Expediente Académico</span>
    </nav>

    <!-- Encabezado del Estudiante -->
    <div class="card portal-header-flex mb-6">
        <div>
            <span class="auth-badge mb-2">ESTUDIANTE REGISTRADO</span>
            <h2 class="text-primary font-extrabold mt-2" style="font-size: 1.65rem;">
                <?= htmlspecialchars($estudiante['nombre']) ?>
            </h2>
            <p class="text-muted mt-2" style="font-size: 0.95rem;">
                Codigo: <strong class="table-code"><?= htmlspecialchars($estudiante['codigo']) ?></strong> &bull; 
                Carrera: <strong><?= htmlspecialchars($estudiante['carrera']) ?></strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= $base ?>/asistencia/escanear" class="btn btn-dorado">Registrar Nueva Asistencia</a>
            <a href="<?= $base ?>/logout-estudiante" class="btn btn-outline">Cerrar Sesion</a>
        </div>
    </div>

    <?php
    $totalAsistencias = count($asistencias);
    $mesActual = date('Y-m');
    $asistenciasMes = 0;
    $materiasDistintas = [];
    foreach ($asistencias as $a) {
        if (str_starts_with($a['fecha'] ?? '', $mesActual)) {
            $asistenciasMes++;
        }
        if (!empty($a['materia'])) {
            $materiasDistintas[$a['materia']] = true;
        }
    }
    $totalMaterias = count($materiasDistintas);
    ?>

    <!-- Métricas del Alumno -->
    <div class="stats-grid mb-6">
        <div class="stat-card">
            <span class="stat-label">Total de Asistencias</span>
            <span class="stat-value text-primary"><?= $totalAsistencias ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Asistencias en <?= strftime('%B') ?: date('F') ?></span>
            <span class="stat-value" style="color: var(--color-accent-dark);"><?= $asistenciasMes ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Materias Registradas</span>
            <span class="stat-value"><?= $totalMaterias ?></span>
        </div>
    </div>

    <!-- Historial de Asistencias del Alumno -->
    <div class="card">
        <h3 class="text-primary font-bold mb-4" style="font-size: 1.2rem;">
            Mi Historial de Asistencias
        </h3>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Materia</th>
                        <th>Docente</th>
                        <th>Cod. Sesion</th>
                        <th class="text-right">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($asistencias)): ?>
                        <tr>
                            <td colspan="6" class="table-empty">
                                No tienes asistencias registradas todavia.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($asistencias as $a): ?>
                            <tr>
                                <td class="font-semibold"><?= htmlspecialchars($a['fecha']) ?></td>
                                <td class="font-semibold text-primary"><?= htmlspecialchars($a['hora']) ?></td>
                                <td class="font-medium"><?= htmlspecialchars($a['materia']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($a['docente']) ?></td>
                                <td class="table-code"><?= htmlspecialchars($a['codigo_sesion']) ?></td>
                                <td class="text-right">
                                    <span class="badge badge-success">PRESENTE</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
