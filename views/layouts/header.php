<?php
$base = $base ?? '';
$titulo = $titulo ?? 'Sistema de Asistencia - ISTPET';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/estilos.css">
    <style>
        :root {
            --azul-marino: #2C356D;
            --azul-oscuro: #1b2247;
            --dorado: #B79B4A;
            --dorado-hover: #c9ad5d;
            --fondo: #f4f6fa;
            --tarjeta: #ffffff;
            --texto-principal: #1e293b;
            --texto-secundario: #64748b;
            --borde: #e2e8f0;
            --exito: #10b981;
            --peligro: #ef4444;
            --info: #3b82f6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        body { background-color: var(--fondo); color: var(--texto-principal); min-height: 100vh; display: flex; flex-direction: column; }
        
        /* Navbar */
        .navbar { background: var(--azul-marino); color: white; padding: 0 24px; height: 68px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: white; font-weight: 700; font-size: 1.15rem; }
        .navbar-brand .badge-istpet { background: var(--dorado); color: var(--azul-marino); font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; font-weight: 700; letter-spacing: 0.5px; }
        .nav-links { display: flex; align-items: center; gap: 16px; list-style: none; }
        .nav-links a { color: #e2e8f0; text-decoration: none; font-size: 0.92rem; font-weight: 500; padding: 8px 14px; border-radius: 6px; transition: all 0.2s ease; }
        .nav-links a:hover, .nav-links a.active { background: rgba(255,255,255,0.12); color: white; }
        .nav-user { display: flex; align-items: center; gap: 12px; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 16px; }
        .btn-logout { background: rgba(239, 68, 68, 0.15); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3); padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .btn-logout:hover { background: #ef4444; color: white; }

        /* Contenedor principal */
        .main-content { flex: 1; max-width: 1200px; width: 100%; margin: 0 auto; padding: 28px 20px; }

        /* Alertas */
        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 22px; font-size: 0.95rem; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* Botones generales */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 0.92rem; cursor: pointer; text-decoration: none; border: none; transition: all 0.2s; }
        .btn-primary { background: var(--azul-marino); color: white; }
        .btn-primary:hover { background: var(--azul-oscuro); }
        .btn-dorado { background: var(--dorado); color: var(--azul-marino); }
        .btn-dorado:hover { background: var(--dorado-hover); }
        .btn-danger { background: var(--peligro); color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-outline { background: transparent; border: 1px solid var(--borde); color: var(--texto-principal); }
        .btn-outline:hover { background: #f8fafc; }
    </style>
</head>
<body>
<?php if (!empty($_SESSION['docente_id'])): ?>
<nav class="navbar">
    <a href="<?= $base ?>/dashboard" class="navbar-brand">
        <span>ISTPET</span>
        <span class="badge-istpet">DOCENTE</span>
    </a>
    <ul class="nav-links">
        <li><a href="<?= $base ?>/dashboard">Panel QR</a></li>
        <li><a href="<?= $base ?>/estudiantes">Estudiantes</a></li>
        <li><a href="<?= $base ?>/reportes">Reportes</a></li>
        <li class="nav-user">
            <span style="font-size: 0.88rem; color: #cbd5e1;"><?= htmlspecialchars($_SESSION['docente_nombre'] ?? '') ?></span>
            <a href="<?= $base ?>/logout" class="btn-logout">Cerrar Sesión</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
<main class="main-content">
