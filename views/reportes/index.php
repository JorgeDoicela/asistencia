<?php
$titulo = 'Reportes de Asistencia - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';

$esAdmin = $esAdmin ?? false;

$paramsExport = [
    'fecha_inicio' => $filtros['fecha_inicio'] ?? '',
    'fecha_fin'    => $filtros['fecha_fin'] ?? '',
    'materia'      => $filtros['materia'] ?? '',
    'busqueda'     => $filtros['busqueda'] ?? ''
];

if ($esAdmin) {
    if (!empty($filtros['docente_id'])) {
        $paramsExport['docente_id'] = $filtros['docente_id'];
    }
    if (!empty($filtros['carrera'])) {
        $paramsExport['carrera'] = $filtros['carrera'];
    }
}

$queryExport = http_build_query($paramsExport);
?>

<nav class="breadcrumb">
    <a href="<?= $base ?><?= $esAdmin ? '/admin' : '/dashboard' ?>">
        <?= $esAdmin ? 'Panel Administración' : 'Panel Docente' ?>
    </a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Reportes de Asistencia</span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title">Reportes de Asistencia</h1>
        <p class="page-subtitle">
            <?= $esAdmin ? 'Auditoría y exportación consolidada de asistencia institucional' : 'Filtra, consulta y exporta los registros de asistencia académica de tus materias' ?>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= $base ?><?= $esAdmin ? '/admin' : '/dashboard' ?>" class="btn btn-back" title="Regresar al panel principal">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            <?= $esAdmin ? 'Volver a Supervisión' : 'Volver al Panel QR' ?>
        </a>

        <a href="<?= $base ?>/reportes/csv?<?= $queryExport ?>" 
           class="btn btn-outline" title="Exportar reporte en formato CSV">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 4px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Descargar CSV
        </a>

        <a href="<?= $base ?>/reportes/excel?<?= $queryExport ?>" 
           class="btn btn-excel" title="Descargar libro de Microsoft Excel (.xls)">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 4px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>
            Descargar Excel
        </a>

        <a href="<?= $base ?>/reportes/pdf?<?= $queryExport ?>" 
           class="btn btn-pdf" title="Descargar documento oficial en PDF">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 4px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M12 18v-6"></path><path d="m9 15 3 3 3-3"></path></svg>
            Descargar PDF
        </a>
    </div>
</div>

