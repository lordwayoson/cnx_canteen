<?php
declare(strict_types=1);

use Canteen\Lib\Auth;

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../backend/lib/auth.php';

$user = Auth::user();
$logoutUrl = \Canteen\Config\frontendUrl('logout.php');
$dashboardUrl = \Canteen\Config\frontendUrl('dashboard.php');
$reportsUrl = \Canteen\Config\frontendUrl('reports/index.php');
$queueUrl = \Canteen\Config\frontendUrl('queue.php');
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo \Canteen\Config\h($dashboardUrl); ?>">Concentrix Canteen</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?php echo \Canteen\Config\h($dashboardUrl); ?>">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo \Canteen\Config\h($reportsUrl); ?>">Reports</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo \Canteen\Config\h($queueUrl); ?>" target="_blank">Kitchen Queue</a></li>
      </ul>
      <span class="navbar-text me-3 text-white">Logged in as <?php echo \Canteen\Config\h((string) ($user['username'] ?? 'Unknown')); ?></span>
      <a class="btn btn-outline-light" href="<?php echo \Canteen\Config\h($logoutUrl); ?>">Logout</a>
    </div>
  </div>
</nav>
