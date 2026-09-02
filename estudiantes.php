<?php
require_once 'includes/auth.php';
requiereLogin();
require_once 'includes/db.php';

// Control de seguridad de sesión
if (!isset($_SESSION['control_seguridad'])) {
    session_regenerate_id(true);
    $_SESSION['control_seguridad'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $codigo   = strtoupper(trim($_POST['codigo'] ?? ''));
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $carrera  = trim($_POST['carrera'] ?? '');

    if ($codigo !== '' && $nombre !== '' && $apellido !== '') {
        $stmt = $pdo->prepare('INSERT IGNORE INTO estudiantes (codigo, nombre, apellido, carrera) VALUES (?, ?, ?, ?)');
        $stmt->execute([$codigo, $nombre, $apellido, $carrera]);
    }
    header('Location: estudiantes.php');
    exit;
}

if (isset($_GET['eliminar'])) {
    $idEliminar = filter_var($_GET['eliminar'], FILTER_VALIDATE_INT);
    if ($idEliminar) {
        $stmt = $pdo->prepare('DELETE FROM estudiantes WHERE id = ?');
        $stmt->execute([$idEliminar]);
    }
    header('Location: estudiantes.php');
    exit;
}

$estudiantes = $pdo->query('SELECT * FROM estudiantes ORDER BY nombre ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes - Sistema de Asistencia QR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --azul: #2C356D;
            --dorado: #B79B4A;
            --gris: #f4f4f4;
            --blanco: #ffffff;
            --texto-oscuro: #333333;
            --rojo-cerrar: #c0392b;
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
        }

        .btn:hover {
            background: var(--dorado);
            color: var(--azul);
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

        .btn-eliminar {
            color: var(--rojo-cerrar);
            text-decoration: none;
            font-weight: bold;
        }

        .btn-eliminar:hover {
            text-decoration: underline;
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
        <a href="estudiantes.php" class="active"><i class="fas fa-user-graduated"></i> Estudiantes</a>
        <a href="reportes.php"><i class="fas fa-file-alt"></i> Reportes</a>
    </nav>
</aside>

<div class="container">

    <div class="card">
        <h2>Agregar estudiante</h2>
        <form method="POST" class="form-row">
            <div class="form-group">
                <label for="codigo">Código</label>
                <input type="text" id="codigo" name="codigo" placeholder="Ej. EST005" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre completo" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" placeholder="Apellido" required>
            </div>
            <div class="form-group">
                <label for="carrera">Carrera</label>
                <input type="text" id="carrera" name="carrera" placeholder="Ej. Desarrollo de Software">
            </div>
            <button class="btn" type="submit" name="agregar">Agregar estudiante</button>
        </form>
    </div>

    <div class="card">
        <h2>Listado de estudiantes</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Carrera</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($estudiantes)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #888;">No hay estudiantes registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($estudiantes as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['codigo']) ?></td>
                                <td><?= htmlspecialchars($e['nombre']) ?></td>
                                <td><?= htmlspecialchars($e['apellido'] ?? '') ?></td>
                                <td><?= htmlspecialchars($e['carrera'] ?? '') ?></td>
                                <td style="text-align: center;">
                                    <a href="?eliminar=<?= (int)$e['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este estudiante?');">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </td>
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