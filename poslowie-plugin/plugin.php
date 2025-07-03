<?php
/*
 * Plugin Name: Posłowie Importer
 * Description: Importuje i zarządza posłami Sejmu RP na podstawie danych z API Sejmu.
 * Version: 1.0.0
 * Author: Przemysław Zienkiewicz
 */

use App\Importer;
use App\ImporterAdmin;

if (!defined('ABSPATH')) {
    exit;
}

define('POSLOWIE_IMPORTER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('POSLOWIE_IMPORTER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('POSLOWIE_IMPORTER_PLUGIN_VERSION', '1.0.0');

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook(__FILE__, 'poslowie_importer_activate');
function poslowie_importer_activate(): void
{
    $importer = new Importer();
    $importer->register_cpt();

    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'poslowie_importer_deactivate');
function poslowie_importer_deactivate(): void
{
    flush_rewrite_rules();
}

add_action('plugins_loaded', function () {
    new Importer();
    new ImporterAdmin();
});
