<?php
declare(strict_types=1);

require_once __DIR__ . '/../partials/auth.php';
require_once __DIR__ . '/../../backend/lib/auth.php';

$user = \Canteen\Lib\Auth::user();
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
=======
=======
>>>>>>> theirs
=======
>>>>>>> theirs

// Resolve stable frontend/base paths so CSS and JS load even from nested routes
// or custom DocumentRoots (e.g., XAMPP pointing directly to /frontend).
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$frontendPos = strpos($scriptName, '/frontend');
$assetBase = $frontendPos !== false ? substr($scriptName, 0, $frontendPos + strlen('/frontend')) : '';

$docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$projectRoot = realpath(__DIR__ . '/..') ?: '';
$basePath = '';
if ($docRoot && $projectRoot && str_starts_with($projectRoot, $docRoot)) {
    $basePath = '/' . trim(str_replace('\\', '/', substr($projectRoot, strlen($docRoot))), '/');
}
if ($basePath === '' && $frontendPos !== false) {
    $basePath = substr($scriptName, 0, $frontendPos);
}
$basePath = rtrim($basePath, '/');
$frontendBase = rtrim($basePath . '/frontend', '/');
$assetBase = $frontendBase ?: ($assetBase ?: '/frontend');
<<<<<<< ours
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reports - Concentrix Canteen</title>
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
  <link rel="icon" type="image/png" href="/canteen/canteen-system/frontend/img/TabIcon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/custom.css">

  <style>
      @media print {
        .no-print, #pdf-link, #report-filter, #served-chart-card, #selected-chart-card {
          display: none;
        }
      }
  </style>

</head>
  <body>
    <?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container-fluid mt-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h1 class="h3">Meal Reports</h1>
          </div>

          <img src="/canteen/canteen-system/frontend/img/Pal-AfricLogo.jpg" alt="Pal-Afric Logo" class="img-fluid" style="max-width: 100px;">
          
          <div class="btn-group">
            <button class="btn btn-outline-secondary no-print" onclick="window.print()">Print</button>
            <a class="btn btn-primary" id="pdf-link" href="pdf.php" target="_blank">Save as PDF</a>
          </div>
        </div>
    </div>
    <div class="container-md mt-4 mb-5">
      <main class="content">        
        <form id="report-filter" class="row g-3 mb-4">
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
            <button type="submit" class="btn btn-success w-100">Apply Filters</button>
          </div>
        </form>

        <div class="card shadow-sm mb-4" id="served-chart-card">
          <div class="card-header bg-white"><strong>Meals Served</strong></div>
          <div class="card-body">
            <canvas id="servedMealsChart" height="120"></canvas>
          </div>
        </div>

        <div class="card shadow-sm mb-4" id="served-table-card" style="display:none;">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Meals Served (Table)</strong>
            <small class="text-muted">Per day per meal</small>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="served-meals-table">
                <thead>
                  <tr><th>Date</th><th>Meal</th><th>Served</th></tr>
                </thead>
                <tbody><tr><td colspan="3" class="text-center">No data</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="card shadow-sm mb-4" id="selected-chart-card">
          <div class="card-header bg-white"><strong>Selected Meals</strong></div>
          <div class="card-body">
            <canvas id="selectedMealsChart" height="120"></canvas>
          </div>
        </div>

        <div class="card shadow-sm mb-4" id="selected-table-card" style="display:none;">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Selected Meals (Table)</strong>
            <small class="text-muted">Per day per meal</small>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="selected-meals-table">
                <thead>
                  <tr><th>Date</th><th>Meal</th><th>Selected</th></tr>
                </thead>
                <tbody><tr><td colspan="3" class="text-center">No data</td></tr></tbody>
=======
=======
>>>>>>> theirs
=======
>>>>>>> theirs
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjPPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
  >
  <link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $assetBase; ?>/assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<div class="sidebar-layout">
  <?php require __DIR__ . '/../partials/sidebar.php'; ?>
  <main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3">Meal Reports</h1>
        <p class="text-muted mb-0">Filter by date range and shift to analyze serving trends.</p>
      </div>
      <div class="btn-group">
        <button class="btn btn-outline-secondary" onclick="window.print()" type="button">Print</button>
        <button class="btn btn-primary" type="submit" form="report-filter" id="pdf-button" formaction="pdf.php" formtarget="_blank">Save as PDF</button>
      </div>
    </div>

    <form id="report-filter" class="row g-3 mb-4" method="get">
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
<<<<<<< ours
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
              </table>
            </div>
          </div>
        </div>
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours

        <div class="row g-4">
          <div class="col-lg-6" id="daily-totals-card">
            <div class="card shadow-sm">
              <div class="card-header bg-white"><strong>Daily Totals</strong></div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="totals-table">
                    <thead><tr><th>Date</th><th>Total Meals</th></tr></thead>
                    <tbody><tr><td colspan="2" class="text-center">Loading...</td></tr></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6" id="top-meals-card">
            <div class="card shadow-sm mb-4" id="top-meals-table-card">
              <div class="card-header bg-white"><strong>Top Meals</strong></div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="meals-table">
                    <thead><tr><th>Meal</th><th>Served</th></tr></thead>
                    <tbody><tr><td colspan="2" class="text-center">Loading...</td></tr></tbody>
                  </table>
                </div>
              </div>
            </div>
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

    <?php require __DIR__ . '/../partials/scripts.php'; ?>
    <script>
      // Ensure summary endpoint always resolves even when the document root is set to /frontend
      // or the page is served from nested routes.
      window.CANTEEN_SUMMARY_API = (window.CANTEEN_BACKEND_API_URL || '') + '/reports/summary.php';
    </script>
  </body>
</html>
=======
=======
>>>>>>> theirs
=======
>>>>>>> theirs
      </div>
    </div>
  </main>
</div>

<?php require __DIR__ . '/../partials/scripts.php'; ?>
<script>
  // Ensure summary endpoint always resolves even when the document root is set to /frontend
  // or the page is served from nested routes.
  window.CANTEEN_SUMMARY_API = (window.CANTEEN_BACKEND_API_URL || '') + '/reports/summary.php';
</script>
</body>
</html>
<<<<<<< ours
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
