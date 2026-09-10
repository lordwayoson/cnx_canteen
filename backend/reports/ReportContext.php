<?php
declare(strict_types=1);

namespace Canteen\Reports;

use InvalidArgumentException;

final class ReportContext
{
    public static function save(array $report): string
    {
        $contexts = $_SESSION['report_contexts'] ?? [];
        $contexts = array_filter($contexts, static fn(array $entry): bool => $entry['expires'] > time());
        while (count($contexts) >= 5) array_shift($contexts);
        $token = bin2hex(random_bytes(24));
        $contexts[$token] = ['expires' => time() + 7200, 'report' => $report];
        $_SESSION['report_contexts'] = $contexts;
        return $token;
    }

    public static function get(mixed $token): array
    {
        if (!is_string($token) || !preg_match('/^[a-f0-9]{48}$/D', $token)) throw new InvalidArgumentException('Invalid report reference.');
        $entry = $_SESSION['report_contexts'][$token] ?? null;
        if (!$entry || $entry['expires'] <= time()) throw new InvalidArgumentException('This report preview has expired. Please generate it again from Reports.');
        return $entry['report'];
    }
}
