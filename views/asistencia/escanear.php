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

        <!-- Banner de QR Detectado por Cámara -->
        <div id="qrBannerDetectado" class="qr-detected-banner"></div>

        <!-- Input invisible para captura directa de cámara móvil en conexiones HTTP -->
        <input type="file" id="inputFotoQR" accept="image/*" capture="environment" style="display: none;" onchange="procesarFotoQR(event)">

        <!-- Botón para Activar Cámara Web / Móvil -->
        <button type="button" id="btnToggleCamara" onclick="toggleCamara()" class="btn btn-outline btn-block mb-4" style="display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600; padding: 10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
            <span id="btnCamaraTexto">Escanear Código QR con la Cámara</span>
        </button>

        <!-- Contenedor del Visor de Cámara -->
        <div id="contenedorCamara" class="camera-scanner-card" style="display: none; margin-bottom: 20px;">
            <div class="camera-viewport-wrapper">
                <video id="videoCamara" playsinline autoplay muted></video>
                <canvas id="canvasCamara" style="display: none;"></canvas>
                <div class="scanner-laser-overlay">
                    <div class="scanner-frame-corner top-left"></div>
                    <div class="scanner-frame-corner top-right"></div>
                    <div class="scanner-frame-corner bottom-left"></div>
                    <div class="scanner-frame-corner bottom-right"></div>
                    <div class="scanner-scan-bar"></div>
                </div>
            </div>
            <div class="d-flex justify-between align-center mt-2">
                <span id="scannerStatusText" style="color: #94a3b8; font-size: 0.78rem;">
                    Apunte la cámara hacia el código QR de la clase...
                </span>
                <button type="button" onclick="detenerCamara()" class="btn btn-sm btn-outline" style="padding: 2px 10px; font-size: 0.76rem; color: #cbd5e1; border-color: #475569;">
                    Cerrar Cámara
                </button>
            </div>
        </div>

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
                        <span class="badge badge-success" style="font-size: 0.72rem; padding: 2px 8px;">QR Detectado</span>
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
                        Puedes escanearlo con el botón de cámara arriba o escribirlo manualmente.
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

<!-- Biblioteca Standalone de Decodificación QR -->
<script src="<?= $base ?>/assets/js/jsqr.js"></script>

<script>
function cargarDemoAlumno() {
    document.getElementById('codigo_estudiante').value = 'EST001';
}

// Variables del Escáner con Cámara
let streamCamara = null;
let animacionEscaneo = null;
let camaraActiva = false;

const contenedorCamara = document.getElementById('contenedorCamara');
const videoCamara = document.getElementById('videoCamara');
const canvasCamara = document.getElementById('canvasCamara');
const btnCamaraTexto = document.getElementById('btnCamaraTexto');
const scannerStatusText = document.getElementById('scannerStatusText');
const bannerDetectado = document.getElementById('qrBannerDetectado');

function reproducirBeepExito() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(1760, audioCtx.currentTime + 0.12);
        gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.15);
    } catch (e) {
        // En navegadores con políticas estrictas de audio
    }
}

function toggleCamara() {
    if (camaraActiva) {
        detenerCamara();
    } else {
        iniciarCamara();
    }
}

async function iniciarCamara() {
    // En conexiones HTTP (como IP local 192.168.X.X), los navegadores móviles bloquean getUserMedia por no ser HTTPS.
    // En ese caso, activamos inmediatamente la cámara fotográfica nativa del dispositivo.
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        document.getElementById('inputFotoQR').click();
        return;
    }

    try {
        scannerStatusText.textContent = 'Solicitando acceso a la cámara...';
        contenedorCamara.style.display = 'block';

        // Preferir cámara trasera en teléfonos móviles
        const constraints = {
            video: {
                facingMode: { ideal: 'environment' },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        };

        streamCamara = await navigator.mediaDevices.getUserMedia(constraints);
        videoCamara.srcObject = streamCamara;
        videoCamara.setAttribute('playsinline', true);
        await videoCamara.play();

        camaraActiva = true;
        btnCamaraTexto.textContent = 'Detener Cámara';
        scannerStatusText.textContent = 'Apunte la cámara hacia el código QR de la clase...';

        animacionEscaneo = requestAnimationFrame(escanearFrame);
    } catch (err) {
        console.warn('Acceso directo a video bloqueado (típico en HTTP). Abriendo cámara fotográfica nativa...');
        contenedorCamara.style.display = 'none';
        camaraActiva = false;
        btnCamaraTexto.textContent = 'Escanear Código QR con la Cámara';
        // Abrir la cámara nativa del teléfono
        document.getElementById('inputFotoQR').click();
    }
}

