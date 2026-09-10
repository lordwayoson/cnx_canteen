<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/lib/command.php';

use Canteen\Lib\Command;

$script = tempnam(sys_get_temp_dir(), 'command-test-');
if ($script === false) throw new RuntimeException('Unable to create test script');
function check(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}
try {
    file_put_contents($script, 'process.stderr.write("e".repeat(262144)); process.stdout.write("ok");');
    $result = Command::runNode($script, [], 10);
    check($result['exitCode'] === 0 && $result['stdout'] === 'ok', 'Large stderr must not block stdout');
    check(strlen($result['stderr']) === 262144, 'Stderr must be captured completely');

    file_put_contents($script, 'process.stderr.write("failed"); process.exitCode = 7;');
    $result = Command::runNode($script, [], 10);
    check($result['exitCode'] === 7 && $result['stderr'] === 'failed', 'Preserve nonzero exit code');

    file_put_contents($script, 'setInterval(() => {}, 1000);');
    $start = microtime(true);
    $result = Command::runNode($script, [], 1);
    check($result['exitCode'] === 124, 'Stalled process must time out');
    check(microtime(true) - $start < 5, 'Timeout must terminate the process promptly');
    check(str_contains($result['stderr'], 'timed out'), 'Timeout must explain the failure');
    echo "All command tests passed\n";
} finally {
    unlink($script);
}
