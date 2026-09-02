<?php
$titulo = 'Mi Portal Académico - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="max-width: 900px; margin: 20px auto;">
    <!-- Encabezado del Estudiante -->
    <div style="background: white; border-radius: 12px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-top: 5px solid var(--dorado); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="background: var(--azul-marino); color: var(--dorado); font-size: 0.75rem; padding: 4px 10px; border-radius: 4px; font-weight: 700; letter-spacing: 0.5px;">ESTUDIANTE REGISTRADO</span>
            <h2 style="color: var(--azul-marino); margin-top: 8px; font-size: 1.6rem;"><?= htmlspecialchars($estudiante['nombre']) ?></h2>
            <p style="color: var(--texto-secundario); font-size: 0.95rem; margin-top: 4px;">
                Código: <strong style="color: var(--azul-marino); font-family: monospace; font-size: 1.05rem;"><?= htmlspecialchars($estudiante['codigo']) ?></strong> &bull; 
                Carrera: <strong><?= htmlspecialchars($estudiante['carrera']) ?></strong>
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= $base ?>/asistencia/escanear" class="btn btn-dorado">Registrar Nueva Asistencia</a>
            <a href="<?= $base ?>/logout-estudiante" class="btn btn-outline">Cerrar Sesión</a>
        </div>
    </div>

    <!-- Historial de Asistencias del Alumno -->
    <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="color: var(--azul-marino); font-size: 1.2rem; font-weight: 700; margin-bottom: 16px;">Mi Historial de Asistencias</h3>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--borde); color: var(--texto-secundario);">
                        <th style="padding: 10px 12px;">Fecha</th>
                        <th style="padding: 10px 12px;">Hora</th>
                        <th style="padding: 10px 12px;">Materia</th>
                        <th style="padding: 10px 12px;">Docente</th>
                        <th style="padding: 10px 12px;">Cód. Sesión</th>
                        <th style="padding: 10px 12px; text-align: right;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($asistencias)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--texto-secundario);">
                                No tienes asistencias registradas todavía.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($asistencias as $a): ?>
                            <tr style="border-bottom: 1px solid var(--borde);">
                                <td style="padding: 10px 12px; font-weight: 600;"><?= htmlspecialchars($a['fecha']) ?></td>
                                <td style="padding: 10px 12px; color: var(--azul-marino); font-weight: 600;"><?= htmlspecialchars($a['hora']) ?></td>
                                <td style="padding: 10px 12px; font-weight: 500;"><?= htmlspecialchars($a['materia']) ?></td>
                                <td style="padding: 10px 12px; color: var(--texto-secundario);"><?= htmlspecialchars($a['docente']) ?></td>
                                <td style="padding: 10px 12px; font-family: monospace; font-weight: 700;"><?= htmlspecialchars($a['codigo_sesion']) ?></td>
                                <td style="padding: 10px 12px; text-align: right;">
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 700;">PRESENTE</span>
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
