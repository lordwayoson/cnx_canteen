<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/lib/auth.php';

\Canteen\Lib\Auth::requireLogin(['kitchen', 'admin']);
$user = \Canteen\Lib\Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kitchen Queue - Concentrix Canteen</title>
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
  <link rel="icon" type="image/png" href="/canteen/canteen-system/frontend/img/TabIcon.png">
=======
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/queue.css">
</head>
<body class="queue-page" data-queue-poll="2">
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
  <div class="container-fluid py-2">
    <div class="card shadow-sm mb-3 queue-hero position-relative overflow-hidden">
=======
  <div class="container py-4">
    <div class="card shadow-sm mb-4 queue-hero position-relative overflow-hidden">
>>>>>>> theirs
=======
  <div class="container py-4">
    <div class="card shadow-sm mb-4 queue-hero position-relative overflow-hidden">
>>>>>>> theirs
=======
  <div class="container py-4">
    <div class="card shadow-sm mb-4 queue-hero position-relative overflow-hidden">
>>>>>>> theirs
      <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <img src="img/Pal-AfricLogo.jpg" alt="Company Logo" class="queue-logo img-fluid">
          <div>
            <h1 class="h3 mb-1">Kitchen Serving Queue</h1>
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
           <span class="badge bg-primary">Logged in as <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></span>
=======
            <small class="text-muted">Auto-updating directly from the database (FIFO)</small>
>>>>>>> theirs
=======
            <small class="text-muted">Auto-updating directly from the database (FIFO)</small>
>>>>>>> theirs
=======
            <small class="text-muted">Auto-updating directly from the database (FIFO)</small>
>>>>>>> theirs
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <img src="img/dish1.jpg" alt="Dish" class="queue-hero-img d-none d-md-inline-block">
          <img src="img/dish2.jpg" alt="Dish" class="queue-hero-img d-none d-lg-inline-block">
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
          <img src="img/dish3.jpg" alt="Dish" class="queue-hero-img d-none d-md-inline-block">
          <img src="img/dish4.jpg" alt="Dish" class="queue-hero-img d-none d-lg-inline-block">
          <img src="img/dish5.jpg" alt="Dish" class="queue-hero-img d-none d-lg-inline-block">
=======
          <span class="badge bg-primary">Logged in as <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></span>
>>>>>>> theirs
=======
          <span class="badge bg-primary">Logged in as <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></span>
>>>>>>> theirs
=======
          <span class="badge bg-primary">Logged in as <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></span>
>>>>>>> theirs
        </div>
      </div>
    </div>

    <div id="queue-list" class="card shadow-sm mb-3">
      <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h5 class="mb-0">Today's Queue</h5>
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
=======
          <small id="queue-summary" class="text-muted">Auto-updating every 2 seconds (FIFO)</small>
>>>>>>> theirs
=======
          <small id="queue-summary" class="text-muted">Auto-updating every 2 seconds (FIFO)</small>
>>>>>>> theirs
=======
          <small id="queue-summary" class="text-muted">Auto-updating every 2 seconds (FIFO)</small>
>>>>>>> theirs
        </div>
        <form id="queue-search-form" class="d-flex align-items-center gap-2 queue-search" autocomplete="off">
          <input type="number" min="0" class="form-control form-control-sm" id="queue-search-input" placeholder="Search by Staff ID">
          <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="queue-reset">Reset</button>
        </form>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-striped align-middle mb-0 queue-table">
          <thead class="table-light">
            <tr>
              <th scope="col">Full Name</th>
              <th scope="col">Staff ID</th>
              <th scope="col">Project</th>
              <th scope="col">Meal</th>
              <th scope="col">Diet</th>
              <th scope="col">Shift</th>
              <th scope="col">Queued At</th>
              <th scope="col">Receipt</th>
            </tr>
          </thead>
          <tbody id="queue-items">
            <tr><td colspan="8" class="text-center text-muted">Loading queue...</td></tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer text-muted small" id="queue-date">Today</div>
    </div>

    <div class="queue-search-toast card shadow-lg" id="queue-search-panel">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <strong id="queue-search-title">Queue Results</strong>
          <div class="small text-muted" id="queue-search-summary">Enter a Staff ID to search.</div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="queue-search-close">Close</button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0 queue-table">
            <thead class="table-light">
              <tr>
                <th scope="col">Full Name</th>
                <th scope="col">Staff ID</th>
                <th scope="col">Project</th>
                <th scope="col">Meal</th>
                <th scope="col">Diet</th>
                <th scope="col">Shift</th>
                <th scope="col">Queued At</th>
                <th scope="col">Receipt</th>
              </tr>
            </thead>
            <tbody id="queue-search-results">
              <tr><td colspan="8" class="text-center text-muted">Awaiting Staff ID...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
    <!-- <div class="text-start">
      <img src="img/Pal-AfricLogo.jpg" alt="Pal-Afric Logo" class="img-fluid queue-logo-foot"> 
    </div>-->
=======
    <div class="text-start">
      <img src="img/Pal-AfricLogo.jpg" alt="Pal-Afric Logo" class="img-fluid queue-logo-foot">
    </div>
>>>>>>> theirs
=======
    <div class="text-start">
      <img src="img/Pal-AfricLogo.jpg" alt="Pal-Afric Logo" class="img-fluid queue-logo-foot">
    </div>
>>>>>>> theirs
=======
    <div class="text-start">
      <img src="img/Pal-AfricLogo.jpg" alt="Pal-Afric Logo" class="img-fluid queue-logo-foot">
    </div>
>>>>>>> theirs
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/queue.js"></script>
</body>
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
</html>
=======
</html>
>>>>>>> theirs
=======
</html>
>>>>>>> theirs
=======
</html>
>>>>>>> theirs
