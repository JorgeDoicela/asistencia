<?php
$titulo = 'Bienvenido - Sistema de Asistencia ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="max-width: 800px; margin: 40px auto; text-align: center;">
    <div style="display: inline-block; background: var(--azul-marino); padding: 12px 24px; border-radius: 50px; margin-bottom: 20px;">
        <span style="color: var(--dorado); font-weight: 700; font-size: 0.95rem; letter-spacing: 1px;">ISTPET DIGITAL</span>
    </div>
    <h1 style="font-size: 2.4rem; color: var(--azul-marino); margin-bottom: 12px; font-weight: 800;">Sistema de Control de Asistencia QR</h1>
    <p style="color: var(--texto-secundario); font-size: 1.1rem; max-width: 620px; margin: 0 auto 36px;">
        Plataforma institucional para el registro y monitoreo en tiempo real de asistencia académica.
    </p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; text-align: left;">
        <!-- Tarjeta Docente -->
        <div style="background: white; border-radius: 12px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-top: 5px solid var(--azul-marino); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="color: var(--azul-marino); margin-bottom: 10px; font-size: 1.25rem;">Panel Docente</h3>
                <p style="color: var(--texto-secundario); font-size: 0.92rem; margin-bottom: 20px;">
                    Inicia sesión para generar códigos QR de clase, monitorear asistencias en vivo y generar reportes.
                </p>
            </div>
            <a href="<?= $base ?>/login" class="btn btn-primary" style="width: 100%;">
                Acceso Docente &rarr;
            </a>
        </div>

        <!-- Tarjeta Estudiante -->
        <div style="background: white; border-radius: 12px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-top: 5px solid var(--dorado); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="color: var(--azul-marino); margin-bottom: 10px; font-size: 1.25rem;">Portal Estudiante</h3>
                <p style="color: var(--texto-secundario); font-size: 0.92rem; margin-bottom: 20px;">
                    Consulta tu historial académico de asistencias registradas con tu código de estudiante.
                </p>
            </div>
            <a href="<?= $base ?>/login-estudiante" class="btn btn-dorado" style="width: 100%;">
                Consultar Mis Asistencias &rarr;
            </a>
        </div>

        <!-- Tarjeta Escaneo QR -->
        <div style="background: white; border-radius: 12px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-top: 5px solid var(--info); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="color: var(--azul-marino); margin-bottom: 10px; font-size: 1.25rem;">Registro Manual / QR</h3>
                <p style="color: var(--texto-secundario); font-size: 0.92rem; margin-bottom: 20px;">
                    Ingresa el código de sesión mostrado por tu docente si no puedes escanear el QR directamente.
                </p>
            </div>
            <a href="<?= $base ?>/asistencia/escanear" class="btn btn-outline" style="width: 100%;">
                Registrar Asistencia &rarr;
            </a>
        </div>
    </div>

    <div style="margin-top: 36px;">
        <a href="<?= $base ?>/institucional" style="color: var(--texto-secundario); text-decoration: none; font-size: 0.9rem; font-weight: 500;">
            Conocer más sobre las Carreras y el ISTPET &bull; Información Institucional
        </a>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
