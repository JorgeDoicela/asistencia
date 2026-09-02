<?php
$titulo = 'Registro de Asistencia - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="max-width: 480px; margin: 30px auto;">
    <div style="background: white; border-radius: 12px; padding: 32px 28px; box-shadow: 0 4px 25px rgba(0,0,0,0.08); border-top: 6px solid var(--azul-marino);">
        <div style="text-align: center; margin-bottom: 22px;">
            <span style="background: var(--dorado); color: var(--azul-marino); font-size: 0.75rem; padding: 4px 10px; border-radius: 4px; font-weight: 700; letter-spacing: 0.5px;">ASISTENCIA ACADÉMICA</span>
            <h2 style="color: var(--azul-marino); margin-top: 8px; font-size: 1.5rem; font-weight: 700;">Registro de Asistencia</h2>
            <p style="color: var(--texto-secundario); font-size: 0.88rem; margin-top: 4px;">Instituto Superior Tecnológico Mayor Pedro Traversari</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($sesion): ?>
            <div style="background: #f8fafc; border: 1px solid var(--borde); border-radius: 8px; padding: 14px 16px; margin-bottom: 20px;">
                <p style="font-size: 0.82rem; color: var(--texto-secundario); margin-bottom: 2px;">Materia / Clase:</p>
                <p style="font-size: 1.05rem; font-weight: 700; color: var(--azul-marino);"><?= htmlspecialchars($sesion['materia']) ?></p>
                <p style="font-size: 0.85rem; color: var(--texto-secundario); margin-top: 4px;">Docente: <strong><?= htmlspecialchars($sesion['docente_nombre']) ?></strong></p>
            </div>
        <?php endif; ?>

        <form action="<?= $base ?>/asistencia/registrar" method="POST">
            <div style="margin-bottom: 16px;">
                <label for="codigo_sesion" style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Código de Sesión de Clase</label>
                <input type="text" id="codigo_sesion" name="codigo_sesion" required 
                       value="<?= htmlspecialchars($codigoSesion) ?>"
                       placeholder="Ej: A1B2C3D4"
                       style="width: 100%; padding: 12px; border: 1px solid var(--borde); border-radius: 6px; font-family: monospace; font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px;"
                       <?= !empty($codigoSesion) ? 'readonly' : '' ?>>
            </div>

            <div style="margin-bottom: 24px;">
                <label for="codigo_estudiante" style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Tu Código de Estudiante</label>
                <input type="text" id="codigo_estudiante" name="codigo_estudiante" required autofocus
                       placeholder="Ej: EST001"
                       style="width: 100%; padding: 12px; border: 1px solid var(--borde); border-radius: 6px; font-size: 1.05rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem;">
                Confirmar Asistencia
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--borde);">
            <a href="<?= $base ?>/" style="color: var(--texto-secundario); text-decoration: none; font-size: 0.85rem;">
                &larr; Volver al Inicio
            </a>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
