<?php
$titulo = 'Sistema de Asistencia QR - ISTPET';
$bodyClass = 'home-layout';
$ocultarNavbar = true;
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="home-wrapper">
    <div class="home-container">
        <!-- Encabezado con Logo Oficial -->
        <div class="home-header">
            <div class="home-logo-box">
                <img src="<?= $base ?>/assets/img/logo-istpet.jpg" alt="Logo ISTPET">
            </div>
            <h1 class="home-title">Sistema de Asistencia QR</h1>
            <p class="home-subtitle">Instituto Superior Tecnológico Mayor Pedro Traversari</p>
        </div>

        <?php if (!empty($_SESSION['docente_id'])): ?>
            <!-- Banner de Continuación Rápida para Docentes Autenticados -->
            <div class="card mb-6 text-center" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: #ffffff; border: 1px solid #334155; padding: 22px;">
                <span class="badge badge-info pulse-badge mb-2">SESIÓN DOCENTE ACTIVA</span>
                <h3 style="color: #ffffff; font-size: 1.25rem; margin-bottom: 6px;">
                    Bienvenido de vuelta, <?= htmlspecialchars($_SESSION['docente_nombre'] ?? 'Docente') ?>
                </h3>
                <p style="color: #94a3b8; font-size: 0.88rem; margin-bottom: 16px;">
                    Tu sesión está lista. Puedes ingresar directo a proyectar clases o gestionar asistencias.
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="<?= $base ?>/dashboard" class="btn btn-dorado btn-lg">
                        Ir a Mi Panel de Control QR &rarr;
                    </a>
                    <a href="<?= $base ?>/logout" class="btn btn-outline" style="color: #cbd5e1; border-color: #475569;">
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        <?php elseif (!empty($_SESSION['estudiante_id'])): ?>
            <!-- Banner de Continuación Rápida para Estudiantes Autenticados -->
            <div class="card mb-6 text-center" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: #ffffff; border: 1px solid #334155; padding: 22px;">
                <span class="badge badge-success mb-2" style="background: var(--color-accent); color: var(--color-primary-dark);">SESIÓN ESTUDIANTIL ACTIVA</span>
                <h3 style="color: #ffffff; font-size: 1.25rem; margin-bottom: 6px;">
                    Hola, <?= htmlspecialchars($_SESSION['estudiante_nombre'] ?? $_SESSION['estudiante_codigo'] ?? 'Estudiante') ?>
                </h3>
                <p style="color: #94a3b8; font-size: 0.88rem; margin-bottom: 16px;">
                    Accede a tu historial de asistencias o confirma tu clase de hoy.
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="<?= $base ?>/estudiante/portal" class="btn btn-dorado btn-lg">
                        Ver Mi Expediente de Asistencias &rarr;
                    </a>
                    <a href="<?= $base ?>/asistencia/escanear" class="btn btn-primary">
                        Registrar Asistencia en Clase
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Las 2 Opciones Principales de los Chicos -->
        <div class="home-opciones">
            <!-- Opcion Docentes -->
            <div class="home-card-role">
                <div>
                    <h2>Docentes</h2>
                    <p>
                        Accede a tu panel para generar codigos QR de clase en vivo, monitorear asistencias en tiempo real y exportar reportes.
                    </p>
                </div>
                <div class="home-buttons-stack">
                    <a href="<?= $base ?>/login" class="btn btn-primary btn-lg btn-block">
                        Iniciar Sesion Docente &rarr;
                    </a>
                    <div style="font-size: 0.82rem; color: var(--color-text-muted); padding: 8px 0;">
                        Panel institucional para profesores
                    </div>
                </div>
            </div>

            <!-- Opcion Estudiantes -->
            <div class="home-card-role">
                <div>
                    <h2>Estudiantes</h2>
                    <p>
                        Confirma tu presencia en clase mediante el codigo QR o consulta tu historial academico de asistencias.
                    </p>
                </div>
                <div class="home-buttons-stack">
                    <a href="<?= $base ?>/asistencia/escanear" class="btn btn-dorado btn-lg btn-block">
                        Registrar Asistencia &rarr;
                    </a>
                    <a href="<?= $base ?>/login-estudiante" class="btn btn-outline btn-block" style="font-size: 0.88rem;">
                        Consultar Mi Historial
                    </a>
                </div>
            </div>
        </div>

        <!-- Enlace Institucional Oficial que hicieron los chicos -->
        <div class="home-secondary-links">
            <a href="<?= $base ?>/institucional">
                Conocer mas sobre las Carreras y el ISTPET &bull; Informacion Institucional &rarr;
            </a>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
