<?php
declare(strict_types=1);

require_once __DIR__ . '/../../partials/auth.php';
require_once __DIR__ . '/../../../backend/lib/auth.php';

$user = \Canteen\Lib\Auth::user();
if ($user['role'] !== 'admin') {
    header('Location: /canteen-system/frontend/dashboard.php');
    exit;
}

$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
$frontendPos = strpos($scriptPath, '/frontend');
$basePath = $frontendPos !== false ? substr($scriptPath, 0, $frontendPos) : '';
$assetBase = rtrim($basePath . '/frontend', '/');
$backendBase = rtrim($basePath . '/backend', '/');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$backendApiAbsolute = $scheme . '://' . $host . $backendBase . '/api/auth';
$flashError = isset($_GET['error']) ? trim((string) $_GET['error']) : '';
$flashSuccess = isset($_GET['created']) ? 'User created successfully.' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create User</title>
<<<<<<< ours
<<<<<<< ours
  <link rel="icon" type="image/png" href="/canteen/canteen-system/frontend/img/TabIcon.png">
=======
>>>>>>> theirs
=======
>>>>>>> theirs
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo $assetBase; ?>/assets/css/custom.css">
</head>
<body data-auth-base="<?php echo $backendBase; ?>/api/auth" data-auth-base-abs="<?php echo $backendApiAbsolute; ?>">
<?php require __DIR__ . '/../../partials/navbar.php'; ?>
<div class="sidebar-layout">
  <?php require __DIR__ . '/../../partials/sidebar.php'; ?>
  <main class="content">
    <script>
      // Explicit auth base for user creation requests.
      window.AUTH_API_BASE = '<?php echo $backendApiAbsolute; ?>';
    </script>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Add New User</h1>
      <a href="index.php" class="btn btn-outline-secondary">Back to Users</a>
    </div>
    <div class="card shadow-sm">
      <div class="card-body">
        <?php if ($flashError): ?>
          <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($flashSuccess): ?>
          <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <div id="create-alert" class="alert d-none" role="alert"></div>
        <form id="create-user-form" method="post" action="<?php echo $backendBase; ?>/api/auth/register.php" novalidate>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required minlength="4">
            </div>
            <div class="col-md-6">
              <label for="role" class="form-label">Role</label>
              <select class="form-select" id="role" name="role">
                <option value="admin">Admin</option>
                <option value="kitchen" selected>Kitchen</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required minlength="8">
            </div>
            <div class="col-md-6">
              <label for="confirm_password" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
            </div>
          </div>
          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create User</button>
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
</html>
=======
</html>
>>>>>>> theirs
=======
</html>
>>>>>>> theirs
