<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Hug\Html\Html as Html;

$html = file_get_contents(__DIR__ . '/../data/free.fr.html');

/* ************************************************* */
/* ************* Html::is_external_link ************ */
/* ************************************************* */

$domain = 'hugo.maugey.fr';
$link = 'http://maugey.fr/coucou.html';
$test = Html::is_external_link($domain, $link);
echo 'Html::is_external_link' . "\n";
echo var_dump($test) . "\n";

$domain = 'maugey.fr';
$link = 'http://maugey.fr/coucou.html';
$test = Html::is_external_link($domain, $link);
echo 'Html::is_external_link' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ************** Html::is_domain_link ************* */
/* ************************************************* */

$domain = 'maugey.fr';
$link = 'http://maugey.fr/coucou.html';
$test = Html::is_domain_link($domain, $link);
echo 'Html::is_domain_link' . "\n";
echo var_dump($test) . "\n";

$domain = 'tata.maugey.fr';
$link = 'http://maugey.fr/coucou.html';
$test = Html::is_domain_link($domain, $link);
echo 'Html::is_domain_link' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* **** Html::replace_rel_to_abs_paths *** */
/* ************************************************* */

$page_url = 'http://portail.free.fr/m/';
$test = Html::replace_rel_to_abs_paths($html, $page_url);
echo 'Html::replace_rel_to_abs_paths' . "\n";
echo $test . "\n";

/* ************************************************* */
/* **************** Html::rel_to_abs *************** */
/* ************************************************* */

$rel = '/js/script.js';
$base = 'http://portail.free.fr/m/';
$test = Html::rel_to_abs($rel, $base);
echo 'Html::rel_to_abs' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ************** Html::find_backlinks ************* */
/* ************************************************* */

$domain = 'www.free.fr';
$test = Html::find_backlinks($html, $domain);
echo 'Html::find_backlinks' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ************** Html::get_canonicals ************* */
/* ************************************************* */

$canonical_content = 'index, follow, noindex, noarchive';
$test = Html::get_canonicals($canonical_content);
echo 'Html::get_canonicals' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ************** Html::set_href_ssl *************** */
/* ************************************************* */

$test = Html::set_href_ssl($html);
echo 'Html::set_href_ssl' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ************ Html::set_charset_utf_8 ************ */
/* ************************************************* */

$test = Html::set_charset_utf_8($html);
echo 'Html::set_charset_utf_8' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ********** Html::remove_external_links ********** */
/* ************************************************* */

$domain = 'free.fr';
$test = Html::remove_external_links($html, $domain);
echo 'Html::remove_external_links' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* *********** Html::remove_script_style *********** */
/* ************************************************* */

$test = Html::remove_script_style($html);
echo 'Html::remove_script_style' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* *********** Html::extract_body_content ********** */
/* ************************************************* */

$test = Html::extract_body_content($html);
echo 'Html::extract_body_content' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ****************** Html::is_spa ***************** */
/* ************************************************* */

$test = Html::is_spa($html);
echo 'Html::is_spa' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* *********** Html::add_escaped_fragment ********** */
/* ************************************************* */

$test = Html::add_escaped_fragment($url);
echo 'Html::add_escaped_fragment' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ***************** Html::get_links *************** */
/* ************************************************* */
$html = file_get_contents(__DIR__ . '/../data/hugo.maugey.fr.html');
$url = 'https://hugo.maugey.fr/developeur-web/Linux?_escaped_fragment_';
$test = Html::get_links($html);
echo 'Html::get_links' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ***************** Html::get_iframes *************** */
/* ************************************************* */
$html = file_get_contents(__DIR__ . '/../data/www.customdom.com.html');
$test = Html::get_iframes($html);
echo 'Html::get_iframes' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ************ Html::get_external_links *********** */
/* ************************************************* */

$test = Html::get_external_links($html, $url);
echo 'Html::get_external_links' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* ************ Html::get_internal_links *********** */
/* ************************************************* */

$test = Html::get_internal_links($html, $url);
echo 'Html::get_internal_links' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* **************** Html::get_images *************** */
/* ************************************************* */

$test = Html::get_images($html);
echo 'Html::get_images' . "\n";
echo var_dump($test) . "\n";

/* ************************************************* */
/* **************** Html::DJNikMail **************** */
/* ************************************************* */

$str = 'tatayoyo@free.fr';
$test = Html::DJNikMail($str);
echo 'Html::DJNikMail' . "\n";
echo var_dump($test) . "\n";
