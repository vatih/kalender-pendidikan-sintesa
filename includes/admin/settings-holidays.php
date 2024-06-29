<?php
function kalender_pendidikan_sintesa_settings_holidays_page() {
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kalender_pendidikan_sintesa_holidays_settings_nonce'])) {
        if (!wp_verify_nonce($_POST['kalender_pendidikan_sintesa_holidays_settings_nonce'], 'kalender_pendidikan_sintesa_holidays_settings')) {
            echo 'Nonce verification failed!';
            return;
        }

        // Update options for national holidays
        if (isset($_POST['kalender_pendidikan_sintesa_hari_libur_nasional'])) {
            $national_holidays = [];
            foreach ($_POST['kalender_pendidikan_sintesa_hari_libur_nasional'] as $holiday) {
                if (isset($holiday['date']) && isset($holiday['description']) && (!isset($holiday['delete']) || $holiday['delete'] != '1')) {
                    $national_holidays[sanitize_text_field($holiday['date'])] = sanitize_text_field($holiday['description']);
                }
            }
            update_option('kalender_pendidikan_sintesa_hari_libur_nasional', $national_holidays);
        }

        // Update options for special holidays
        if (isset($_POST['kalender_pendidikan_sintesa_hari_khusus'])) {
            $special_holidays = [];
            foreach ($_POST['kalender_pendidikan_sintesa_hari_khusus'] as $holiday) {
                if (isset($holiday['date']) && isset($holiday['description']) && (!isset($holiday['delete']) || $holiday['delete'] != '1')) {
                    $special_holidays[sanitize_text_field($holiday['date'])] = sanitize_text_field($holiday['description']);
                }
            }
            update_option('kalender_pendidikan_sintesa_hari_khusus', $special_holidays);
        }

        // Update options for lebaran holiday
        if (isset($_POST['kalender_pendidikan_sintesa_hari_libur_lebaran_start'])) {
            $lebaran_start_date = sanitize_text_field($_POST['kalender_pendidikan_sintesa_hari_libur_lebaran_start']);
            update_option('kalender_pendidikan_sintesa_hari_libur_lebaran', $lebaran_start_date);
        }

        // Update options for semester holiday
        if (isset($_POST['kalender_pendidikan_sintesa_hari_libur_semester_start']) && isset($_POST['kalender_pendidikan_sintesa_hari_libur_semester_duration'])) {
            $semester_end_date = new DateTime(sanitize_text_field($_POST['kalender_pendidikan_sintesa_hari_libur_semester_start']));
            $semester_end_date->modify('+' . intval($_POST['kalender_pendidikan_sintesa_hari_libur_semester_duration']) . ' days');
            update_option('kalender_pendidikan_sintesa_hari_libur_semester', [
                'start' => sanitize_text_field($_POST['kalender_pendidikan_sintesa_hari_libur_semester_start']),
                'end' => $semester_end_date->format('Y-m-d'),
            ]);
        }

        // Update options for class meeting
        if (isset($_POST['kalender_pendidikan_sintesa_hari_classmeeting_start']) && isset($_POST['kalender_pendidikan_sintesa_hari_classmeeting_duration'])) {
            $classmeeting_end_date = new DateTime(sanitize_text_field($_POST['kalender_pendidikan_sintesa_hari_classmeeting_start']));
            $classmeeting_end_date->modify('+' . intval($_POST['kalender_pendidikan_sintesa_hari_classmeeting_duration']) . ' days');
            update_option('kalender_pendidikan_sintesa_hari_classmeeting', [
                'start' => sanitize_text_field($_POST['kalender_pendidikan_sintesa_hari_classmeeting_start']),
                'end' => $classmeeting_end_date->format('Y-m-d'),
            ]);
        }

        echo '<div class="updated"><p>Settings saved</p></div>';
    }

    // Get current values
    $national_holidays = get_option('kalender_pendidikan_sintesa_hari_libur_nasional', []);
    $special_holidays = get_option('kalender_pendidikan_sintesa_hari_khusus', []);
    $lebaran_holiday = get_option('kalender_pendidikan_sintesa_hari_libur_lebaran', '');
    $semester_holiday = get_option('kalender_pendidikan_sintesa_hari_libur_semester', ['start' => '', 'end' => '']);
    $classmeeting = get_option('kalender_pendidikan_sintesa_hari_classmeeting', ['start' => '', 'end' => '']);
    $semester_duration = (new DateTime($semester_holiday['end']))->diff(new DateTime($semester_holiday['start']))->days;
    $classmeeting_duration = (new DateTime($classmeeting['end']))->diff(new DateTime($classmeeting['start']))->days;

    ?>

    <div class="wrap">
        <h1>Hari Spesial</h1>
        <form method="post" action="">
            <?php wp_nonce_field('kalender_pendidikan_sintesa_holidays_settings', 'kalender_pendidikan_sintesa_holidays_settings_nonce'); ?>

            <h2>Libur Lebaran</h2>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Hari-H</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                        <td>
                            <input type="date" name="kalender_pendidikan_sintesa_hari_libur_lebaran_start" value="<?php echo esc_attr($lebaran_holiday); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="keterangan">Tentukan hari H, nanti H-10 dan H+10 akan otomatis menjadi hari libur di kalender.</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h2>Libur Semester</h2>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Mulai</th>
                        <th>Durasi (hari)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="date" name="kalender_pendidikan_sintesa_hari_libur_semester_start" value="<?php echo esc_attr($semester_holiday['start']); ?>"></td>
                        <td><input type="number" name="kalender_pendidikan_sintesa_hari_libur_semester_duration" value="<?php echo esc_attr($semester_duration); ?>"></td>
                    </tr>
                </tbody>
            </table>

            <h2>Class Meeting</h2>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Mulai</th>
                        <th>Durasi (hari)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="date" name="kalender_pendidikan_sintesa_hari_classmeeting_start" value="<?php echo esc_attr($classmeeting['start']); ?>"></td>
                        <td><input type="number" name="kalender_pendidikan_sintesa_hari_classmeeting_duration" value="<?php echo esc_attr($classmeeting_duration); ?>"></td>
                    </tr>
                </tbody>
            </table>

            <h2>Libur Nasional</h2>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="national-holidays">
                    <?php if (!empty($national_holidays)) {
                        foreach ($national_holidays as $date => $description) {
                            ?>
                            <tr>
                                <td><input type="date" name="kalender_pendidikan_sintesa_hari_libur_nasional[<?php echo esc_attr($date); ?>][date]" value="<?php echo esc_attr(trim($date)); ?>"></td>
                                <td><input type="text" name="kalender_pendidikan_sintesa_hari_libur_nasional[<?php echo esc_attr($date); ?>][description]" value="<?php echo esc_attr(trim($description)); ?>"></td>
                                <td><button type="button" class="remove-row button">Hapus</button></td>
                                <input type="hidden" name="kalender_pendidikan_sintesa_hari_libur_nasional[<?php echo esc_attr($date); ?>][delete]" value="0">
                            </tr>
                            <?php
                        }
                    } ?>
                </tbody>
            </table>
            <button type="button" id="add-national-holiday" class="button">Tambah Libur Nasional</button>

            <h2>Hari Khusus</h2>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="special-holidays">
                    <?php if (!empty($special_holidays)) {
                        foreach ($special_holidays as $date => $description) {
                            ?>
                            <tr>
                                <td><input type="date" name="kalender_pendidikan_sintesa_hari_khusus[<?php echo esc_attr($date); ?>][date]" value="<?php echo esc_attr(trim($date)); ?>"></td>
                                <td><input type="text" name="kalender_pendidikan_sintesa_hari_khusus[<?php echo esc_attr($date); ?>][description]" value="<?php echo esc_attr(trim($description)); ?>"></td>
                                <td><button type="button" class="remove-row button">Hapus</button></td>
                                <input type="hidden" name="kalender_pendidikan_sintesa_hari_khusus[<?php echo esc_attr($date); ?>][delete]" value="0">
                            </tr>
                            <?php
                        }
                    } ?>
                </tbody>
            </table>
            <button type="button" id="add-special-holiday" class="button">Tambah Hari Khusus</button>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
?>
