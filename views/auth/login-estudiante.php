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
                <label for="codigo" class="form-label">Tu Codigo de Estudiante</label>
                <input type="text" id="codigo" name="codigo" class="form-control form-control-code" required autofocus placeholder="Ej: EST001">
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
