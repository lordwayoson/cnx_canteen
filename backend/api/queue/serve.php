<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Receipt;
use Canteen\Lib\Response;
use Canteen\Models\MealSelectionModel;
use Canteen\Models\QueueModel;
use Canteen\Models\UserModel;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/receipt.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/MealSelectionModel.php';
require_once __DIR__ . '/../../models/QueueModel.php';

Cors::apply();
$sessionUser = Auth::requireLogin(['admin', 'kitchen']);

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$cardnumber = isset($input['cardnumber']) ? trim((string) $input['cardnumber']) : null;
$userid = isset($input['userid']) ? (int) $input['userid'] : null;
$shiftType = isset($input['shift_type']) && in_array($input['shift_type'], ['Day', 'Night'], true) ? $input['shift_type'] : 'Day';

try {
    $canteenPdo = Config\getCanteenPdo();
    $userModel = new UserModel($canteenPdo);
    $mealModel = new MealSelectionModel($canteenPdo);
    $queueModel = new QueueModel($canteenPdo);

    $user = $userModel->findByCardOrUserId($cardnumber, $userid);
    if (!$user) {
        Response::json(['error' => 'Staff member not found'], 404);
        exit;
    }

    $date = new DateTimeImmutable('now', new DateTimeZone(getenv('TZ') ?: 'Africa/Accra'));
    $dateString = $date->format('Y-m-d');

    $isVisitor = isset($user['project']) && strtolower((string) $user['project']) === 'visitor';
    if (!$isVisitor && $queueModel->hasServedOnDate((int) $user['userid'], $dateString)) {
        Response::json(['error' => 'Staff has already been served today'], 409);
        exit;
    }

    $meal = $mealModel->getMealForDate((int) $user['userid'], $date);
    if (!$meal || empty($meal['meal_label'])) {
        Response::json(['error' => 'No meal selection found for today'], 404);
        exit;
    }

    $resolvedShift = $meal['shift_type'] ?? $shiftType;
    $fullName = trim(($user['name'] ?? '') . ' ' . ($user['lastname'] ?? ''));
    $timestamp = $date->format('Y-m-d H:i:s');

    $ticketHtml = sprintf(
        '<div style="font-family:Arial,sans-serif;font-size:14px;padding:8px"><h2 style="margin:0">Canteen Meal</h2><p style="margin:4px 0"><strong>Name:</strong> %s<br><strong>Staff ID:</strong> %s<br><strong>Project:</strong> %s<br><strong>Shift:</strong> %s<br><strong>Meal:</strong> %s<br><strong>Diet Notes:</strong> %s<br><strong>Served At:</strong> %s</p></div>',
        htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars((string) $user['userid'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars((string) ($user['project'] ?? ''), ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($resolvedShift, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars((string) $meal['meal_label'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meal['diet_notes'] ?? 'None', ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($timestamp, ENT_QUOTES, 'UTF-8')
    );

    $receiptPrinted = attemptPrintTicket($ticketHtml);
    $receiptStatus = $receiptPrinted ? 'Receipt printed' : 'Receipt not printed';

    $queueModel->addToQueue((int) $user['userid'], $resolvedShift, (string) $meal['meal_label'], $meal['diet_notes'], $sessionUser['id'] ?? null, $receiptStatus);

    Response::json([
        'message' => 'Meal served',
        'staff' => [
            'id' => (int) $user['userid'],
            'name' => $fullName,
            'project' => $user['project'],
        ],
        'meal' => $meal,
        'served_at' => $timestamp,
        'ticket_html' => $ticketHtml,
        'receipt_status' => $receiptStatus,
    ]);
} catch (Throwable $throwable) {
    Response::json(['error' => 'Serving failed', 'detail' => $throwable->getMessage()], 500);
}

function attemptPrintTicket(string $ticketHtml): bool
{
    $plainText = trim(strip_tags($ticketHtml));
    return Receipt::printTicket($plainText);
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
