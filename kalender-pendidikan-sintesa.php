<?php
/*
Plugin Name: Kalender Pendidikan Sintesa
Description: Plugin untuk mengelola kalender di Pesantren Sintesa.
Author: Ibrahim Vatih
Version: 1.4.2
*/

// Define constants for paths
define('KALENDER_PENDIDIKAN_SINTESA_PATH', plugin_dir_path(__FILE__));
define('KALENDER_PENDIDIKAN_SINTESA_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/settings-general.php';
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/settings-subjects.php';
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/settings-exams.php';
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/settings-holidays.php';
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/functions/shortcode.php';

// Add admin menu
function kalender_pendidikan_sintesa_admin_menu() {
    add_menu_page('Kalender Pendidikan', 'Kaldik', 'manage_options', 'kalender-pendidikan-sintesa', 'kalender_pendidikan_sintesa_settings_page', 'dashicons-schedule', 6);

    add_submenu_page('kalender-pendidikan-sintesa', 'Pengaturan Umum', 'Pengaturan Umum', 'manage_options', 'kalender-pendidikan-sintesa', 'kalender_pendidikan_sintesa_settings_page');
    add_submenu_page('kalender-pendidikan-sintesa', 'Mata Pelajaran', 'Mata Pelajaran', 'manage_options', 'kalender-pendidikan-sintesa-subjects', 'kalender_pendidikan_sintesa_subjects_page');
    add_submenu_page('kalender-pendidikan-sintesa', 'Ujian', 'Ujian', 'manage_options', 'kalender-pendidikan-sintesa-exams', 'kalender_pendidikan_sintesa_exams_page');
    add_submenu_page('kalender-pendidikan-sintesa', 'Hari Spesial', 'Hari Spesial', 'manage_options', 'kalender-pendidikan-sintesa-holidays', 'kalender_pendidikan_sintesa_holidays_page');

    // Enqueue the admin CSS and JS
    add_action('admin_enqueue_scripts', 'kalender_pendidikan_sintesa_enqueue_admin_assets');
}
add_action('admin_menu', 'kalender_pendidikan_sintesa_admin_menu');

// General settings page
function kalender_pendidikan_sintesa_settings_page() {
    kalender_pendidikan_sintesa_settings_general_page();
}

// Subjects settings page
function kalender_pendidikan_sintesa_subjects_page() {
    kalender_pendidikan_sintesa_settings_subjects_page();
}

// Exams settings page
function kalender_pendidikan_sintesa_exams_page() {
    kalender_pendidikan_sintesa_settings_exams_page();
}

// Holidays settings page
function kalender_pendidikan_sintesa_holidays_page() {
    kalender_pendidikan_sintesa_settings_holidays_page();
}

// Enqueue admin CSS and JS
function kalender_pendidikan_sintesa_enqueue_admin_assets($hook) {
    // Load only on our plugin's admin pages
    if (strpos($hook, 'kalender-pendidikan-sintesa') !== false) {
        wp_enqueue_style('kalender_pendidikan_sintesa_admin_css', KALENDER_PENDIDIKAN_SINTESA_URL . 'includes/assets/css/admin-style.css');
        wp_enqueue_script('kalender_pendidikan_sintesa_admin_js', KALENDER_PENDIDIKAN_SINTESA_URL . 'includes/assets/js/admin-script.js', ['jquery'], null, true);
    }
}
add_action('admin_enqueue_scripts', 'kalender_pendidikan_sintesa_enqueue_admin_assets');

// Shortcode for displaying schedules
function kalender_pendidikan_sintesa_register_shortcodes() {
    add_shortcode('jadwal_kalender_pendidikan_sintesa', 'kalender_pendidikan_sintesa_display_schedule');
}
add_action('init', 'kalender_pendidikan_sintesa_register_shortcodes');
?>
