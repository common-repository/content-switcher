<?php
/*
Plugin Name: Content Switcher
Plugin URI: https://www.kleor.com/content-switcher/
Description: Allows you to easily display a random number, a random or variable content on your website, and to optimize your website with Google Optimize and Google Analytics.
Version: 4.2
Author: Kleor
Author URI: https://www.kleor.com
Text Domain: content-switcher
Domain Path: /languages
License: GPL2
*/

/* 
Copyright 2010 Kleor (https://www.kleor.com)

This program is a free software. You can redistribute it and/or 
modify it under the terms of the GNU General Public License as 
published by the Free Software Foundation, either version 2 of 
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, 
but without any warranty, without even the implied warranty of 
merchantability or fitness for a particular purpose. See the 
GNU General Public License for more details.
*/


if (!defined('ABSPATH')) { exit(); }
define('CONTENT_SWITCHER_PATH', plugin_dir_path(__FILE__));
define('CONTENT_SWITCHER_URL', plugin_dir_url(__FILE__));
define('CONTENT_SWITCHER_FOLDER', substr(plugin_basename(__FILE__), 0, -strlen('/content-switcher.php')));
if (!defined('CONTENT_SWITCHER_CUSTOM_FILES_PATH')) { define('CONTENT_SWITCHER_CUSTOM_FILES_PATH', substr(CONTENT_SWITCHER_PATH, 0, -(strlen(CONTENT_SWITCHER_FOLDER) + 1)).'content-switcher-custom-files/'); }
define('CONTENT_SWITCHER_CUSTOM_FILES_URL', site_url().'/'.substr(CONTENT_SWITCHER_CUSTOM_FILES_PATH, strlen(ABSPATH)));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('CONTENT_SWITCHER_VERSION', $plugin_data['Version']);

function content_switcher_path($file) { return (file_exists(CONTENT_SWITCHER_CUSTOM_FILES_PATH.$file) ? CONTENT_SWITCHER_CUSTOM_FILES_PATH : CONTENT_SWITCHER_PATH).$file; }

function content_switcher_url($file) { return (file_exists(CONTENT_SWITCHER_CUSTOM_FILES_PATH.$file) ? CONTENT_SWITCHER_CUSTOM_FILES_URL : CONTENT_SWITCHER_URL).$file; }

if (!function_exists('kleor_do_shortcode')) { include_once content_switcher_path('libraries/shortcodes-functions.php'); }
if (is_admin()) { include_once content_switcher_path('admin.php'); }

function install_content_switcher() { include content_switcher_path('includes/install.php'); }

register_activation_hook(__FILE__, 'install_content_switcher');

$content_switcher_options = (array) get_option('content_switcher');
if ((!isset($content_switcher_options['version'])) || ($content_switcher_options['version'] != CONTENT_SWITCHER_VERSION)) { install_content_switcher(); }


function content_switcher_analytics_tracking_js() { include content_switcher_path('includes/analytics-tracking-js.php'); }

if (content_switcher_data('javascript_enabled') == 'yes') {
if (content_switcher_data('back_office_tracked') == 'yes') { add_action('admin_head', 'content_switcher_analytics_tracking_js'); }
if (content_switcher_data('front_office_tracked') == 'yes') { foreach (array('login_head', 'wp_head') as $hook) { add_action($hook, 'content_switcher_analytics_tracking_js'); } } }


function content_switcher_data($atts) { include content_switcher_path('includes/data.php'); return $data; }


function content_switcher_filter_data($filter, $data) { include content_switcher_path('includes/filter-data.php'); return $data; }


function content_switcher_format_nice_name($string) {
$string = strtolower(content_switcher_strip_accents(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-z0-9_-]/', '', $string);
return $string; }


function content_switcher_i18n($string) { load_content_switcher_textdomain(); return __(__($string), 'content-switcher'); }


function content_switcher_optimize_tracking_js() { include content_switcher_path('includes/optimize-tracking-js.php'); }

if (content_switcher_data('javascript_enabled') == 'yes') { add_action('wp_head', 'content_switcher_optimize_tracking_js'); }


function content_switcher_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


function load_content_switcher_textdomain($domain = '') {
$domain = 'content-switcher'.($domain == '' ? '' : '-'.$domain);
$file = $domain.'-'.apply_filters('plugin_locale', get_locale(), $domain).'.mo';
if (load_textdomain($domain, content_switcher_path('languages/'.$file))) { return true; }
else { return load_textdomain($domain, WP_LANG_DIR.'/plugins/'.$file); } }


$tags = array();
foreach (array('random', 'variable') as $string) {
$function = function($atts, $content) use($string) { include_once content_switcher_path("shortcodes.php"); $function2 = 'content_switcher_'.$string.'_content'; return @$function2($atts, $content); };
for ($i = 0; $i < 4; $i++) { $tag = $string.'-content'.($i == 0 ? '' : $i); $tags[] = $tag; add_shortcode($tag, $function); } }
foreach (array('random-number', 'variable-string') as $tag) {
$tags[] = $tag; add_shortcode($tag, function($atts) use($tag) { include_once content_switcher_path("shortcodes.php"); $function2 = 'content_switcher_'.str_replace('-', '_', $tag); return @$function2($atts); }); }
$tags[] = 'content-switcher'; add_shortcode('content-switcher', 'content_switcher_data');
$content_switcher_shortcodes = $tags;


function replace_content_switcher_shortcodes($data) { include content_switcher_path('includes/replace-shortcodes.php'); return $data; }

add_filter('wp_insert_post_data', 'replace_content_switcher_shortcodes', 10, 1);


foreach (array(
'get_the_excerpt',
'get_the_title',
'single_post_title',
'the_excerpt',
'the_excerpt_rss',
'the_title',
'the_title_attribute',
'the_title_rss',
'widget_text',
'widget_title') as $function) { add_filter($function, 'do_shortcode'); }