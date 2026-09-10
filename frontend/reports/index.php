<?php
declare(strict_types=1);

require_once __DIR__ . '/../partials/auth.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../backend/lib/auth.php';

$user = \Canteen\Lib\Auth::requireLogin(['admin']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reports - Concentrix Canteen</title>
  <link rel="icon" type="image/png" href="<?php echo \Canteen\Config\h(\Canteen\Config\assetUrl('img/TabIcon.png')); ?>">
  <link rel="stylesheet" href="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/vendor/bootstrap.min.css')) ?>?v=<?= filemtime(__DIR__ . '/../assets/vendor/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/css/custom.css')) ?>?v=<?= filemtime(__DIR__ . '/../assets/css/custom.css') ?>">
  <link rel="stylesheet" href="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/css/reports.css')) ?>?v=<?= filemtime(__DIR__ . '/../assets/css/reports.css') ?>">
</head>
<body class="reports-screen">
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<div class="sidebar-layout">
  <?php require __DIR__ . '/../partials/sidebar.php'; ?>
  <main class="content">
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3">Meal Reports</h1>
        <p class="text-muted mb-0">Filter by date range and shift to analyze serving trends.</p>
      </div>
      <div class="report-actions no-print">
        <button class="btn btn-outline-secondary" id="print-button" type="submit" form="report-filter" formaction="<?= \Canteen\Config\h(\Canteen\Config\frontendUrl('reports/print.php')) ?>" formtarget="_blank">Print Report</button>
        <button class="btn btn-primary" type="submit" form="report-filter" id="pdf-button" formaction="<?= \Canteen\Config\h(\Canteen\Config\frontendUrl('reports/pdf.php')) ?>" formtarget="_blank">Export PDF</button>
      </div>
    </div>

    <p id="report-status" role="status" aria-live="polite" class="text-muted"></p>
    <form id="report-filter" class="row g-3 mb-4" method="get" data-summary-url="<?= \Canteen\Config\h(\Canteen\Config\apiUrl('reports/summary.php')) ?>">
      <div class="col-md-3">
        <label class="form-label" for="start_date">Start Date</label>
        <input type="date" class="form-control" id="start_date" name="start_date">
      </div>
      <div class="col-md-3">
        <label class="form-label" for="end_date">End Date</label>
        <input type="date" class="form-control" id="end_date" name="end_date">
      </div>
      <div class="col-md-3">
        <label class="form-label" for="shift_type">Shift</label>
        <select class="form-select" id="shift_type" name="shift_type">
          <option value="">All</option>
          <option value="Day">Day</option>
          <option value="Night">Night</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label" for="report_type">Report Type</label>
        <select class="form-select" id="report_type" name="report_type">
          <option value="">All (Charts + Tables)</option>
          <option value="meals_served">Meals Served</option>
          <option value="selected_meals">Selected Meals</option>
          <option value="daily_totals">Daily Totals</option>
          <option value="top_meals">Top Meals</option>
        </select>
      </div>
      <div class="col-md-3 align-self-end">
        <button type="submit" class="btn btn-success w-100" id="apply-filters">Apply Filters</button>
      </div>
    </form>

    <div class="card shadow-sm mb-4" id="served-chart-card">
      <div class="card-header bg-white"><strong>Meals Served</strong></div>
      <div class="card-body">
        <canvas id="servedMealsChart" height="120"></canvas>
      </div>
    </div>

    <?php require __DIR__ . '/tables/meals_served_table.php'; ?>

    <div class="card shadow-sm mb-4" id="selected-chart-card">
      <div class="card-header bg-white"><strong>Selected Meals</strong></div>
      <div class="card-body">
        <canvas id="selectedMealsChart" height="120"></canvas>
      </div>
    </div>

    <?php require __DIR__ . '/tables/selected_meals_table.php'; ?>

    <div class="row g-4">
      <div class="col-lg-6" id="daily-totals-wrapper">
        <?php require __DIR__ . '/tables/daily_totals_table.php'; ?>
      </div>
      <div class="col-lg-6" id="top-meals-wrapper">
        <?php require __DIR__ . '/tables/top_meals_table.php'; ?>
        <div class="card shadow-sm" id="top-staff-card">
          <div class="card-header bg-white"><strong>Top Staff Served</strong></div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="staff-table">
                <thead><tr><th>Staff</th><th>Meals</th></tr></thead>
                <tbody><tr><td colspan="2" class="text-center">Loading...</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/vendor/bootstrap.bundle.min.js')) ?>?v=<?= filemtime(__DIR__ . '/../assets/vendor/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/vendor/chart.umd.min.js')) ?>?v=<?= filemtime(__DIR__ . '/../assets/vendor/chart.umd.min.js') ?>"></script>
<script src="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/js/charts.js')) ?>?v=<?= filemtime(__DIR__ . '/../assets/js/charts.js') ?>"></script>
<script src="<?= \Canteen\Config\h(\Canteen\Config\assetUrl('assets/js/report-actions.js')) ?>?v=<?= filemtime(__DIR__ . '/../assets/js/report-actions.js') ?>"></script>
<script>
  window.CANTEEN_SUMMARY_API = <?= json_encode(\Canteen\Config\apiUrl('reports/summary.php')) ?>;
</script>
</body>
</html>
