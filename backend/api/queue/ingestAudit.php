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

$logFile = __DIR__ . '/../../logs/queue_ingest.log';
if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0775, true);
}

$tz = new DateTimeZone(getenv('TZ') ?: 'Africa/Accra');
<<<<<<< ours
<<<<<<< ours
$serialFilter = getenv('READER_SERIAL') ?: '3212275';

=======
>>>>>>> theirs
=======
>>>>>>> theirs
try {
    $canteenPdo = Config\getCanteenPdo();
    $ingressPdo = Config\getIngressPdo();
    $queueModel = new QueueModel($canteenPdo);
    $userModel = new UserModel($canteenPdo);
    $mealModel = new MealSelectionModel($canteenPdo);

    $result = ingestIngressAudit(
        $canteenPdo,
        $ingressPdo,
        $userModel,
        $mealModel,
        $queueModel,
<<<<<<< ours
<<<<<<< ours
        $serialFilter,
=======
>>>>>>> theirs
=======
>>>>>>> theirs
        $tz
    );

    $line = sprintf(
        "[%s] processed=%d last_audit_id=%d skipped=%d\n",
        (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s'),
        $result['processed_count'] ?? 0,
        $result['last_audit_id'] ?? 0,
        isset($result['skipped']) ? count($result['skipped']) : 0
    );
    @file_put_contents($logFile, $line, FILE_APPEND);

    Response::json([
        'success' => true,
        'processed' => $result['processed_count'] ?? 0,
        'last_audit_id' => $result['last_audit_id'] ?? 0,
        'skipped' => $result['skipped'] ?? [],
    ]);
} catch (Throwable $throwable) {
    @file_put_contents(
        $logFile,
        sprintf("[%s] error: %s\n", (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s'), $throwable->getMessage()),
        FILE_APPEND
    );
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
