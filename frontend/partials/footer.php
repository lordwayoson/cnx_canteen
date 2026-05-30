<?php
declare(strict_types=1);
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours

use Canteen\Lib\Auth;

require_once __DIR__ . '/../../backend/lib/auth.php';

$user = Auth::user();
if (!$user) {
    header('Location: index.php');
    exit;
}
=======
=======
>>>>>>> theirs
=======
>>>>>>> theirs
?>
<footer class="mt-auto py-3 bg-white border-top text-center">
  <div class="container">
    <span class="text-muted">&copy; <?php echo date('Y'); ?> Concentrix Ghana Canteen</span>
  </div>
</footer>
<<<<<<< ours
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
