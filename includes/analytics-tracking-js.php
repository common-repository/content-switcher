<?php if (!defined('ABSPATH')) { exit(); }
$analytics_tracking_id = content_switcher_data('analytics_tracking_id');
if ($analytics_tracking_id != '') {
$analytics_tracking = false;
if (current_user_can('manage_options')) { if (content_switcher_data('administrator_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('edit_pages')) { if (content_switcher_data('editor_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('publish_posts')) { if (content_switcher_data('author_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('edit_posts')) { if (content_switcher_data('contributor_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('read')) { if (content_switcher_data('subscriber_tracked') == 'yes') { $analytics_tracking = true; } }
else { if (content_switcher_data('visitor_tracked') == 'yes') { $analytics_tracking = true; } }
if ($analytics_tracking) { ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $analytics_tracking_id; ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo $analytics_tracking_id; ?>');
</script>
<?php } }