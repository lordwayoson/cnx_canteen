<?php
declare(strict_types=1);

use Canteen\Lib\Auth;

require_once __DIR__ . '/../../backend/lib/auth.php';

$user = Auth::user();

// Derive a stable frontend base so navigation works from nested paths.
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$frontendPos = strpos($scriptName, '/frontend');
$frontendBase = $frontendPos !== false ? substr($scriptName, 0, $frontendPos + strlen('/frontend')) : '/frontend';
$frontendBase = rtrim($frontendBase, '/');
$logoutUrl = $frontendBase . '/logout.php';
$dashboardUrl = $frontendBase . '/dashboard.php';
$reportsUrl = $frontendBase . '/reports/index.php';
$queueUrl = $frontendBase . '/queue.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>">Concentrix Canteen</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($reportsUrl, ENT_QUOTES, 'UTF-8'); ?>">Reports</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($queueUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Kitchen Queue</a></li>
      </ul>
      <span class="navbar-text me-3 text-white">Logged in as <?php echo htmlspecialchars($user['username'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></span>
      <a class="btn btn-outline-light" href="<?php echo htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8'); ?>">Logout</a>
    </div>
  </div>
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
</nav>
=======
</nav>
>>>>>>> theirs
=======
</nav>
>>>>>>> theirs
=======
</nav>
>>>>>>> theirs
