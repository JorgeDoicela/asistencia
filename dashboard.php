<?php
require_once 'includes/auth.php';
requiereLogin();
require_once 'includes/db.php';

// Control de seguridad de sesión
if (!isset($_SESSION['control_seguridad'])) {
    session_regenerate_id(true);
    $_SESSION['control_seguridad'] = true;
}

$mensaje = '';
$sesionActiva = null;

// Generar nueva sesión de clase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar'])) {
    $carrera = trim($_POST['carrera'] ?? '');
    $nivel   = trim($_POST['nivel'] ?? '');
    $materia = trim($_POST['materia'] ?? '');

    if (empty($carrera) || empty($nivel) || empty($materia)) {
        $mensaje = 'Por favor, complete todos los campos obligatorios.';
    } else {
        $materiaCompleta = $materia . " ($carrera - $nivel)";

        // Generar un código único y seguro de 8 caracteres
        $codigoSesion = strtoupper(bin2hex(random_bytes(4)));

        try {
            $stmt = $pdo->prepare('INSERT INTO sesiones (docente_id, codigo_sesion, materia, fecha, hora_inicio, activa) VALUES (?, ?, ?, CURDATE(), CURTIME(), 1)');
            $stmt->execute([
                $_SESSION['docente_id'],
                $codigoSesion,
                $materiaCompleta
            ]);

            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            error_log("Error al crear sesión: " . $e->getMessage());
            $mensaje = 'Ocurrió un error al generar la sesión.';
        }
    }
}

// Cerrar sesión activa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerrar_sesion'])) {
    $sesion_id = filter_var($_POST['sesion_id'] ?? null, FILTER_VALIDATE_INT);

    if ($sesion_id) {
        try {
            $stmt = $pdo->prepare('UPDATE sesiones SET activa = 0 WHERE id = ? AND docente_id = ?');
            $stmt->execute([$sesion_id, $_SESSION['docente_id']]);

            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            error_log("Error al cerrar sesión: " . $e->getMessage());
            $mensaje = 'Ocurrió un error al cerrar la sesión.';
        }
    }
}

// Buscar si hay una sesión activa del docente
try {
    $stmt = $pdo->prepare('SELECT * FROM sesiones WHERE docente_id = ? AND activa = 1 ORDER BY id DESC LIMIT 1');
    $stmt->execute([$_SESSION['docente_id']]);
    $sesionActiva = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al consultar sesión activa: " . $e->getMessage());
}

// URL base para el código QR (Compatible con Docker, Localhost y Tunnels)
$customAppUrl = getenv('APP_URL');
if (!empty($customAppUrl)) {
    $urlBase = rtrim($customAppUrl, '/');
} else {
    $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $protocol = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    $scriptDir = rtrim(dirname($_SERVER['PHP_SELF'] ?? ''), '/\\');
    $urlBase = $protocol . '://' . $host . ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.' ? '' : $scriptDir);
}

