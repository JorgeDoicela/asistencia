<?php
$titulo = 'Gestión de Estudiantes - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 style="color: var(--azul-marino); font-size: 1.8rem; font-weight: 800;">Gestión de Estudiantes</h1>
        <p style="color: var(--texto-secundario); font-size: 0.95rem;">Total de estudiantes registrados: <strong><?= $total ?></strong></p>
    </div>
    <button onclick="abrirModalCrear()" class="btn btn-primary">
        + Registrar Nuevo Estudiante
    </button>
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

<!-- Barra de Búsqueda -->
<div style="background: white; border-radius: 10px; padding: 18px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); margin-bottom: 24px;">
    <form method="GET" action="<?= $base ?>/estudiantes" style="display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>"
               placeholder="Buscar por código, nombres o carrera..."
               style="flex: 1; min-width: 260px; padding: 10px 14px; border: 1px solid var(--borde); border-radius: 6px; font-size: 0.95rem;">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($busqueda)): ?>
            <a href="<?= $base ?>/estudiantes" class="btn btn-outline">Limpiar Filtro</a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabla de Estudiantes -->
<div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--borde); color: var(--texto-secundario);">
                    <th style="padding: 12px;">Código</th>
                    <th style="padding: 12px;">Nombres</th>
                    <th style="padding: 12px;">Apellidos</th>
                    <th style="padding: 12px;">Carrera</th>
                    <th style="padding: 12px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estudiantes)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: var(--texto-secundario);">
                            No se encontraron estudiantes registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($estudiantes as $e): ?>
                        <tr style="border-bottom: 1px solid var(--borde);">
                            <td style="padding: 12px; font-family: monospace; font-weight: 700; color: var(--azul-marino);">
                                <?= htmlspecialchars($e['codigo']) ?>
                            </td>
                            <td style="padding: 12px; font-weight: 500;"><?= htmlspecialchars($e['nombre']) ?></td>
                            <td style="padding: 12px; font-weight: 500;"><?= htmlspecialchars($e['apellido'] ?? '') ?></td>
                            <td style="padding: 12px; color: var(--texto-secundario);"><?= htmlspecialchars($e['carrera'] ?? '') ?></td>
                            <td style="padding: 12px; text-align: right;">
                                <button type="button" 
                                        onclick="abrirModalEditar(<?= htmlspecialchars(json_encode($e)) ?>)"
                                        class="btn btn-outline" style="padding: 6px 12px; font-size: 0.82rem; margin-right: 6px;">
                                    Editar
                                </button>
                                <form method="POST" action="<?= $base ?>/estudiantes/eliminar" style="display: inline-block;"
                                      onsubmit="return confirm('¿Seguro que desea eliminar al estudiante <?= htmlspecialchars($e['nombre']) ?>? Esta acción no se puede deshacer.');">
                                    <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.82rem;">
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
<div id="modalEstudiante" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 30px; width: 100%; max-width: 480px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h3 id="modalTitulo" style="color: var(--azul-marino); margin-bottom: 18px; font-size: 1.3rem;">Nuevo Estudiante</h3>
        
        <form id="formEstudiante" method="POST" action="<?= $base ?>/estudiantes/crear">
            <input type="hidden" id="estudiante_id" name="id" value="">

            <div style="margin-bottom: 14px;">
                <label for="modal_codigo" style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Código Único</label>
                <input type="text" id="modal_codigo" name="codigo" required placeholder="Ej: EST009"
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--borde); border-radius: 6px; font-family: monospace; font-weight: 700; text-transform: uppercase;">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="modal_nombre" style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Nombres</label>
                <input type="text" id="modal_nombre" name="nombre" required placeholder="Nombres del estudiante"
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--borde); border-radius: 6px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="modal_apellido" style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Apellidos</label>
                <input type="text" id="modal_apellido" name="apellido" required placeholder="Apellidos del estudiante"
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--borde); border-radius: 6px;">
            </div>

            <div style="margin-bottom: 22px;">
                <label for="modal_carrera" style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px;">Carrera</label>
                <select id="modal_carrera" name="carrera" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--borde); border-radius: 6px;">
                    <option value="">-- Seleccione Carrera --</option>
                    <option value="Desarrollo de Software">Desarrollo de Software</option>
                    <option value="Mecánica Automotriz">Mecánica Automotriz</option>
                    <option value="Entrenamiento Deportivo">Entrenamiento Deportivo</option>
                    <option value="Educación Inicial">Educación Inicial</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
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

function abrirModalCrear() {
    modalTitulo.textContent = 'Registrar Nuevo Estudiante';
    form.action = '<?= $base ?>/estudiantes/crear';
    idInput.value = '';
    codigoInput.value = '';
    nombreInput.value = '';
    apellidoInput.value = '';
    carreraInput.value = '';
    modal.style.display = 'flex';
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
}

function cerrarModal() {
    modal.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target === modal) {
        cerrarModal();
    }
}
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
