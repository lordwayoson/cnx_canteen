<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Response;
use Canteen\Models\QueueModel;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../models/QueueModel.php';

Cors::apply();
Auth::requireLogin(['admin', 'kitchen']);

try {
    $canteenPdo = Config\getCanteenPdo();
    $queueModel = new QueueModel($canteenPdo);

    $date = (new DateTimeImmutable('now', new DateTimeZone(getenv('TZ') ?: 'Africa/Accra')))->format('Y-m-d');
    $staffId = isset($_GET['staff_id']) && $_GET['staff_id'] !== '' ? (int) $_GET['staff_id'] : null;
    $items = $queueModel->listForDate($date, 10, $staffId);

    Response::json(['queue' => $items, 'date' => $date]);
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
