<?php
declare(strict_types=1);

namespace Canteen\Lib;

/**
 * FingerTec TA200 socket reachability helper.
 * The socket is used ONLY to test connectivity; it does not parse card events.
 */
final class ReaderSocket
{
    public static function isReachable(string $host, int $port, float $timeout = 1.5): bool
    {
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($socket === false) {
            return false;
        }
        fclose($socket);
        return true;
    }
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
