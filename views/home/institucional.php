<?php
$titulo = 'Institucional - ISTPET';
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="content-medium">
    <nav class="breadcrumb">
        <a href="<?= $base ?>/">Inicio</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Información Institucional</span>
    </nav>

    <div class="mb-4">
        <a href="<?= $base ?>/" class="btn btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Volver al Inicio
        </a>
    </div>

    <div class="card">
        <h1 class="page-title mb-4">Instituto Superior Tecnologico Mayor Pedro Traversari</h1>
        <p class="text-gold font-bold mb-6" style="font-size: 1.05rem;">
            Formando profesionales tecnicos y tecnologicos de excelencia
        </p>

        <section class="mb-6">
            <h2 class="text-primary font-bold mb-4" style="font-size: 1.25rem; border-bottom: 2px solid var(--color-border); padding-bottom: 8px;">
                Nuestra Oferta Academica
            </h2>
            <div class="careers-grid">
                <div class="career-card">
                    <h3 class="career-title">Desarrollo de Software</h3>
                    <p class="career-desc">Construccion de aplicaciones web, moviles y sistemas corporativos con estandares modernos.</p>
                </div>
                <div class="career-card">
                    <h3 class="career-title">Mecanica Automotriz</h3>
                    <p class="career-desc">Diagnostico electronico, electromovilidad y mantenimiento automotriz integral.</p>
                </div>
                <div class="career-card">
                    <h3 class="career-title">Entrenamiento Deportivo</h3>
                    <p class="career-desc">Preparacion fisica de alto rendimiento, fisiologia y gestion de eventos deportivos.</p>
                </div>
                <div class="career-card">
                    <h3 class="career-title">Educacion Inicial</h3>
                    <p class="career-desc">Pedagogia innovadora, desarrollo infantil temprano y estimulacion integral.</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-primary font-bold mb-4" style="font-size: 1.25rem; border-bottom: 2px solid var(--color-border); padding-bottom: 8px;">
                Ubicacion y Contacto
            </h2>
            <p class="text-muted" style="font-size: 0.95rem; line-height: 1.6;">
                Av. Maldonado y Calle Mayor Pedro Traversari, Quito - Ecuador.<br>
                Telefonos de atencion institucional: (+593) 2 300-1234 &bull; Email: info@istpet.edu.ec
            </p>
        </section>
    </div>
</div>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
