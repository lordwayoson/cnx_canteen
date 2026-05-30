<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Models\MealSelectionModel;

require_once __DIR__ . '/../../backend/vendor/autoload.php';
require_once __DIR__ . '/../../backend/lib/auth.php';
require_once __DIR__ . '/../../backend/config/db.php';
require_once __DIR__ . '/../../backend/models/MealSelectionModel.php';

Auth::requireLogin(['admin']);

if (ob_get_length()) {
    ob_end_clean();
}
ob_start();

$filters = [
    'start_date' => $_GET['start_date'] ?? null,
    'end_date' => $_GET['end_date'] ?? null,
    'shift_type' => isset($_GET['shift_type']) && in_array($_GET['shift_type'], ['Day', 'Night'], true)
        ? $_GET['shift_type']
        : null,
];

$reportTypeRaw = isset($_GET['report_type']) ? strtolower(trim((string) $_GET['report_type'])) : null;
$allowedTypes = ['meals_served', 'selected_meals', 'daily_totals', 'top_meals'];
$reportType = $reportTypeRaw && in_array($reportTypeRaw, $allowedTypes, true) ? $reportTypeRaw : null;

$typeLabels = [
    null => 'General Report',
    'meals_served' => 'Meals Served',
    'selected_meals' => 'Selected Meals',
    'daily_totals' => 'Daily Totals',
    'top_meals' => 'Top Meals',
];

$canteenPdo = Config\getCanteenPdo();
$model = new MealSelectionModel($canteenPdo);
$data = $model->getSummary($filters, $reportType);

class ReportPdf extends \TCPDF
{
    private string $logoPath;

    public function __construct(string $logoPath)
    {
        parent::__construct();
        $this->logoPath = $logoPath;
    }

    public function Footer(): void
    {
        $this->SetY(-20);
        if (is_readable($this->logoPath)) {
            $this->Image($this->logoPath, 15, '', 20, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }
    }
}

$logoPath = __DIR__ . '/../img/Pal-AfricLogo.jpg';
$pdf = new ReportPdf($logoPath);
$pdf->SetCreator('Concentrix Canteen');
$pdf->SetAuthor('Concentrix Ghana');
$pdf->SetTitle('Meal Report');
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(true, 25);

$pdf->AddPage();

$heading = $typeLabels[$reportType] ?? $typeLabels[null];
$generatedAt = date('Y-m-d H:i:s');
$rangeText = htmlspecialchars($filters['start_date'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . ' to ' . htmlspecialchars($filters['end_date'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$shiftText = $filters['shift_type'] ? htmlspecialchars($filters['shift_type'], ENT_QUOTES, 'UTF-8') : 'All';

$html = '<h1 style="text-align:center;">' . htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') . '</h1>';
$html .= '<p><strong>Generated:</strong> ' . $generatedAt . '</p>';
$html .= '<p><strong>Date Range:</strong> ' . $rangeText . '</p>';
$html .= '<p><strong>Shift:</strong> ' . $shiftText . '</p>';

$sections = buildSections($data, $reportType);
foreach ($sections as $section) {
    $html .= renderSection($section['title'], $section['headers'], $section['rows']);
}

$pdf->writeHTML($html, true, false, true, false, '');

if (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output('canteen-report-' . date('Ymd-His') . '.pdf', 'I');

function renderSection(string $title, array $headers, array $rows): string
{
    $html = '<h3 style="margin-top:18px;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h3>';
    $html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%">';
    $html .= '<thead><tr>';
    foreach ($headers as $header) {
        $html .= '<th style="font-weight:bold; background-color:#f8f9fa;">' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
    }
    $html .= '</tr></thead><tbody>';

    if (empty($rows)) {
        $html .= '<tr><td colspan="' . count($headers) . '" style="text-align:center;">No data</td></tr>';
    } else {
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') . '</td>';
            }
            $html .= '</tr>';
        }
    }

    $html .= '</tbody></table>';
    return $html;
}

function buildSections(array $data, ?string $reportType): array
{
    $sections = [];

    if ($reportType === null || $reportType === 'meals_served') {
        $servedRows = array_map(static fn($row) => [
            $row['date'] ?? '',
            $row['meal_label'] ?? '',
            (int) ($row['count'] ?? 0),
        ], $data['servedMeals'] ?? []);
        $sections[] = [
            'title' => 'Meals Served',
            'headers' => ['Date', 'Meal', 'Served'],
            'rows' => $servedRows,
        ];
        if ($reportType === 'meals_served') {
            return $sections;
        }
    }

    if ($reportType === null || $reportType === 'selected_meals') {
        $selectedRows = array_map(static fn($row) => [
            $row['date'] ?? '',
            $row['meal_label'] ?? '',
            (int) ($row['count'] ?? 0),
        ], $data['selectedMeals'] ?? []);
        $sections[] = [
            'title' => 'Selected Meals',
            'headers' => ['Date', 'Meal', 'Selected'],
            'rows' => $selectedRows,
        ];
        if ($reportType === 'selected_meals') {
            return $sections;
        }
    }

    if ($reportType === null || $reportType === 'daily_totals') {
        $totalsRows = array_map(static fn($row) => [
            $row['date'] ?? '',
            (int) ($row['total'] ?? 0),
        ], $data['totals'] ?? []);
        $sections[] = [
            'title' => 'Daily Totals',
            'headers' => ['Date', 'Total Meals'],
            'rows' => $totalsRows,
        ];
        if ($reportType === 'daily_totals') {
            return $sections;
        }
    }

    if ($reportType === null || $reportType === 'top_meals') {
        $topMealRows = array_map(static fn($row) => [
            $row['meal_label'] ?? '',
            (int) ($row['count'] ?? 0),
        ], $data['topMeals'] ?? []);
        $sections[] = [
            'title' => 'Top Meals',
            'headers' => ['Meal', 'Served'],
            'rows' => $topMealRows,
        ];
        if ($reportType === 'top_meals') {
            return $sections;
        }
    }

    if ($reportType === null) {
        $staffRows = array_map(static fn($row) => [
            trim(($row['name'] ?? '') . ' ' . ($row['lastname'] ?? '')),
            (int) ($row['count'] ?? 0),
        ], $data['staff'] ?? []);
        $sections[] = [
            'title' => 'Top Staff Served',
            'headers' => ['Staff', 'Meals'],
            'rows' => $staffRows,
        ];
    }

    return $sections;
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
