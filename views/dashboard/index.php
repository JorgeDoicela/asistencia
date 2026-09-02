<?php
$titulo = 'Panel de Control Docente - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Panel de Asistencia</h1>
        <p class="page-subtitle">Gestiona sesiones en tiempo real con codigos QR dinamicos</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= $base ?>/estudiantes" class="btn btn-outline">Gestionar Estudiantes</a>
        <a href="<?= $base ?>/reportes" class="btn btn-primary">Ver Reportes</a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-success">
        <span><?= htmlspecialchars($mensaje) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<!-- Tarjetas de Metricas (Minimalismo Limpio) -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-label">Estudiantes Registrados</span>
        <span class="stat-value"><?= $totalEstudiantes ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Asistencias Registradas Hoy</span>
        <span class="stat-value"><?= $asistenciasHoy ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Total de Sesiones Creadas</span>
        <span class="stat-value"><?= $totalSesiones ?></span>
    </div>
</div>

<?php if ($sesionActiva): ?>
    <!-- SESION ACTIVA CON QR -->
    <div class="dashboard-grid">
        <!-- Tarjeta QR -->
        <div class="card qr-panel">
            <span class="badge badge-info mb-4">
                SESION EN CURSO
            </span>
            <h3 class="text-primary font-bold mb-2" style="font-size: 1.2rem;">
                <?= htmlspecialchars($sesionActiva['materia']) ?>
            </h3>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">
                Inicio a las <?= htmlspecialchars($sesionActiva['hora_inicio']) ?>
            </p>

            <div class="qr-code-box">
                <img src="<?= $qrUrl ?>" alt="Codigo QR de Asistencia">
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="button" onclick="abrirProyector()" class="btn btn-primary flex-1 btn-sm" title="Proyectar en pantalla gigante para el aula">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 5px;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    Modo Proyector
                </button>
                <button type="button" onclick="copiarEnlaceDirecto('<?= $qrUrl ?>')" class="btn btn-outline flex-1 btn-sm" title="Copiar enlace para compartir">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 5px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    Copiar Enlace
                </button>
            </div>

            <div class="access-code-box">
                <span class="access-code-title">Codigo de Acceso Manual:</span>
                <strong class="access-code-val"><?= htmlspecialchars($sesionActiva['codigo_sesion']) ?></strong>
            </div>

            <form action="<?= $base ?>/dashboard/sesion/cerrar" method="POST" onsubmit="return confirm('Esta seguro de cerrar la sesion de asistencia? Los estudiantes ya no podran registrarse.');">
                <input type="hidden" name="sesion_id" value="<?= $sesionActiva['id'] ?>">
                <button type="submit" class="btn btn-danger btn-block">
                    Finalizar Sesion de Clase
                </button>
            </form>
        </div>

        <!-- Tabla en vivo de asistencias -->
        <div class="card">
            <div class="card-header-flex">
                <div>
                    <h3 class="text-primary font-bold" style="font-size: 1.2rem;">Asistencias en Vivo</h3>
                    <p class="text-muted" style="font-size: 0.85rem;">Se actualiza automaticamente cada 5 segundos</p>
                </div>
                <div class="d-flex align-center gap-2">
                    <span id="liveIndicator" class="live-indicator"></span>
                    <span id="contadorAsistencias" class="font-extrabold text-primary" style="font-size: 1.2rem;"><?= count($asistenciasSesion) ?></span>
                    <span class="text-muted" style="font-size: 0.85rem;">presentes</span>
                </div>
            </div>

            <div class="table-responsive table-scroll-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Codigo</th>
                            <th>Estudiante</th>
                            <th>Carrera</th>
                        </tr>
                    </thead>
                    <tbody id="tablaAsistenciasCuerpo">
                        <?php if (empty($asistenciasSesion)): ?>
                            <tr id="filaVacia">
                                <td colspan="4" class="table-empty">
                                    Esperando que los estudiantes escaneen el codigo QR...
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($asistenciasSesion as $asist): ?>
                                <tr>
                                    <td class="font-semibold text-primary"><?= htmlspecialchars($asist['hora']) ?></td>
                                    <td class="table-code"><?= htmlspecialchars($asist['codigo']) ?></td>
                                    <td class="font-medium"><?= htmlspecialchars($asist['nombre'] . ' ' . $asist['apellido']) ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($asist['carrera']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script de polling en tiempo real -->
    <script>
    (function() {
        const urlApi = '<?= $base ?>/api/asistencias/activas';
        const tbody = document.getElementById('tablaAsistenciasCuerpo');
        const contador = document.getElementById('contadorAsistencias');
        const liveIndicator = document.getElementById('liveIndicator');

        async function actualizarAsistencias() {
            try {
                if (liveIndicator) liveIndicator.style.opacity = '0.3';
                const resp = await fetch(urlApi);
                const data = await resp.json();
                if (liveIndicator) liveIndicator.style.opacity = '1';

                if (data.success && data.activa) {
                    contador.textContent = data.asistencias.length;

                    if (data.asistencias.length === 0) {
                        tbody.innerHTML = `
                            <tr id="filaVacia">
                                <td colspan="4" class="table-empty">
                                    Esperando que los estudiantes escaneen el codigo QR...
                                </td>
                            </tr>`;
                        return;
                    }

                    tbody.innerHTML = data.asistencias.map(a => `
                        <tr>
                            <td class="font-semibold text-primary">${a.hora}</td>
                            <td class="table-code">${a.codigo}</td>
                            <td class="font-medium">${a.nombre} ${a.apellido}</td>
                            <td class="text-muted">${a.carrera}</td>
                        </tr>
                    `).join('');
                } else if (!data.activa) {
                    location.reload();
                }
            } catch (err) {
                console.error('Error al actualizar asistencias:', err);
            }
        }

        setInterval(actualizarAsistencias, 5000);
    })();
    </script>
<?php else: ?>
    <!-- FORMULARIO PARA CREAR NUEVA SESION -->
    <div class="card content-medium mb-6">
        <h2 class="text-primary font-bold mb-2" style="font-size: 1.45rem;">
            Iniciar Nueva Sesion de Clase
        </h2>
        <p class="text-muted mb-6" style="font-size: 0.92rem;">
            Configura la materia y nivel para generar el codigo QR que mostraras a los estudiantes.
        </p>

        <form action="<?= $base ?>/dashboard/sesion/crear" method="POST">
            <div class="form-group">
                <label for="carrera" class="form-label">Carrera</label>
                <select id="carrera" name="carrera" class="form-select" required>
                    <option value="">-- Seleccionar Carrera --</option>
                    <option value="Desarrollo de Software">Desarrollo de Software</option>
                    <option value="Mecanica Automotriz">Mecanica Automotriz</option>
                    <option value="Entrenamiento Deportivo">Entrenamiento Deportivo</option>
                    <option value="Educacion Inicial">Educacion Inicial</option>
                </select>
            </div>

            <div class="form-group">
                <label for="nivel" class="form-label">Nivel / Semestre</label>
                <select id="nivel" name="nivel" class="form-select" required>
                    <option value="">-- Seleccionar Nivel --</option>
                    <option value="Primer Nivel">Primer Nivel</option>
                    <option value="Segundo Nivel">Segundo Nivel</option>
                    <option value="Tercer Nivel">Tercer Nivel</option>
                    <option value="Cuarto Nivel">Cuarto Nivel</option>
                    <option value="Quinto Nivel">Quinto Nivel</option>
                </select>
            </div>

            <div class="form-group mb-6">
                <label for="materia" class="form-label">Nombre de la Materia</label>
                <input type="text" id="materia" name="materia" class="form-control" required placeholder="Ej: Programacion Web II, Redes, etc.">
                
                <div class="mt-2">
                    <span class="text-muted" style="font-size: 0.8rem; font-weight: 600;">Sugerencias de materias para esta carrera:</span>
                    <div id="chipsMaterias" class="chips-container">
                        <!-- Llenado dinámicamente por JS -->
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-dorado btn-block btn-lg">
                Generar Codigo QR de Asistencia
            </button>
        </form>
    </div>
<?php endif; ?>

<!-- HISTORIAL DE SESIONES RECIENTES -->
<div class="card">
    <h3 class="text-primary font-bold mb-4" style="font-size: 1.2rem;">
        Historial de Sesiones Recientes
    </h3>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Codigo</th>
                    <th>Materia</th>
                    <th>Horario</th>
                    <th>Estado</th>
                    <th class="text-right">Total Asistentes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historial)): ?>
                    <tr>
                        <td colspan="6" class="table-empty">
                            No hay sesiones registradas todavia.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($historial as $h): ?>
                        <tr>
                            <td class="font-semibold"><?= htmlspecialchars($h['fecha']) ?></td>
                            <td class="table-code"><?= htmlspecialchars($h['codigo_sesion']) ?></td>
                            <td><?= htmlspecialchars($h['materia']) ?></td>
                            <td class="text-muted">
                                <?= htmlspecialchars($h['hora_inicio']) ?> - <?= htmlspecialchars($h['hora_fin'] ?? 'Activa') ?>
                            </td>
                            <td>
                                <?php if ($h['activa']): ?>
                                    <span class="badge badge-success">ACTIVA</span>
                                <?php else: ?>
                                    <span class="badge badge-neutral">FINALIZADA</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right font-bold text-primary">
                                <?= $h['total_asistencias'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($sesionActiva): ?>
<!-- MODAL MODO PROYECTOR (QR PANTALLA COMPLETA) -->
<div id="modalProyector" class="modal-proyector-overlay">
    <div class="modal-proyector-card">
        <button type="button" onclick="cerrarProyector()" class="modal-proyector-close" title="Cerrar">&times;</button>
        <span class="badge badge-info pulse-badge mb-2">SESIÓN ACTIVA — ESCANEO QR</span>
        <h2 class="text-primary font-black" style="font-size: 1.8rem; margin: 8px 0 4px;">
            <?= htmlspecialchars($sesionActiva['materia']) ?>
        </h2>
        <p class="text-muted font-medium mb-2" style="font-size: 0.95rem;">
            Escanea el código QR con la cámara de tu teléfono móvil
        </p>
        <img src="<?= $qrUrl ?>" alt="Código QR en Grande" class="qr-proyector-img">
        <div class="access-code-box mt-3" style="max-width: 320px; margin-left: auto; margin-right: auto; padding: 12px 16px;">
            <span class="access-code-title">Código Manual de Acceso:</span>
            <strong style="font-size: 2.1rem; letter-spacing: 4px; color: var(--color-primary); display: block; font-family: monospace;">
                <?= htmlspecialchars($sesionActiva['codigo_sesion']) ?>
            </strong>
        </div>
        <p class="text-muted mt-4 mb-0" style="font-size: 0.82rem;">
            Presiona <kbd style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: bold;">ESC</kbd> o haz clic fuera para salir del proyector.
        </p>
    </div>
</div>
<?php endif; ?>

<!-- Toast de Notificación Flotante -->
<div id="toastApp" class="toast-msg">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span id="toastTexto">Mensaje</span>
</div>

<script>
// Funcionalidades Interactivas del Dashboard
function abrirProyector() {
    const modal = document.getElementById('modalProyector');
    if (modal) modal.style.display = 'flex';
}

function cerrarProyector() {
    const modal = document.getElementById('modalProyector');
    if (modal) modal.style.display = 'none';
}

function mostrarToast(mensaje) {
    const toast = document.getElementById('toastApp');
    const texto = document.getElementById('toastTexto');
    if (toast && texto) {
        texto.textContent = mensaje;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
}

function copiarEnlaceDirecto(url) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            mostrarToast('¡Enlace de asistencia copiado al portapapeles!');
        }).catch(() => fallbackCopiar(url));
    } else {
        fallbackCopiar(url);
    }
}

