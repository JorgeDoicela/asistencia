<?php
$titulo = 'Panel de Control Docente - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 style="color: var(--azul-marino); font-size: 1.8rem; font-weight: 800;">Panel de Asistencia</h1>
        <p style="color: var(--texto-secundario); font-size: 0.95rem;">Gestiona sesiones en tiempo real con códigos QR dinámicos</p>
    </div>
    <div style="display: flex; gap: 10px;">
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

<!-- Tarjetas de Métricas -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 28px;">
    <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border-left: 4px solid var(--azul-marino);">
        <p style="font-size: 0.82rem; color: var(--texto-secundario); text-transform: uppercase; font-weight: 700;">Estudiantes Registrados</p>
        <p style="font-size: 1.8rem; font-weight: 800; color: var(--azul-marino); margin-top: 6px;"><?= $totalEstudiantes ?></p>
    </div>
    <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border-left: 4px solid var(--dorado);">
        <p style="font-size: 0.82rem; color: var(--texto-secundario); text-transform: uppercase; font-weight: 700;">Asistencias Registradas Hoy</p>
        <p style="font-size: 1.8rem; font-weight: 800; color: var(--dorado); margin-top: 6px;"><?= $asistenciasHoy ?></p>
    </div>
    <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border-left: 4px solid var(--info);">
        <p style="font-size: 0.82rem; color: var(--texto-secundario); text-transform: uppercase; font-weight: 700;">Total de Sesiones Creadas</p>
        <p style="font-size: 1.8rem; font-weight: 800; color: var(--info); margin-top: 6px;"><?= $totalSesiones ?></p>
    </div>
</div>

