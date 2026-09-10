<?php
declare(strict_types=1);
// Isolated test session storage; never modifies application users or production sessions.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../backend/reports/ReportFormatter.php';
require_once __DIR__ . '/../backend/reports/ReportContext.php';
$dir = __DIR__ . '/../storage/reports-qa/sessions';
if (!is_dir($dir)) mkdir($dir, 0700, true);
session_save_path($dir);
session_name(\Canteen\Config\env('SESSION_NAME', 'canteen_session'));
$ids = ['cookieName' => session_name()];
foreach (['admin','kitchen'] as $role) {
    $id = bin2hex(random_bytes(16));
    session_id($id);
    session_start();
    $_SESSION['user'] = ['id'=>0, 'username'=>'qa.' . $role, 'role'=>$role];
    $ids[$role] = $id;
    if ($role === 'admin') $ids['fixture'] = \Canteen\Reports\ReportContext::save(json_decode(file_get_contents(dirname($dir) . '/fixture.json'), true, 512, JSON_THROW_ON_ERROR));
    session_write_close();
}
file_put_contents(dirname($dir) . '/sessions.json', json_encode($ids));
echo "Isolated QA sessions prepared.\n";


