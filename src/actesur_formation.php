<?php

/**
 * Plugin Name: Actesur Formation
 * Description: Système de formation
 * Version: 1.0.12
 * Author: AtomikAgency
 * Author URI: https://atomikagency.fr/
 */

define('ACTESUR_FORMATION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ACTESUR_FORMATION_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

require_once ACTESUR_FORMATION_PLUGIN_DIR . 'update-checker.php';
require_once ACTESUR_FORMATION_PLUGIN_DIR . 'cpt/formation.php';
require_once ACTESUR_FORMATION_PLUGIN_DIR . 'cpt/offre-emploi.php';
require_once ACTESUR_FORMATION_PLUGIN_DIR . 'includes/menu.php';
require_once ACTESUR_FORMATION_PLUGIN_DIR . 'includes/formation_list.shortcode.php';
require_once ACTESUR_FORMATION_PLUGIN_DIR . 'includes/offre_emploi.shortcode.php';

function ta_enqueue_styles()
{
    wp_enqueue_style('actesur-formation-menu-style', ACTESUR_FORMATION_PLUGIN_URL . 'assets/menu-style.css', [], '0.0.1');
    wp_enqueue_script_module('actesur-formation-menu-js', ACTESUR_FORMATION_PLUGIN_URL . 'assets/menu.js', [], '0.0.1');
}


add_action('wp_enqueue_scripts', 'ta_enqueue_styles');