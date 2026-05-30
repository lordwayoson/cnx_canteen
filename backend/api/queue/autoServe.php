<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use function Canteen\Lib\QueueIngest\ingestIngressAudit;
use Canteen\Lib\Receipt;
use Canteen\Lib\Response;
use Canteen\Models\MealSelectionModel;
use Canteen\Models\QueueModel;
use Canteen\Models\UserModel;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/queue_ingest.php';
require_once __DIR__ . '/../../lib/receipt.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/MealSelectionModel.php';
require_once __DIR__ . '/../../models/QueueModel.php';

Cors::apply();
Auth::requireLogin(['admin', 'kitchen']);

$tz = new DateTimeZone(getenv('TZ') ?: 'Africa/Accra');
$canteenPdo = Config\getCanteenPdo();
$ingressPdo = Config\getIngressPdo();
$userModel = new UserModel($canteenPdo);
$mealModel = new MealSelectionModel($canteenPdo);
$queueModel = new QueueModel($canteenPdo);

try {
    $result = ingestIngressAudit(
        $canteenPdo,
        $ingressPdo,
        $userModel,
        $mealModel,
        $queueModel,
        $tz
    );

    Response::json(['success' => true] + $result);
} catch (Throwable $throwable) {
    if ($canteenPdo->inTransaction()) {
        $canteenPdo->rollBack();
    }
    Response::json(['success' => false, 'error' => $throwable->getMessage()], 500);
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
