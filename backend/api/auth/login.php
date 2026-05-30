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

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$username = isset($input['username']) ? trim((string) $input['username']) : '';
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    Response::json(['error' => 'Username and password are required'], 422);
    exit;
}

$pdo = Config\getCanteenPdo();
$userModel = new UserModel($pdo);
$user = $userModel->findAdminByUsername($username);

if (!$user || !password_verify($password, $user['password_hash'])) {
    Response::json(['error' => 'Invalid credentials'], 401);
    exit;
}

Auth::login([
    'id' => (int) $user['id'],
    'username' => $user['username'],
    'role' => $user['role'],
]);

<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
Response::json(['message' => 'Login successful', 'user' => ['username' => $user['username'], 'role' => $user['role']]]);
=======
Response::json(['message' => 'Login successful', 'user' => ['username' => $user['username'], 'role' => $user['role']]]);
>>>>>>> theirs
=======
Response::json(['message' => 'Login successful', 'user' => ['username' => $user['username'], 'role' => $user['role']]]);
>>>>>>> theirs
=======
Response::json(['message' => 'Login successful', 'user' => ['username' => $user['username'], 'role' => $user['role']]]);
>>>>>>> theirs
