<?php
// Include the necessary WordPress file for WP_List_Table
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Kelas_List_Table extends WP_List_Table {
    // Define your table columns, data retrieval, and data display functions here
    function get_columns() {
        $columns = array(
            'cb'    => '<input type="checkbox" />', // Checkbox for bulk actions
            'title' => 'Nama Kelas',
            'date'  => 'Date',
        );
        return $columns;
    }

    function prepare_items() {
        $per_page = 10;
        $current_page = $this->get_pagenum();

        // Use WP_Query to count total items
        $args = array(
            'post_type'      => 'kelas',
            'posts_per_page' => -1,
        );

        $query = new WP_Query($args);
        $total_items = $query->found_posts;

        // Fetch the items for the current page
        $args = array(
            'post_type'      => 'kelas',
            'posts_per_page' => $per_page,
            'paged'          => $current_page,
        );

        $query = new WP_Query($args);

        $this->items = $query->posts;

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ));
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'title':
                return $item->post_title;
            case 'date':
                return $item->post_date;
            default:
                return print_r($item, true); // Show the whole array for troubleshooting
        }
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="id[]" value="%s" />', $item->ID);
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }
}

function kalender_pendidikan_sintesa_all_kelas_page() {
    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">All Kelas</h1>';
    echo '<a href="?page=kalender-pendidikan-sintesa-add-kelas" class="page-title-action">Add New Kelas</a>';
    echo '<hr class="wp-header-end">';
    
    $kelasListTable = new Kelas_List_Table();
    $kelasListTable->prepare_items();
    $kelasListTable->display();
    
    echo '</div>';
}
?>
