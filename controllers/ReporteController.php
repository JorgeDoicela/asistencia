<?php

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/models/Asistencia.php';
require_once dirname(__DIR__) . '/models/Docente.php';

// Controlador de Reportes: Filtra asistencias y genera exportaciones (CSV, Excel, PDF)

class ReporteController extends BaseController
{
    public function index(): void
    {
        $this->verificarDocente();
        $esAdmin = self::esAdmin();

        // Parámetros de filtrado
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin    = $_GET['fecha_fin'] ?? date('Y-m-d');
        $materia     = trim($_GET['materia'] ?? '');
        $busqueda    = trim($_GET['busqueda'] ?? '');
        $carrera     = trim($_GET['carrera'] ?? '');

        $docenteId = null;
        $docentes = [];

        if ($esAdmin) {
            $docentes = Docente::listar('', 'docente');
            $docenteSeleccionado = filter_var($_GET['docente_id'] ?? null, FILTER_VALIDATE_INT);
            if ($docenteSeleccionado && $docenteSeleccionado > 0) {
                $docenteId = $docenteSeleccionado;
            }
        } else {
            $docenteId = (int)$_SESSION['docente_id'];
        }

        // Consultar asistencias al modelo
        $asistencias = Asistencia::filtrar($docenteId, $fechaInicio, $fechaFin, $materia, $busqueda, $carrera);

        $filtros = [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin,
            'materia'      => $materia,
            'busqueda'     => $busqueda,
            'carrera'      => $carrera,
            'docente_id'   => $docenteId
        ];

        $this->vista('reportes.index', [
            'base'         => self::obtenerRutaBase(),
            'asistencias'  => $asistencias,
            'filtros'      => $filtros,
            'fechaInicio'  => $fechaInicio,
            'fechaFin'     => $fechaFin,
            'materia'      => $materia,
            'busqueda'     => $busqueda,
            'carrera'      => $carrera,
            'docenteId'    => $docenteId,
            'docentes'     => $docentes,
            'esAdmin'      => $esAdmin,
            'total'        => count($asistencias)
        ]);
    }

    public function exportarCsv(): void
    {
        $this->verificarDocente();
        $esAdmin = self::esAdmin();

        $fechaInicio = !empty($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
        $fechaFin    = !empty($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : null;
        $materia     = trim($_GET['materia'] ?? '');
        $busqueda    = trim($_GET['busqueda'] ?? '');
        $carrera     = trim($_GET['carrera'] ?? '');

        $docenteId = null;
        if ($esAdmin) {
            $docenteSel = filter_var($_GET['docente_id'] ?? null, FILTER_VALIDATE_INT);
            if ($docenteSel && $docenteSel > 0) {
                $docenteId = $docenteSel;
            }
        } else {
            $docenteId = (int)$_SESSION['docente_id'];
        }

        $asistencias = Asistencia::filtrar($docenteId, $fechaInicio, $fechaFin, $materia, $busqueda, $carrera);

        $nombreArchivo = 'asistencias_' . date('Ymd_His') . '.csv';

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$nombreArchivo}\"");
        header('Pragma: no-cache');
        header('Expires: 0');

        $salida = fopen('php://output', 'w');

        // BOM UTF-8 para compatibilidad con Excel
        fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Cabeceras del CSV
        $cabecera = ['ID', 'Fecha', 'Hora', 'Codigo', 'Estudiante', 'Carrera', 'Materia / Sesion', 'Codigo Sesion'];
        if ($esAdmin) {
            $cabecera[] = 'Docente';
        }
        fputcsv($salida, $cabecera);

        // Filas de datos
        foreach ($asistencias as $fila) {
            $linea = [
                $fila['id'] ?? '',
                $fila['fecha'] ?? '',
                $fila['hora'] ?? '',
                $fila['codigo'] ?? '',
                $fila['estudiante'] ?? '',
                $fila['carrera'] ?? '',
                $fila['materia'] ?? '',
                $fila['codigo_sesion'] ?? ''
            ];
            if ($esAdmin) {
                $linea[] = $fila['docente'] ?? '';
            }
            fputcsv($salida, $linea);
        }

        fclose($salida);
        exit;
    }

