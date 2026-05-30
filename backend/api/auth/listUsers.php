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

try {
    $pdo = Config\getCanteenPdo();
    $model = new UserModel($pdo);
    $users = $model->allAdminUsers();
    Response::json(['success' => true, 'data' => $users]);
} catch (Throwable $e) {
    // Surface a concise error so the frontend can show a meaningful message
    Response::json([
        'success' => false,
        'error' => 'Unable to load users: ' . $e->getMessage(),
    ], 500);
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
