<?php
$titulo = 'Reportes de Asistencia - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 style="color: var(--azul-marino); font-size: 1.8rem; font-weight: 800;">Reportes de Asistencia</h1>
        <p style="color: var(--texto-secundario); font-size: 0.95rem;">Filtra registros por fecha, materia o estudiante y expórtalos en formato CSV</p>
    </div>
    <div>
        <a href="<?= $base ?>/reportes/csv?fecha_inicio=<?= urlencode($fechaInicio ?? '') ?>&fecha_fin=<?= urlencode($fechaFin ?? '') ?>&materia=<?= urlencode($materia ?? '') ?>&busqueda=<?= urlencode($busqueda ?? '') ?>" 
           class="btn btn-dorado">
            Descargar CSV (Excel)
        </a>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div style="background: white; border-radius: 12px; padding: 22px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); margin-bottom: 24px;">
    <form method="GET" action="<?= $base ?>/reportes" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; align-items: end;">
        <div>
            <label for="fecha_inicio" style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 4px;">Desde:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio ?? '') ?>"
                   style="width: 100%; padding: 8px 10px; border: 1px solid var(--borde); border-radius: 6px; font-size: 0.9rem;">
        </div>

        <div>
            <label for="fecha_fin" style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 4px;">Hasta:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fechaFin ?? '') ?>"
                   style="width: 100%; padding: 8px 10px; border: 1px solid var(--borde); border-radius: 6px; font-size: 0.9rem;">
        </div>

        <div>
            <label for="materia" style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 4px;">Materia:</label>
            <input type="text" id="materia" name="materia" value="<?= htmlspecialchars($materia ?? '') ?>" placeholder="Ej: Programación"
                   style="width: 100%; padding: 8px 10px; border: 1px solid var(--borde); border-radius: 6px; font-size: 0.9rem;">
        </div>

        <div>
            <label for="busqueda" style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 4px;">Estudiante (código/nombre):</label>
            <input type="text" id="busqueda" name="busqueda" value="<?= htmlspecialchars($busqueda ?? '') ?>" placeholder="Ej: Juan o EST001"
                   style="width: 100%; padding: 8px 10px; border: 1px solid var(--borde); border-radius: 6px; font-size: 0.9rem;">
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Filtrar</button>
            <a href="<?= $base ?>/reportes" class="btn btn-outline">Limpiar</a>
        </div>
    </form>
</div>

<!-- Tabla de Resultados -->
<div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="color: var(--azul-marino); font-size: 1.15rem; font-weight: 700;">Resultados Encontrados</h3>
        <span style="font-weight: 700; color: var(--azul-marino); background: var(--fondo); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem;">
            <?= $total ?> asistencias
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--borde); color: var(--texto-secundario);">
                    <th style="padding: 10px 12px;">Fecha</th>
                    <th style="padding: 10px 12px;">Hora</th>
                    <th style="padding: 10px 12px;">Código</th>
                    <th style="padding: 10px 12px;">Estudiante</th>
                    <th style="padding: 10px 12px;">Carrera</th>
                    <th style="padding: 10px 12px;">Materia / Sesión</th>
                    <th style="padding: 10px 12px;">Cód. Sesión</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($asistencias)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--texto-secundario);">
                            No hay asistencias que coincidan con los filtros seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($asistencias as $r): ?>
                        <tr style="border-bottom: 1px solid var(--borde);">
                            <td style="padding: 10px 12px; font-weight: 600;"><?= htmlspecialchars($r['fecha']) ?></td>
                            <td style="padding: 10px 12px; color: var(--azul-marino); font-weight: 600;"><?= htmlspecialchars($r['hora']) ?></td>
                            <td style="padding: 10px 12px; font-family: monospace; font-weight: 700;"><?= htmlspecialchars($r['codigo']) ?></td>
                            <td style="padding: 10px 12px;"><?= htmlspecialchars($r['estudiante']) ?></td>
                            <td style="padding: 10px 12px; color: var(--texto-secundario);"><?= htmlspecialchars($r['carrera']) ?></td>
                            <td style="padding: 10px 12px; font-weight: 500;"><?= htmlspecialchars($r['materia']) ?></td>
                            <td style="padding: 10px 12px; font-family: monospace;"><?= htmlspecialchars($r['codigo_sesion']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
