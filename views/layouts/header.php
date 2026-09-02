<?php
$base = $base ?? '';
$titulo = $titulo ?? 'Sistema de Asistencia - ISTPET';

// Detección precisa de la ruta actual para marcar enlaces activos
$uriActual = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$dirActual = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($dirActual !== '/' && !empty($dirActual) && str_starts_with($uriActual, $dirActual)) {
    $uriActual = substr($uriActual, strlen($dirActual));
}
$rutaActual = '/' . trim($uriActual, '/');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? '') ?>">

<?php if (!empty($_SESSION['docente_id']) && empty($ocultarNavbar)): ?>
<!-- Barra de Navegación del Docente -->
<nav class="navbar">
    <a href="<?= $base ?>/dashboard" class="navbar-brand">
        <img src="<?= $base ?>/assets/img/logo-istpet.jpg" alt="ISTPET" style="height: 38px; width: auto; border-radius: 4px;">
        <span>ISTPET</span>
        <span class="badge-istpet">DOCENTE</span>
    </a>
    <ul class="nav-links">
        <li><a href="<?= $base ?>/dashboard" class="nav-link <?= $rutaActual === '/dashboard' ? 'active' : '' ?>">Panel QR</a></li>
        <li><a href="<?= $base ?>/estudiantes" class="nav-link <?= str_starts_with($rutaActual, '/estudiantes') ? 'active' : '' ?>">Estudiantes</a></li>
        <li><a href="<?= $base ?>/reportes" class="nav-link <?= str_starts_with($rutaActual, '/reportes') ? 'active' : '' ?>">Reportes</a></li>
        <li class="nav-user">
            <span class="user-name"><?= htmlspecialchars($_SESSION['docente_nombre'] ?? '') ?></span>
            <a href="<?= $base ?>/logout" class="btn-logout" title="Cerrar sesión de docente">Cerrar Sesión</a>
        </li>
    </ul>
</nav>
<?php elseif (!empty($_SESSION['estudiante_id']) && empty($ocultarNavbar)): ?>
<!-- Barra de Navegación del Estudiante -->
<nav class="navbar">
    <a href="<?= $base ?>/estudiante/portal" class="navbar-brand">
        <img src="<?= $base ?>/assets/img/logo-istpet.jpg" alt="ISTPET" style="height: 38px; width: auto; border-radius: 4px;">
        <span>ISTPET</span>
        <span class="badge-istpet" style="background: var(--color-accent); color: var(--color-primary-dark);">ESTUDIANTE</span>
    </a>
    <ul class="nav-links">
        <li><a href="<?= $base ?>/estudiante/portal" class="nav-link <?= str_starts_with($rutaActual, '/estudiante/portal') ? 'active' : '' ?>">Mi Expediente</a></li>
        <li><a href="<?= $base ?>/asistencia/escanear" class="nav-link <?= str_starts_with($rutaActual, '/asistencia') ? 'active' : '' ?>">Escanear QR</a></li>
        <li class="nav-user">
            <span class="user-name"><?= htmlspecialchars($_SESSION['estudiante_nombre'] ?? $_SESSION['estudiante_codigo'] ?? '') ?></span>
            <a href="<?= $base ?>/logout-estudiante" class="btn-logout" title="Cerrar sesión de estudiante">Cerrar Sesión</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
<main class="main-content">