$urlEscaneo = $sesionActiva ? $urlBase . '/formulario.php?clase=' . urlencode($sesionActiva['codigo_sesion']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Docente - Sistema de Asistencia QR</title>
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

        .logo-istpet {
            height: 45px;
            width: auto;
            object-fit: contain;
            background: white;
            padding: 2px 8px;
            border-radius: 4px;
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

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media(max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
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

        .form-group input, .form-group select {
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
            background-color: var(--blanco);
        }

        .form-group input:focus, .form-group select:focus {
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

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
            color: #000;
        }

        /* SECCIÓN DEL CÓDIGO QR */
        .qr-box {
            text-align: center;
        }

        .qr-box img {
            border: 4px solid var(--azul);
            border-radius: 8px;
            padding: 10px;
            background: white;
            margin: 15px 0;
        }

        .tag {
            background: var(--dorado);
            color: var(--azul);
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .qr-info {
            background: var(--gris);
            padding: 15px;
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.6;
            border-left: 4px solid var(--dorado);
            text-align: left;
            margin-top: 10px;
        }

        .qr-codigo {
            font-weight: bold;
            color: var(--azul);
            font-size: 18px;
            letter-spacing: 1px;
        }

        .qr-info a {
            color: var(--azul);
            font-weight: bold;
        }

        /* ALERTAS */
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: left;
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

        /* BADGES */
        .badge-live {
            background: #2ecc71;
            color: white;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
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
        <img src="https://istpet.edu.ec/wp-content/uploads/2025/02/ISTPET-LOGO-300x300.jpg" alt="Logo ISTPET" class="logo-istpet">
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
        <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Inicio</a>
        <a href="estudiantes.php"><i class="fas fa-user-graduated"></i> Estudiantes</a>
        <a href="reportes.php"><i class="fas fa-file-alt"></i> Reportes</a>
    </nav>
</aside>

<div class="container">

    <div class="grid-2">

        <div class="card qr-box">
            <h2>Generar código QR de asistencia</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if (!$sesionActiva): ?>
                <form method="POST" class="form-row" style="text-align: left;">
                    
                    <div class="form-group">
                        <label for="carrera">Carrera</label>
                        <select name="carrera" id="carrera" required>
                            <option value="">Seleccione Carrera</option>
                            <option value="Desarrollo de Software">Desarrollo de Software</option>
                            <option value="Mecánica Automotriz">Mecánica Automotriz</option>
                            <option value="Entrenamiento Deportivo">Entrenamiento Deportivo</option>
                            <option value="Educación Inicial">Educación Inicial</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nivel">Nivel</label>
                        <select name="nivel" id="nivel" required>
                            <option value="">Seleccione Nivel</option>
                            <option value="1er Semestre">1er Semestre</option>
                            <option value="2do Semestre">2do Semestre</option>
                            <option value="3er Semestre">3er Semestre</option>
                            <option value="4to Semestre">4to Semestre</option>
                            <option value="5to Semestre">5to Semestre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="materia">Materia / Clase</label>
                        <select name="materia" id="materia" required>
                            <option value="">Seleccione Materia</option>
                            <option value="Programación Web">Programación Web</option>
                            <option value="Base de Datos">Base de Datos</option>
                            <option value="Sistemas Operativos">Sistemas Operativos</option>
                            <option value="Matemáticas">Matemáticas</option>
                            <option value="Redes">Redes</option>
                        </select>
                    </div>

                    <button class="btn" type="submit" name="generar">Generar QR</button>
                </form>
                <p style="color:#888; font-size:13px; margin-top:15px;">No hay ninguna sesión activa en este momento.</p>
            <?php else: ?>
                <span class="tag"><?= htmlspecialchars($sesionActiva['materia']) ?> — <?= htmlspecialchars($sesionActiva['fecha']) ?></span>
                <br>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?= urlencode($urlEscaneo) ?>" alt="Código QR de Asistencia">

                <div class="qr-info">
                    <i class="fas fa-info-circle" style="color: var(--azul);"></i> Código de sesión: <span class="qr-codigo"><?= htmlspecialchars($sesionActiva['codigo_sesion']) ?></span><br>
                    <small style="display:block; margin-top:6px; color:#555; word-break:break-all;">
                        <i class="fas fa-link"></i> <strong>Enlace QR:</strong> <a href="<?= htmlspecialchars($urlEscaneo) ?>" target="_blank"><?= htmlspecialchars($urlEscaneo) ?></a>
                    </small>
                    <?php if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false): ?>
                        <div style="margin-top:8px; padding:6px 10px; background:#fff3cd; border:1px solid #ffeeba; border-radius:4px; font-size:12px; color:#856404; text-align:left;">
                            <i class="fas fa-mobile-alt"></i> <strong>Para escanear con celulares:</strong> Abre este panel docente usando la IP local de tu PC (ej: <code>http://<?= gethostbyname(gethostname()) ?>:8080</code> o tu IP Wi-Fi) para que el QR contenga una dirección accesible desde otros dispositivos.
                        </div>
                    <?php endif; ?>
                </div>

                <form method="POST" style="margin-top:14px;">
                    <input type="hidden" name="sesion_id" value="<?= (int)$sesionActiva['id'] ?>">
                    <button class="btn" style="background: var(--rojo-cerrar); color: white;" type="submit" name="cerrar_sesion" onclick="return confirm('¿Cerrar esta sesión de asistencia?');">Cerrar sesión</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Filtrar asistencias</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="f_fecha">Fecha</label>
                    <input type="date" id="f_fecha" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label for="f_nombre">Estudiante</label>
                    <input type="text" id="f_nombre" placeholder="Nombre o código">
                </div>
                <div class="form-group">
                    <label for="f_materia">Materia</label>
                    <input type="text" id="f_materia" placeholder="Materia">
                </div>
                <div style="display: flex; gap: 10px; margin-top: 5px;">
                    <button class="btn" id="btnFiltrar" style="flex: 1;">Filtrar</button>
                    <button class="btn btn-secondary" id="btnLimpiar" type="button" style="flex: 1;">Limpiar</button>
                </div>
            </div>
            <p style="font-size:12px; color:#888; margin-top: 15px;"><i class="fas fa-sync-alt"></i> La tabla inferior se actualiza sola, pero estos filtros se aplican al instante.</p>
        </div>
    </div>

    <div class="card">
        <h2>Asistencias registradas <span class="badge-live" id="liveBadge">● en vivo</span></h2>
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
                <tbody id="tablaAsistencias">
                    <tr><td colspan="5" style="text-align: center; color: #888;">Cargando...</td></tr>
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
<script src="assets/js/dashboard.js"></script>
</body>
</html>