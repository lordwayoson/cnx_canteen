<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Response;
use Canteen\Models\UserModel;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../models/UserModel.php';

Cors::apply();
$sessionUser = Auth::requireLogin(['admin']);

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$id = isset($input['id']) ? (int) $input['id'] : 0;
$password = $input['password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

if ($id <= 0) {
    Response::json(['success' => false, 'error' => 'Invalid user id'], 422);
    exit;
}

if (strlen($password) < 8) {
    Response::json(['success' => false, 'error' => 'Password must be at least 8 characters'], 422);
    exit;
}

if ($password !== $confirmPassword) {
    Response::json(['success' => false, 'error' => 'Passwords do not match'], 422);
    exit;
}

try {
    $pdo = Config\getCanteenPdo();
    $userModel = new UserModel($pdo);
    $existing = $userModel->findAdminById($id);
    if (!$existing) {
        Response::json(['success' => false, 'error' => 'User not found'], 404);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $userModel->updateAdminPassword($id, $hash);

    Response::json([
        'success' => true,
        'message' => 'Password updated',
        'updated_by' => $sessionUser['username'],
    ]);
} catch (Throwable $e) {
    Response::json(['success' => false, 'error' => 'Unable to update password'], 500);
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