<!-- Formulario de Filtros Multicriterio -->
<div class="card card-filter mb-6">
    <div class="d-flex justify-content-between align-center flex-wrap gap-2 mb-3">
        <span class="text-primary font-bold" style="font-size: 0.95rem;">Filtros de Búsqueda</span>
        <div class="d-flex gap-2 flex-wrap">
            <span class="text-muted" style="font-size: 0.8rem; align-self: center;">Periodo rápido:</span>
            <button type="button" onclick="establecerPeriodo('hoy')" class="chip-btn">Hoy</button>
            <button type="button" onclick="establecerPeriodo('mes')" class="chip-btn">Este Mes</button>
            <button type="button" onclick="establecerPeriodo('30dias')" class="chip-btn">Últimos 30 días</button>
            <button type="button" onclick="establecerPeriodo('todo')" class="chip-btn">Todo el Historial</button>
        </div>
    </div>

    <form method="GET" action="<?= $base ?>/reportes" class="filter-form-grid" id="formFiltrosReporte" onsubmit="return validarFiltrosReporte(event)">
        <div class="form-group mb-0">
            <label for="fecha_inicio" class="form-label">Fecha Desde</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" 
                   value="<?= htmlspecialchars($filtros['fecha_inicio'] ?? '') ?>" class="form-control">
        </div>

        <div class="form-group mb-0">
            <label for="fecha_fin" class="form-label">Fecha Hasta</label>
            <input type="date" id="fecha_fin" name="fecha_fin" 
                   value="<?= htmlspecialchars($filtros['fecha_fin'] ?? '') ?>" class="form-control">
        </div>

        <?php if ($esAdmin): ?>
            <div class="form-group mb-0">
                <label for="docente_id" class="form-label">Docente</label>
                <select id="docente_id" name="docente_id" class="form-select">
                    <option value="">-- Todos los Docentes --</option>
                    <?php foreach ($docentes as $d): ?>
                        <option value="<?= (int)$d['id'] ?>" <?= ((int)($filtros['docente_id'] ?? 0) === (int)$d['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-0">
                <label for="carrera" class="form-label">Carrera</label>
                <select id="carrera" name="carrera" class="form-select">
                    <option value="">-- Todas las Carreras --</option>
                    <option value="Desarrollo de Software" <?= ($filtros['carrera'] ?? '') === 'Desarrollo de Software' ? 'selected' : '' ?>>Desarrollo de Software</option>
                    <option value="Mecanica Automotriz" <?= ($filtros['carrera'] ?? '') === 'Mecanica Automotriz' ? 'selected' : '' ?>>Mecanica Automotriz</option>
                    <option value="Entrenamiento Deportivo" <?= ($filtros['carrera'] ?? '') === 'Entrenamiento Deportivo' ? 'selected' : '' ?>>Entrenamiento Deportivo</option>
                    <option value="Educacion Inicial" <?= ($filtros['carrera'] ?? '') === 'Educacion Inicial' ? 'selected' : '' ?>>Educacion Inicial</option>
                </select>
            </div>
        <?php endif; ?>

        <div class="form-group mb-0">
            <label for="materia" class="form-label">Materia</label>
            <input type="text" id="materia" name="materia" 
                   value="<?= htmlspecialchars($filtros['materia'] ?? '') ?>" 
                   placeholder="Ej: Programacion Web" class="form-control">
        </div>

        <div class="form-group mb-0">
            <label for="busqueda" class="form-label">Estudiante / Código</label>
            <input type="text" id="busqueda" name="busqueda" 
                   value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>" 
                   placeholder="Ej: EST001 o Pérez" class="form-control">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-1">Filtrar</button>
            <a href="<?= $base ?>/reportes" class="btn btn-outline" title="Restablecer filtros">Limpiar</a>
        </div>
    </form>

    <?php 
    $hayFiltrosActivos = !empty($filtros['materia']) || !empty($filtros['busqueda']) || !empty($filtros['carrera']) || !empty($filtros['docente_id']) || (!empty($filtros['fecha_inicio']) && $filtros['fecha_inicio'] !== date('Y-m-01'));
    if ($hayFiltrosActivos): ?>
        <div class="d-flex gap-2 flex-wrap align-center mt-3 pt-3" style="border-top: 1px dashed var(--color-border);">
            <span class="text-muted" style="font-size: 0.8rem; font-weight: 600;">Filtros activos:</span>
            <?php if (!empty($filtros['docente_id'])): ?>
                <span class="filter-pill">
                    Docente ID: <?= (int)$filtros['docente_id'] ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($filtros['carrera'])): ?>
                <span class="filter-pill">
                    Carrera: <?= htmlspecialchars($filtros['carrera']) ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($filtros['materia'])): ?>
                <span class="filter-pill">
                    Materia: <?= htmlspecialchars($filtros['materia']) ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($filtros['busqueda'])): ?>
                <span class="filter-pill">
                    Estudiante: <?= htmlspecialchars($filtros['busqueda']) ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($filtros['fecha_inicio'])): ?>
                <span class="filter-pill">
                    Desde: <?= htmlspecialchars($filtros['fecha_inicio']) ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($filtros['fecha_fin'])): ?>
                <span class="filter-pill">
                    Hasta: <?= htmlspecialchars($filtros['fecha_fin']) ?>
                </span>
            <?php endif; ?>
            <a href="<?= $base ?>/reportes" class="text-danger" style="font-size: 0.8rem; text-decoration: underline; margin-left: 4px;">
                Quitar todos
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function validarFiltrosReporte(e) {
    const inicio = document.getElementById('fecha_inicio')?.value;
    const fin = document.getElementById('fecha_fin')?.value;
    if (inicio && fin && inicio > fin) {
        alert('La fecha de inicio no puede ser posterior a la fecha final.');
        e.preventDefault();
        return false;
    }
    return true;
}

function establecerPeriodo(tipo) {
    const inicio = document.getElementById('fecha_inicio');
    const fin = document.getElementById('fecha_fin');
    const hoy = new Date();
    const formato = d => d.toISOString().split('T')[0];

    if (tipo === 'hoy') {
        inicio.value = formato(hoy);
        fin.value = formato(hoy);
    } else if (tipo === 'mes') {
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        inicio.value = formato(primerDia);
        fin.value = formato(hoy);
    } else if (tipo === '30dias') {
        const hace30 = new Date();
        hace30.setDate(hoy.getDate() - 30);
        inicio.value = formato(hace30);
        fin.value = formato(hoy);
    } else if (tipo === 'todo') {
        inicio.value = '';
        fin.value = '';
    }
    document.getElementById('formFiltrosReporte').submit();
}
</script>

<!-- Tabla de Reporte -->
<div class="card">
    <div class="card-header-flex">
        <h3 class="text-primary font-bold" style="font-size: 1.15rem;">Resultados Encontrados</h3>
        <span class="badge badge-neutral">Total: <?= count($asistencias) ?> registros</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Código</th>
                    <th>Estudiante</th>
                    <th>Carrera</th>
                    <th>Materia</th>
                    <th>Docente</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($asistencias)): ?>
                    <tr>
                        <td colspan="7" class="table-empty">
                            No se encontraron asistencias para el periodo o criterio seleccionado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($asistencias as $a): ?>
                        <tr>
                            <td class="font-semibold"><?= htmlspecialchars($a['fecha']) ?></td>
                            <td class="font-semibold text-primary"><?= htmlspecialchars($a['hora']) ?></td>
                            <td class="table-code"><?= htmlspecialchars($a['codigo']) ?></td>
                            <td class="font-medium"><?= htmlspecialchars($a['estudiante']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($a['carrera'] ?? '') ?></td>
                            <td><?= htmlspecialchars($a['materia']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($a['docente']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
