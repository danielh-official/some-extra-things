<?php

namespace App\Services;

class AppleScriptRunner
{
    /**
     * Run an AppleScript and return [stdout, stderr, exitCode].
     *
     * @return array{string, string, int}
     */
    public function run(string $script): array
    {
        $descriptors = [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']];
        $process = proc_open('osascript', $descriptors, $pipes);

        if (! is_resource($process)) {
            return ['', 'Failed to start osascript process.', 1];
        }

        fwrite($pipes[0], $script);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return [$output ?: '', $error ?: '', $exitCode];
    }
}
