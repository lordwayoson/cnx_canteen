<?php
declare(strict_types=1);

namespace Canteen\Lib;

final class Cors
{
    public static function apply(): void
    {
        $allowedOrigins = getenv('CORS_ALLOWED_ORIGINS') ?: 'http://localhost';
        header('Access-Control-Allow-Origin: ' . $allowedOrigins);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Vary: Origin');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
