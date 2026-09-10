<?php
declare(strict_types=1);

use Canteen\Lib\Auth;

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../backend/lib/auth.php';

Auth::logout();
header('Location: ' . \Canteen\Config\frontendUrl('index.php'));
exit;
