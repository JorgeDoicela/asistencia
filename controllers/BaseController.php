<?php

// Controlador Base: Proporciona metodos auxiliares para cargar vistas y redireccionar

class BaseController
{
    // Carga un archivo de vista ubicado en la carpeta views/ y le pasa variables
    protected function vista(string $nombreVista, array $datos = []): void
    {
        extract($datos);
        $rutaArchivo = dirname(__DIR__) . '/views/' . str_replace('.', '/', $nombreVista) . '.php';

        if (file_exists($rutaArchivo)) {
            require $rutaArchivo;
        } else {
            echo "Error: La vista {$nombreVista} no existe.";
        }
    }

    // Devuelve una respuesta en formato JSON (util para la API de asistencias en vivo)
    protected function json(array $datos): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos);
        exit;
    }

    // Redirecciona al navegador a otra ruta del sistema
    protected function redireccionar(string $ruta): void
    {
        $base = self::obtenerRutaBase();
        header("Location: {$base}{$ruta}");
        exit;
    }

    // Calcula la carpeta base si el proyecto corre en subdirectorios como XAMPP
    public static function obtenerRutaBase(): string
    {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        return ($dir === '/' || empty($dir)) ? '' : $dir;
    }

    // Inicia la sesión PHP si no ha sido iniciada previamente
    protected function iniciarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Verifica que el usuario tenga sesión activa (docente o administrador)
    protected function verificarDocente(): void
    {
        $this->iniciarSesion();
        if (empty($_SESSION['usuario_id']) && empty($_SESSION['docente_id'])) {
            $this->redireccionar('/login');
        }
    }

    // Verifica que el usuario tenga el rol de Administrador
    protected function verificarAdmin(): void
    {
        $this->iniciarSesion();
        $rol = $_SESSION['usuario_rol'] ?? $_SESSION['docente_rol'] ?? '';

        if (empty($_SESSION['usuario_id']) && empty($_SESSION['docente_id'])) {
            $_SESSION['flash_error'] = 'Debe iniciar sesión para acceder al panel de administración.';
            $this->redireccionar('/login');
        }

        if ($rol !== 'admin') {
            $_SESSION['flash_error'] = 'Acceso denegado: se requieren permisos de Administrador.';
            $this->redireccionar('/dashboard');
        }
    }

    // Retorna verdadero si el usuario en sesión es Administrador
    public static function esAdmin(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return ($_SESSION['usuario_rol'] ?? $_SESSION['docente_rol'] ?? '') === 'admin';
    }
}
