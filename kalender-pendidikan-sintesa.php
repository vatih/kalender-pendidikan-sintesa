<?php
/*
Plugin Name: Kalender Pendidikan Sintesa
Description: Plugin untuk mengelola kalender di Pesantren Sintesa.
Author: Ibrahim Vatih
Version: 1.9.2
*/

// Define constants for paths
define('KALENDER_PENDIDIKAN_SINTESA_PATH', plugin_dir_path(__FILE__));
define('KALENDER_PENDIDIKAN_SINTESA_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/settings-general.php';
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/settings-exams.php';
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/settings-holidays.php';
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/functions/display-calendar.php';
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/all-kelas.php'; // Include All Kelas file
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/add-kelas.php'; // Include Add Kelas file
require_once KALENDER_PENDIDIKAN_SINTESA_PATH . 'includes/admin/register-kelas.php'; // Include Register Kelas file

// Add admin menu
function kalender_pendidikan_sintesa_admin_menu() {
    add_menu_page('Kalender Pendidikan', 'Kaldik', 'manage_options', 'kalender-pendidikan-sintesa', 'kalender_pendidikan_sintesa_settings_page', 'dashicons-schedule', 6);

    add_submenu_page('kalender-pendidikan-sintesa', 'Hari Spesial', 'Hari Spesial', 'manage_options', 'kalender-pendidikan-sintesa-holidays', 'kalender_pendidikan_sintesa_holidays_page');
    add_submenu_page('kalender-pendidikan-sintesa', 'Ujian', 'Ujian', 'manage_options', 'kalender-pendidikan-sintesa-exams', 'kalender_pendidikan_sintesa_exams_page');
    add_submenu_page('kalender-pendidikan-sintesa', 'Pengaturan Umum', 'Pengaturan Umum', 'manage_options', 'kalender-pendidikan-sintesa', 'kalender_pendidikan_sintesa_settings_page');
    

    // Enqueue the admin CSS and JS
    add_action('admin_enqueue_scripts', 'kalender_pendidikan_sintesa_enqueue_admin_assets');
}
add_action('admin_menu', 'kalender_pendidikan_sintesa_admin_menu');

// General settings page
function kalender_pendidikan_sintesa_settings_page() {
    kalender_pendidikan_sintesa_settings_general_page();
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
    global $typenow;
    // Load only on our plugin's admin pages or 'kelas' post type pages
    if ($typenow == 'kelas' || strpos($hook, 'kalender-pendidikan-sintesa') !== false) {
        wp_enqueue_style('kalender_pendidikan_sintesa_admin_css', KALENDER_PENDIDIKAN_SINTESA_URL . 'includes/assets/css/admin-style.css');
        wp_enqueue_script('kalender_pendidikan_sintesa_admin_js', KALENDER_PENDIDIKAN_SINTESA_URL . 'includes/assets/js/admin-script.js', ['jquery'], null, true);
    }
}
add_action('admin_enqueue_scripts', 'kalender_pendidikan_sintesa_enqueue_admin_assets');

// Display schedule
function kalender_pendidikan_sintesa_add_calendar_to_kelas_content($content) {
    if (is_singular('kelas')) {
        global $post;
        if (function_exists('kalender_pendidikan_sintesa_display_schedule')) {
            $calendar = kalender_pendidikan_sintesa_display_schedule($post->ID);
            return $content . $calendar;
        }
    }
    return $content;
}
add_filter('the_content', 'kalender_pendidikan_sintesa_add_calendar_to_kelas_content');

?>
