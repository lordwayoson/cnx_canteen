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
$username = isset($input['username']) ? trim((string) $input['username']) : '';
$password = $input['password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';
$roleRaw = $input['role'] ?? 'kitchen';
$role = strtolower((string) $roleRaw);
$role = in_array($role, ['admin', 'kitchen'], true) ? $role : 'kitchen';

// Detect whether the caller expects a redirect (e.g., a normal form post) or
// a JSON response (AJAX/fetch). This keeps the UI from dumping raw JSON when
// JavaScript is unavailable or fails to intercept the submission.
$isJsonRequest = false;
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if ($contentType && stripos($contentType, 'application/json') !== false) {
    $isJsonRequest = true;
}
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $isJsonRequest = true;
}

$redirectBase = (function (): string {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $backendPos = strpos($scriptName, '/backend');
    $base = $backendPos !== false ? substr($scriptName, 0, $backendPos) : '';
    return rtrim($base, '/');
})();

$validationError = function (string $message) use ($isJsonRequest, $redirectBase) {
    if ($isJsonRequest) {
        Response::json(['success' => false, 'error' => $message], 422);
    } else {
        header('Location: ' . $redirectBase . '/frontend/admin/users/create.php?error=' . urlencode($message));
    }
    exit;
};

if (strlen($username) < 4) {
    $validationError('Username must be at least 4 characters');
}

if (strlen($password) < 8) {
    $validationError('Password must be at least 8 characters');
}

if ($password !== $confirmPassword) {
    $validationError('Password confirmation does not match');
}

try {
    $pdo = Config\getCanteenPdo();
    $userModel = new UserModel($pdo);

    if ($userModel->findAdminByUsername($username)) {
        if ($isJsonRequest) {
            Response::json(['success' => false, 'error' => 'Username already exists'], 409);
        } else {
            header('Location: ' . $redirectBase . '/frontend/admin/users/create.php?error=' . urlencode('Username already exists'));
        }
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $userId = $userModel->createAdminUser($username, $hash, $role);

    // If this was a traditional form post (non-AJAX), redirect back to the
    // user list to avoid dumping raw JSON in the browser. Compute the base
    // path relative to "/backend" so installs under nested folders (e.g.,
    // /canteen/canteen-system) resolve correctly.
    if (!$isJsonRequest) {
        $location = $redirectBase . '/frontend/admin/users/index.php?created=1';
        header('Location: ' . $location);
        exit;
    }

    Response::json([
        'success' => true,
        'message' => 'User created',
        'id' => $userId,
        'created_by' => $sessionUser['username'],
    ]);
} catch (Throwable $e) {
    Response::json(['success' => false, 'error' => 'Unable to create user'], 500);
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
