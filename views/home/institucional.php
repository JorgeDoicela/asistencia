<?php
$titulo = 'Institucional - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div style="max-width: 900px; margin: 20px auto;">
    <div style="margin-bottom: 24px;">
        <a href="<?= $base ?>/" class="btn btn-outline">&larr; Volver al Inicio</a>
    </div>

    <div style="background: white; border-radius: 12px; padding: 36px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <h1 style="color: var(--azul-marino); font-size: 2rem; margin-bottom: 8px;">Instituto Superior Tecnológico Mayor Pedro Traversari</h1>
        <p style="color: var(--dorado); font-weight: 700; margin-bottom: 24px; font-size: 1.05rem;">Formando profesionales técnicos y tecnológicos de excelencia</p>

        <section style="margin-bottom: 30px;">
            <h2 style="color: var(--azul-marino); font-size: 1.3rem; margin-bottom: 12px; border-bottom: 2px solid var(--fondo); padding-bottom: 6px;">Nuestra Oferta Académica</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-top: 16px;">
                <div style="padding: 16px; background: var(--fondo); border-radius: 8px; border-left: 4px solid var(--azul-marino);">
                    <h3 style="color: var(--azul-marino); font-size: 1.05rem; margin-bottom: 4px;">Desarrollo de Software</h3>
                    <p style="color: var(--texto-secundario); font-size: 0.88rem;">Construcción de aplicaciones web, móviles y sistemas corporativos con estándares modernos.</p>
                </div>
                <div style="padding: 16px; background: var(--fondo); border-radius: 8px; border-left: 4px solid var(--dorado);">
                    <h3 style="color: var(--azul-marino); font-size: 1.05rem; margin-bottom: 4px;">Mecánica Automotriz</h3>
                    <p style="color: var(--texto-secundario); font-size: 0.88rem;">Diagnóstico electrónico, electromovilidad y mantenimiento automotriz integral.</p>
                </div>
                <div style="padding: 16px; background: var(--fondo); border-radius: 8px; border-left: 4px solid var(--info);">
                    <h3 style="color: var(--azul-marino); font-size: 1.05rem; margin-bottom: 4px;">Entrenamiento Deportivo</h3>
                    <p style="color: var(--texto-secundario); font-size: 0.88rem;">Preparación física de alto rendimiento, fisiología y gestión de eventos deportivos.</p>
                </div>
                <div style="padding: 16px; background: var(--fondo); border-radius: 8px; border-left: 4px solid var(--exito);">
                    <h3 style="color: var(--azul-marino); font-size: 1.05rem; margin-bottom: 4px;">Educación Inicial</h3>
                    <p style="color: var(--texto-secundario); font-size: 0.88rem;">Pedagogía innovadora, desarrollo infantil temprano y estimulación integral.</p>
                </div>
            </div>
        </section>

        <section>
            <h2 style="color: var(--azul-marino); font-size: 1.3rem; margin-bottom: 12px; border-bottom: 2px solid var(--fondo); padding-bottom: 6px;">Ubicación y Contacto</h2>
            <p style="color: var(--texto-secundario); font-size: 0.95rem; line-height: 1.6;">
                Av. Maldonado y Calle Mayor Pedro Traversari, Quito - Ecuador.<br>
                Teléfonos de atención institucional: (+593) 2 300-1234 &bull; Email: info@istpet.edu.ec
            </p>
        </section>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
