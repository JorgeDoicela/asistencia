<?php
$titulo = 'Acceso Docente - ISTPET';
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
            <h2 class="auth-title">Acceso Institucional</h2>
            <p class="auth-subtitle">Ingresa tus credenciales de Docente o Administrador</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Asistente de Credenciales para Evaluación -->
        <div class="demo-credentials-box" style="flex-direction: column; align-items: stretch; gap: 8px;">
            <div class="d-flex justify-content-between align-center">
                <div class="demo-credentials-info">
                    <strong>Administrador:</strong> <code>admin</code> / <code>admin123</code>
                </div>
                <button type="button" onclick="cargarAdmin()" class="demo-credentials-btn">
                    Cargar Admin
                </button>
            </div>
            <div class="d-flex justify-content-between align-center pt-2" style="border-top: 1px dashed var(--color-border);">
                <div class="demo-credentials-info">
                    <strong>Docente Titular:</strong> <code>profesor</code> / <code>12345</code>
                </div>
                <button type="button" onclick="cargarDocente()" class="demo-credentials-btn">
                    Cargar Docente
                </button>
            </div>
        </div>

        <form action="<?= $base ?>/login" method="POST">
            <div class="form-group">
                <label for="usuario" class="form-label">Usuario Institucional <span class="text-danger">*</span></label>
                <input type="text" id="usuario" name="usuario" class="form-control" required autofocus minlength="3" maxlength="30" placeholder="Ej: admin o profesor" autocomplete="username">
            </div>

            <div class="form-group mb-6">
                <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                <input type="password" id="password" name="password" class="form-control" required minlength="4" maxlength="50" placeholder="••••••••" autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Ingresar al Sistema &rarr;
            </button>
        </form>

        <div class="auth-footer">
            <a href="<?= $base ?>/">&larr; Volver al inicio</a>
        </div>
    </div>
</div>

<script>
function cargarAdmin() {
    document.getElementById('usuario').value = 'admin';
    document.getElementById('password').value = 'admin123';
}

function cargarDocente() {
    document.getElementById('usuario').value = 'profesor';
    document.getElementById('password').value = '12345';
}
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
