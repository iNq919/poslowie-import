<?php
/*
* Plugin Name: Posłowie Importer
* Description: Importuje i zarządza posłami Sejmu RP na podstawie danych z API Sejmu.
* Version: 1.0
* Author: Przemysław Zienkiewicz
*/

use App\Importer;
use App\ImporterAdmin;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

add_action('plugins_loaded', function () {
    new Importer();
    new ImporterAdmin();
});
