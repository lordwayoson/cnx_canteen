<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Models\UserModel;

require_once __DIR__ . '/../backend/config/db.php';
require_once __DIR__ . '/../backend/lib/auth.php';
require_once __DIR__ . '/../backend/models/UserModel.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        $pdo = Config\getCanteenPdo();
        $userModel = new UserModel($pdo);
        $user = $userModel->findAdminByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            Auth::login([
                'id' => (int) $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
            ]);
            header('Location: dashboard.php');
            exit;
        }
        $error = 'Invalid credentials provided.';
    }
}

if (Auth::user()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Concentrix Canteen Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="login-page">
  <div class="card shadow-lg login-card">
    <div class="card-body p-4">
      <h1 class="h4 mb-3 text-center">Concentrix Ghana Canteen</h1>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>
      <form method="post" novalidate>
        <div class="mb-3">
          <label class="form-label" for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label" for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>
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
