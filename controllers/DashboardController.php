<?php

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/models/Sesion.php';
require_once dirname(__DIR__) . '/models/Asistencia.php';
require_once dirname(__DIR__) . '/models/Estudiante.php';

// Controlador del Panel Docente: Administra la clase en vivo y los codigos QR

class DashboardController extends BaseController
{
    private function verificarDocente(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['docente_id'])) {
            $this->redireccionar('/login');
        }
    }

    public function index(): void
    {
        $this->verificarDocente();
        $docenteId = (int)$_SESSION['docente_id'];

        // 1. Obtener la sesion activa actual si existe
        $sesionActiva = Sesion::obtenerActiva($docenteId);
        $asistenciasSesion = [];
        $qrUrl = '';
        $urlRegistro = '';

        $base = self::obtenerRutaBase();
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        if ($sesionActiva) {
            // Asistencias registradas en la sesion actual
            $asistenciasSesion = Asistencia::listarPorSesion((int)$sesionActiva['id']);
            
            // Construir URL a la que apuntara el codigo QR
            $urlRegistro = "{$protocolo}://{$host}{$base}/asistencia/escanear?codigo=" . urlencode($sesionActiva['codigo_sesion']);
            
            // Usar la API publica de QR
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($urlRegistro);
        }

        // 2. Datos estadisticos para las tarjetas
        $totalEstudiantes = Estudiante::contar();
        $asistenciasHoy   = Asistencia::contarHoyPorDocente($docenteId);
        $totalSesiones    = Sesion::contarPorDocente($docenteId);
        $historial        = Sesion::listarHistorial($docenteId, 8);

        $mensaje = $_SESSION['flash_mensaje'] ?? null;
        $error   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_mensaje'], $_SESSION['flash_error']);

        // 3. Cargar la vista con todos los datos
        $this->vista('dashboard.index', [
            'base'              => $base,
            'docenteNombre'     => $_SESSION['docente_nombre'],
            'sesionActiva'      => $sesionActiva,
            'asistenciasSesion' => $asistenciasSesion,
            'qrUrl'             => $qrUrl,
            'urlRegistro'       => $urlRegistro,
            'historial'         => $historial,
            'totalEstudiantes'  => $totalEstudiantes,
            'asistenciasHoy'    => $asistenciasHoy,
            'totalSesiones'     => $totalSesiones,
            'mensaje'           => $mensaje,
            'error'             => $error
        ]);
    }

    public function crearSesion(): void
    {
        $this->verificarDocente();
        $docenteId = (int)$_SESSION['docente_id'];

        $carrera = trim($_POST['carrera'] ?? '');
        $nivel   = trim($_POST['nivel'] ?? '');
        $materia = trim($_POST['materia'] ?? '');

        if (empty($carrera) || empty($nivel) || empty($materia)) {
            $_SESSION['flash_error'] = 'Debe completar todos los campos.';
            $this->redireccionar('/dashboard');
        }

        // Si ya habia una sesion abierta, cerrarla
        $activa = Sesion::obtenerActiva($docenteId);
        if ($activa) {
            Sesion::cerrar((int)$activa['id'], $docenteId);
        }

        // Generar codigo aleatorio de 8 caracteres
        $codigoSesion = strtoupper(bin2hex(random_bytes(4)));
        $materiaCompleta = "{$materia} ({$carrera} - {$nivel})";

        // Guardar la nueva sesion
        if (Sesion::crear($docenteId, $codigoSesion, $materiaCompleta)) {
            $_SESSION['flash_mensaje'] = "Nueva sesion iniciada con codigo: {$codigoSesion}";
        } else {
            $_SESSION['flash_error'] = 'No se pudo crear la sesion.';
        }

        $this->redireccionar('/dashboard');
    }

    public function cerrarSesion(): void
    {
        $this->verificarDocente();
        $docenteId = (int)$_SESSION['docente_id'];
        $sesionId = filter_var($_POST['sesion_id'] ?? null, FILTER_VALIDATE_INT);

        if ($sesionId) {
            Sesion::cerrar($sesionId, $docenteId);
            $_SESSION['flash_mensaje'] = 'Sesion de clase finalizada correctamente.';
        }

        $this->redireccionar('/dashboard');
    }
}
