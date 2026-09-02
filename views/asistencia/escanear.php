<?php
$titulo = 'Registrar Asistencia - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="auth-page-bg">
    <div class="auth-card">
        <div class="auth-top-bar">
            <?php if (!empty($_SESSION['estudiante_id'])): ?>
                <a href="<?= $base ?>/estudiante/portal" class="auth-top-back">
                    &larr; Volver a Mi Expediente
                </a>
            <?php elseif (!empty($_SESSION['docente_id'])): ?>
                <a href="<?= $base ?>/dashboard" class="auth-top-back">
                    &larr; Volver al Panel QR
                </a>
            <?php else: ?>
                <a href="<?= $base ?>/" class="auth-top-back">
                    &larr; Volver al Inicio
                </a>
            <?php endif; ?>
        </div>

        <!-- Logo Institucional Original -->
        <div class="auth-logo-wrap">
            <img src="<?= $base ?>/assets/img/logo-istpet.jpg" alt="Logo ISTPET" class="auth-logo-img">
            <h2 class="auth-title">Registro de Asistencia</h2>
            <p class="auth-subtitle">Confirma tu presencia en la clase activa</p>
        </div>

        <div class="steps-guide">
            <div class="step-item <?= !empty($codigoSesion) ? 'active' : '' ?>">
                <span class="step-num">1</span>
                <span>Sesión QR</span>
            </div>
            <div class="step-item <?= empty($codigoSesion) ? 'active' : '' ?>">
                <span class="step-num">2</span>
                <span>Tu Código</span>
            </div>
            <div class="step-item">
                <span class="step-num">3</span>
                <span>Confirmación</span>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($sesion)): ?>
            <div class="access-code-box mb-6 text-center">
                <span class="access-code-title">Clase Activa Encontrada:</span>
                <strong style="font-size: 1.15rem; color: var(--color-primary); display: block; margin-top: 2px;">
                    <?= htmlspecialchars($sesion['materia']) ?>
                </strong>
                <span style="font-size: 0.85rem; color: var(--color-text-muted);">
                    Docente: <?= htmlspecialchars($sesion['docente_nombre'] ?? 'Docente Titular') ?>
                </span>
            </div>
        <?php endif; ?>

        <!-- Asistente de Codigo Demo para Evaluacion -->
        <div class="demo-credentials-box">
            <div class="demo-credentials-info">
                <strong>Estudiante de Prueba:</strong><br>
                Codigo: <code>EST001</code> (Juan Perez)
            </div>
            <button type="button" onclick="cargarDemoAlumno()" class="demo-credentials-btn">
                Autocompletar
            </button>
        </div>

        <form action="<?= $base ?>/asistencia/registrar" method="POST">
            <div class="form-group">
                <div class="d-flex justify-content-between align-center mb-1">
                    <label for="codigo_sesion" class="form-label mb-0">Código de la Sesión</label>
                    <?php if (!empty($codigoSesion)): ?>
                        <span class="badge badge-success" style="font-size: 0.72rem; padding: 2px 8px;">✓ QR Detectado</span>
                    <?php endif; ?>
                </div>
                <input type="text" id="codigo_sesion" name="codigo_sesion" 
                       value="<?= htmlspecialchars($codigoSesion) ?>" 
                       required placeholder="Ej: A1B2C3D4"
                       class="form-control form-control-code"
                       style="text-transform: uppercase;"
                       oninput="this.value = this.value.toUpperCase().trim()"
                       <?= !empty($codigoSesion) ? 'readonly' : 'autofocus' ?>>
                <?php if (empty($codigoSesion)): ?>
                    <small class="text-muted" style="display: block; margin-top: 4px; font-size: 0.8rem;">
                        Ingresa el código de 8 caracteres que muestra el docente en la pantalla del aula.
                    </small>
                <?php endif; ?>
            </div>

            <div class="form-group mb-6">
                <label for="codigo_estudiante" class="form-label">Tu Código de Estudiante</label>
                <input type="text" id="codigo_estudiante" name="codigo_estudiante" 
                       required placeholder="Ej: EST001"
                       class="form-control form-control-code"
                       style="text-transform: uppercase;"
                       oninput="this.value = this.value.toUpperCase().trim()"
                       <?= !empty($codigoSesion) ? 'autofocus' : '' ?>>
                <small class="text-muted" style="display: block; margin-top: 4px; font-size: 0.8rem;">
                    Tu código institucional asignado al matricularte (ej. EST001).
                </small>
            </div>

            <button type="submit" class="btn btn-dorado btn-block btn-lg">
                Confirmar Mi Asistencia &rarr;
            </button>
        </form>

        <div class="auth-footer">
            <a href="<?= $base ?>/">&larr; Volver al inicio</a>
        </div>
    </div>
</div>

<script>
function cargarDemoAlumno() {
    document.getElementById('codigo_estudiante').value = 'EST001';
}
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
