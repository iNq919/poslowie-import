<?php

namespace App\Importer;

class Logger
{
    private const string LOG_FILE_PATH = 'logs/poslowie_import.log';

    public static function error(string $message): void
    {
        $log_path = POSLOWIE_IMPORTER_PLUGIN_DIR . self::LOG_FILE_PATH;

        if (!file_exists(dirname($log_path))) {
            mkdir(dirname($log_path), 0755, true);
        }

        $timestamp = date('[Y-m-d H:i:s]');
        $log_message = $timestamp . ' ' . $message . PHP_EOL;

        error_log($log_message, 3, $log_path);
    }
}