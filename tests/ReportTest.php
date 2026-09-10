<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../backend/reports/ReportRenderer.php';
require_once __DIR__ . '/../backend/reports/ReportContext.php';
use Canteen\Reports\ReportDataService as D;
use Canteen\Reports\ReportFormatter as F;
use Canteen\Reports\ReportRenderer as R;
use Canteen\Reports\ReportContext as C;
function reportCheck(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }
foreach ([['start_date' => '2026-02-30'], ['start_date' => ['2026-01-01']], ['start_date' => '2026-09-10', 'end_date' => '2026-09-01'], ['shift_type' => 'Bad'], ['report_type' => '../pdf'], ['end_date' => "2026-09-01' OR 1=1"]] as $invalid) {
    try { D::validate($invalid); throw new RuntimeException('Invalid filters accepted'); } catch (InvalidArgumentException) { }
}
$user = ['username' => 'qa.administrator', 'role' => 'admin'];
foreach ([['2026-09-10','2026-09-10','Daily'], ['2026-09-07','2026-09-13','Weekly'], ['2026-09-01','2026-09-30','Monthly'], ['2026-09-02','2026-09-10','Custom']] as [$start,$end,$type]) {
    foreach (['','Day','Night'] as $shift) {
        foreach (array_keys(D::TYPES) as $reportType) {
            $filters = D::validate(['start_date'=>$start,'end_date'=>$end,'shift_type'=>$shift,'report_type'=>$reportType]);
            $report = F::build([], $filters, $user);
            reportCheck($report['type'] === $type . ' Report', 'Period classification');
            reportCheck($report['generated_by'] === 'qa.administrator', 'Session username fallback');
            reportCheck(str_contains(R::html($report), 'No records were found'), 'Empty state');
            reportCheck(count($report['sections']) === ($reportType === '' ? 5 : 1), 'Report sections');
        }
    }
}
$filters = D::validate(['start_date'=>'2026-09-07','end_date'=>'2026-09-13']);
$data = ['servedMeals'=>[], 'selectedMeals'=>[], 'totals'=>[], 'topMeals'=>[], 'staff'=>[]];
for ($i=1; $i<=180; $i++) $data['servedMeals'][] = ['date'=>'2026-09-10','meal_label'=>sprintf('QA meal %03d - Rice, vegetables and grilled chicken',$i),'count'=>2];
$data['selectedMeals'][] = ['date'=>'2026-09-10','meal_label'=>'Vegetarian <script>alert("test")</script> & salad','count'=>7];
$data['totals'][] = ['date'=>'2026-09-10','total'=>360];
$data['topMeals'][] = ['meal_label'=>'Rice & chicken','count'=>45];
$data['staff'][] = ['name'=>'Sample','lastname'=>'Staff','count'=>3];
$report = F::build($data, $filters, $user);
$html = R::html($report);
reportCheck(!str_contains($html, '<script>'), 'Escape dynamic HTML');
reportCheck($report['sections'][0]['total'] === 360, 'Totals');
reportCheck(count($report['sections'][0]['rows']) === 180, 'Full dataset');
$token = C::save($report);
reportCheck(C::get($token) === $report, 'Snapshot consistency');
$_SESSION['report_contexts'][$token]['expires'] = 0;
try { C::get($token); throw new RuntimeException('Expired snapshot accepted'); } catch (InvalidArgumentException) { }
$dir = __DIR__ . '/../storage/reports-qa';
if (!is_dir($dir)) mkdir($dir, 0700, true);
file_put_contents($dir . '/.htaccess', "Require all denied\n");
file_put_contents($dir . '/fixture.json', json_encode($report));
$pdf = R::pdf($report);
reportCheck(str_starts_with($pdf, '%PDF-'), 'PDF signature');
file_put_contents($dir . '/multi-page.pdf', $pdf);
file_put_contents($dir . '/preview.html', '<!doctype html><meta charset="utf-8"><link rel="stylesheet" href="../../frontend/assets/css/reports.css"><link rel="stylesheet" href="../../frontend/assets/css/print.css"><body class="report-preview">' . $html);
file_put_contents($dir . '/empty.pdf', R::pdf(F::build([], $filters, $user)));
echo "Report tests passed: 60 filter/type/period cases, validation, totals, escaping, snapshot expiry, empty and 180-row PDFs.\n";
if (in_array('--database', $argv, true)) {
    require_once __DIR__ . '/../backend/config/db.php';
    $pdo = \Canteen\Config\getCanteenPdo();
    foreach (array_keys(D::TYPES) as $type) foreach (['','Day','Night'] as $shift) {
        $f = D::validate(['report_type'=>$type,'shift_type'=>$shift]);
        reportCheck(D::fetch($pdo,$f) === (new \Canteen\Models\MealSelectionModel($pdo))->getSummary($f,$type ?: null), 'Live model parity');
    }
    echo "Read-only database parity passed for all 15 report/shift combinations.\n";
}


