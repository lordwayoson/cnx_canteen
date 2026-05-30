<?php
declare(strict_types=1);

use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Hardware;
use Canteen\Lib\Response;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/hardware.php';

Cors::apply();
Auth::requireLogin(['admin', 'kitchen']);

$statuses = Hardware::snapshotStatuses();

Response::json([
    'reader' => $statuses['reader'],
    'printer' => $statuses['printer'],
    'database' => $statuses['database'],
    'timestamp' => (new \DateTimeImmutable('now', new \DateTimeZone(getenv('TZ') ?: 'Africa/Accra')))->format(\DateTimeInterface::ATOM),
<<<<<<< ours
<<<<<<< ours
]);
=======
]);
>>>>>>> theirs
=======
]);
>>>>>>> theirs
