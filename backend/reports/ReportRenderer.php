<?php
declare(strict_types=1);
namespace Canteen\Reports;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ReportFormatter.php';

final class ReportRenderer
{
    public const FOOTER_IDENTITY = 'Concentrix Ghana - Canteen Management System';
    public static function html(array $report): string
    {
        ob_start();
        try {
            require __DIR__ . '/../../frontend/reports/partials/document.php';
            return (string) ob_get_contents();
        } finally { ob_end_clean(); }
    }

    public static function pdf(array $report): string
    {
        if (!defined('K_PATH_CACHE')) define('K_PATH_CACHE', rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR);
        $pdf = new class extends \TCPDF {
            public string $stamp = '';
            public function Footer(): void
            {
                $this->SetY(-14);
                $this->SetFont('dejavusans', '', 7);
                $this->SetTextColor(83, 97, 107);
                $this->Cell(0, 4, ReportRenderer::FOOTER_IDENTITY, 0, 1, 'L');
                $this->Cell(0, 4, $this->stamp . '   |   Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'L');
            }
        };
        $pdf->stamp = $report['generated_at'];
        $pdf->setPrintHeader(false);
        $pdf->SetCreator('Concentrix Ghana - Canteen Management System');
        $pdf->SetAuthor($report['generated_by']);
        $pdf->SetTitle($report['title']);
        $pdf->SetMargins(12, 14, 12);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->AddPage('P', 'A4');
        $css = file_get_contents(__DIR__ . '/../../frontend/assets/css/reports.css');
        $css = explode('@media screen', $css)[0];
        $pdf->writeHTML('<style>' . $css . '</style>' . self::html($report), true, false, true, false, '');
        return $pdf->Output($report['filename'], 'S');
    }
}

