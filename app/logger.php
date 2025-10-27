<?php
// app/logger.php

/**
 * General application error logger
 */
function log_error($msg) {
    $logPath = __DIR__ . '/../storage/logs/app.log';
    write_log($logPath, $msg);
}

/**
 * Catalog-specific action logger
 */
function logCatalogAction($msg) {
    $logPath = __DIR__ . '/../storage/logs/catalog.log';
    write_log($logPath, $msg);
}

/**
 * Shared log writer (used by both functions)
 */
function write_log($path, $msg) {
    $date = date('Y-m-d H:i:s');
    $entry = "[$date] $msg" . PHP_EOL;

    // Ensure log directory exists
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Rotate if log file > 5MB
    if (file_exists($path) && filesize($path) > 5 * 1024 * 1024) {
        rename($path, $path . '.' . date('Ymd_His') . '.bak');
    }

    // Append the log entry
    file_put_contents($path, $entry, FILE_APPEND);
}
