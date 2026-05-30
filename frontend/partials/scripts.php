<?php
declare(strict_types=1);

// Ensure asset and API paths resolve correctly whether the current page lives at
// /frontend or a nested path such as /frontend/reports/.
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$frontendPos = strpos($scriptName, '/frontend');
$assetBase = $frontendPos !== false
    ? substr($scriptName, 0, $frontendPos + strlen('/frontend'))
    : '';

// Compute the base path of the project relative to the document root. When the
// DocumentRoot points directly at /frontend (a common Windows/XAMPP setup), the
// project root is actually one level up; fall back to deriving the base from
// the current script path in that scenario.
$docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$projectRoot = realpath(__DIR__ . '/../..') ?: '';
$basePath = '';
if ($docRoot && $projectRoot && str_starts_with($projectRoot, $docRoot)) {
    $basePath = str_replace('\\', '/', substr($projectRoot, strlen($docRoot)));
    $basePath = '/' . trim($basePath, '/');
}
// Fallback: derive the base from the URL path when the document root is the
// frontend directory (or otherwise not a prefix of the project root).
if ($basePath === '' && $frontendPos !== false) {
    $basePath = substr($scriptName, 0, $frontendPos);
}
$basePath = rtrim($basePath, '/');
$frontendBase = rtrim($basePath . '/frontend', '/');
$backendBase = rtrim($basePath . '/backend', '/');

// Build a fully qualified backend API base so nested pages always reach the
// PHP endpoints even when the frontend lives under a different DocumentRoot.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') === '443';
$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$backendApiUrl = sprintf('%s://%s%s/api', $scheme, $host, $backendBase);
?>
<script>
  window.CANTEEN_BASE = '<?php echo $basePath; ?>';
  window.CANTEEN_FRONTEND_BASE = '<?php echo $frontendBase; ?>';
  window.CANTEEN_BACKEND_BASE = '<?php echo $backendBase; ?>';
  window.CANTEEN_BACKEND_API_URL = '<?php echo $backendApiUrl; ?>';
  window.CANTEEN_BACKEND_API_BASE = '<?php echo $backendBase; ?>/api';
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.8/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/r-2.5.0/datatables.min.js"></script>
<script src="<?php echo $assetBase; ?>/assets/js/formHandlers.js"></script>
<script src="<?php echo $assetBase; ?>/assets/js/charts.js"></script>
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
<script src="<?php echo $assetBase; ?>/assets/js/users.js"></script>
=======
<script src="<?php echo $assetBase; ?>/assets/js/users.js"></script>
>>>>>>> theirs
=======
<script src="<?php echo $assetBase; ?>/assets/js/users.js"></script>
>>>>>>> theirs
=======
<script src="<?php echo $assetBase; ?>/assets/js/users.js"></script>
>>>>>>> theirs
