<?php
declare(strict_types=1);
ini_set('display_errors', '0');
use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Response;
use Canteen\Reports\ReportDataService;
use Canteen\Reports\ReportFormatter;
use Canteen\Reports\ReportContext;
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../reports/ReportFormatter.php';
require_once __DIR__ . '/../../reports/ReportContext.php';
Cors::apply();
$user = Auth::requireLogin(['admin']);
header('Cache-Control: no-store, private');
try {
    $filters = ReportDataService::validate($_GET);
    require_once __DIR__ . '/../../config/db.php';
    $data = ReportDataService::fetch(Config\getCanteenPdo(), $filters);
    $context = ReportContext::save(ReportFormatter::build($data, $filters, $user));
    session_write_close();
    Response::json(['filters' => $filters, 'report_type' => $filters['report_type'] ?: null, 'data' => $data, 'context' => $context]);
} catch (\Throwable $exception) {
    error_log('Report summary: ' . $exception->getMessage());
    Response::json(['error' => $exception instanceof \InvalidArgumentException ? $exception->getMessage() : 'Unable to load report summary. Please try again.'], $exception instanceof \InvalidArgumentException ? 422 : 500);
}
