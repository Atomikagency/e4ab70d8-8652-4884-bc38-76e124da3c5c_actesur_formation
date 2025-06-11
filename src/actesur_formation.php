<?php

/**
 * Plugin Name: Actesur Formation
 * Description: Système de formation
 * Version: 1.0.0
 * Author: AtomikAgency
 * Author URI: https://atomikagency.fr/
 */

define('ACTESUR_FORMATION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ACTESUR_FORMATION_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

require_once ACTESUR_FORMATION_PLUGIN_DIR . 'update-checker.php';