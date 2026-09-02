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
            <h2 class="auth-title">Acceso Docentes</h2>
            <p class="auth-subtitle">Ingresa tus credenciales para administrar tus clases</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Asistente de Credenciales para Evaluacion -->
        <div class="demo-credentials-box">
            <div class="demo-credentials-info">
                <strong>Docente Demo:</strong><br>
                Usuario: <code>profesor</code> &bull; Clave: <code>12345</code>
            </div>
            <button type="button" onclick="cargarDemo()" class="demo-credentials-btn">
                Autocompletar
            </button>
        </div>

        <form action="<?= $base ?>/login" method="POST">
            <div class="form-group">
                <label for="usuario" class="form-label">Usuario del Docente</label>
                <input type="text" id="usuario" name="usuario" class="form-control" required autofocus placeholder="Ej: profesor o Demo">
            </div>

            <div class="form-group mb-6">
                <label for="password" class="form-label">Contrasena</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Ingresar al Panel Docente &rarr;
            </button>
        </form>

        <div class="auth-footer">
            <a href="<?= $base ?>/">&larr; Volver al inicio</a>
        </div>
    </div>
</div>

<script>
function cargarDemo() {
    document.getElementById('usuario').value = 'profesor';
    document.getElementById('password').value = '12345';
}
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
