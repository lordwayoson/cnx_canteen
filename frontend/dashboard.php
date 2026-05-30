<?php
declare(strict_types=1);

require_once __DIR__ . '/partials/auth.php';
require_once __DIR__ . '/../backend/lib/auth.php';

$user = \Canteen\Lib\Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Concentrix Canteen</title>
<<<<<<< ours
<<<<<<< ours
  <link rel="icon" type="image/png" href="/canteen/canteen-system/frontend/img/TabIcon.png">
=======
>>>>>>> theirs
=======
>>>>>>> theirs
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/partials/navbar.php'; ?>
<div class="sidebar-layout">
  <?php require __DIR__ . '/partials/sidebar.php'; ?>
  <main class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3">Welcome, <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></h1>
      <div class="badge bg-primary text-wrap">Role: <?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title">Ingress Sync</h5>
<<<<<<< ours
<<<<<<< ours
            <!--<p class="card-text">Import staff data and RFID card numbers from the FingerTec ingress database.</p>-->
=======
            <p class="card-text">Import staff data and RFID card numbers from the FingerTec ingress database.</p>
>>>>>>> theirs
=======
            <p class="card-text">Import staff data and RFID card numbers from the FingerTec ingress database.</p>
>>>>>>> theirs
            <button class="btn btn-outline-primary" id="ingress-sync" <?php echo $user['role'] === 'kitchen' ? 'disabled' : ''; ?>>Ingress Sync</button>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title">Google Sheet Intake</h5>
<<<<<<< ours
<<<<<<< ours
            <!--p class="card-text">Preview only-new rows from the Google Form intake and confirm import.</p>-->
=======
            <p class="card-text">Preview only-new rows from the Google Form intake and confirm import.</p>
>>>>>>> theirs
=======
            <p class="card-text">Preview only-new rows from the Google Form intake and confirm import.</p>
>>>>>>> theirs
            <div class="d-grid gap-2">
              <button class="btn btn-outline-secondary" id="preview-btn" <?php echo $user['role'] === 'kitchen' ? 'disabled' : ''; ?>>Preview New Rows</button>
              <button class="btn btn-success" id="import-btn" <?php echo $user['role'] === 'kitchen' ? 'disabled' : ''; ?>>Confirm Import</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title">Reports</h5>
<<<<<<< ours
<<<<<<< ours
            <!--<p class="card-text">View meal serving analytics, export to PDF, and print service summaries.</p>-->
=======
            <p class="card-text">View meal serving analytics, export to PDF, and print service summaries.</p>
>>>>>>> theirs
=======
            <p class="card-text">View meal serving analytics, export to PDF, and print service summaries.</p>
>>>>>>> theirs
            <a class="btn btn-outline-dark" href="reports/index.php">Open Reports</a>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <h2 class="h5 mb-0">Google Sheet Preview</h2>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="preview-table">
            <thead>
              <tr>
                <th>Sheet Row ID</th>
                <th>Staff ID</th>
                <th>Project</th>
                <th>Shift Type</th>
                <th>Week Start</th>
                <th>Diet Notes</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="7" class="text-center">Click "Preview New Rows" to load data.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<?php require __DIR__ . '/partials/scripts.php'; ?>
<script src="assets/js/queue.js" defer></script>
</body>
<<<<<<< ours
<<<<<<< ours
</html>
=======
</html>
>>>>>>> theirs
=======
</html>
>>>>>>> theirs
