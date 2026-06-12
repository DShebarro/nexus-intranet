<?php
namespace App\Core;

class Logger
{
    private const LEVELS = ['DEBUG', 'INFO', 'WARNING', 'ERROR'];

    public static function log(string $level, string $message, array $context = []): void
    {
        $level = strtoupper($level);
        if (!in_array($level, self::LEVELS, true)) {
            $level = 'INFO';
        }

        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $contextStr = $context ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $line = sprintf(
            "[%s] %s: %s%s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $contextStr
        );

        file_put_contents("{$logDir}/app.log", $line, FILE_APPEND | LOCK_EX);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log('DEBUG', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }
}
