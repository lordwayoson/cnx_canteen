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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/custom.css">
</head>
<body>
<?php require __DIR__ . '/../../partials/navbar.php'; ?>
<div class="sidebar-layout">
  <?php require __DIR__ . '/../../partials/sidebar.php'; ?>
  <main class="content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Edit User</h1>
      <a href="index.php" class="btn btn-outline-secondary">Back to Users</a>
    </div>
    <div class="card shadow-sm">
      <div class="card-body">
        <form id="edit-user-form" data-user-id="<?php echo $userId; ?>">
          <input type="hidden" name="id" id="edit-id" value="<?php echo $userId; ?>">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Username</label>
              <div class="form-control-plaintext fw-semibold" id="edit-username-display">Loading...</div>
            </div>
            <div class="col-md-6">
              <label for="edit-role" class="form-label">Role</label>
              <select class="form-select" name="role" id="edit-role">
                <option value="admin">Admin</option>
                <option value="kitchen">Kitchen</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="edit-password" class="form-label">New Password</label>
              <input type="password" class="form-control" name="password" id="edit-password" minlength="8">
            </div>
            <div class="col-md-6">
              <label for="edit-confirm" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" name="confirm_password" id="edit-confirm" minlength="8">
            </div>
          </div>
          <div class="form-text mt-2">Leave password fields blank to keep the current password.</div>
          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
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
