<?php
$titulo = ($exito ?? false) ? '¡Asistencia Registrada!' : 'Aviso de Asistencia';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="max-width: 500px; margin: 40px auto; text-align: center;">
    <div style="background: white; border-radius: 12px; padding: 40px 32px; box-shadow: 0 4px 25px rgba(0,0,0,0.08); border-top: 6px solid <?= ($exito ?? false) ? 'var(--exito)' : 'var(--peligro)' ?>;">
        
        <?php if ($exito ?? false): ?>
            <div style="width: 50px; height: 50px; background: #ecfdf5; color: var(--exito); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; margin: 0 auto 18px;">
                OK
            </div>
            <h2 style="color: var(--azul-marino); font-size: 1.6rem; margin-bottom: 8px;">Asistencia Registrada</h2>
            <p style="color: var(--texto-secundario); font-size: 0.95rem; margin-bottom: 24px;"><?= htmlspecialchars($mensaje) ?></p>

            <div style="background: #f8fafc; border: 1px solid var(--borde); border-radius: 10px; padding: 18px; text-align: left; margin-bottom: 24px;">
                <p style="font-size: 0.85rem; color: var(--texto-secundario); margin-bottom: 2px;">Estudiante:</p>
                <p style="font-size: 1.05rem; font-weight: 700; color: var(--azul-marino);"><?= htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido']) ?></p>
                
                <p style="font-size: 0.85rem; color: var(--texto-secundario); margin-top: 10px; margin-bottom: 2px;">Carrera:</p>
                <p style="font-size: 0.95rem; font-weight: 600;"><?= htmlspecialchars($estudiante['carrera']) ?></p>

                <p style="font-size: 0.85rem; color: var(--texto-secundario); margin-top: 10px; margin-bottom: 2px;">Materia:</p>
                <p style="font-size: 0.95rem; font-weight: 600;"><?= htmlspecialchars($sesion['materia']) ?></p>

                <p style="font-size: 0.85rem; color: var(--texto-secundario); margin-top: 10px; margin-bottom: 2px;">Hora de Registro:</p>
                <p style="font-size: 0.95rem; font-weight: 700; color: var(--exito);"><?= htmlspecialchars($hora ?? date('H:i:s')) ?></p>
            </div>
        <?php else: ?>
            <div style="width: 50px; height: 50px; background: #fef2f2; color: var(--peligro); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 700; margin: 0 auto 18px;">
                NO
            </div>
            <h2 style="color: var(--peligro); font-size: 1.5rem; margin-bottom: 8px;">No se pudo registrar</h2>
            <p style="color: var(--texto-principal); font-size: 0.95rem; margin-bottom: 24px; line-height: 1.5;"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="<?= $base ?>/asistencia/escanear" class="btn btn-outline" style="width: 100%;">
                Registrar Otra Asistencia
            </a>
            <a href="<?= $base ?>/" class="btn btn-primary" style="width: 100%;">
                Ir al Inicio
            </a>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
