<?php
declare(strict_types=1);

namespace Canteen\Lib;

use DateTimeImmutable;
use DateTimeZone;

/**
 * FingerTec SDK helper. In production this should call the vendor SDK to pull
 * recent card swipes directly from the device. For local/offline testing we
 * support reading a buffer file defined by READER_BUFFER_FILE (one cardnumber
 * per line); new lines are treated as new swipe events.
 */
final class ReaderSdk
{
    /**
     * @return array<int,array{id:int,cardnumber:string,logged_at:?string}>
     */
    public static function readCards(int $lastId = 0, int $limit = 10): array
    {
        $events = self::readFromBuffer($lastId, $limit);
        // Replace the line above with real SDK reads when available.
        return $events;
    }

    private static function bufferPath(): string
    {
        return getenv('READER_BUFFER_FILE') ?: dirname(__DIR__, 1) . '/../storage/card_events.log';
    }

    /**
     * Simulated reader feed for environments without the SDK.
     */
    private static function readFromBuffer(int $lastId, int $limit): array
    {
        $path = self::bufferPath();
        if (!is_file($path)) {
            return [];
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $events = [];
        $tz = new DateTimeZone(getenv('TZ') ?: 'Africa/Accra');
        foreach ($lines as $index => $line) {
            $id = $index + 1;
            if ($id <= $lastId) {
                continue;
            }
            $card = trim($line);
            if ($card === '') {
                continue;
            }
            $events[] = [
                'id' => $id,
                'cardnumber' => $card,
                'logged_at' => (new DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s'),
            ];
            if (count($events) >= $limit) {
                break;
            }
        }
        return $events;
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
