<?php
declare(strict_types=1);

namespace Canteen\Lib;

final class Command
{
    /**
     * Execute a Node.js script with arguments and capture stdout/stderr.
     *
     * @param string $script Absolute path to the script file.
     * @param array<int, string> $args Additional CLI arguments.
     * @return array{exitCode:int, stdout:string, stderr:string}
     */
    public static function runNode(string $script, array $args = [], int $timeoutSeconds = 60): array
    {
        $nodeBinary = getenv('NODE_BINARY');
        $node = trim($nodeBinary !== false ? $nodeBinary : '', " \t\n\r\0\x0B\"'");
        $node = $node !== '' ? $node : 'node';

        $command = array_merge([$node, $script], $args);
        // File-backed output avoids pipe deadlocks, including on Windows where
        // proc_open pipes cannot reliably be made non-blocking.
        $stdoutFile = tmpfile();
        $stderrFile = tmpfile();
        if ($stdoutFile === false || $stderrFile === false) {
            if (is_resource($stdoutFile)) fclose($stdoutFile);
            if (is_resource($stderrFile)) fclose($stderrFile);
            return ['exitCode' => 1, 'stdout' => '', 'stderr' => 'Unable to create command output files'];
        }
        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => $stdoutFile,
            2 => $stderrFile,
        ];

        $process = @proc_open($command, $descriptorSpec, $pipes, dirname($script));
        if (!\is_resource($process)) {
            fclose($stdoutFile);
            fclose($stderrFile);
            return ['exitCode' => 1, 'stdout' => '', 'stderr' => 'Unable to spawn process'];
        }

        fclose($pipes[0]);
        $phpLimit = (int) ini_get('max_execution_time');
        $timeoutSeconds = max(1, $phpLimit > 0
            ? min($timeoutSeconds, max(1, $phpLimit - 10)) : $timeoutSeconds);
        $deadline = microtime(true) + $timeoutSeconds;
        $timedOut = false;
        do {
            $status = proc_get_status($process);
            if (!$status['running']) break;
            if (microtime(true) >= $deadline) {
                $timedOut = true;
                proc_terminate($process, 9);
                break;
            }
            usleep(50000);
        } while (true);
        $closeCode = proc_close($process);
        $exitCode = $timedOut ? 124 : ($status['exitcode'] >= 0 ? $status['exitcode'] : $closeCode);
        rewind($stdoutFile);
        rewind($stderrFile);
        $stdout = stream_get_contents($stdoutFile) ?: '';
        $stderr = stream_get_contents($stderrFile) ?: '';
        fclose($stdoutFile);
        fclose($stderrFile);
        if ($timedOut) {
            $stderr = 'Google Sheets command timed out after ' . $timeoutSeconds
                . ' seconds. Check the server connection to Google Sheets and the database, then retry.';
        }

        return [
            'exitCode' => (int) $exitCode,
            'stdout' => $stdout,
            'stderr' => $stderr,
        ];
    }
}
