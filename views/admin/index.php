<?php
$titulo = 'Panel de Administración - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Supervisión Institucional</h1>
        <p class="page-subtitle">Monitoreo en tiempo real, analítica académica y gobernanza del sistema</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= $base ?>/admin/docentes" class="btn btn-outline" title="Gestionar catálogo de profesores">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 4px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            Personal Docente
        </a>
        <a href="<?= $base ?>/estudiantes" class="btn btn-outline" title="Consultar padrón de estudiantes">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 4px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            Padrón Alumnos
        </a>
        <a href="<?= $base ?>/reportes" class="btn btn-primary" title="Exportar reportes de toda la institución">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 4px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Reportes Globales
        </a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span><?= htmlspecialchars($mensaje) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<!-- Tarjetas de Métricas Institucionales -->
<div class="stats-grid mb-6">
    <div class="stat-card">
        <span class="stat-label">Docentes Activos</span>
        <span class="stat-value"><?= $totalDocentes ?></span>
        <span class="text-muted" style="font-size: 0.8rem; margin-top: 4px;">Personal académico registrado</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Estudiantes Matriculados</span>
        <span class="stat-value"><?= $totalEstudiantes ?></span>
        <span class="text-muted" style="font-size: 0.8rem; margin-top: 4px;">En las 4 carreras técnicas</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Asistencias de Hoy</span>
        <span class="stat-value" style="color: var(--color-primary);"><?= $asistenciasHoy ?></span>
        <span class="text-muted" style="font-size: 0.8rem; margin-top: 4px;">Marcaciones con código QR</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Total Clases Dictadas</span>
        <span class="stat-value"><?= $totalSesiones ?></span>
        <span class="text-muted" style="font-size: 0.8rem; margin-top: 4px;">Histórico acumulado</span>
    </div>
</div>

<!-- Monitoreo de Clases en Tiempo Real -->
<div class="card mb-6">
    <div class="d-flex justify-content-between align-center flex-wrap gap-2 mb-4 pb-3" style="border-bottom: 1px solid var(--color-border);">
        <div class="d-flex align-center gap-2">
            <span class="badge badge-info pulse-badge">EN VIVO</span>
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--color-primary-dark); margin: 0;">
                Clases Activas en Tiempo Real
            </h2>
        </div>
        <span class="text-muted" style="font-size: 0.85rem;">
            <?= count($sesionesActivas) ?> aula(s) en transmisión
        </span>
    </div>

    <?php if (empty($sesionesActivas)): ?>
        <div class="text-center py-6" style="padding: 35px 20px;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--color-surface-hover); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; color: var(--color-text-muted);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <h3 style="font-size: 1rem; color: var(--color-text-main); margin-bottom: 4px; font-weight: 600;">
                No hay sesiones de clase activas en este momento
            </h3>
            <p class="text-muted" style="font-size: 0.88rem; max-width: 480px; margin: 0 auto;">
                Cuando los docentes inicien una clase y proyecten su código QR, aparecerán aquí para su supervisión y control inmediato.
            </p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Materia y Nivel</th>
                        <th>Código QR</th>
                        <th>Hora de Inicio</th>
                        <th>Alumnos Registrados</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sesionesActivas as $sesion): ?>
                        <tr>
                            <td>
                                <div class="font-bold text-primary"><?= htmlspecialchars($sesion['docente_nombre']) ?></div>
                                <span class="text-muted" style="font-size: 0.8rem;">@<?= htmlspecialchars($sesion['docente_usuario']) ?></span>
                            </td>
                            <td>
                                <div style="font-weight: 500;"><?= htmlspecialchars($sesion['materia']) ?></div>
                                <span class="text-muted" style="font-size: 0.8rem;"><?= htmlspecialchars($sesion['fecha']) ?></span>
                            </td>
                            <td>
                                <span class="badge" style="font-family: var(--font-mono); font-size: 0.85rem; letter-spacing: 0.05em; background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe;">
                                    <?= htmlspecialchars($sesion['codigo_sesion']) ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-variant-numeric: tabular-nums;"><?= htmlspecialchars($sesion['hora_inicio']) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-success" style="font-weight: 600; font-size: 0.85rem;">
                                    <?= (int)$sesion['total_asistencias'] ?> presente(s)
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <form method="POST" action="<?= $base ?>/admin/sesion/cerrar" style="display: inline;" onsubmit="return confirm('¿Desea forzar el cierre de esta sesión de clase?');">
                                    <input type="hidden" name="sesion_id" value="<?= (int)$sesion['id'] ?>">
                                    <button type="submit" class="btn btn-outline" style="color: var(--color-danger-text); border-color: var(--color-danger-border); padding: 5px 10px; font-size: 0.8rem;" title="Cerrar sesión que quedó abierta">
                                        Finalizar Clase
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
    <!-- Distribución de Asistencias por Carrera -->
    <div class="card">
        <h2 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary-dark); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--color-border);">
            Distribución por Carrera Institucional
        </h2>
        <?php if (empty($distribucionCarreras)): ?>
            <p class="text-muted" style="font-size: 0.88rem;">Sin datos de asistencia suficientes aún.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <?php 
                $maxAsist = max(array_column($distribucionCarreras, 'total')) ?: 1;
                foreach ($distribucionCarreras as $c): 
                    $pct = round(($c['total'] / $maxAsist) * 100);
                ?>
                    <div>
                        <div class="d-flex justify-content-between mb-1" style="font-size: 0.88rem;">
                            <span class="font-bold text-primary"><?= htmlspecialchars($c['carrera']) ?></span>
                            <span class="text-muted font-bold"><?= (int)$c['total'] ?> registros</span>
                        </div>
                        <div style="height: 8px; background: var(--color-surface-hover); border-radius: 4px; overflow: hidden;">
                            <div style="height: 100%; width: <?= $pct ?>%; background: linear-gradient(90deg, var(--color-primary), var(--color-accent)); border-radius: 4px;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Historial Reciente de Sesiones Dictadas -->
    <div class="card">
        <div class="d-flex justify-content-between align-center mb-3 pb-2" style="border-bottom: 1px solid var(--color-border);">
            <h2 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary-dark); margin: 0;">
                Últimas Clases Dictadas
            </h2>
            <a href="<?= $base ?>/reportes" style="font-size: 0.8rem; color: var(--color-primary); font-weight: 600;">
                Ver Historial Completo &rarr;
            </a>
        </div>
        <?php if (empty($historialReciente)): ?>
            <p class="text-muted" style="font-size: 0.88rem;">No se registran clases anteriores.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Docente</th>
                            <th>Materia</th>
                            <th style="text-align: right;">Asistencias</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historialReciente as $h): ?>
                            <tr>
                                <td style="white-space: nowrap; font-variant-numeric: tabular-nums;">
                                    <?= htmlspecialchars($h['fecha']) ?>
                                </td>
                                <td><?= htmlspecialchars($h['docente_nombre']) ?></td>
                                <td>
                                    <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($h['materia']) ?>">
                                        <?= htmlspecialchars($h['materia']) ?>
                                    </div>
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    <?= (int)$h['total_asistencias'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
