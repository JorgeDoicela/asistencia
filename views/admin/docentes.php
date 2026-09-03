<?php
$titulo = 'Gestión de Personal Docente - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<nav class="breadcrumb">
    <a href="<?= $base ?>/admin">Panel Administración</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Personal Docente</span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title">Personal Docente y Usuarios</h1>
        <p class="page-subtitle">Directorio institucional, asignación de roles y control de credenciales (Total: <strong><?= $total ?></strong>)</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= $base ?>/admin" class="btn btn-back" title="Regresar al panel de administración">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Volver a Supervisión
        </a>
        <button type="button" onclick="abrirModalCrear()" class="btn btn-primary">
            + Registrar Nuevo Usuario
        </button>
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

<!-- Barra de Búsqueda y Filtro por Rol -->
<div class="card card-filter mb-6">
    <form method="GET" action="<?= $base ?>/admin/docentes" class="search-bar-form">
        <input type="text" name="buscar" value="<?= htmlspecialchars($busqueda) ?>"
               placeholder="Buscar por nombre o usuario institucional..."
               class="form-control search-bar-input">
        
        <select name="rol" class="form-select" style="max-width: 180px;">
            <option value="">Todos los Roles</option>
            <option value="docente" <?= $rolFiltro === 'docente' ? 'selected' : '' ?>>Solo Docentes</option>
            <option value="admin" <?= $rolFiltro === 'admin' ? 'selected' : '' ?>>Administradores</option>
        </select>

        <button type="submit" class="btn btn-primary">Buscar</button>
        <?php if (!empty($busqueda) || !empty($rolFiltro)): ?>
            <a href="<?= $base ?>/admin/docentes" class="btn btn-outline">Limpiar Filtros</a>
        <?php endif; ?>
    </form>
</div>

<!-- Tabla de Personal -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre y Apellidos</th>
                    <th>Usuario</th>
                    <th>Rol Institucional</th>
                    <th>Estado</th>
                    <th style="text-align: center;">Clases</th>
                    <th style="text-align: center;">Asistencias</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($docentes)): ?>
                    <tr>
                        <td colspan="7" class="table-empty">
                            No se encontraron usuarios registrados con los criterios indicados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($docentes as $d): 
                        $esMismoUsuario = ((int)$d['id'] === $usuarioActualId);
                        $esActivo = ((int)($d['activo'] ?? 1) === 1);
                    ?>
                        <tr>
                            <td>
                                <div class="font-bold text-primary"><?= htmlspecialchars($d['nombre']) ?></div>
                                <span class="text-muted" style="font-size: 0.8rem;">Registrado: <?= htmlspecialchars(substr($d['creado_en'] ?? '', 0, 10)) ?></span>
                            </td>
                            <td>
                                <span style="font-family: var(--font-mono); font-size: 0.88rem; font-weight: 600; color: var(--color-primary-dark);">
                                    <?= htmlspecialchars($d['usuario']) ?>
                                </span>
                                <?php if ($esMismoUsuario): ?>
                                    <span class="badge" style="background: #e2e8f0; color: #475569; font-size: 0.72rem; margin-left: 4px;">Tu cuenta</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($d['rol'] ?? 'docente') === 'admin'): ?>
                                    <span class="badge" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; font-weight: 700;">
                                        ADMINISTRADOR
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-info" style="font-weight: 600;">
                                        DOCENTE
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($esActivo): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; font-variant-numeric: tabular-nums;">
                                <?= (int)$d['total_sesiones'] ?>
                            </td>
                            <td style="text-align: center; font-variant-numeric: tabular-nums;">
                                <?= (int)$d['total_asistencias'] ?>
                            </td>
                            <td class="text-right">
                                <div class="d-flex gap-1 justify-content-end align-center flex-wrap">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline"
                                            onclick='abrirModalEditar(<?= json_encode($d, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            title="Editar información">
                                        Editar
                                    </button>

                                    <button type="button" 
                                            class="btn btn-sm btn-outline"
                                            onclick="abrirModalClave(<?= (int)$d['id'] ?>, '<?= htmlspecialchars(addslashes($d['nombre'])) ?>')"
                                            title="Restablecer contraseña">
                                        Clave
                                    </button>

                                    <?php if (!$esMismoUsuario): ?>
                                        <form method="POST" action="<?= $base ?>/admin/docentes/cambiar-estado" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                                            <input type="hidden" name="activo" value="<?= $esActivo ? 0 : 1 ?>">
                                            <button type="submit" 
                                                    class="btn btn-sm <?= $esActivo ? 'btn-outline' : 'btn-dorado' ?>"
                                                    style="<?= $esActivo ? 'color: var(--color-text-muted); border-color: var(--color-border);' : '' ?>"
                                                    title="<?= $esActivo ? 'Desactivar acceso' : 'Habilitar acceso' ?>">
                                                <?= $esActivo ? 'Desactivar' : 'Activar' ?>
                                            </button>
                                        </form>

                                        <?php if ((int)$d['total_sesiones'] === 0): ?>
                                            <form method="POST" action="<?= $base ?>/admin/docentes/eliminar" style="display: inline;" onsubmit="return confirm('¿Seguro que desea eliminar a este docente?');">
                                                <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline" style="color: var(--color-danger-text); border-color: var(--color-danger-border);" title="Eliminar registro">
                                                    Eliminar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal 1: Crear Usuario -->
