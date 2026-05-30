<?php
declare(strict_types=1);

namespace Canteen\Lib;

use DateTimeImmutable;
use DateTimeZone;
use PDO;

require_once __DIR__ . '/reader_socket.php';
require_once __DIR__ . '/reader_sdk.php';

final class Hardware
{
    /** Verify TCP reachability for devices (reader/printer). */
    public static function checkSocket(string $host, int $port, float $timeout = 1.5): bool
    {
        return ReaderSocket::isReachable($host, $port, $timeout);
    }

    /** Reader availability snapshot (TA200) via connectivity check only. */
    public static function readerStatus(): array
    {
<<<<<<< ours
<<<<<<< ours
        $host = getenv('READER_HOST') ?: '192.168.1.30';
=======
        $host = getenv('READER_HOST') ?: '192.168.1.201';
>>>>>>> theirs
=======
        $host = getenv('READER_HOST') ?: '192.168.1.201';
>>>>>>> theirs
        $port = (int) (getenv('READER_PORT') ?: 4370);
        $ok = self::checkSocket($host, $port, 1.5);
        return [
            'ok' => $ok,
            'host' => $host,
            'port' => $port,
            'message' => $ok ? 'Reader Connected' : 'Reader Not Connected',
        ];
    }

    /** ESC/POS printer availability snapshot. */
    public static function printerStatus(): array
    {
        $host = getenv('PRINTER_HOST') ?: '127.0.0.1';
        $port = (int) (getenv('PRINTER_PORT') ?: 9100);
        $ok = self::checkSocket($host, $port, 1.5);
        return [
            'ok' => $ok,
            'host' => $host,
            'port' => $port,
            'message' => $ok ? 'Printer Connected' : 'Printer Not Connected',
        ];
    }

    /** Canteen database availability snapshot. */
    public static function databaseStatus(): array
    {
        try {
            $pdo = \Canteen\Config\getCanteenPdo();
            $pdo->query('SELECT 1');
            return ['ok' => true, 'message' => 'Database Connected'];
        } catch (\Throwable $throwable) {
            return ['ok' => false, 'message' => 'Database Error: ' . $throwable->getMessage()];
        }
    }

    /** Consolidated hardware status snapshot for UI. */
    public static function snapshotStatuses(): array
    {
        return [
            'reader' => self::readerStatus(),
            'printer' => self::printerStatus(),
            'database' => self::databaseStatus(),
        ];
    }

    /**
     * Poll the SDK (or buffer) for new card events. Socket is NOT used for data.
     *
     * @return array<int,array{id:int,cardnumber:string,logged_at:?string}>
     */
    public static function pollReaderEvents(int $lastId, int $limit = 10): array
    {
        return ReaderSdk::readCards($lastId, $limit);
    }

    /** Persist last processed reader id locally. */
    public static function loadReaderCursor(): int
    {
        $path = self::cursorPath();
        if (!is_file($path)) {
            return 0;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            return 0;
        }
        $decoded = json_decode($content, true);
        return isset($decoded['last_id']) ? (int) $decoded['last_id'] : 0;
    }

    public static function saveReaderCursor(int $id): void
    {
        $path = self::cursorPath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        file_put_contents($path, json_encode(['last_id' => $id], JSON_PRETTY_PRINT));
    }

    private static function cursorPath(): string
    {
        return dirname(__DIR__, 1) . '/../storage/cardlog_cursor.json';
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