    public function exportarExcel(): void
    {
        $this->verificarDocente();
        $esAdmin = self::esAdmin();

        $fechaInicio = !empty($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
        $fechaFin    = !empty($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : null;
        $materia     = trim($_GET['materia'] ?? '');
        $busqueda    = trim($_GET['busqueda'] ?? '');
        $carrera     = trim($_GET['carrera'] ?? '');

        $docenteId = null;
        if ($esAdmin) {
            $docenteSel = filter_var($_GET['docente_id'] ?? null, FILTER_VALIDATE_INT);
            if ($docenteSel && $docenteSel > 0) {
                $docenteId = $docenteSel;
            }
        } else {
            $docenteId = (int)$_SESSION['docente_id'];
        }

        $asistencias = Asistencia::filtrar($docenteId, $fechaInicio, $fechaFin, $materia, $busqueda, $carrera);

        $nombreArchivo = 'asistencias_' . date('Ymd_His') . '.xls';

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$nombreArchivo}\"");
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="utf-8">';
        echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Asistencias</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        echo '<style>
            .titulo-instituto { font-family: Calibri, Arial, sans-serif; font-size: 15pt; font-weight: bold; color: #2C356D; }
            .subtitulo { font-family: Calibri, Arial, sans-serif; font-size: 11pt; font-weight: bold; color: #B79B4A; }
            .meta { font-family: Calibri, Arial, sans-serif; font-size: 9.5pt; color: #475569; }
            .th-header { background-color: #2C356D; color: #FFFFFF; font-family: Calibri, Arial, sans-serif; font-size: 10pt; font-weight: bold; text-align: center; border: 1px solid #CBD5E1; height: 28px; }
            .td-cell { font-family: Calibri, Arial, sans-serif; font-size: 9.5pt; border: 1px solid #E2E8F0; padding: 6px; }
            .td-center { text-align: center; }
            .td-code { text-align: center; font-weight: bold; color: #2C356D; mso-number-format:"\@"; }
            .td-date { text-align: center; mso-number-format:"yyyy-mm-dd"; }
            .td-time { text-align: center; mso-number-format:"hh:mm:ss"; }
            .zebra { background-color: #F8FAFC; }
        </style>';
        echo '</head><body>';
        echo '<table border="0" cellpadding="4" cellspacing="0">';
        echo '<tr><td colspan="' . ($esAdmin ? 8 : 7) . '" class="titulo-instituto">INSTITUTO SUPERIOR TECNOLÓGICO MAYOR PEDRO TRAVERSARI</td></tr>';
        echo '<tr><td colspan="' . ($esAdmin ? 8 : 7) . '" class="subtitulo">SISTEMA INTEGRAL DE CONTROL DE ASISTENCIA — REPORTE OFICIAL</td></tr>';
        echo '<tr><td colspan="' . ($esAdmin ? 8 : 7) . '" class="meta">Fecha de Emisión: ' . date('d/m/Y H:i:s') . ' | Total de registros: ' . count($asistencias) . '</td></tr>';
        if (!empty($fechaInicio) || !empty($fechaFin)) {
            echo '<tr><td colspan="' . ($esAdmin ? 8 : 7) . '" class="meta">Periodo: ' . htmlspecialchars($fechaInicio ?? 'Inicio') . ' al ' . htmlspecialchars($fechaFin ?? 'Hoy') . '</td></tr>';
        }
        if (!empty($materia)) {
            echo '<tr><td colspan="' . ($esAdmin ? 8 : 7) . '" class="meta">Materia: ' . htmlspecialchars($materia) . '</td></tr>';
        }
        if (!empty($carrera)) {
            echo '<tr><td colspan="' . ($esAdmin ? 8 : 7) . '" class="meta">Carrera: ' . htmlspecialchars($carrera) . '</td></tr>';
        }
        echo '<tr><td colspan="' . ($esAdmin ? 8 : 7) . '"></td></tr>';

        echo '<thead><tr>';
        echo '<th class="th-header" style="width: 100px;">Fecha</th>';
        echo '<th class="th-header" style="width: 80px;">Hora</th>';
        echo '<th class="th-header" style="width: 100px;">Código</th>';
        echo '<th class="th-header" style="width: 260px;">Estudiante</th>';
        echo '<th class="th-header" style="width: 220px;">Carrera</th>';
        echo '<th class="th-header" style="width: 220px;">Materia</th>';
        if ($esAdmin) {
            echo '<th class="th-header" style="width: 200px;">Docente</th>';
        }
        echo '<th class="th-header" style="width: 120px;">Código Sesión</th>';
        echo '</tr></thead>';

        echo '<tbody>';
        if (empty($asistencias)) {
            echo '<tr><td colspan="' . ($esAdmin ? 8 : 7) . '" class="td-cell td-center" style="color: #64748B; font-style: italic;">No se encontraron asistencias para los criterios seleccionados.</td></tr>';
        } else {
            $esZebra = false;
            foreach ($asistencias as $fila) {
                $claseZebra = $esZebra ? ' zebra' : '';
                echo '<tr>';
                echo '<td class="td-cell td-date' . $claseZebra . '">' . htmlspecialchars($fila['fecha'] ?? '') . '</td>';
                echo '<td class="td-cell td-time' . $claseZebra . '">' . htmlspecialchars($fila['hora'] ?? '') . '</td>';
                echo '<td class="td-cell td-code' . $claseZebra . '">' . htmlspecialchars($fila['codigo'] ?? '') . '</td>';
                echo '<td class="td-cell' . $claseZebra . '">' . htmlspecialchars($fila['estudiante'] ?? '') . '</td>';
                echo '<td class="td-cell' . $claseZebra . '">' . htmlspecialchars($fila['carrera'] ?? '') . '</td>';
                echo '<td class="td-cell' . $claseZebra . '">' . htmlspecialchars($fila['materia'] ?? '') . '</td>';
                if ($esAdmin) {
                    echo '<td class="td-cell' . $claseZebra . '">' . htmlspecialchars($fila['docente'] ?? '') . '</td>';
                }
                echo '<td class="td-cell td-center' . $claseZebra . '">' . htmlspecialchars($fila['codigo_sesion'] ?? '') . '</td>';
                echo '</tr>';
                $esZebra = !$esZebra;
            }
        }
        echo '</tbody></table></body></html>';
        exit;
    }

    public function exportarPdf(): void
    {
        $this->verificarDocente();
        $esAdmin = self::esAdmin();

        $docenteNombre = $esAdmin ? 'Supervisión Institucional' : ($_SESSION['docente_nombre'] ?? 'Docente ISTPET');

        $fechaInicio = !empty($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
        $fechaFin    = !empty($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : null;
        $materia     = trim($_GET['materia'] ?? '');
        $busqueda    = trim($_GET['busqueda'] ?? '');
        $carrera     = trim($_GET['carrera'] ?? '');

        $docenteId = null;
        if ($esAdmin) {
            $docenteSel = filter_var($_GET['docente_id'] ?? null, FILTER_VALIDATE_INT);
            if ($docenteSel && $docenteSel > 0) {
                $docenteId = $docenteSel;
                $docEncontrado = Docente::buscarPorId($docenteId);
                if ($docEncontrado) {
                    $docenteNombre = $docEncontrado['nombre'];
                }
            }
        } else {
            $docenteId = (int)$_SESSION['docente_id'];
        }

        $asistencias = Asistencia::filtrar($docenteId, $fechaInicio, $fechaFin, $materia, $busqueda, $carrera);

        require_once dirname(__DIR__) . '/libs/ReportePdf.php';

        $rango = '';
        if ($fechaInicio && $fechaFin) {
            $rango = "{$fechaInicio} al {$fechaFin}";
        } elseif ($fechaInicio) {
            $rango = "Desde {$fechaInicio}";
        } elseif ($fechaFin) {
            $rango = "Hasta {$fechaFin}";
        }

        $pdf = new ReportePdf($docenteNombre, $rango, $materia);
        $pdf->AddPage();

        if (empty($asistencias)) {
            $pdf->Ln(5);
            $pdf->SetFont('Helvetica', 'I', 10);
            $pdf->SetTextColor(100, 116, 139);
            $pdf->Cell(273, 14, $pdf->conv('No se encontraron registros de asistencias para los criterios seleccionados.'), 1, 1, 'C');
        } else {
            $pdf->SetFont('Helvetica', '', 8);
            $fill = false;
            foreach ($asistencias as $fila) {
                if ($fill) {
                    $pdf->SetFillColor(248, 250, 252);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                $pdf->SetTextColor(30, 41, 59);
                $pdf->SetDrawColor(226, 232, 240);

                $pdf->Cell(22, 6.5, $fila['fecha'] ?? '', 1, 0, 'C', true);
                $pdf->Cell(18, 6.5, $fila['hora'] ?? '', 1, 0, 'C', true);
                $pdf->Cell(22, 6.5, $fila['codigo'] ?? '', 1, 0, 'C', true);
                $pdf->celdaAjustada(64, 6.5, $fila['estudiante'] ?? '', 1, 0, 'L', $fill);
                $pdf->celdaAjustada(55, 6.5, $fila['carrera'] ?? '', 1, 0, 'L', $fill);
                $pdf->celdaAjustada(56, 6.5, $fila['materia'] ?? '', 1, 0, 'L', $fill);
                $pdf->Cell(36, 6.5, $fila['codigo_sesion'] ?? '', 1, 1, 'C', true);

                $fill = !$fill;
            }
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        $nombreArchivo = 'asistencias_' . date('Ymd_His') . '.pdf';
        $pdf->Output('D', $nombreArchivo);
        exit;
    }
}
