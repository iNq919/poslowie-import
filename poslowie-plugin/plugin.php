<?php
/**
 * Plugin Name: Posłowie Importer
 * Description: Importuje i zarządza posłami Sejmu RP na podstawie danych z API Sejmu.
 * Version: 1.0
 * Author: Przemysław Zienkiewicz
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'inc/class-importer.php';

add_filter('acf/settings/save_json', function () {
    return plugin_dir_path(__FILE__) . 'acf-json';
});

add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = plugin_dir_path(__FILE__) . 'acf-json';
    return $paths;
});

add_action('init', function() {
    new Poslowie_Import();
});
