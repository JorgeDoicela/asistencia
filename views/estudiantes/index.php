<?php
$titulo = 'Gestion de Estudiantes - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
$esAdmin = $esAdmin ?? false;
?>

<nav class="breadcrumb">
    <a href="<?= $base ?><?= $esAdmin ? '/admin' : '/dashboard' ?>">
        <?= $esAdmin ? 'Panel Administración' : 'Panel Docente' ?>
    </a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Gestión de Estudiantes</span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title">Gestión de Estudiantes</h1>
        <p class="page-subtitle">Total de estudiantes registrados: <strong><?= $total ?></strong></p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= $base ?><?= $esAdmin ? '/admin' : '/dashboard' ?>" class="btn btn-back" title="Regresar al panel principal">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            <?= $esAdmin ? 'Volver a Supervisión' : 'Volver al Panel QR' ?>
        </a>
        <button onclick="abrirModalCrear()" class="btn btn-primary">
            + Registrar Nuevo Estudiante
        </button>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-success">
        <span><?= htmlspecialchars($mensaje) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<!-- Barra de Busqueda -->
<div class="card card-filter mb-6">
    <form method="GET" action="<?= $base ?>/estudiantes" class="search-bar-form">
        <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>"
               placeholder="Buscar por codigo, nombres o carrera..."
               class="form-control search-bar-input">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($busqueda)): ?>
            <a href="<?= $base ?>/estudiantes" class="btn btn-outline">Limpiar Filtro</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($busqueda)): ?>
        <div class="mt-3 pt-2 d-flex align-center gap-2" style="border-top: 1px dashed var(--color-border);">
            <span class="text-muted" style="font-size: 0.85rem;">Resultados para:</span>
            <span class="filter-pill">"<?= htmlspecialchars($busqueda) ?>"</span>
            <span class="text-muted" style="font-size: 0.82rem;">(<?= count($estudiantes) ?> encontrados)</span>
            <a href="<?= $base ?>/estudiantes" class="text-danger font-medium" style="font-size: 0.82rem; text-decoration: underline; margin-left: 4px;">Restablecer lista</a>
        </div>
    <?php endif; ?>
</div>

<!-- Tabla de Estudiantes -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Carrera</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estudiantes)): ?>
                    <tr>
                        <td colspan="5" class="table-empty">
                            No se encontraron estudiantes registrados<?= !empty($busqueda) ? ' con el término buscado' : '' ?>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($estudiantes as $e): ?>
                        <tr>
                            <td class="table-code"><?= htmlspecialchars($e['codigo']) ?></td>
                            <td class="font-medium"><?= htmlspecialchars($e['nombre']) ?></td>
                            <td class="font-medium"><?= htmlspecialchars($e['apellido'] ?? '') ?></td>
                            <td class="text-muted"><?= htmlspecialchars($e['carrera'] ?? '') ?></td>
                            <td class="text-right">
                                <button type="button" 
                                        onclick="abrirModalEditar(<?= htmlspecialchars(json_encode($e)) ?>)"
                                        class="btn btn-outline btn-sm">
                                    Editar
                                </button>
                                <form method="POST" action="<?= $base ?>/estudiantes/eliminar" style="display: inline-block; margin-left: 4px;"
                                      onsubmit="return confirm('Seguro que desea eliminar al estudiante <?= htmlspecialchars($e['nombre']) ?>?');">
                                    <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL CREAR / EDITAR ESTUDIANTE -->
