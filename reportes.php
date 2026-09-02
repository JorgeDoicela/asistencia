<?php
require_once 'includes/auth.php';
requiereLogin();
require_once 'includes/db.php';

// Control de seguridad de sesión
if (!isset($_SESSION['control_seguridad'])) {
    session_regenerate_id(true);
    $_SESSION['control_seguridad'] = true;
}

// Filtros
$fechaInicio = $_GET['desde'] ?? '';
$fechaFin = $_GET['hasta'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$materia = $_GET['materia'] ?? '';

$sql = "SELECT e.nombre AS estudiante, e.codigo, s.materia, a.fecha, a.hora
        FROM asistencias a
        JOIN estudiantes e ON e.id = a.estudiante_id
        JOIN sesiones s ON s.id = a.sesion_id
        WHERE s.docente_id = ?";
$params = [$_SESSION['docente_id']];

if ($fechaInicio !== '') {
    $sql .= " AND a.fecha >= ?";
    $params[] = $fechaInicio;
}
if ($fechaFin !== '') {
    $sql .= " AND a.fecha <= ?";
    $params[] = $fechaFin;
}
if ($nombre !== '') {
    $sql .= " AND (e.nombre LIKE ? OR e.codigo LIKE ?)";
    $params[] = "%$nombre%";
    $params[] = "%$nombre%";
}
if ($materia !== '') {
    $sql .= " AND s.materia LIKE ?";
    $params[] = "%$materia%";
}

$sql .= " ORDER BY a.fecha DESC, a.hora DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Exportar a CSV si lo piden
if (isset($_GET['exportar'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reporte_asistencias.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Estudiante', 'Codigo', 'Materia', 'Fecha', 'Hora']);
    foreach ($registros as $r) {
        fputcsv($out, [$r['estudiante'], $r['codigo'], $r['materia'], $r['fecha'], $r['hora']]);
    }
    fclose($out);
    exit;
}

$totalRegistros = count($registros);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema de Asistencia QR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --azul: #2C356D;
            --dorado: #B79B4A;
            --gris: #f4f4f4;
            --blanco: #ffffff;
            --texto-oscuro: #333333;
            --rojo-cerrar: #c0392b;
            --verde-csv: #27ae60;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: var(--gris);
            color: var(--texto-oscuro);
        }

        /* BARRA DE NAVEGACIÓN SUPERIOR */
        .navbar {
            background: var(--azul);
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            padding: 10px 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
            z-index: 100;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-btn {
            background: transparent;
            border: none;
            color: var(--blanco);
            font-size: 22px;
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s;
        }

        .menu-btn:hover {
            color: var(--dorado);
        }

        .logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .logo-istpet {
            height: 45px;
            width: auto;
            object-fit: contain;
            background: white;
            padding: 2px 8px;
            border-radius: 4px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .logo-link:hover .logo-istpet {
            transform: scale(1.03);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .navbar .brand {
            color: var(--blanco);
            font-size: 22px;
            font-weight: bold;
            text-align: center;
        }

        .navbar-right {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 15px;
            color: var(--blanco);
        }

        .navbar-right span {
            font-weight: 600;
            color: var(--dorado);
            font-size: 14px;
        }

        .navbar-right a.salir {
            color: var(--blanco);
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 4px;
            background: rgba(255,255,255,0.1);
            transition: .3s;
            font-size: 14px;
        }

        .navbar-right a.salir:hover {
            background: var(--rojo-cerrar);
        }

        /* MENÚ DESPLEGABLE LATERAL (SIDEBAR) */
        .sidebar {
            position: fixed;
            top: 0;
            left: -260px;
            width: 260px;
            height: 100%;
            background: var(--azul);
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
            transition: left 0.3s ease;
            z-index: 1000;
            padding-top: 20px;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: var(--blanco);
        }

        .close-btn {
            background: transparent;
            border: none;
            color: var(--blanco);
            font-size: 20px;
            cursor: pointer;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            margin-top: 15px;
        }

        .sidebar nav a {
            color: var(--blanco);
            text-decoration: none;
            padding: 15px 25px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: 0.3s;
        }

        .sidebar nav a:hover, .sidebar nav a.active {
            background: var(--dorado);
            color: var(--azul);
            font-weight: bold;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* CONTENEDOR PRINCIPAL Y GRID */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        @media(max-width: 768px) {
            .navbar {
                grid-template-columns: auto 1fr auto;
                padding: 10px 15px;
            }
            .navbar .brand {
                font-size: 16px;
            }
            .navbar-right span {
                display: none;
            }
            .acciones-grupo {
                flex-direction: column;
            }
        }

        /* TARJETAS (CARDS) */
        .card {
            background: var(--blanco);
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-top: 4px solid var(--azul);
        }

        .card h2 {
            color: var(--azul);
            font-size: 22px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--gris);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* FORMULARIOS Y ELEMENTOS */
        .form-row {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--azul);
            font-size: 14px;
        }

        .form-group input {
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: var(--azul);
            box-shadow: 0 0 5px rgba(44,53,109,0.2);
        }

        /* BOTONES */
        .btn {
            background: var(--azul);
            color: var(--blanco);
            border: none;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: var(--dorado);
            color: var(--azul);
        }

        .btn-green {
            background: var(--verde-csv);
            color: white;
        }

        .btn-green:hover {
            background: #219150;
            color: white;
        }

        .acciones-grupo {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .acciones-grupo .btn {
            flex: 1;
        }

        /* TABLAS */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 15px;
        }

        table th {
            background: var(--azul);
            color: var(--blanco);
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
        }

        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        table tr:nth-child(even) {
            background: #fafafa;
        }

        .badge-total {
            background: var(--azul);
            color: white;
            font-size: 13px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- BARRA DE NAVEGACIÓN SUPERIOR -->
<div class="navbar">
    <div class="navbar-left">
        <button class="menu-btn" id="openMenu" title="Abrir menú">
            <i class="fas fa-bars"></i>
        </button>
        <a href="dashboard.php" class="logo-link" title="Ir al Inicio">
            <img src="https://istpet.edu.ec/wp-content/uploads/2021/04/LOGO-ISTPET.png" alt="Logo ISTPET" class="logo-istpet">
        </a>
    </div>
    <div class="brand">Asistencia ISTPET</div>
    <div class="navbar-right">
        <span>Hola, <?= htmlspecialchars($_SESSION['docente_nombre'] ?? 'Docente') ?></span>
        <a href="logout.php" class="salir"><i class="fas fa-sign-out-alt"></i> Salir</a>
    </div>
</div>

<!-- MENÚ LATERAL DESPLEGABLE -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Menú</h3>
        <button class="close-btn" id="closeMenu"><i class="fas fa-times"></i></button>
    </div>
    <nav>
        <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
        <a href="estudiantes.php"><i class="fas fa-user-graduated"></i> Estudiantes</a>
        <a href="reportes.php" class="active"><i class="fas fa-file-alt"></i> Reportes</a>
    </nav>
</aside>

<div class="container">

    <div class="card">
        <h2>Filtrar reporte de asistencias</h2>
        <form method="GET" class="form-row">
            <div class="form-group">
                <label>Desde</label>
                <input type="date" name="desde" value="<?= htmlspecialchars($fechaInicio) ?>">
            </div>
            <div class="form-group">
                <label>Hasta</label>
                <input type="date" name="hasta" value="<?= htmlspecialchars($fechaFin) ?>">
            </div>
            <div class="form-group">
                <label>Estudiante</label>
                <input type="text" name="nombre" placeholder="Nombre o código" value="<?= htmlspecialchars($nombre) ?>">
            </div>
            <div class="form-group">
                <label>Materia</label>
                <input type="text" name="materia" placeholder="Materia" value="<?= htmlspecialchars($materia) ?>">
            </div>
            <div class="acciones-grupo">
                <button class="btn" type="submit"><i class="fas fa-filter"></i> Filtrar</button>
                <a class="btn btn-green" href="?<?= http_build_query(array_merge($_GET, ['exportar' => 1])) ?>">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </a>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Resultados <span class="badge-total"><?= $totalRegistros ?> registros</span></h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Código</th>
                        <th>Materia</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalRegistros === 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #888; padding: 20px;">
                                <i class="fas fa-info-circle"></i> No se encontraron registros con esos filtros.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($registros as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['estudiante']) ?></td>
                                <td><?= htmlspecialchars($r['codigo']) ?></td>
                                <td><?= htmlspecialchars($r['materia']) ?></td>
                                <td><?= htmlspecialchars($r['fecha']) ?></td>
                                <td><?= htmlspecialchars($r['hora']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    // Gestión del menú lateral (Sidebar)
    const openMenuBtn = document.getElementById('openMenu');
    const closeMenuBtn = document.getElementById('closeMenu');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('active');
        sidebarOverlay.classList.add('active');
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    }

    openMenuBtn.addEventListener('click', openSidebar);
    closeMenuBtn.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
</script>
</body>
</html>