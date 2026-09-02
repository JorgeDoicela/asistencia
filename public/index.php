<?php

// Soporte para servidor web integrado de PHP (php -S)
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($path !== '/' && is_file(__DIR__ . $path)) {
        return false;
    }
}

// Punto de Entrada Principal (Front Controller)
// Recibe las peticiones del navegador y llama al controlador correspondiente

// 1. Cargar los controladores
require_once dirname(__DIR__) . '/controllers/HomeController.php';
require_once dirname(__DIR__) . '/controllers/AuthController.php';
require_once dirname(__DIR__) . '/controllers/DashboardController.php';
require_once dirname(__DIR__) . '/controllers/EstudianteController.php';
require_once dirname(__DIR__) . '/controllers/AsistenciaController.php';
require_once dirname(__DIR__) . '/controllers/ReporteController.php';

// 2. Obtener la ruta solicitada por el usuario
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($metodo === 'HEAD') {
    $metodo = 'GET';
}

// Ajustar la ruta si el proyecto esta dentro de una subcarpeta en XAMPP
$directorio = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($directorio !== '/' && !empty($directorio) && str_starts_with($uri, $directorio)) {
    $uri = substr($uri, strlen($directorio));
}
$ruta = '/' . trim($uri, '/');

// 3. Enrutar segun la ruta y el metodo HTTP (GET o POST)
switch ("{$metodo} {$ruta}") {

    // Pagina de inicio e institucional
    case 'GET /':
        (new HomeController())->index();
        break;
    case 'GET /institucional':
        (new HomeController())->institucional();
        break;

    // Login y Logout de Docentes
    case 'GET /login':
        (new AuthController())->mostrarLoginDocente();
        break;
    case 'POST /login':
        (new AuthController())->loginDocente();
        break;
    case 'GET /logout':
        (new AuthController())->logoutDocente();
        break;

    // Acceso y Portal de Estudiantes
    case 'GET /login-estudiante':
        (new AuthController())->mostrarLoginEstudiante();
        break;
    case 'POST /login-estudiante':
        (new AuthController())->loginEstudiante();
        break;
    case 'GET /logout-estudiante':
        (new AuthController())->logoutEstudiante();
        break;
    case 'GET /estudiante/portal':
        (new EstudianteController())->portal();
        break;

    // Panel Docente (Dashboard)
    case 'GET /dashboard':
        (new DashboardController())->index();
        break;
    case 'POST /dashboard/sesion/crear':
        (new DashboardController())->crearSesion();
        break;
    case 'POST /dashboard/sesion/cerrar':
        (new DashboardController())->cerrarSesion();
        break;

    // Administracion de Estudiantes (CRUD)
    case 'GET /estudiantes':
        (new EstudianteController())->index();
        break;
    case 'POST /estudiantes/crear':
        (new EstudianteController())->crear();
        break;
    case 'POST /estudiantes/actualizar':
        (new EstudianteController())->actualizar();
        break;
    case 'POST /estudiantes/eliminar':
        (new EstudianteController())->eliminar();
        break;

    // Escaneo y Registro de Asistencias
    case 'GET /asistencia/escanear':
        (new AsistenciaController())->mostrarEscanear();
        break;
    case 'POST /asistencia/registrar':
        (new AsistenciaController())->registrar();
        break;

    // API JSON para el refresco en tiempo real
    case 'GET /api/asistencias/activas':
        (new AsistenciaController())->apiListarActivas();
        break;

    // Reportes y Exportación (CSV, Excel, PDF)
    case 'GET /reportes':
        (new ReporteController())->index();
        break;
    case 'GET /reportes/csv':
        (new ReporteController())->exportarCsv();
        break;
    case 'GET /reportes/excel':
        (new ReporteController())->exportarExcel();
        break;
    case 'GET /reportes/pdf':
        (new ReporteController())->exportarPdf();
        break;

    // Ruta no encontrada (404)
    default:
        http_response_code(404);
        require dirname(__DIR__) . '/views/errors/404.php';
        break;
}
