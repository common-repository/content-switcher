<?php if (!defined('ABSPATH')) { exit(); }


function content_switcher_random_content($atts, $content) {
$atts = array_map('kleor_do_shortcode_in_attribute', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'string' => ''), $atts));
if ($string != '') { $string = kleor_do_shortcode_in_attribute($string); }
if (isset($GLOBALS['content_switcher_string'])) { $original_content_switcher_string = $GLOBALS['content_switcher_string']; }
$GLOBALS['content_switcher_string'] = $string;
$content = explode('[other]', kleor_do_shortcode($content));
$m = count($content) - 1;
$n = mt_rand(0, $m);
remove_shortcode('string'); add_shortcode('string', 'content_switcher_string');
$content[$n] = content_switcher_filter_data($filter, kleor_do_shortcode($content[$n]));
if (isset($original_content_switcher_string)) { $GLOBALS['content_switcher_string'] = $original_content_switcher_string; }
remove_shortcode('string');
return $content[$n]; }


function content_switcher_random_number($atts) {
$atts = array_map('kleor_do_shortcode_in_attribute', (array) $atts);
extract(shortcode_atts(array('digits' => '', 'filter' => '', 'max' => '', 'min' => '', 'set' => ''), $atts));
foreach (array('digits', 'max', 'min') as $variable) { $$variable = (int) preg_replace('/[^0-9]/', '', $$variable); }
if ($set == '') { if ($min <= $max) { $n = mt_rand($min, $max); } else { $n = mt_rand($max, $min); } }
else { $set = explode('/', $set); $n = $set[mt_rand(0, count($set) - 1)]; }
if ($n >= 0) { $symbol = ''; } else { $symbol = '-'; $n = -$n; }
$number = (string) $n;
$length = strlen($number);
while ($length < $digits) { $number = '0'.$number; $length = $length + 1; }
$number = $symbol.$number;
$number = content_switcher_filter_data($filter, $number);
return $number; }


function content_switcher_string($atts) {
$atts = array_map('kleor_do_shortcode_in_attribute', (array) $atts);
extract(shortcode_atts(array('default' => '', 'filter' => ''), $atts));
$string = $GLOBALS['content_switcher_string'];
if ($string === '') { $string = $default; }
$string = content_switcher_filter_data($filter, $string);
return $string; }


function content_switcher_variable_content($atts, $content) {
$atts = array_map('kleor_do_shortcode_in_attribute', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'name' => 'content', 'string' => '', 'type' => 'get', 'values' => ''), $atts));
if ($string != '') { $string = kleor_do_shortcode_in_attribute($string); }
if (isset($GLOBALS['content_switcher_string'])) { $original_content_switcher_string = $GLOBALS['content_switcher_string']; }
$GLOBALS['content_switcher_string'] = $string;
$content = explode('[other]', kleor_do_shortcode($content));
$m = count($content);

$type = strtolower($type); switch ($type) {
case 'cookie': $TYPE = $_COOKIE; break;
case 'env': $TYPE = $_ENV; break;
case 'globals': $TYPE = $GLOBALS; break;
case 'post': $TYPE = $_POST; break;
case 'request': $TYPE = $_REQUEST; break;
case 'server': $TYPE = $_SERVER; break;
case 'session': $TYPE = $_SESSION; break;
default: $TYPE = $_GET; }

if (isset($TYPE[$name])) {
if ($m == 1) { $n = 0; $content[0] = htmlspecialchars($TYPE[$name]); }
else {
if ($values == '') { $n = (floor((float) $TYPE[$name]))%$m; }
else {
$values = explode('/', $values);
$v = count($values); $n = 0;
for ($i = 0; $i < $v; $i++) { if ($TYPE[$name] == $values[$i]) { $n = $i; } } } } }
else { $n = 0; }

add_shortcode('string', 'content_switcher_string');
$content[$n] = content_switcher_filter_data($filter, kleor_do_shortcode($content[$n]));
if (isset($original_content_switcher_string)) { $GLOBALS['content_switcher_string'] = $original_content_switcher_string; }
remove_shortcode('string');
return $content[$n]; }


function content_switcher_variable_string($atts) {
$atts = array_map('kleor_do_shortcode_in_attribute', (array) $atts);
extract(shortcode_atts(array('default' => '', 'filter' => '', 'name' => 'content', 'type' => 'get'), $atts));

$type = strtolower($type); switch ($type) {
case 'cookie': $TYPE = $_COOKIE; break;
case 'env': $TYPE = $_ENV; break;
case 'globals': $TYPE = $GLOBALS; break;
case 'post': $TYPE = $_POST; break;
case 'request': $TYPE = $_REQUEST; break;
case 'server': $TYPE = $_SERVER; break;
case 'session': $TYPE = $_SESSION; break;
default: $TYPE = $_GET; }

if (!isset($TYPE[$name])) { $string = ''; }
else { $string = htmlspecialchars($TYPE[$name]); }
if ($string === '') { $string = $default; }
$string = content_switcher_filter_data($filter, $string);
return $string; }