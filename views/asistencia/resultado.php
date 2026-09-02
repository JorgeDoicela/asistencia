<?php
$titulo = 'Resultado de Asistencia - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="content-narrow">
    <div class="card text-center">
        
        <?php if ($exito): ?>
            <div class="result-status-circle success">
                &#10003;
            </div>
            <h2 style="color: var(--color-success); font-size: 1.6rem; font-weight: 800;">
                Asistencia Registrada
            </h2>
            <p style="color: var(--color-text-muted); font-size: 0.95rem; margin-top: 6px;">
                Tu presencia ha sido confirmada satisfactoriamente en el sistema.
            </p>

            <div class="result-data-box">
                <div class="result-data-item">
                    <span class="result-label">Estudiante</span>
                    <span class="result-value primary"><?= htmlspecialchars($estudiante['nombre'] . ' ' . ($estudiante['apellido'] ?? '')) ?></span>
                </div>
                <div class="result-data-item">
                    <span class="result-label">Codigo Estudiantil</span>
                    <span class="result-value table-code"><?= htmlspecialchars($estudiante['codigo']) ?></span>
                </div>
                <div class="result-data-item">
                    <span class="result-label">Materia</span>
                    <span class="result-value"><?= htmlspecialchars($sesion['materia']) ?></span>
                </div>
                <div class="result-data-item">
                    <span class="result-label">Docente a Cargo</span>
                    <span class="result-value"><?= htmlspecialchars($sesion['docente_nombre'] ?? 'Docente Titular') ?></span>
                </div>
                <div class="result-data-item">
                    <span class="result-label">Fecha y Hora de Registro</span>
                    <span class="result-value success"><?= date('d/m/Y') ?> &bull; <?= htmlspecialchars($hora) ?></span>
                </div>
            </div>

            <div class="d-flex flex-column gap-2 mt-6">
                <a href="<?= $base ?>/estudiante/portal" class="btn btn-primary btn-block btn-lg">
                    Ver Mi Historial en el Portal &rarr;
                </a>
                <a href="<?= $base ?>/asistencia/escanear" class="btn btn-dorado btn-block">
                    Registrar Otra Asistencia
                </a>
                <a href="<?= $base ?>/" class="btn btn-outline btn-block">
                    &larr; Volver a la Página Principal
                </a>
            </div>

        <?php else: ?>
            <div class="result-status-circle danger">
                &#10007;
            </div>
            <h2 style="color: var(--color-danger); font-size: 1.6rem; font-weight: 800;">
                No se pudo registrar
            </h2>
            <p style="color: var(--color-text-muted); font-size: 0.95rem; margin-top: 6px; margin-bottom: 24px;">
                <?= htmlspecialchars($mensaje) ?>
            </p>

            <div class="d-flex flex-column gap-2 mt-4">
                <a href="<?= $base ?>/asistencia/escanear<?= !empty($codigoSesion) ? '?codigo=' . urlencode($codigoSesion) : '' ?>" 
                   class="btn btn-primary btn-block btn-lg">
                    Reintentar Registro &rarr;
                </a>
                <a href="<?= $base ?>/" class="btn btn-outline btn-block">
                    &larr; Volver a la Página Principal
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    function emitirFeedbackAudio(tipo) {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();

            if (tipo === 'exito') {
                // Doble tono ascendente tipo escáner de acceso confirmado (880 Hz a 1760 Hz)
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1760, ctx.currentTime + 0.12);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.22);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.22);
            } else {
                // Tono grave de advertencia / no registrado (320 Hz a 240 Hz)
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(320, ctx.currentTime);
                osc.frequency.setValueAtTime(240, ctx.currentTime + 0.12);
                gain.gain.setValueAtTime(0.28, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.25);
            }
        } catch (err) {
            // Silencioso si las políticas del navegador restringen audio previo
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        emitirFeedbackAudio('<?= $exito ? "exito" : "error" ?>');
    });
})();
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