function procesarFotoQR(event) {
    const file = event.target.files && event.target.files[0];
    if (!file) return;

    bannerDetectado.style.display = 'block';
    bannerDetectado.style.background = '#eff6ff';
    bannerDetectado.style.color = '#1e40af';
    bannerDetectado.style.borderColor = '#bfdbfe';
    bannerDetectado.textContent = 'Procesando captura de la cámara...';

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = async function() {
            // Las cámaras de los celulares toman fotos gigantes (12MP a 48MP, 4000x3000px).
            // Para que el procesamiento sea instantáneo (menos de 50ms), redimensionamos a un tamaño óptimo (máx 800px).
            const MAX_DIM = 800;
            let ancho = img.width;
            let alto = img.height;

            if (ancho > MAX_DIM || alto > MAX_DIM) {
                if (ancho > alto) {
                    alto = Math.round((alto * MAX_DIM) / ancho);
                    ancho = MAX_DIM;
                } else {
                    ancho = Math.round((ancho * MAX_DIM) / alto);
                    alto = MAX_DIM;
                }
            }

            canvasCamara.width = ancho;
            canvasCamara.height = alto;
            const ctx = canvasCamara.getContext('2d', { willReadFrequently: true });
            ctx.drawImage(img, 0, 0, ancho, alto);

            let qrDetectado = null;

            // 1. Detección nativa ultra-rápida por hardware (si el teléfono lo soporta)
            if ('BarcodeDetector' in window) {
                try {
                    const detector = new BarcodeDetector({ formats: ['qr_code'] });
                    const barcodes = await detector.detect(canvasCamara);
                    if (barcodes && barcodes.length > 0) {
                        qrDetectado = barcodes[0].rawValue;
                    }
                } catch (errBD) {}
            }

            // 2. Decodificador jsQR de alta precisión sobre la imagen optimizada
            if (!qrDetectado && typeof jsQR !== 'undefined') {
                const imageData = ctx.getImageData(0, 0, ancho, alto);
                const resultado = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'attemptBoth'
                });
                if (resultado && resultado.data) {
                    qrDetectado = resultado.data;
                }
            }

            if (qrDetectado) {
                procesarResultadoQR(qrDetectado);
            } else {
                bannerDetectado.style.display = 'block';
                bannerDetectado.style.background = '#fef2f2';
                bannerDetectado.style.color = '#991b1b';
                bannerDetectado.style.borderColor = '#fecaca';
                bannerDetectado.textContent = 'No se pudo leer el código QR en la foto. Intenta tomarla más cerca o escribe el código manualmente.';
            }
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function detenerCamara() {
    if (streamCamara) {
        streamCamara.getTracks().forEach(track => track.stop());
        streamCamara = null;
    }
    if (animacionEscaneo) {
        cancelAnimationFrame(animacionEscaneo);
        animacionEscaneo = null;
    }
    videoCamara.srcObject = null;
    contenedorCamara.style.display = 'none';
    camaraActiva = false;
    btnCamaraTexto.textContent = 'Escanear Código QR con la Cámara';
}

async function escanearFrame() {
    if (!camaraActiva || videoCamara.readyState !== videoCamara.HAVE_ENOUGH_DATA) {
        if (camaraActiva) {
            animacionEscaneo = requestAnimationFrame(escanearFrame);
        }
        return;
    }

    let qrEncontrado = null;

    // 1. Intentar con BarcodeDetector nativo si el navegador lo soporta
    if ('BarcodeDetector' in window) {
        try {
            const detector = new BarcodeDetector({ formats: ['qr_code'] });
            const barcodes = await detector.detect(videoCamara);
            if (barcodes && barcodes.length > 0) {
                qrEncontrado = barcodes[0].rawValue;
            }
        } catch (e) {
            // Fallback a decodificador canvas
        }
    }

    // 2. Si no lo detectó nativo, usar decodificador jsQR en Canvas
    if (!qrEncontrado && typeof jsQR !== 'undefined') {
        canvasCamara.width = videoCamara.videoWidth;
        canvasCamara.height = videoCamara.videoHeight;
        const ctx = canvasCamara.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(videoCamara, 0, 0, canvasCamara.width, canvasCamara.height);
        const imageData = ctx.getImageData(0, 0, canvasCamara.width, canvasCamara.height);
        const codigo = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'attemptBoth'
        });
        if (codigo && codigo.data) {
            qrEncontrado = codigo.data;
        }
    }

    if (qrEncontrado) {
        procesarResultadoQR(qrEncontrado);
        return; // Detiene el bucle de escaneo al encontrarlo
    }

    if (camaraActiva) {
        animacionEscaneo = requestAnimationFrame(escanearFrame);
    }
}

function procesarResultadoQR(textoDetectado) {
    let codigoExtraido = '';

    // Buscar parámetro ?codigo= o &codigo= en la URL escaneada
    const matchParam = textoDetectado.match(/[?&]codigo=([A-Za-z0-9]+)/i);
    if (matchParam && matchParam[1]) {
        codigoExtraido = matchParam[1].toUpperCase();
    } else if (/^[A-Za-z0-9]{6,12}$/.test(textoDetectado.trim())) {
        // Es directamente el código de sesión
        codigoExtraido = textoDetectado.trim().toUpperCase();
    } else {
        // Buscar un bloque alfanumérico de 8 caracteres
        const matchFin = textoDetectado.match(/([A-Za-z0-9]{8})/i);
        if (matchFin && matchFin[1]) {
            codigoExtraido = matchFin[1].toUpperCase();
        }
    }

    if (codigoExtraido) {
        reproducirBeepExito();
        document.getElementById('codigo_sesion').value = codigoExtraido;
        detenerCamara();

        bannerDetectado.style.display = 'block';
        bannerDetectado.textContent = 'Código QR detectado exitosamente: ' + codigoExtraido;

        const inputEst = document.getElementById('codigo_estudiante');
        if (inputEst) {
            inputEst.focus();
            inputEst.select();
        }
    } else {
        // Continuar escaneando si el texto no contiene un formato de sesión
        if (camaraActiva) {
            animacionEscaneo = requestAnimationFrame(escanearFrame);
        }
    }
}
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
