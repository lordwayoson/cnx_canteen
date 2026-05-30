<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Response;
use function Canteen\Lib\QueueIngest\ingestIngressAudit;
use Canteen\Models\MealSelectionModel;
use Canteen\Models\QueueModel;
use Canteen\Models\UserModel;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/queue_ingest.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/MealSelectionModel.php';
require_once __DIR__ . '/../../models/QueueModel.php';

Cors::apply();
Auth::requireLogin(['admin', 'kitchen']);

try {
    $tz = new DateTimeZone(getenv('TZ') ?: 'Africa/Accra');
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
    $serialFilter = getenv('READER_SERIAL') ?: '3212275';
=======
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
    $canteenPdo = Config\getCanteenPdo();
    $queueModel = new QueueModel($canteenPdo);
    $userModel = new UserModel($canteenPdo);
    $mealModel = new MealSelectionModel($canteenPdo);

<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
    // Ingest new ingress.auditdata rows on each poll so the queue is sourced from live reader events.
=======
    // Ingest new ingress.badging rows on each poll so the queue is sourced from live reader events.
>>>>>>> theirs
=======
    // Ingest new ingress.badging rows on each poll so the queue is sourced from live reader events.
>>>>>>> theirs
=======
    // Ingest new ingress.badging rows on each poll so the queue is sourced from live reader events.
>>>>>>> theirs
    // If ingress is unreachable or an event fails to parse, we still want to return the current queue
    // instead of failing the entire request.
    $ingestWarning = null;
    $ingressPdo = null;
    try {
        $ingressPdo = Config\getIngressPdo();
    } catch (\Throwable $connError) {
        $ingestWarning = 'Ingress unavailable: ' . $connError->getMessage();
    }

    if ($ingressPdo instanceof PDO) {
        try {
            ingestIngressAudit(
                $canteenPdo,
                $ingressPdo,
                $userModel,
                $mealModel,
                $queueModel,
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
                //$serialFilter,
=======
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
                $tz
            );
        } catch (\Throwable $ingestError) {
            $ingestWarning = $ingestError->getMessage();
        }
    }

    $date = (new DateTimeImmutable('now', $tz))->format('Y-m-d');
    $staffId = isset($_GET['staff_id']) && $_GET['staff_id'] !== '' ? (int) $_GET['staff_id'] : null;
    $sinceId = isset($_GET['since_id']) && $_GET['since_id'] !== '' ? (int) $_GET['since_id'] : null;

    // When filtering by staff, return all of today's entries for that staff.
    // Otherwise, support incremental polling by returning only rows with id greater than since_id,
    // falling back to the most recent 10 for the initial load.
    $items = $queueModel->listForDate($date, 10, $staffId, $staffId !== null ? null : $sinceId);

    $payload = ['queue' => $items, 'date' => $date];
    if ($ingestWarning) {
        $payload['warning'] = 'Ingress ingest skipped: ' . $ingestWarning;
    }

    Response::json($payload);
} catch (\Throwable $throwable) {
    Response::json(['error' => 'Queue load failed', 'detail' => $throwable->getMessage()], 500);
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
