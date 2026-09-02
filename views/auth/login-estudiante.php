<?php
$titulo = 'Portal Estudiante - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="max-width: 440px; margin: 40px auto;">
    <div style="background: white; border-radius: 12px; padding: 36px 32px; box-shadow: 0 4px 25px rgba(0,0,0,0.08); border-top: 6px solid var(--dorado);">
        <div style="text-align: center; margin-bottom: 24px;">
            <span style="background: var(--azul-marino); color: var(--dorado); font-size: 0.75rem; padding: 4px 10px; border-radius: 4px; font-weight: 700; letter-spacing: 0.5px;">ESTUDIANTES</span>
            <h2 style="color: var(--azul-marino); margin-top: 10px; font-size: 1.6rem; font-weight: 700;">Portal de Asistencias</h2>
            <p style="color: var(--texto-secundario); font-size: 0.9rem; margin-top: 4px;">Ingresa tu código estudiantil registrado</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= $base ?>/login-estudiante" method="POST">
            <div style="margin-bottom: 24px;">
                <label for="codigo" style="display: block; font-size: 0.88rem; font-weight: 600; color: var(--texto-principal); margin-bottom: 6px;">Código de Estudiante</label>
                <input type="text" id="codigo" name="codigo" required autofocus
                       style="width: 100%; padding: 12px 14px; border: 1px solid var(--borde); border-radius: 6px; font-size: 1.05rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; outline: none; transition: border-color 0.2s;"
                       placeholder="Ej: EST001">
            </div>

            <button type="submit" class="btn btn-dorado" style="width: 100%; padding: 12px; font-size: 1rem;">
                Consultar Mis Registros
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--borde);">
            <a href="<?= $base ?>/" style="color: var(--texto-secundario); text-decoration: none; font-size: 0.88rem; font-weight: 500;">
                &larr; Volver a la página principal
            </a>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
