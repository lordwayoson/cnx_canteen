<?php
declare(strict_types=1);

namespace Canteen\Lib;

final class Receipt
{
    /**
     * Attempt to print a plain-text ticket to an ESC/POS-compatible printer.
     * Returns false if the printer cannot be reached or write fails.
     */
    public static function printTicket(string $text): bool
    {
        $host = getenv('PRINTER_HOST') ?: '127.0.0.1';
        $port = (int) (getenv('PRINTER_PORT') ?: 9100);
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($host, $port, $errno, $errstr, 2.0);
        if ($socket === false) {
            return false;
        }
        stream_set_timeout($socket, 2);
        $payload = $text . "\n\n\n"; // basic feed to ensure print
        $written = fwrite($socket, $payload);
        fclose($socket);
        return $written !== false;
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
