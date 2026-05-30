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

if ($id <= 0) {
    Response::json(['success' => false, 'error' => 'Invalid user id'], 422);
    exit;
}

if ($id === (int) $sessionUser['id']) {
    Response::json(['success' => false, 'error' => 'You cannot delete your own account'], 400);
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

    $userModel->deleteAdminUser($id);
    Response::json(['success' => true, 'message' => 'User deleted']);
} catch (Throwable $e) {
    Response::json(['success' => false, 'error' => 'Unable to delete user'], 500);
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
