<?php if (!defined('ABSPATH')) { exit(); }
if (((isset($_GET['page'])) && (strstr($_GET['page'], 'content-switcher'))) || (strstr($_SERVER['REQUEST_URI'], '/plugins.php'))) { load_content_switcher_textdomain(); }


function content_switcher_options_page() {
add_options_page('Content Switcher', 'Content Switcher', 'manage_options', 'content-switcher', function() { include_once content_switcher_path('options-page.php'); }); }

add_action('admin_menu', 'content_switcher_options_page');


function content_switcher_options_page_css() { ?>
<style media="all">
.wrap h2 { float: left; }
.wrap input.button-secondary, .wrap select { vertical-align: 0; }
.wrap p.submit { margin: 0 20%; }
.wrap ul.subsubsub { margin: 1em 0 1.5em 6em; float: left; white-space: normal; }
.wrap .description { color: #606060; }
.wrap .updated-notice { background-color: #ffffc0; border: 1px solid #f0e080; margin: 1em 1.5em 1em 0; padding: 0 0.5em; }
</style>
<?php }

if ((isset($_GET['page'])) && (strstr($_GET['page'], 'content-switcher'))) { add_action('admin_head', 'content_switcher_options_page_css'); }


function content_switcher_meta_box($post) {
load_content_switcher_textdomain();
$links = array(
'' => __('Documentation', 'content-switcher'),
'#variable-contents' => __('Display a variable content', 'content-switcher'),
'#random-contents' => __('Display a random content', 'content-switcher'),
'#screen-options-wrap' => __('Hide this box', 'content-switcher')); ?>
<p><a target="_blank" href="https://www.kleor.com/content-switcher/"><?php echo $links['']; ?></a><span id="content-switcher-screen-options-link"></span></p>
<script>document.getElementById("content-switcher-screen-options-link").innerHTML = ' | <a style="color: #606060;" href="#screen-options-wrap" onclick="document.getElementById(\'show-settings-link\').click(); document.getElementById(\'content-switcher-hide\').click();"><?php echo $links['#screen-options-wrap']; ?></a>';</script>
<ul>
<?php foreach (array('', '#screen-options-wrap') as $url) { unset($links[$url]); }
foreach ($links as $url => $text) {
echo '<li><a target="_blank" href="https://www.kleor.com/content-switcher/'.$url.'">'.$text.'</a></li>'; } ?>
</ul>
<?php }

add_action('add_meta_boxes', function() { if (current_user_can("manage_options")) {
foreach (array("page", "post") as $type) { add_meta_box("content-switcher", "Content Switcher", "content_switcher_meta_box", $type, "side"); } } });


function content_switcher_action_links($links) {
if (current_user_can('manage_options')) {
if (!is_network_admin()) {
$links = array_merge($links, array(
'<span class="delete"><a href="options-general.php?page=content-switcher&amp;action=uninstall" title="'.__('Delete the options of Content Switcher', 'content-switcher').'">'.__('Uninstall', 'content-switcher').'</a></span>',
'<span class="delete"><a href="options-general.php?page=content-switcher&amp;action=reset" title="'.__('Reset the options of Content Switcher', 'content-switcher').'">'.__('Reset', 'content-switcher').'</a></span>',
'<a href="options-general.php?page=content-switcher">'.__('Options', 'content-switcher').'</a>')); }
else {
$links = array_merge($links, array(
'<span class="delete"><a href="../options-general.php?page=content-switcher&amp;action=uninstall&amp;for=network" title="'.__('Delete the options of Content Switcher for all sites in this network', 'content-switcher').'">'.__('Uninstall', 'content-switcher').'</a></span>')); } }
return $links; }

foreach (array('', 'network_admin_') as $prefix) { add_filter($prefix.'plugin_action_links_'.CONTENT_SWITCHER_FOLDER.'/content-switcher.php', 'content_switcher_action_links', 10, 2); }


function content_switcher_row_meta($links, $file) {
if ($file == CONTENT_SWITCHER_FOLDER.'/content-switcher.php') {
$links = array_merge($links, array(
'<a href="https://www.kleor.com/content-switcher/">'.__('Documentation', 'content-switcher').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'content_switcher_row_meta', 10, 2);


function reset_content_switcher() {
load_content_switcher_textdomain();
include content_switcher_path('initial-options.php');
update_option('content_switcher', $initial_options); }


function uninstall_content_switcher($for = 'single') { include content_switcher_path('includes/uninstall.php'); }