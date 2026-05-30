<?php
declare(strict_types=1);

require_once __DIR__ . '/../../partials/auth.php';
require_once __DIR__ . '/../../../backend/lib/auth.php';

$user = \Canteen\Lib\Auth::user();
if (($user['role'] ?? '') !== 'admin') {
    header('Location: /canteen-system/frontend/dashboard.php');
    exit;
}

$userId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($userId <= 0) {
    header('Location: index.php');
    exit;
}

$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
$frontendPos = strpos($scriptPath, '/frontend');
$basePath = $frontendPos !== false ? substr($scriptPath, 0, $frontendPos) : '';
$backendBase = rtrim($basePath . '/backend', '/');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$backendApiAbsolute = $scheme . '://' . $host . $backendBase . '/api/auth';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="../../assets/css/custom.css">
</head>
<body data-auth-base="<?php echo $backendBase; ?>/api/auth" data-auth-base-abs="<?php echo $backendApiAbsolute; ?>">
<?php require __DIR__ . '/../../partials/navbar.php'; ?>
<div class="sidebar-layout">
  <?php require __DIR__ . '/../../partials/sidebar.php'; ?>
  <main class="content">
    <script>
      // Explicit auth API base for password changes.
      window.AUTH_API_BASE = '<?php echo $backendApiAbsolute; ?>';
    </script>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h4 mb-0">Change Password</h1>
        <div class="text-muted small">User: <span id="password-username" class="fw-semibold">Loading...</span></div>
      </div>
      <a href="index.php" class="btn btn-outline-secondary">Back to Users</a>
    </div>
    <div class="card shadow-sm">
      <div class="card-body">
        <form id="change-password-form" data-user-id="<?php echo $userId; ?>">
          <input type="hidden" name="id" id="password-id" value="<?php echo $userId; ?>">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="password-new" class="form-label">New Password</label>
              <input type="password" class="form-control" id="password-new" name="password" required minlength="8">
            </div>
            <div class="col-md-6">
              <label for="password-confirm" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="password-confirm" name="confirm_password" required minlength="8">
            </div>
          </div>
          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-warning">Update Password</button>
            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<?php require __DIR__ . '/../../partials/scripts.php'; ?>
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
