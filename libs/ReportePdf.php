<?php

require_once __DIR__ . '/fpdf/fpdf.php';

// Generador de Reportes en PDF para ISTPET
class ReportePdf extends FPDF
{
    private string $docente;
    private string $rango;
    private string $materia;

    public function __construct(string $docente, string $rango, string $materia)
    {
        parent::__construct('L', 'mm', 'A4'); // Horizontal A4
        $this->docente = $docente;
        $this->rango = $rango;
        $this->materia = $materia;
        $this->SetAutoPageBreak(true, 15);
        $this->AliasNbPages();
    }

    public function Header(): void
    {
        // Logo institucional ISTPET
        $rutaLogo = dirname(__DIR__) . '/public/assets/img/logo-istpet.jpg';
        if (file_exists($rutaLogo)) {
            $this->Image($rutaLogo, 12, 10, 24);
        }

        // Encabezado institucional
        $this->SetXY(39, 10);
        $this->SetFont('Helvetica', 'B', 13);
        $this->SetTextColor(26, 43, 76); // Azul institucional #1A2B4C
        $this->Cell(155, 6, $this->conv('INSTITUTO SUPERIOR TECNOLÓGICO MAYOR PEDRO TRAVERSARI'), 0, 1, 'L');

        $this->SetX(39);
        $this->SetFont('Helvetica', 'B', 9);
        $this->SetTextColor(184, 145, 46); // Dorado acreditación #B8912E
        $this->Cell(155, 5, $this->conv('SISTEMA DE ASISTENCIA ACADÉMICA — REPORTE OFICIAL'), 0, 1, 'L');

        // Metadatos a la derecha
        $this->SetXY(195, 10);
        $this->SetFont('Helvetica', 'B', 8);
        $this->SetTextColor(71, 85, 105);
        $this->Cell(88, 4, $this->conv('Fecha de emisión: ' . date('d/m/Y H:i:s')), 0, 1, 'R');

        $this->SetX(195);
        $this->Cell(88, 4, $this->conv('Docente: ' . ($this->docente ?: 'No especificado')), 0, 1, 'R');

        if (!empty($this->rango)) {
            $this->SetX(195);
            $this->Cell(88, 4, $this->conv('Periodo: ' . $this->rango), 0, 1, 'R');
        }
        if (!empty($this->materia)) {
            $this->SetX(195);
            $this->Cell(88, 4, $this->conv('Materia: ' . $this->materia), 0, 1, 'R');
        }

        // Línea divisoria
        $this->SetY(28);
        $this->SetDrawColor(184, 145, 46);
        $this->SetLineWidth(0.6);
        $this->Line(12, 28, 285, 28);
        $this->Ln(4);

        // Cabecera de la tabla
        $this->SetFont('Helvetica', 'B', 8.5);
        $this->SetFillColor(26, 43, 76);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(203, 213, 225);
        $this->SetLineWidth(0.2);

        // Anchos totales: 285 - 12 = 273 mm
        $this->Cell(22, 7, 'FECHA', 1, 0, 'C', true);
        $this->Cell(18, 7, 'HORA', 1, 0, 'C', true);
        $this->Cell(22, 7, $this->conv('CÓDIGO'), 1, 0, 'C', true);
        $this->Cell(64, 7, 'ESTUDIANTE', 1, 0, 'L', true);
        $this->Cell(55, 7, 'CARRERA', 1, 0, 'L', true);
        $this->Cell(56, 7, 'MATERIA', 1, 0, 'L', true);
        $this->Cell(36, 7, $this->conv('SESIÓN'), 1, 1, 'C', true);
    }

    public function Footer(): void
    {
        $this->SetY(-12);
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(140, 6, $this->conv('ISTPET — Documento emitido automáticamente por el Sistema de Asistencia'), 0, 0, 'L');
        $this->Cell(133, 6, $this->conv('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
    }

    public function celdaAjustada(float $w, float $h, string $texto, int $borde, int $salto, string $alineacion, bool $fondo): void
    {
        $textoFinal = $texto;
        $textoConv = $this->conv($textoFinal);
        while ($this->GetStringWidth($textoConv) > ($w - 2) && mb_strlen($textoFinal) > 3) {
            $textoFinal = mb_substr($textoFinal, 0, -1);
            $textoConv = $this->conv($textoFinal . '...');
        }
        $this->Cell($w, $h, $textoConv, $borde, $salto, $alineacion, $fondo);
    }

    public function conv(string $texto): string
    {
        return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
    }
}
