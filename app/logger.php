<?php
// app/logger.php
 
function log_error($msg) {
    $logPath = __DIR__ . '/../storage/logs/app.log';
    $date = date('Y-m-d H:i:s');
    $entry = "[$date] $msg" . PHP_EOL;
    
    // Create directory if not exists
    if (!is_dir(dirname($logPath))) {
        mkdir(dirname($logPath), 0755, true);
    }
 
    file_put_contents($logPath, $entry, FILE_APPEND);
}