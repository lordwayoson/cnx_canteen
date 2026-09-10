<?php
declare(strict_types=1);
require __DIR__ . '/context.php';
use Canteen\Reports\ReportFormatter as F;
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= F::text($report['title']) ?> - Concentrix Ghana</title>
<link rel="stylesheet" href="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/vendor/bootstrap.min.css')) ?>?v=<?= filemtime(__DIR__ . '/../assets/vendor/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/css/reports.css')) ?>?v=<?= filemtime(__DIR__ . '/../assets/css/reports.css') ?>">
<link rel="stylesheet" href="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/css/print.css')) ?>?v=<?= filemtime(__DIR__ . '/../assets/css/print.css') ?>">
<style>
@page {
  @bottom-left {
    content: <?= json_encode(\Canteen\Reports\ReportRenderer::FOOTER_IDENTITY, JSON_HEX_TAG) ?> "\A" <?= json_encode($report['generated_at'], JSON_HEX_TAG | JSON_UNESCAPED_SLASHES) ?>;
  }
}
</style>
</head><body class="report-preview">
<div class="print-toolbar no-print">
<span class="preview-label">A4 report preview</span>
<a class="btn btn-outline-secondary" id="back-to-reports" href="<?= \Canteen\Config\h(\Canteen\Config\frontendUrl('reports/index.php')) ?>?<?= F::text(http_build_query($report['filters'])) ?>">Back to Reports</a>
<button class="btn btn-outline-secondary" type="button" id="print-document">Print</button>
<form action="<?= \Canteen\Config\h(\Canteen\Config\frontendUrl('reports/pdf.php')) ?>" method="get">
<input type="hidden" name="context" value="<?= F::text($contextToken) ?>">
<button class="btn btn-primary" type="submit" id="pdf-button" data-url="<?= \Canteen\Config\h(\Canteen\Config\frontendUrl('reports/pdf.php')) ?>" data-context="<?= F::text($contextToken) ?>">Export PDF</button>
</form>
<span id="report-status" role="status" aria-live="polite"></span>
</div>
<main class="report-scroll"><div class="report-paper">
<?= \Canteen\Reports\ReportRenderer::html($report) ?>
<footer class="print-page-footer">
<div><?= F::text(\Canteen\Reports\ReportRenderer::FOOTER_IDENTITY) ?></div>
<div><?= F::text($report['generated_at']) ?></div>
</footer>
</div></main>
<script src="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/js/report-actions.js')) ?>?v=<?= filemtime(__DIR__ . '/../assets/js/report-actions.js') ?>"></script>
</body></html>
