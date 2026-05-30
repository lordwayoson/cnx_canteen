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
    public static function runNode(string $script, array $args = []): array
    {
        $nodeBinary = getenv('NODE_BINARY');
        $node = $nodeBinary !== false && $nodeBinary !== '' ? $nodeBinary : 'node';

        $command = array_merge([$node, $script], $args);
        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes, dirname($script));
        if (!\is_resource($process)) {
            return ['exitCode' => 1, 'stdout' => '', 'stderr' => 'Unable to spawn process'];
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]) ?: '';
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [
            'exitCode' => (int) $exitCode,
            'stdout' => $stdout,
            'stderr' => $stderr,
        ];
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
