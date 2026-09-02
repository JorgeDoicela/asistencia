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
}
