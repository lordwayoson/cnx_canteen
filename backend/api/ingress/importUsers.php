<?php
declare(strict_types=1);

use Canteen\Config;
use Canteen\Lib\Auth;
use Canteen\Lib\Cors;
use Canteen\Lib\Response;
use Canteen\Models\UserModel;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../models/UserModel.php';

Cors::apply();
Auth::requireLogin(['admin']);

try {
    $ingressPdo = Config\getIngressPdo();
} catch (\Throwable $exception) {
    Response::json([
        'error' => 'Unable to connect to ingress database',
        'details' => $exception->getMessage(),
    ], 500);
    return;
}

try {
    $canteenPdo = Config\getCanteenPdo();
} catch (\Throwable $exception) {
    Response::json([
        'error' => 'Unable to connect to canteen database',
        'details' => $exception->getMessage(),
    ], 500);
    return;
}

$userModel = new UserModel($canteenPdo);

$sql = <<<SQL
    SELECT
        ui.employeeid,
        u.username,
        u.name,
        u.lastname,
        u.email,
<<<<<<< ours
<<<<<<< ours
        COALESCE(ug.gName, u.user_group) AS project,
=======
        COALESCE(ug.gName, u.project) AS project,
>>>>>>> theirs
=======
        COALESCE(ug.gName, u.project) AS project,
>>>>>>> theirs
        MAX(ui.cardnumber) AS cardnumber
    FROM `user_info` AS ui
    INNER JOIN `user` AS u ON u.userid = ui.userid
    LEFT JOIN `user_group` AS ug ON u.user_group = ug.id
    WHERE ui.employeeid IS NOT NULL AND ui.employeeid <> ''
<<<<<<< ours
<<<<<<< ours
    GROUP BY ui.employeeid, u.username, u.name, u.lastname, u.email, ug.gName, u.user_group
=======
    GROUP BY ui.employeeid, u.username, u.name, u.lastname, u.email, ug.gName, u.project
>>>>>>> theirs
=======
    GROUP BY ui.employeeid, u.username, u.name, u.lastname, u.email, ug.gName, u.project
>>>>>>> theirs
    ORDER BY ui.employeeid
SQL;

try {
    $statement = $ingressPdo->query($sql);
} catch (\Throwable $exception) {
    Response::json([
        'error' => 'Failed to read ingress users',
        'details' => $exception->getMessage(),
    ], 500);
    return;
}

if ($statement === false) {
    Response::json(['error' => 'Ingress query failed to execute'], 500);
    return;
}

$imported = 0;

while ($row = $statement->fetch()) {
    $employeeIdRaw = $row['employeeid'];
    $employeeId = null;

    if ($employeeIdRaw !== null && $employeeIdRaw !== '') {
        $employeeId = is_numeric($employeeIdRaw) ? (int) $employeeIdRaw : null;
    }

    if ($employeeId === null) {
        // Skip users that cannot be correlated to canteen IDs.
        continue;
    }

    $username = $row['username'] ?? '';
    $firstName = $row['name'] ?? '';
    $lastName = $row['lastname'] ?? '';
    $cardNumber = $row['cardnumber'] ?? null;

    $username = is_string($username) ? trim($username) : '';
    $firstName = is_string($firstName) ? trim($firstName) : '';
    $lastName = is_string($lastName) ? trim($lastName) : '';
    if (is_string($cardNumber)) {
        $cardNumber = trim($cardNumber) !== '' ? trim($cardNumber) : null;
    } elseif ($cardNumber === '') {
        $cardNumber = null;
    }

    if ($username === '') {
        // Skip rows that cannot be addressed reliably.
        continue;
    }

    if ($firstName === '') {
        $firstName = $username;
    }

    if ($lastName === '') {
        $lastName = 'Staff';
    }

    $userModel->upsertUser([
        'userid' => $employeeId,
        'username' => $username,
        'name' => $firstName,
        'lastname' => $lastName,
        'email' => $row['email'],
        'project' => $row['project'],
        'cardnumber' => $cardNumber,
    ]);
    $imported++;
}

<<<<<<< ours
<<<<<<< ours
Response::json(['message' => 'Ingress users synchronized', 'count' => $imported]);
=======
Response::json(['message' => 'Ingress users synchronized', 'count' => $imported]);
>>>>>>> theirs
=======
Response::json(['message' => 'Ingress users synchronized', 'count' => $imported]);
>>>>>>> theirs
