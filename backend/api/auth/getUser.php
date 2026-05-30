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
Auth::requireLogin(['admin']);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    Response::json(['success' => false, 'error' => 'Invalid user id'], 422);
    exit;
}

try {
    $pdo = Config\getCanteenPdo();
    $userModel = new UserModel($pdo);
    $user = $userModel->findAdminById($id);

    if (!$user) {
        Response::json(['success' => false, 'error' => 'User not found'], 404);
        exit;
    }

    Response::json(['success' => true, 'data' => $user]);
} catch (Throwable $e) {
    Response::json(['success' => false, 'error' => 'Unable to load user'], 500);
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
