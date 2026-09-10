<?php
declare(strict_types=1);

use Canteen\Lib\Auth;

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../backend/lib/auth.php';

$navUser = $user ?? Auth::user();
$dashboardUrl = \Canteen\Config\frontendUrl('dashboard.php');
$reportsUrl = \Canteen\Config\frontendUrl('reports/index.php');
$queueUrl = \Canteen\Config\frontendUrl('queue.php');
$usersUrl = \Canteen\Config\frontendUrl('admin/users/index.php');
$createUserUrl = \Canteen\Config\frontendUrl('admin/users/create.php');
$logoUrl = \Canteen\Config\assetUrl('img/Pal-AfricLogo.jpg');
?>
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 240px; min-height: 100vh;">
  <a href="<?php echo \Canteen\Config\h($dashboardUrl); ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
    <span class="fs-4">Menu</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item"><a href="<?php echo \Canteen\Config\h($dashboardUrl); ?>" class="nav-link">Dashboard</a></li>
    <li><a href="<?php echo \Canteen\Config\h($reportsUrl); ?>" class="nav-link">Reports</a></li>
    <li><a href="<?php echo \Canteen\Config\h($queueUrl); ?>" class="nav-link" target="_blank">Kitchen Queue</a></li>
    <?php if ($navUser && ($navUser['role'] ?? '') === 'admin'): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#userMenu" role="button" aria-expanded="false" aria-controls="userMenu">
          User Management
        </a>
        <div class="collapse ps-3" id="userMenu">
          <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="<?php echo \Canteen\Config\h($usersUrl); ?>">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo \Canteen\Config\h($createUserUrl); ?>">Add New</a></li>
          </ul>
        </div>
      </li>
    <?php endif; ?>
  </ul>
  <div class="mt-auto pt-3 d-flex align-items-end bottom-fixed-image">
    <img src="<?php echo \Canteen\Config\h($logoUrl); ?>" alt="Pal-Afric Logo" class="img-fluid" style="max-width: 140px;">
  </div>
</div>