<?php if ($sesionActiva): ?>
    <!-- SESIÓN ACTIVA CON QR -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; margin-bottom: 36px; align-items: start;">
        <!-- Tarjeta QR -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); text-align: center; border: 2px solid #e0e7ff;">
            <div style="display: inline-block; background: #dbeafe; color: #1e40af; font-size: 0.75rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; margin-bottom: 12px; letter-spacing: 0.5px;">
                ● SESIÓN EN CURSO
            </div>
            <h3 style="color: var(--azul-marino); font-size: 1.2rem; margin-bottom: 4px;"><?= htmlspecialchars($sesionActiva['materia']) ?></h3>
            <p style="color: var(--texto-secundario); font-size: 0.85rem; margin-bottom: 16px;">Inició a las <?= htmlspecialchars($sesionActiva['hora_inicio']) ?></p>

            <div style="background: white; padding: 12px; border: 1px solid var(--borde); border-radius: 10px; display: inline-block; margin-bottom: 16px;">
                <img src="<?= $qrUrl ?>" alt="Código QR de Asistencia" style="width: 220px; height: 220px; display: block; border-radius: 4px;">
            </div>

            <div style="background: #f8fafc; padding: 10px; border-radius: 8px; margin-bottom: 16px;">
                <span style="font-size: 0.8rem; color: var(--texto-secundario); display: block; margin-bottom: 4px;">Código de Acceso Manual:</span>
                <strong style="font-size: 1.6rem; color: var(--azul-marino); letter-spacing: 3px; font-family: monospace;"><?= htmlspecialchars($sesionActiva['codigo_sesion']) ?></strong>
            </div>

            <form action="<?= $base ?>/dashboard/sesion/cerrar" method="POST" onsubmit="return confirm('¿Está seguro de cerrar la sesión de asistencia? Los estudiantes ya no podrán registrarse.');">
                <input type="hidden" name="sesion_id" value="<?= $sesionActiva['id'] ?>">
                <button type="submit" class="btn btn-danger" style="width: 100%;">
                    Finalizar Sesión de Clase
                </button>
            </form>
        </div>

        <!-- Tabla en vivo de asistencias -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--borde); padding-bottom: 12px;">
                <div>
                    <h3 style="color: var(--azul-marino); font-size: 1.15rem; font-weight: 700;">Asistencias en Vivo</h3>
                    <p style="color: var(--texto-secundario); font-size: 0.85rem;">Se actualiza automáticamente cada 5 segundos</p>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span id="liveIndicator" style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: var(--exito);"></span>
                    <span id="contadorAsistencias" style="font-weight: 700; color: var(--azul-marino); font-size: 1.1rem;"><?= count($asistenciasSesion) ?></span>
                    <span style="color: var(--texto-secundario); font-size: 0.85rem;">presentes</span>
                </div>
            </div>

            <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--borde); color: var(--texto-secundario);">
                            <th style="padding: 10px 12px;">Hora</th>
                            <th style="padding: 10px 12px;">Código</th>
                            <th style="padding: 10px 12px;">Estudiante</th>
                            <th style="padding: 10px 12px;">Carrera</th>
                        </tr>
                    </thead>
                    <tbody id="tablaAsistenciasCuerpo">
                        <?php if (empty($asistenciasSesion)): ?>
                            <tr id="filaVacia">
                                <td colspan="4" style="text-align: center; padding: 30px; color: var(--texto-secundario);">
                                    Esperando que los estudiantes escaneen el código QR...
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($asistenciasSesion as $asist): ?>
                                <tr style="border-bottom: 1px solid var(--borde);">
                                    <td style="padding: 10px 12px; font-weight: 600; color: var(--azul-marino);"><?= htmlspecialchars($asist['hora']) ?></td>
                                    <td style="padding: 10px 12px; font-family: monospace; font-weight: 700;"><?= htmlspecialchars($asist['codigo']) ?></td>
                                    <td style="padding: 10px 12px;"><?= htmlspecialchars($asist['nombre'] . ' ' . $asist['apellido']) ?></td>
                                    <td style="padding: 10px 12px; color: var(--texto-secundario);"><?= htmlspecialchars($asist['carrera']) ?></td>
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
                liveIndicator.style.opacity = '0.3';
                const resp = await fetch(urlApi);
                const data = await resp.json();
                liveIndicator.style.opacity = '1';

                if (data.success && data.activa) {
                    contador.textContent = data.asistencias.length;

                    if (data.asistencias.length === 0) {
                        tbody.innerHTML = `
                            <tr id="filaVacia">
                                <td colspan="4" style="text-align: center; padding: 30px; color: var(--texto-secundario);">
                                    Esperando que los estudiantes escaneen el código QR...
                                </td>
                            </tr>`;
                        return;
                    }

                    tbody.innerHTML = data.asistencias.map(a => `
                        <tr style="border-bottom: 1px solid var(--borde);">
                            <td style="padding: 10px 12px; font-weight: 600; color: var(--azul-marino);">${a.hora}</td>
                            <td style="padding: 10px 12px; font-family: monospace; font-weight: 700;">${a.codigo}</td>
                            <td style="padding: 10px 12px;">${a.nombre} ${a.apellido}</td>
                            <td style="padding: 10px 12px; color: var(--texto-secundario);">${a.carrera}</td>
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
    <!-- FORMULARIO PARA CREAR NUEVA SESIÓN -->
    <div style="background: white; border-radius: 12px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); max-width: 650px; margin: 0 auto 36px; border-top: 5px solid var(--azul-marino);">
        <h2 style="color: var(--azul-marino); font-size: 1.4rem; margin-bottom: 6px;">Iniciar Nueva Sesión de Clase</h2>
        <p style="color: var(--texto-secundario); font-size: 0.9rem; margin-bottom: 24px;">Configura la materia y nivel para generar el código QR que mostrarás a los estudiantes.</p>

        <form action="<?= $base ?>/dashboard/sesion/crear" method="POST">
            <div style="margin-bottom: 18px;">
                <label for="carrera" style="display: block; font-size: 0.88rem; font-weight: 600; margin-bottom: 6px;">Carrera</label>
                <select id="carrera" name="carrera" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--borde); border-radius: 6px; font-size: 0.95rem;">
                    <option value="">-- Seleccionar Carrera --</option>
                    <option value="Desarrollo de Software">Desarrollo de Software</option>
                    <option value="Mecánica Automotriz">Mecánica Automotriz</option>
                    <option value="Entrenamiento Deportivo">Entrenamiento Deportivo</option>
                    <option value="Educación Inicial">Educación Inicial</option>
                </select>
            </div>

            <div style="margin-bottom: 18px;">
                <label for="nivel" style="display: block; font-size: 0.88rem; font-weight: 600; margin-bottom: 6px;">Nivel / Semestre</label>
                <select id="nivel" name="nivel" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--borde); border-radius: 6px; font-size: 0.95rem;">
                    <option value="">-- Seleccionar Nivel --</option>
                    <option value="Primer Nivel">Primer Nivel</option>
                    <option value="Segundo Nivel">Segundo Nivel</option>
                    <option value="Tercer Nivel">Tercer Nivel</option>
                    <option value="Cuarto Nivel">Cuarto Nivel</option>
                    <option value="Quinto Nivel">Quinto Nivel</option>
                </select>
            </div>

            <div style="margin-bottom: 24px;">
                <label for="materia" style="display: block; font-size: 0.88rem; font-weight: 600; margin-bottom: 6px;">Nombre de la Materia</label>
                <input type="text" id="materia" name="materia" required placeholder="Ej: Programación Web II, Redes, etc."
                       style="width: 100%; padding: 10px 14px; border: 1px solid var(--borde); border-radius: 6px; font-size: 0.95rem;">
            </div>

            <button type="submit" class="btn btn-dorado" style="width: 100%; padding: 12px; font-size: 1rem;">
                Generar Código QR de Asistencia
            </button>
        </form>
    </div>
<?php endif; ?>

<!-- HISTORIAL DE SESIONES RECIENTES -->
<div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
    <h3 style="color: var(--azul-marino); font-size: 1.15rem; font-weight: 700; margin-bottom: 16px;">Historial de Sesiones Recientes</h3>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--borde); color: var(--texto-secundario);">
                    <th style="padding: 10px 12px;">Fecha</th>
                    <th style="padding: 10px 12px;">Código</th>
                    <th style="padding: 10px 12px;">Materia</th>
                    <th style="padding: 10px 12px;">Horario</th>
                    <th style="padding: 10px 12px;">Estado</th>
                    <th style="padding: 10px 12px; text-align: right;">Total Asistentes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historial)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 24px; color: var(--texto-secundario);">
                            No hay sesiones registradas todavía.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($historial as $h): ?>
                        <tr style="border-bottom: 1px solid var(--borde);">
                            <td style="padding: 10px 12px; font-weight: 600;"><?= htmlspecialchars($h['fecha']) ?></td>
                            <td style="padding: 10px 12px; font-family: monospace; font-weight: 700;"><?= htmlspecialchars($h['codigo_sesion']) ?></td>
                            <td style="padding: 10px 12px;"><?= htmlspecialchars($h['materia']) ?></td>
                            <td style="padding: 10px 12px; color: var(--texto-secundario);">
                                <?= htmlspecialchars($h['hora_inicio']) ?> - <?= htmlspecialchars($h['hora_fin'] ?? 'Activa') ?>
                            </td>
                            <td style="padding: 10px 12px;">
                                <?php if ($h['activa']): ?>
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-size: 0.78rem; font-weight: 700;">ACTIVA</span>
                                <?php else: ?>
                                    <span style="background: #f1f5f9; color: #64748b; padding: 4px 8px; border-radius: 4px; font-size: 0.78rem; font-weight: 600;">FINALIZADA</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 10px 12px; text-align: right; font-weight: 700; color: var(--azul-marino);">
                                <?= $h['total_asistencias'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
