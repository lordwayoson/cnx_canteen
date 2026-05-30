<?php
declare(strict_types=1);

use Canteen\Lib\Auth;

require_once __DIR__ . '/../../backend/lib/auth.php';

$user = Auth::user();
if (!$user) {
    header('Location: index.php');
    exit;
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
