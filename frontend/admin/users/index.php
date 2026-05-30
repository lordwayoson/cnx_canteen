<?php
declare(strict_types=1);

require_once __DIR__ . '/../../partials/auth.php';
require_once __DIR__ . '/../../../backend/lib/auth.php';
require_once __DIR__ . '/../../../backend/config/db.php';
require_once __DIR__ . '/../../../backend/models/UserModel.php';

$user = \Canteen\Lib\Auth::user();
if (($user['role'] ?? '') !== 'admin') {
    header('Location: /canteen-system/frontend/dashboard.php');
    exit;
}

$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
$frontendPos = strpos($scriptPath, '/frontend');
$basePath = $frontendPos !== false ? substr($scriptPath, 0, $frontendPos) : '';
$backendBase = rtrim($basePath . '/backend', '/');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$backendApiAbsolute = $scheme . '://' . $host . $backendBase . '/api/auth';
$assetBase = rtrim($basePath . '/frontend', '/');

$serverUsers = [];
$serverUserError = '';
$flashMessage = '';
$flashVariant = 'success';
if (isset($_GET['created'])) {
    $flashMessage = 'User created successfully.';
}
if (isset($_GET['updated'])) {
    $flashMessage = 'User updated successfully.';
}
if (isset($_GET['deleted'])) {
    $flashMessage = 'User deleted successfully.';
}
try {
    $pdo = \Canteen\Config\getCanteenPdo();
    $userModel = new \Canteen\Models\UserModel($pdo);
    $serverUsers = $userModel->allAdminUsers();
} catch (\Throwable $e) {
    $serverUserError = 'Unable to load users: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
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
      // Provide an explicit auth API base for user management requests.
      window.AUTH_API_BASE = '<?php echo $backendApiAbsolute; ?>';
    </script>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">User Management</h1>
      <a href="create.php" class="btn btn-primary">Add New</a>
    </div>
    <?php if ($flashMessage): ?>
      <div class="alert alert-<?php echo $flashVariant; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <span class="fw-semibold">Users</span>
          <div id="user-table-loader" class="text-muted small d-none">Loading...</div>
        </div>
        <div class="card-body">
          <div id="user-alert" class="alert d-none" role="alert"></div>
          <div class="table-responsive">
            <table class="table table-striped align-middle" id="user-table">
              <thead class="table-light">
              <tr>
                <th scope="col">User ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Created</th>
                <th>Updated</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
                <?php if ($serverUserError): ?>
                  <tr><td colspan="6" class="text-danger text-center"><?php echo htmlspecialchars($serverUserError, ENT_QUOTES, 'UTF-8'); ?></td></tr>
                <?php elseif (empty($serverUsers)): ?>
                  <tr><td colspan="6" class="text-center text-muted">No users found</td></tr>
                <?php else: ?>
                  <?php $rowIndex = 0; foreach ($serverUsers as $row): $rowIndex++; ?>
                    <tr data-user-id="<?php echo (int)($row['id'] ?? 0); ?>" data-row-index="<?php echo $rowIndex; ?>">
                      <td class="text-muted fw-semibold"><?php echo (int)($row['id'] ?? 0); ?></td>
                      <td><?php echo htmlspecialchars($row['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                      <td>
                        <?php
                          $roleValue = strtolower((string)($row['role'] ?? $row['user_role'] ?? 'kitchen'));
                          $roleLabel = $roleValue === 'admin' ? 'Admin' : 'Kitchen';
                        ?>
                        <span class="badge <?php echo $roleLabel === 'Admin' ? 'bg-primary' : 'bg-secondary'; ?>"><?php echo $roleLabel; ?></span>
                      </td>
                    <td><?php echo htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['updated_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm" role="group">
                        <button
                          type="button"
                          class="btn btn-outline-primary btn-edit-user"
                          data-bs-toggle="modal"
                          data-bs-target="#editUserModal"
                          data-action="edit"
                          data-id="<?php echo (int)($row['id'] ?? 0); ?>"
                          data-user-id="<?php echo (int)($row['id'] ?? 0); ?>"
                          data-username="<?php echo htmlspecialchars($row['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                          data-role="<?php echo htmlspecialchars($roleValue, ENT_QUOTES, 'UTF-8'); ?>">
                          Edit
                        </button>
                        <button
                          type="button"
                          class="btn btn-outline-danger btn-delete-user"
                          data-bs-toggle="modal"
                          data-bs-target="#deleteUserModal"
                          data-action="delete"
                          data-id="<?php echo (int)($row['id'] ?? 0); ?>"
                          data-user-id="<?php echo (int)($row['id'] ?? 0); ?>"
                          data-username="<?php echo htmlspecialchars($row['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                          Delete
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="edit-user-form">
        <div class="modal-body">
<<<<<<< ours
<<<<<<< ours
         <div class="container mb-3">
            <div class="mb-3">
              <label class="form-label">User ID:</label>
              <input type="text" class="form-control" id="edit-id" name="id" readonly>
            </div>
            <div class="d-flex mb-2">
              <label class="form-label mt-2 mb-0 me-3">Username:</label>
              <div class="form-control-plaintext fw-semibold" id="edit-username-display">—</div>
            </div>
            <div class="mb-3">
              <label for="edit-role" class="form-label">Role:</label>
              <select class="form-select" name="role" id="edit-role">
                <option value="admin">Admin</option>
                <option value="kitchen">Kitchen</option>
              </select>
            </div>
          </div>
          <div class=" container-fluid border border-1 rounded-2 p-3 mt-3">
            <a class="form-text text-primary mb-4" data-bs-toggle="collapse" href="#password-collapse" aria-expanded="false" aria-controls="password-collapse">
              Edit Password
            </a>
            <div class="container-fluid collapse" id="password-collapse">
              <!--<div class="form-text mb-2 text-primary">Leave password fields blank to keep the current password.</div>-->
              <div class="d-inline col-md-6 mt-3">
                <label for="edit-password" class="form-label">New Password:</label>
                <input type="password" class="form-control" name="password" id="edit-password" minlength="8">
              </div>
              <div class="d-inline col-md-6 mb-3">
                <label for="edit-confirm" class="form-label">Confirm Password:</label>
                <input type="password" class="form-control" name="confirm_password" id="edit-confirm" minlength="8">
              </div>
            </div>
          </div>
=======
=======
>>>>>>> theirs
          <div class="mb-3">
            <label class="form-label">User ID</label>
            <input type="text" class="form-control" id="edit-id" name="id" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="form-control-plaintext fw-semibold" id="edit-username-display">—</div>
          </div>
          <div class="mb-3">
            <label for="edit-role" class="form-label">Role</label>
            <select class="form-select" name="role" id="edit-role">
              <option value="admin">Admin</option>
              <option value="kitchen">Kitchen</option>
            </select>
          </div>
          <div class="row g-3">
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
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="confirm-edit-btn">Confirm Edit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">Are you sure you want to delete <span id="delete-username" class="fw-semibold"></span>?</p>
        <input type="hidden" id="delete-user-id">
        <div class="small text-muted mt-2">User ID: <span id="delete-user-id-display">—</span></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm-delete-btn">Confirm Delete</button>
      </div>
    </div>
  </div>
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
