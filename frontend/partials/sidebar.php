<?php
declare(strict_types=1);

use Canteen\Lib\Auth;

require_once __DIR__ . '/../../backend/lib/auth.php';
$navUser = $user ?? Auth::user();
?>
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
 <link rel="stylesheet" href="assets/css/custom.css">


=======
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 240px; min-height: 100vh;">
  <a href="<?php echo isset($navUser) ? 'dashboard.php' : '#'; ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
    <span class="fs-4">Menu</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto">
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
    <li class="nav-item"><a href="/canteen/canteen-system/frontend/dashboard.php" class="nav-link">Dashboard</a></li>
    <li><a href="/canteen/canteen-system/frontend/reports/index.php" class="nav-link">Reports</a></li>
    <li><a href="/canteen/canteen-system/frontend/queue.php" class="nav-link" target="_blank">Kitchen Queue</a></li>
=======
    <li class="nav-item"><a href="/canteen-system/frontend/dashboard.php" class="nav-link">Dashboard</a></li>
    <li><a href="/canteen-system/frontend/reports/index.php" class="nav-link">Reports</a></li>
    <li><a href="/canteen-system/frontend/queue.php" class="nav-link" target="_blank">Kitchen Queue</a></li>
>>>>>>> theirs
=======
    <li class="nav-item"><a href="/canteen-system/frontend/dashboard.php" class="nav-link">Dashboard</a></li>
    <li><a href="/canteen-system/frontend/reports/index.php" class="nav-link">Reports</a></li>
    <li><a href="/canteen-system/frontend/queue.php" class="nav-link" target="_blank">Kitchen Queue</a></li>
>>>>>>> theirs
=======
    <li class="nav-item"><a href="/canteen-system/frontend/dashboard.php" class="nav-link">Dashboard</a></li>
    <li><a href="/canteen-system/frontend/reports/index.php" class="nav-link">Reports</a></li>
    <li><a href="/canteen-system/frontend/queue.php" class="nav-link" target="_blank">Kitchen Queue</a></li>
>>>>>>> theirs
    <?php if ($navUser && ($navUser['role'] ?? '') === 'admin'): ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#userMenu" role="button" aria-expanded="false" aria-controls="userMenu">
          User Management
        </a>
        <div class="collapse ps-3" id="userMenu">
          <ul class="nav flex-column">
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
            <li class="nav-item"><a class="nav-link" href="/canteen/canteen-system/frontend/admin/users/index.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="/canteen/canteen-system/frontend/admin/users/create.php">Add New</a></li>
          </ul>
        </div>
      </li>     
      <?php endif; ?>
  </ul>
  <div class="mt-auto pt-3 d-flex align-items-end bottom-fixed-image">
    <img src="/canteen/canteen-system/frontend/img/Pal-AfricLogo.jpg" alt="Pal-Afric Logo" class="img-fluid" style="max-width: 140px;">
  </div>
</div>
=======
=======
>>>>>>> theirs
=======
>>>>>>> theirs
            <li class="nav-item"><a class="nav-link" href="/canteen-system/frontend/admin/users/index.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="/canteen-system/frontend/admin/users/create.php">Add New</a></li>
          </ul>
        </div>
      </li>
    <?php endif; ?>
  </ul>
  <div class="mt-auto pt-3 d-flex align-items-end">
    <img src="/canteen-system/frontend/img/Pal-AfricLogo.jpg" alt="Pal-Afric Logo" class="img-fluid" style="max-width: 140px;">
  </div>
</div>
<<<<<<< ours
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
