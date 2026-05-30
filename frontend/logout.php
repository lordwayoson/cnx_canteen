<?php
declare(strict_types=1);

use Canteen\Lib\Auth;

require_once __DIR__ . '/../backend/lib/auth.php';

Auth::logout();
// Build a consistent redirect target regardless of the current directory
// (e.g., /frontend/reports/ or /frontend/admin/users/).
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$frontendPos = strpos($script, '/frontend');
$frontendBase = $frontendPos !== false ? substr($script, 0, $frontendPos + strlen('/frontend')) : '/frontend';
$frontendBase = rtrim($frontendBase, '/');
header('Location: ' . $frontendBase . '/index.php');
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
exit;
=======
exit;
>>>>>>> theirs
=======
exit;
>>>>>>> theirs
=======
exit;
>>>>>>> theirs
