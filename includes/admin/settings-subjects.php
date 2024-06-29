<?php
function kalender_pendidikan_sintesa_settings_subjects_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('kalender_pendidikan_sintesa_settings');

        // Save serial subjects
        $serial_subjects = [];
        if (!empty($_POST['serial_subjects'])) {
            foreach ($_POST['serial_subjects'] as $subject) {
                // Skip if the subject is marked for deletion
                if (isset($subject['delete']) && $subject['delete'] === '1') {
                    continue;
                }
                $serial_subjects[] = [
                    'name' => sanitize_text_field($subject['name']),
                    'duration' => intval($subject['duration']),
                    'teacher' => sanitize_text_field($subject['teacher']),
                ];
            }
        }
        update_option('kalender_pendidikan_sintesa_mata_pelajaran_serial', $serial_subjects);

        // Save plus subjects
        $plus_subjects = [];
        if (!empty($_POST['plus_subjects'])) {
            foreach ($_POST['plus_subjects'] as $subject) {
                // Skip if the subject is marked for deletion
                if (isset($subject['delete']) && $subject['delete'] === '1') {
                    continue;
                }
                $days = isset($subject['days']) ? array_map('intval', $subject['days']) : [];
                $plus_subjects[] = [
                    'name' => sanitize_text_field($subject['name']),
                    'days' => $days,
                ];
            }
        }
        update_option('kalender_pendidikan_sintesa_mata_pelajaran_plus', $plus_subjects);

        echo '<div class="updated"><p>Pengaturan disimpan.</p></div>';
    }

    $serial_subjects = get_option('kalender_pendidikan_sintesa_mata_pelajaran_serial', []);
    $plus_subjects = get_option('kalender_pendidikan_sintesa_mata_pelajaran_plus', []);
    ?>

    <div class="wrap">
        <h1>Pengaturan Mata Pelajaran</h1>
        <form method="post" action="">
            <?php wp_nonce_field('kalender_pendidikan_sintesa_settings'); ?>

            <h2>Mata Pelajaran Serial</h2>
            <table class="form-table" id="serial-subjects">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Durasi (hari)</th>
                        <th>Guru</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($serial_subjects as $index => $subject) { ?>
                        <tr>
                            <td><input type="text" name="serial_subjects[<?php echo $index; ?>][name]" value="<?php echo esc_attr($subject['name']); ?>"></td>
                            <td><input type="number" name="serial_subjects[<?php echo $index; ?>][duration]" value="<?php echo esc_attr($subject['duration']); ?>"></td>
                            <td><input type="text" name="serial_subjects[<?php echo $index; ?>][teacher]" value="<?php echo esc_attr($subject['teacher']); ?>"></td>
                            <td><button type="button" class="remove-row button">Hapus</button></td>
                            <input type="hidden" name="serial_subjects[<?php echo $index; ?>][delete]" value="0">
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button type="button" id="add-serial-subject" class="button">Tambahkan Mata Pelajaran Serial</button>

            <h2>Mata Pelajaran Plus</h2>
            <table class="form-table" id="plus-subjects">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Hari</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plus_subjects as $index => $subject) { ?>
                        <tr>
                            <td><input type="text" name="plus_subjects[<?php echo $index; ?>][name]" value="<?php echo esc_attr($subject['name']); ?>"></td>
                            <td>
                                <label><input type="checkbox" name="plus_subjects[<?php echo $index; ?>][days][]" value="1" <?php echo in_array(1, $subject['days']) ? 'checked' : ''; ?>> Senin</label><br>
                                <label><input type="checkbox" name="plus_subjects[<?php echo $index; ?>][days][]" value="2" <?php echo in_array(2, $subject['days']) ? 'checked' : ''; ?>> Selasa</label><br>
                                <label><input type="checkbox" name="plus_subjects[<?php echo $index; ?>][days][]" value="3" <?php echo in_array(3, $subject['days']) ? 'checked' : ''; ?>> Rabu</label><br>
                                <label><input type="checkbox" name="plus_subjects[<?php echo $index; ?>][days][]" value="4" <?php echo in_array(4, $subject['days']) ? 'checked' : ''; ?>> Kamis</label><br>
                                <label><input type="checkbox" name="plus_subjects[<?php echo $index; ?>][days][]" value="5" <?php echo in_array(5, $subject['days']) ? 'checked' : ''; ?>> Jumat</label><br>
                                <label><input type="checkbox" name="plus_subjects[<?php echo $index; ?>][days][]" value="6" <?php echo in_array(6, $subject['days']) ? 'checked' : ''; ?>> Sabtu</label>
                            </td>
                            <td><button type="button" class="remove-row button">Hapus</button></td>
                            <input type="hidden" name="plus_subjects[<?php echo $index; ?>][delete]" value="0">
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button type="button" id="add-plus-subject" class="button">Tambahkan Mata Pelajaran Plus</button>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
?>
