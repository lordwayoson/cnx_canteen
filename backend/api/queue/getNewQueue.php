<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Response;
use function Canteen\Lib\QueueIngest\ingestIngressAudit;
use Canteen\Models\QueueModel;
use Canteen\Models\UserModel;
use Canteen\Models\MealSelectionModel;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/queue_ingest.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../models/QueueModel.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/MealSelectionModel.php';

Cors::apply();
Auth::requireLogin(['admin', 'kitchen']);

try {
    $tz = new DateTimeZone(getenv('TZ') ?: 'Africa/Accra');
    $date = (new DateTimeImmutable('now', $tz))->format('Y-m-d');
    $afterId = isset($_GET['after_id']) && $_GET['after_id'] !== '' ? (int) $_GET['after_id'] : null;
    $staffId = isset($_GET['staff_id']) && $_GET['staff_id'] !== '' ? (int) $_GET['staff_id'] : null;

    $canteenPdo = Config\getCanteenPdo();
    $queueModel = new QueueModel($canteenPdo);

    // Ingest current-day ingress.badging rows before returning queue items so the UI always sees fresh swipes.
    $ingestSummary = null;
    $ingressPdo = null;
    try {
        $ingressPdo = Config\getIngressPdo();
    } catch (Throwable $connError) {
        Response::json(['success' => false, 'error' => 'Ingress connection failed: ' . $connError->getMessage()], 500);
        return;
    }

    if ($ingressPdo) {
        try {
            $userModel = new UserModel($canteenPdo);
            $mealModel = new MealSelectionModel($canteenPdo);
            $ingestSummary = ingestIngressAudit(
                $canteenPdo,
                $ingressPdo,
                $userModel,
                $mealModel,
                $queueModel,
                $tz
            );
        } catch (Throwable $ingestError) {
            Response::json(['success' => false, 'error' => 'Ingress ingest failed: ' . $ingestError->getMessage()], 500);
            return;
        }
    }

    // When after_id is provided, return only rows greater than that id for today (ascending).
    // Otherwise, return the most recent 10 rows for today ordered oldest->newest.
    $items = $queueModel->listForDate($date, 10, $staffId, $afterId);

    $payload = [
        'success' => true,
        'queue' => $items,
        'date' => $date,
    ];
    if ($ingestSummary !== null) {
        $payload['ingest'] = $ingestSummary;
    }

    Response::json($payload);
} catch (Throwable $throwable) {
    Response::json(['success' => false, 'error' => $throwable->getMessage()], 500);
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
