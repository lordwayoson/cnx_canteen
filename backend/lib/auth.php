<?php
declare(strict_types=1);

namespace Canteen\Lib;

class Auth
{
    private static bool $bootstrapped = false;

    private static function bootstrap(): void
    {
        if (self::$bootstrapped) {
            return;
        }

        $sessionName = getenv('SESSION_NAME') ?: 'canteen_session';
        if (session_status() === PHP_SESSION_NONE) {
            session_name($sessionName);
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
            ]);
        }
        date_default_timezone_set(getenv('TZ') ?: 'Africa/Accra');
        self::$bootstrapped = true;
    }

    public static function user(): ?array
    {
        self::bootstrap();
        return $_SESSION['user'] ?? null;
    }

    public static function requireLogin(array $roles = []): array
    {
        $user = self::user();
        if (!$user) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }
        if ($roles && !in_array($user['role'], $roles, true)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        return $user;
    }

    public static function login(array $adminUser): void
    {
        self::bootstrap();
        session_regenerate_id(true);
        $_SESSION['user'] = $adminUser;
    }

    public static function logout(): void
    {
        self::bootstrap();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
    }

    public static function ensureRole(string $role): void
    {
        self::requireLogin([$role]);
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
