<?php
declare(strict_types=1);

use Canteen\Lib\Auth;

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../backend/lib/auth.php';

$user = Auth::user();
if (!$user) {
    header('Location: ' . \Canteen\Config\frontendUrl('index.php'));
    exit;
}
?>
<footer class="mt-auto py-3 bg-white border-top text-center">
  <div class="container">
    <span class="text-muted">&copy; <?php echo date('Y'); ?> Concentrix Ghana Canteen</span>
  </div>
</footer>
