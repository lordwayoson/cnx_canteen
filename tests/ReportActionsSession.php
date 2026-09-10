<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') exit;
require_once __DIR__ . '/../backend/lib/auth.php';
\Canteen\Config\loadEnv();
session_name(\Canteen\Config\env('SESSION_NAME', 'canteen_session'));
if (($argv[1] ?? '') === 'cleanup') {
    $id = json_decode(file_get_contents(__DIR__ . '/../storage/reports-qa/apache-session.json'),true)['id'];
    session_id($id);
    session_start();
    session_destroy();
    unlink(__DIR__ . '/../storage/reports-qa/apache-session.json');
} else {
    $dir = __DIR__ . '/../storage/reports-qa';
    if (!is_dir($dir)) mkdir($dir, 0700, true);
    file_put_contents($dir . '/.htaccess', "Require all denied\n");
    session_id(bin2hex(random_bytes(16)));
    session_start();
    $_SESSION['user'] = ['id'=>0,'username'=>'qa.report-buttons','role'=>'admin'];
    file_put_contents(__DIR__ . '/../storage/reports-qa/apache-session.json', json_encode(['id'=>session_id(),'name'=>session_name()]));
    session_write_close();
    echo "Temporary Apache QA session ready.\n";
}