<div id="modalCrearDocente" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Registrar Nuevo Usuario</h3>
            <button type="button" onclick="cerrarModal('modalCrearDocente')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="<?= $base ?>/admin/docentes/crear" id="formCrearDocente">
            <div class="form-group">
                <label for="crear_nombre" class="form-label">Nombre Completo con Título <span class="text-danger">*</span></label>
                <input type="text" id="crear_nombre" name="nombre" required minlength="3" maxlength="150"
                       placeholder="Ej: Ing. Carlos Morales" class="form-control">
            </div>

            <div class="form-group">
                <label for="crear_usuario" class="form-label">Nombre de Usuario (Login) <span class="text-danger">*</span></label>
                <input type="text" id="crear_usuario" name="usuario" required minlength="3" maxlength="100"
                       pattern="^[a-zA-Z0-9_.-]+$"
                       title="Solo caracteres alfanuméricos, guiones o puntos"
                       placeholder="Ej: cmorales" class="form-control">
                <small class="text-muted" style="display: block; margin-top: 3px; font-size: 0.78rem;">
                    Identificador único para iniciar sesión en el portal.
                </small>
            </div>

            <div class="form-group">
                <label for="crear_password" class="form-label">Contraseña Inicial <span class="text-danger">*</span></label>
                <input type="password" id="crear_password" name="password" required minlength="4" maxlength="100"
                       placeholder="••••••••" class="form-control">
            </div>

            <div class="form-group mb-6">
                <label for="crear_rol" class="form-label">Rol Asignado <span class="text-danger">*</span></label>
                <select id="crear_rol" name="rol" class="form-select" required>
                    <option value="docente" selected>Docente (Generar QR y gestionar clases)</option>
                    <option value="admin">Administrador (Supervisión, personal y reportes globales)</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="cerrarModal('modalCrearDocente')" class="btn btn-outline">Cancelar</button>
                <button type="submit" class="btn btn-primary">Registrar Usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Editar Usuario -->
<div id="modalEditarDocente" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Editar Usuario</h3>
            <button type="button" onclick="cerrarModal('modalEditarDocente')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="<?= $base ?>/admin/docentes/actualizar" id="formEditarDocente">
            <input type="hidden" id="editar_id" name="id">

            <div class="form-group">
                <label for="editar_nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                <input type="text" id="editar_nombre" name="nombre" required minlength="3" maxlength="150" class="form-control">
            </div>

            <div class="form-group">
                <label for="editar_usuario" class="form-label">Usuario <span class="text-danger">*</span></label>
                <input type="text" id="editar_usuario" name="usuario" required minlength="3" maxlength="100" class="form-control">
            </div>

            <div class="form-group">
                <label for="editar_rol" class="form-label">Rol Institucional <span class="text-danger">*</span></label>
                <select id="editar_rol" name="rol" class="form-select" required>
                    <option value="docente">Docente</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>

            <div class="form-group mb-6">
                <label for="editar_activo" class="form-label">Estado de la Cuenta <span class="text-danger">*</span></label>
                <select id="editar_activo" name="activo" class="form-select" required>
                    <option value="1">Activo (Permite iniciar sesión)</option>
                    <option value="0">Inactivo (Acceso bloqueado)</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="cerrarModal('modalEditarDocente')" class="btn btn-outline">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Restablecer Contraseña -->
<div id="modalClaveDocente" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Restablecer Contraseña</h3>
            <button type="button" onclick="cerrarModal('modalClaveDocente')" class="modal-close">&times;</button>
        </div>
        <form method="POST" action="<?= $base ?>/admin/docentes/resetear-password">
            <input type="hidden" id="clave_id" name="id">

            <p class="text-muted mb-4" style="font-size: 0.88rem;">
                Asigna una nueva clave para: <strong id="clave_nombre_display" class="text-primary"></strong>
            </p>

            <div class="form-group mb-6">
                <label for="clave_nueva" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                <input type="password" id="clave_nueva" name="password" required minlength="4" maxlength="100"
                       placeholder="••••••••" class="form-control">
                <small class="text-muted" style="display: block; margin-top: 3px; font-size: 0.78rem;">
                    Mínimo 4 caracteres. La contraseña se guardará encriptada con Bcrypt.
                </small>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="cerrarModal('modalClaveDocente')" class="btn btn-outline">Cancelar</button>
                <button type="submit" class="btn btn-dorado">Actualizar Contraseña</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalCrear() {
    document.getElementById('formCrearDocente').reset();
    document.getElementById('modalCrearDocente').style.display = 'flex';
}

function abrirModalEditar(docente) {
    document.getElementById('editar_id').value = docente.id;
    document.getElementById('editar_nombre').value = docente.nombre;
    document.getElementById('editar_usuario').value = docente.usuario;
    document.getElementById('editar_rol').value = docente.rol || 'docente';
    document.getElementById('editar_activo').value = (docente.activo !== undefined && docente.activo !== null) ? docente.activo : 1;
    document.getElementById('modalEditarDocente').style.display = 'flex';
}

function abrirModalClave(id, nombre) {
    document.getElementById('clave_id').value = id;
    document.getElementById('clave_nombre_display').textContent = nombre;
    document.getElementById('clave_nueva').value = '';
    document.getElementById('modalClaveDocente').style.display = 'flex';
}

function cerrarModal(idModal) {
    document.getElementById(idModal).style.display = 'none';
}

// Cerrar modales con la tecla Escape o al hacer clic fuera del modal
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal('modalCrearDocente');
        cerrarModal('modalEditarDocente');
        cerrarModal('modalClaveDocente');
    }
});

document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
