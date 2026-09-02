<?php

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/models/Asistencia.php';

// Controlador de Reportes: Filtra asistencias y genera la exportacion en CSV para Excel

class ReporteController extends BaseController
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

        // Obtener filtros desde la URL
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin    = $_GET['fecha_fin'] ?? date('Y-m-d');
        $materia     = trim($_GET['materia'] ?? '');
        $busqueda    = trim($_GET['busqueda'] ?? '');

        // Consultar al modelo Asistencia
        $asistencias = Asistencia::filtrar($docenteId, $fechaInicio, $fechaFin, $materia, $busqueda);

        $this->vista('reportes.index', [
            'base'         => self::obtenerRutaBase(),
            'asistencias'  => $asistencias,
            'fechaInicio'  => $fechaInicio,
            'fechaFin'     => $fechaFin,
            'materia'      => $materia,
            'busqueda'     => $busqueda,
            'total'        => count($asistencias)
        ]);
    }

    public function exportarCsv(): void
    {
        $this->verificarDocente();
        $docenteId = (int)$_SESSION['docente_id'];

        $fechaInicio = $_GET['fecha_inicio'] ?? null;
        $fechaFin    = $_GET['fecha_fin'] ?? null;
        $materia     = trim($_GET['materia'] ?? '');
        $busqueda    = trim($_GET['busqueda'] ?? '');

        $asistencias = Asistencia::filtrar($docenteId, $fechaInicio, $fechaFin, $materia, $busqueda);

        $nombreArchivo = 'asistencias_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$nombreArchivo}\"");

        $salida = fopen('php://output', 'w');

        // Agregar compatibilidad con Microsoft Excel (BOM UTF-8)
        fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Escribir cabecera del archivo
        fputcsv($salida, ['ID', 'Fecha', 'Hora', 'Codigo', 'Estudiante', 'Carrera', 'Materia / Sesion', 'Codigo Sesion']);

        // Escribir filas de datos
        foreach ($asistencias as $fila) {
            fputcsv($salida, [
                $fila['id'],
                $fila['fecha'],
                $fila['hora'],
                $fila['codigo'],
                $fila['estudiante'],
                $fila['carrera'],
                $fila['materia'],
                $fila['codigo_sesion']
            ]);
        }

        fclose($salida);
        exit;
    }
}
