<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';

$basePath = \Canteen\Config\requestBasePath();
$frontendBase = \Canteen\Config\frontendBasePath();
$backendBase = \Canteen\Config\backendBasePath();
$backendApiPath = \Canteen\Config\apiUrl();
$backendApiUrl = \Canteen\Config\absoluteUrl($backendApiPath);
?>
<script>
  window.CANTEEN_BASE = <?php echo json_encode($basePath); ?>;
  window.CANTEEN_FRONTEND_BASE = <?php echo json_encode($frontendBase); ?>;
  window.CANTEEN_BACKEND_BASE = <?php echo json_encode($backendBase); ?>;
  window.CANTEEN_BACKEND_API_BASE = <?php echo json_encode($backendApiPath); ?>;
  window.CANTEEN_BACKEND_API_URL = <?php echo json_encode($backendApiUrl); ?>;
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.8/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/r-2.5.0/datatables.min.js"></script>
<script src="<?php echo \Canteen\Config\h(\Canteen\Config\assetUrl('assets/js/formHandlers.js')); ?>"></script>
<script src="<?php echo \Canteen\Config\h(\Canteen\Config\assetUrl('assets/js/charts.js')); ?>"></script>
<script src="<?php echo \Canteen\Config\h(\Canteen\Config\assetUrl('assets/js/users.js')); ?>"></script>
