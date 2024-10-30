<?php if (!defined('ABSPATH')) { exit(); }
$optimize_container_id = content_switcher_data('optimize_container_id');
if ($optimize_container_id != '') { ?>
<script src="https://www.googleoptimize.com/optimize.js?id=<?php echo $optimize_container_id; ?>"></script>
<?php }