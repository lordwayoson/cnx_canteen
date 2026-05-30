<?php
declare(strict_types=1);

use Canteen\Lib\Cors;
use Canteen\Lib\Hardware;
use Canteen\Lib\Response;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/hardware.php';

Cors::apply();

try {
    $limit = isset($_GET['limit']) ? max(1, (int) $_GET['limit']) : 5;
    $events = Hardware::pollReaderEvents(0, $limit);
    Response::json(['events' => $events]);
} catch (Throwable $throwable) {
    Response::json(['error' => 'Reader listen failed', 'detail' => $throwable->getMessage()], 500);
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
