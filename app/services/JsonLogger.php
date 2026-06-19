<?php

class JsonLogger
{
    private string $logFile;

    public function __construct(string $logFile = null)
    {
        // Default to a server-side only log file (not in /public)
        $this->logFile = $logFile ?? (dirname(__DIR__) . '/logs/activity.jsonl');

        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function log(array $event): void
    {
        $event['ts'] = $event['ts'] ?? date('c');

        // Request metadata (best-effort)
        $event['ip'] = $event['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $event['user_agent'] = $event['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

        $line = json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($line === false) {
            // Do not throw from logger; just fail silently.
            return;
        }

        // Append as JSONL to avoid rewriting large arrays
        @file_put_contents($this->logFile, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

