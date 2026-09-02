<?php
$titulo = 'Portal Estudiante - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="auth-page-bg">
    <div class="auth-card">
        <div class="auth-top-bar">
            <a href="<?= $base ?>/" class="auth-top-back">
                &larr; Volver al Inicio
            </a>
        </div>

        <!-- Logo Institucional Original -->
        <div class="auth-logo-wrap">
            <img src="<?= $base ?>/assets/img/logo-istpet.jpg" alt="Logo ISTPET" class="auth-logo-img">
            <h2 class="auth-title">Portal Estudiantil</h2>
            <p class="auth-subtitle">Consulta tu historial de asistencias a clases</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Asistente de Codigo Demo para Evaluacion -->
        <div class="demo-credentials-box">
            <div class="demo-credentials-info">
                <strong>Estudiante de Prueba:</strong><br>
                Codigo: <code>EST001</code> (Juan Perez)
            </div>
            <button type="button" onclick="cargarDemoEstudiante()" class="demo-credentials-btn">
                Autocompletar
            </button>
        </div>

        <form action="<?= $base ?>/login-estudiante" method="POST">
            <div class="form-group mb-6">
                <label for="codigo" class="form-label">Tu Código de Estudiante <span class="text-danger">*</span></label>
                <input type="text" id="codigo" name="codigo" 
                       class="form-control form-control-code" 
                       required autofocus 
                       minlength="3" maxlength="15"
                       pattern="^[A-Za-z0-9_-]{3,15}$"
                       title="Código institucional de 3 a 15 caracteres (ej: EST001)"
                       placeholder="Ej: EST001"
                       style="text-transform: uppercase;"
                       oninput="this.value = this.value.toUpperCase().replace(/\s+/g, '')">
                <small class="text-muted" style="display: block; margin-top: 4px; font-size: 0.8rem;">
                    Ingresa el código alfanumérico que te asignó el ISTPET.
                </small>
            </div>

            <button type="submit" class="btn btn-dorado btn-block btn-lg">
                Consultar Mis Registros &rarr;
            </button>
        </form>

        <div class="auth-footer">
            <p class="mb-2">
                <a href="<?= $base ?>/asistencia/escanear" class="text-primary font-bold">
                    &iquest;Deseas registrar asistencia en una clase en vivo? Registrar aqui &rarr;
                </a>
            </p>
            <a href="<?= $base ?>/">&larr; Volver al inicio</a>
        </div>
    </div>
</div>

<script>
function cargarDemoEstudiante() {
    document.getElementById('codigo').value = 'EST001';
}
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
