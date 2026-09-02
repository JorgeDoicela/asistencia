<?php
$base = $base ?? '';
$titulo = '404 - Pagina No Encontrada';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="content-narrow">
    <div class="card text-center">
        <span class="auth-badge mb-4">ERROR 404</span>
        <h1 class="error-404-number">404</h1>
        <h2 class="text-primary font-bold mb-2" style="font-size: 1.4rem;">
            Pagina No Encontrada
        </h2>
        <p class="text-muted mb-6" style="font-size: 0.95rem;">
            La direccion web a la que intentas acceder no existe o fue movida.
        </p>

        <div class="d-flex flex-column gap-2">
            <?php if (!empty($_SESSION['docente_id'])): ?>
                <a href="<?= $base ?>/dashboard" class="btn btn-primary btn-block">
                    Ir a Mi Panel Docente &rarr;
                </a>
            <?php elseif (!empty($_SESSION['estudiante_id'])): ?>
                <a href="<?= $base ?>/estudiante/portal" class="btn btn-primary btn-block">
                    Ir a Mi Expediente Estudiantil &rarr;
                </a>
            <?php endif; ?>
            <a href="<?= $base ?>/" class="btn btn-outline btn-block">
                &larr; Volver a la Página Principal
            </a>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
