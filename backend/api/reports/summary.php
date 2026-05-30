<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Response;
use Canteen\Models\MealSelectionModel;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../models/MealSelectionModel.php';

Cors::apply();
Auth::requireLogin(['admin']);

$filters = [
    'start_date' => isset($_GET['start_date']) ? $_GET['start_date'] : null,
    'end_date' => isset($_GET['end_date']) ? $_GET['end_date'] : null,
    'shift_type' => isset($_GET['shift_type']) && in_array($_GET['shift_type'], ['Day', 'Night'], true) ? $_GET['shift_type'] : null,
];

$reportType = isset($_GET['report_type'])
    ? strtolower(trim((string) $_GET['report_type']))
    : null;
if ($reportType !== null && !in_array($reportType, ['meals_served', 'selected_meals', 'daily_totals', 'top_meals'], true)) {
    $reportType = null;
}

try {
    $canteenPdo = Config\getCanteenPdo();
    $mealModel = new MealSelectionModel($canteenPdo);
    $data = $mealModel->getSummary($filters, $reportType);
    Response::json(['filters' => $filters, 'report_type' => $reportType, 'data' => $data]);
} catch (\Throwable $exception) {
    Response::json([
        'error' => 'Unable to load report summary',
        'detail' => $exception->getMessage(),
    ], 500);
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
