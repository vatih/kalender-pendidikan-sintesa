<?php
// Register custom post type 'kelas'
function register_kelas_post_type() {
    $labels = array(
        'name'               => 'Kelas',
        'singular_name'      => 'Kelas',
        'menu_name'          => 'Kelas',
        'name_admin_bar'     => 'Kelas',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Kelas',
        'new_item'           => 'New Kelas',
        'edit_item'          => 'Edit Kelas',
        'view_item'          => 'View Kelas',
        'all_items'          => 'All Kelas',
        'search_items'       => 'Search Kelas',
        'parent_item_colon'  => 'Parent Kelas:',
        'not_found'          => 'No kelas found.',
        'not_found_in_trash' => 'No kelas found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => false, // Changed to false to add it as submenu
        'query_var'          => true,
        'rewrite'            => array('slug' => 'kelas'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array('title'),
    );

    register_post_type('kelas', $args);
}
add_action('init', 'register_kelas_post_type');

// Add "All Kelas" and "Add New Kelas" as submenu under "Kaldik"
function add_kelas_submenus() {
    add_submenu_page(
        'kalender-pendidikan-sintesa', // Parent slug
        'All Kelas', // Page title
        'All Kelas', // Menu title
        'manage_options', // Capability
        'edit.php?post_type=kelas' // Menu slug
    );

    add_submenu_page(
        'kalender-pendidikan-sintesa', // Parent slug
        'Add New Kelas', // Page title
        'Add New Kelas', // Menu title
        'manage_options', // Capability
        'post-new.php?post_type=kelas' // Menu slug
    );
}
add_action('admin_menu', 'add_kelas_submenus');
?>
