<?php
declare(strict_types=1);
ini_set('display_errors', '0');

require_once __DIR__ . '/../../backend/lib/auth.php';
require_once __DIR__ . '/../../backend/reports/ReportFormatter.php';
require_once __DIR__ . '/../../backend/reports/ReportContext.php';
require_once __DIR__ . '/../../backend/reports/ReportRenderer.php';

$user = \Canteen\Lib\Auth::requireLogin(['admin']);
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
try {
    if (isset($_GET['context'])) {
        $report = \Canteen\Reports\ReportContext::get($_GET['context']);
        $contextToken = $_GET['context'];
    } else {
        $filters = \Canteen\Reports\ReportDataService::validate($_GET);
        require_once __DIR__ . '/../../backend/config/db.php';
        $data = \Canteen\Reports\ReportDataService::fetch(\Canteen\Config\getCanteenPdo(), $filters);
        $report = \Canteen\Reports\ReportFormatter::build($data, $filters, $user);
        $contextToken = \Canteen\Reports\ReportContext::save($report);
    }
    session_write_close();
} catch (\Throwable $exception) {
    http_response_code($exception instanceof \InvalidArgumentException ? 422 : 500);
    error_log('Report generation: ' . $exception->getMessage());
    header('Content-Type: text/html; charset=UTF-8');
    $message = $exception instanceof \InvalidArgumentException ? $exception->getMessage() : 'Unable to generate report. Please verify the selected report period and try again.';
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Report unavailable</title><h1>Report unavailable</h1><p>' . \Canteen\Reports\ReportFormatter::text($message) . '</p><a href="index.php">Back to Reports</a></html>';
    exit;
}
