<?php
declare(strict_types=1);
ini_set('display_errors', '0');
ob_start();
require __DIR__ . '/context.php';
try {
    $bytes = \Canteen\Reports\ReportRenderer::pdf($report);
    ob_end_clean();
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $report['filename'] . '"');
    header('Content-Length: ' . strlen($bytes));
    echo $bytes;
} catch (\Throwable $exception) {
    ob_end_clean();
    error_log('PDF report: ' . $exception->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Unable to generate report. Please verify the selected report period and try again.';
}
