<?php

function kalender_pendidikan_sintesa_export_settings() {
    if (isset($_GET['export']) && $_GET['export'] == 'true') {
        $options = [
            'kalender_pendidikan_sintesa_tahun_ajaran_mulai' => get_option('kalender_pendidikan_sintesa_tahun_ajaran_mulai', '2024-01-01'),
            'kalender_pendidikan_sintesa_tahun_ajaran_selesai' => get_option('kalender_pendidikan_sintesa_tahun_ajaran_selesai', '2024-12-31'),
            'kalender_pendidikan_sintesa_mata_pelajaran_serial' => get_option('kalender_pendidikan_sintesa_mata_pelajaran_serial'),
            'kalender_pendidikan_sintesa_mata_pelajaran_plus' => get_option('kalender_pendidikan_sintesa_mata_pelajaran_plus'),
            'kalender_pendidikan_sintesa_hari_libur_nasional' => get_option('kalender_pendidikan_sintesa_hari_libur_nasional'),
            'kalender_pendidikan_sintesa_hari_libur_lebaran' => get_option('kalender_pendidikan_sintesa_hari_libur_lebaran'),
            'kalender_pendidikan_sintesa_hari_libur_semester' => get_option('kalender_pendidikan_sintesa_hari_libur_semester'),
            'kalender_pendidikan_sintesa_hari_classmeeting' => get_option('kalender_pendidikan_sintesa_hari_classmeeting'),
            'kalender_pendidikan_sintesa_ujian' => get_option('kalender_pendidikan_sintesa_ujian')
        ];

        header('Content-Disposition: attachment; filename="kalender_pendidikan_sintesa.json"');
        header('Content-Type: application/json');
        echo json_encode($options);
        exit;
    }
}
add_action('admin_init', 'kalender_pendidikan_sintesa_export_settings');

function kalender_pendidikan_sintesa_settings_general_page() {
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kalender_pendidikan_sintesa_general_settings_nonce'])) {
        if (!wp_verify_nonce($_POST['kalender_pendidikan_sintesa_general_settings_nonce'], 'kalender_pendidikan_sintesa_general_settings')) {
            echo 'Nonce verification failed!';
            return;
        }

        // Handle import
        if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == UPLOAD_ERR_OK) {
            $import_data = file_get_contents($_FILES['import_file']['tmp_name']);
            $imported_options = json_decode($import_data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                foreach ($imported_options as $key => $value) {
                    update_option($key, $value);
                }
                echo '<div class="updated"><p>Pengaturan berhasil diimpor</p></div>';
            } else {
                echo '<div class="error"><p>File impor tidak valid</p></div>';
            }
        } else {
            // Update options for general settings
            update_option('kalender_pendidikan_sintesa_tahun_ajaran_mulai', sanitize_text_field($_POST['kalender_pendidikan_sintesa_tahun_ajaran_mulai']));
            update_option('kalender_pendidikan_sintesa_tahun_ajaran_selesai', sanitize_text_field($_POST['kalender_pendidikan_sintesa_tahun_ajaran_selesai']));

            echo '<div class="updated"><p>Settings saved</p></div>';
        }
    }

    // Get current values
    $tahun_ajaran_mulai = get_option('kalender_pendidikan_sintesa_tahun_ajaran_mulai', '2024-01-01');
    $tahun_ajaran_selesai = get_option('kalender_pendidikan_sintesa_tahun_ajaran_selesai', '2024-12-31');

    ?>

    <div class="wrap">
        <h1>Pengaturan Umum</h1>
        <form method="post" enctype="multipart/form-data" action="">
            <?php wp_nonce_field('kalender_pendidikan_sintesa_general_settings', 'kalender_pendidikan_sintesa_general_settings_nonce'); ?>

            <h2>Tahun Ajaran</h2>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Mulai</th>
                        <th>Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr valign="top">
                        <td><input type="date" id="kalender_pendidikan_sintesa_tahun_ajaran_mulai" name="kalender_pendidikan_sintesa_tahun_ajaran_mulai" value="<?php echo esc_attr($tahun_ajaran_mulai); ?>"></td>
                        <td><input type="date" id="kalender_pendidikan_sintesa_tahun_ajaran_selesai" name="kalender_pendidikan_sintesa_tahun_ajaran_selesai" value="<?php echo esc_attr($tahun_ajaran_selesai); ?>"></td>
                    </tr>
                </tbody>
            </table>

            <h2>Export/Import Pengaturan</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Export Pengaturan</th>
                    <td><a href="<?php echo admin_url('admin.php?page=kalender-pendidikan-sintesa&export=true'); ?>" class="button">Export</a></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="import_file">Import Pengaturan</label></th>
                    <td><input type="file" id="import_file" name="import_file" accept=".json"></td>
                </tr>
            </table>

            <?php submit_button('Simpan Pengaturan'); ?>
        </form>
    </div>
    <?php
}
?>
