<?php
/**
 * Plugin Name: Partyhelp Form
 * Plugin URI: https://partyhelp.com.au
 * Description: Party details form for venue recommendations, synced with get.partyhelp.com.au
 * Version: 1.1.6
 * Author: Partyhelp
 * Text Domain: partyhelp-form
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (! defined('ABSPATH')) {
    exit;
}

define('PARTYHELP_FORM_VERSION', '1.1.6');
define('PARTYHELP_FORM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PARTYHELP_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once PARTYHELP_FORM_PLUGIN_DIR . 'includes/class-partyhelp-form-debug.php';
require_once PARTYHELP_FORM_PLUGIN_DIR . 'includes/class-partyhelp-form.php';
require_once PARTYHELP_FORM_PLUGIN_DIR . 'includes/class-partyhelp-form-sync.php';
require_once PARTYHELP_FORM_PLUGIN_DIR . 'includes/class-partyhelp-form-settings.php';
require_once PARTYHELP_FORM_PLUGIN_DIR . 'includes/class-partyhelp-form-renderer.php';

function partyhelp_form(): Partyhelp_Form
{
    return Partyhelp_Form::instance();
}

add_action('plugins_loaded', [partyhelp_form(), 'init']);

register_activation_hook(__FILE__, function () {
    if (! wp_next_scheduled('partyhelp_form_cron_sync')) {
        wp_schedule_event(time(), 'partyhelp_form_sync', 'partyhelp_form_cron_sync');
    }
});

register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook('partyhelp_form_cron_sync');
});