function fallbackCopiar(texto) {
    const input = document.createElement('input');
    input.value = texto;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    mostrarToast('¡Enlace de asistencia copiado al portapapeles!');
}

// Cerrar modal al hacer clic en fondo o con ESC
window.addEventListener('click', function(e) {
    const modal = document.getElementById('modalProyector');
    if (e.target === modal) cerrarProyector();
});

window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') cerrarProyector();
});

// Sugerencias Dinámicas de Materias
const catalogoMaterias = {
    "Desarrollo de Software": ["Programación Web II", "Bases de Datos Avanzadas", "Estructuras de Datos", "Ingeniería de Software", "Redes y Servidores"],
    "Mecanica Automotriz": ["Motores de Combustión", "Electrónica Automotriz", "Sistemas de Frenos", "Diagnóstico Computarizado"],
    "Entrenamiento Deportivo": ["Fisiología del Ejercicio", "Planificación Deportiva", "Metodología del Entrenamiento", "Preparación Física"],
    "Educacion Inicial": ["Desarrollo Infantil", "Expresión Corporal y Lúdica", "Didáctica de la Educación", "Estimulación Temprana"]
};

const selectCarrera = document.getElementById('carrera');
const contChips = document.getElementById('chipsMaterias');
const inputMateria = document.getElementById('materia');

function actualizarChipsMaterias() {
    if (!selectCarrera || !contChips) return;
    const seleccion = selectCarrera.value;
    contChips.innerHTML = '';
    const lista = catalogoMaterias[seleccion] || ["Tutoría Académica", "Proyecto Integrador", "Clase Magistral"];
    
    lista.forEach(m => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'chip-btn';
        btn.textContent = '+ ' + m;
        btn.onclick = () => {
            if (inputMateria) {
                inputMateria.value = m;
                inputMateria.focus();
            }
        };
        contChips.appendChild(btn);
    });
}

if (selectCarrera) {
    selectCarrera.addEventListener('change', actualizarChipsMaterias);
    actualizarChipsMaterias();
}
</script>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
