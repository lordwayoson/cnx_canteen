<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Response;
use Canteen\Models\MealSelectionModel;
use Canteen\Models\UserModel;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/MealSelectionModel.php';

Cors::apply();
Auth::requireLogin(['admin']);

$staffId = isset($_GET['staff_id']) ? (int) $_GET['staff_id'] : 0;
$shiftType = isset($_GET['shift_type']) && in_array($_GET['shift_type'], ['Day', 'Night'], true) ? $_GET['shift_type'] : 'Day';

if ($staffId <= 0) {
    Response::json(['error' => 'staff_id required'], 422);
    exit;
}

$canteenPdo = Config\getCanteenPdo();
$userModel = new UserModel($canteenPdo);
$mealModel = new MealSelectionModel($canteenPdo);

$user = $userModel->findByCardOrUserId(null, $staffId);
if (!$user) {
    Response::json(['error' => 'Staff member not found'], 404);
    exit;
}

$date = new DateTimeImmutable('now', new DateTimeZone(getenv('TZ') ?: 'Africa/Accra'));
$meal = $mealModel->getMealForStaff((int) $user['userid'], $shiftType, $date);

<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
Response::json(['user' => $user, 'meal' => $meal, 'date' => $date->format('Y-m-d')]);
=======
Response::json(['user' => $user, 'meal' => $meal, 'date' => $date->format('Y-m-d')]);
>>>>>>> theirs
=======
Response::json(['user' => $user, 'meal' => $meal, 'date' => $date->format('Y-m-d')]);
>>>>>>> theirs
=======
Response::json(['user' => $user, 'meal' => $meal, 'date' => $date->format('Y-m-d')]);
>>>>>>> theirs