<div id="modalEstudiante" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header-row">
            <h3 id="modalTitulo" class="modal-title mb-0" style="font-size: 1.25rem;">Registrar Nuevo Estudiante</h3>
            <button type="button" onclick="cerrarModal()" class="modal-close-btn" title="Cerrar ventana">&times;</button>
        </div>
        
        <form id="formEstudiante" method="POST" action="<?= $base ?>/estudiantes/crear" onsubmit="return validarEstudiante(event)">
            <input type="hidden" id="estudiante_id" name="id" value="">

            <div class="form-group">
                <div class="d-flex justify-between align-center mb-1">
                    <label for="modal_codigo" class="form-label mb-0">Código Único Institucional <span class="text-danger">*</span></label>
                    <button type="button" onclick="generarCodigoSugerido()" class="chip-btn" style="font-size: 0.74rem; padding: 2px 8px;" title="Regenerar código correlativo">
                        Autogenerar
                    </button>
                </div>
                <input type="text" id="modal_codigo" name="codigo" required 
                       minlength="3" maxlength="15"
                       pattern="^[A-Za-z0-9_-]{3,15}$"
                       title="Entre 3 y 15 caracteres alfanuméricos (ej: EST009)"
                       placeholder="Ej: EST009"
                       class="form-control form-control-code"
                       style="text-transform: uppercase;"
                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, '')">
                <small class="text-muted" style="display: block; margin-top: 3px; font-size: 0.78rem;">
                    Se sugiere automáticamente el siguiente correlativo. Puedes editarlo libremente.
                </small>
            </div>

            <div class="form-group">
                <label for="modal_nombre" class="form-label">Nombres <span class="text-danger">*</span></label>
                <input type="text" id="modal_nombre" name="nombre" required 
                       minlength="2" maxlength="50"
                       pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                       title="Solo letras y espacios (mínimo 2 caracteres)"
                       placeholder="Ej: Juan Carlos"
                       class="form-control">
            </div>

            <div class="form-group">
                <label for="modal_apellido" class="form-label">Apellidos <span class="text-danger">*</span></label>
                <input type="text" id="modal_apellido" name="apellido" required 
                       minlength="2" maxlength="50"
                       pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                       title="Solo letras y espacios (mínimo 2 caracteres)"
                       placeholder="Ej: Pérez Rodríguez"
                       class="form-control">
            </div>

            <div class="form-group mb-6">
                <label for="modal_carrera" class="form-label">Carrera Técnica <span class="text-danger">*</span></label>
                <select id="modal_carrera" name="carrera" class="form-select" required>
                    <option value="" disabled selected>-- Seleccione Carrera --</option>
                    <option value="Desarrollo de Software">Desarrollo de Software</option>
                    <option value="Mecanica Automotriz">Mecanica Automotriz</option>
                    <option value="Entrenamiento Deportivo">Entrenamiento Deportivo</option>
                    <option value="Educacion Inicial">Educacion Inicial</option>
                </select>
                <small class="text-muted" style="display: block; margin-top: 3px; font-size: 0.78rem;">
                    Selecciona una carrera válida de la lista institucional.
                </small>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="cerrarModal()" class="btn btn-outline">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar Estudiante</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('modalEstudiante');
const form = document.getElementById('formEstudiante');
const modalTitulo = document.getElementById('modalTitulo');
const idInput = document.getElementById('estudiante_id');
const codigoInput = document.getElementById('modal_codigo');
const nombreInput = document.getElementById('modal_nombre');
const apellidoInput = document.getElementById('modal_apellido');
const carreraInput = document.getElementById('modal_carrera');

function validarEstudiante(e) {
    const cod = codigoInput.value.trim();
    const nom = nombreInput.value.trim();
    const ape = apellidoInput.value.trim();
    const car = carreraInput.value.trim();

    if (!cod || cod.length < 3) {
        alert('Por favor ingresa un código de estudiante válido (mínimo 3 caracteres).');
        codigoInput.focus();
        e.preventDefault();
        return false;
    }
    if (!nom || nom.length < 2) {
        alert('Por favor ingresa los nombres del estudiante (mínimo 2 caracteres).');
        nombreInput.focus();
        e.preventDefault();
        return false;
    }
    if (!ape || ape.length < 2) {
        alert('Por favor ingresa los apellidos del estudiante (mínimo 2 caracteres).');
        apellidoInput.focus();
        e.preventDefault();
        return false;
    }
    if (!car) {
        alert('Por favor selecciona una carrera técnica de la lista.');
        carreraInput.focus();
        e.preventDefault();
        return false;
    }
    return true;
}

const codigoSugeridoPorDefecto = '<?= $siguienteCodigo ?? "EST009" ?>';

function generarCodigoSugerido() {
    codigoInput.value = codigoSugeridoPorDefecto;
}

function abrirModalCrear() {
    modalTitulo.textContent = 'Registrar Nuevo Estudiante';
    form.action = '<?= $base ?>/estudiantes/crear';
    idInput.value = '';
    codigoInput.value = codigoSugeridoPorDefecto; // Sugerido automáticamente, pero editable
    nombreInput.value = '';
    apellidoInput.value = '';
    carreraInput.value = '';
    modal.style.display = 'flex';
    setTimeout(() => nombreInput.focus(), 60); // Enfoca directo en el nombre para escribir de inmediato
}

function abrirModalEditar(estudiante) {
    modalTitulo.textContent = 'Editar Estudiante: ' + estudiante.codigo;
    form.action = '<?= $base ?>/estudiantes/actualizar';
    idInput.value = estudiante.id;
    codigoInput.value = estudiante.codigo;
    nombreInput.value = estudiante.nombre;
    apellidoInput.value = estudiante.apellido || '';
    carreraInput.value = estudiante.carrera || '';
    modal.style.display = 'flex';
    setTimeout(() => nombreInput.focus(), 60);
}

function cerrarModal() {
    modal.style.display = 'none';
}

window.addEventListener('click', function(event) {
    if (event.target === modal) {
        cerrarModal();
    }
});

window.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && modal.style.display === 'flex') {
        cerrarModal();
    }
});
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
