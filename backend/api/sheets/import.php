<?php
declare(strict_types=1);

use Canteen\Lib\Auth;
use Canteen\Lib\Command;
use Canteen\Lib\Cors;
use Canteen\Lib\Response;

require_once __DIR__ . '/../../lib/cors.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';
require_once __DIR__ . '/../../lib/command.php';

Cors::apply();
Auth::requireLogin(['admin']);

$root = dirname(__DIR__, 3);
$script = $root . '/google-sync/index.js';
$result = Command::runNode($script, ['--import']);

if ($result['exitCode'] !== 0) {
    Response::json([
        'error' => 'Failed to execute import command',
        'details' => trim($result['stderr']) ?: 'Unknown error'
    ], 500);
    exit;
}

$data = json_decode($result['stdout'], true);
if ($data === null) {
    Response::json([
        'error' => 'Invalid import response',
        'raw' => $result['stdout'],
        'stderr' => $result['stderr']
    ], 500);
    exit;
}

if (isset($data['error'])) {
    Response::json(['error' => 'Import failed', 'details' => $data['error']], 500);
    exit;
}

<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
Response::json(['message' => 'Import completed', 'summary' => $data]);
=======
Response::json(['message' => 'Import completed', 'summary' => $data]);
>>>>>>> theirs
=======
Response::json(['message' => 'Import completed', 'summary' => $data]);
>>>>>>> theirs
=======
Response::json(['message' => 'Import completed', 'summary' => $data]);
>>>>>>> theirs
