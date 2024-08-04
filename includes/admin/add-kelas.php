<?php
// Add Meta Boxes
function add_kelas_meta_boxes() {
    add_meta_box(
        'kelas_serial_subjects_meta_box', // ID
        'Mata Pelajaran Serial', // Title
        'render_kelas_serial_subjects_meta_box', // Callback
        'kelas', // Screen
        'normal', // Context
        'high' // Priority
    );

    add_meta_box(
        'kelas_routine_subjects_meta_box', // ID
        'Mata Pelajaran Rutin', // Title
        'render_kelas_routine_subjects_meta_box', // Callback
        'kelas', // Screen
        'normal', // Context
        'high' // Priority
    );
}
add_action('add_meta_boxes', 'add_kelas_meta_boxes');

// Render Serial Subjects Meta Box Content
function render_kelas_serial_subjects_meta_box($post) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field('kelas_serial_subjects_meta_box', 'kelas_serial_subjects_meta_box_nonce');

    // Retrieve existing meta data
    $serial_subjects = get_post_meta($post->ID, 'serial_subjects', true);

    ?>
    
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
            <?php if (!empty($serial_subjects)) {
                foreach ($serial_subjects as $index => $subject) { ?>
                    <tr>
                        <td><input type="text" name="serial_subjects[<?php echo $index; ?>][name]" value="<?php echo esc_attr($subject['name']); ?>"></td>
                        <td><input type="number" name="serial_subjects[<?php echo $index; ?>][duration]" value="<?php echo esc_attr($subject['duration']); ?>"></td>
                        <td><input type="text" name="serial_subjects[<?php echo $index; ?>][teacher]" value="<?php echo esc_attr($subject['teacher']); ?>"></td>
                        <td><button type="button" class="remove-row button">Hapus</button></td>
                        <input type="hidden" name="serial_subjects[<?php echo $index; ?>][delete]" value="0">
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
    <button type="button" id="add-serial-subject" class="button">Tambahkan Mata Pelajaran Serial</button>
    <?php
}

// Render Routine Subjects Meta Box Content
function render_kelas_routine_subjects_meta_box($post) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field('kelas_routine_subjects_meta_box', 'kelas_routine_subjects_meta_box_nonce');

    // Retrieve existing meta data
    $routine_subjects = get_post_meta($post->ID, 'routine_subjects', true);

    ?>
    
    <table class="form-table" id="routine-subjects">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Hari</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($routine_subjects)) {
                foreach ($routine_subjects as $index => $subject) { ?>
                    <tr>
                        <td><input type="text" name="routine_subjects[<?php echo $index; ?>][name]" value="<?php echo esc_attr($subject['name']); ?>"></td>
                        <td>
                            <label><input type="checkbox" name="routine_subjects[<?php echo $index; ?>][days][]" value="1" <?php echo in_array(1, $subject['days']) ? 'checked' : ''; ?>> Senin</label><br>
                            <label><input type="checkbox" name="routine_subjects[<?php echo $index; ?>][days][]" value="2" <?php echo in_array(2, $subject['days']) ? 'checked' : ''; ?>> Selasa</label><br>
                            <label><input type="checkbox" name="routine_subjects[<?php echo $index; ?>][days][]" value="3" <?php echo in_array(3, $subject['days']) ? 'checked' : ''; ?>> Rabu</label><br>
                            <label><input type="checkbox" name="routine_subjects[<?php echo $index; ?>][days][]" value="4" <?php echo in_array(4, $subject['days']) ? 'checked' : ''; ?>> Kamis</label><br>
                            <label><input type="checkbox" name="routine_subjects[<?php echo $index; ?>][days][]" value="5" <?php echo in_array(5, $subject['days']) ? 'checked' : ''; ?>> Jumat</label><br>
                            <label><input type="checkbox" name="routine_subjects[<?php echo $index; ?>][days][]" value="6" <?php echo in_array(6, $subject['days']) ? 'checked' : ''; ?>> Sabtu</label>
                        </td>
                        <td><button type="button" class="remove-row button">Hapus</button></td>
                        <input type="hidden" name="routine_subjects[<?php echo $index; ?>][delete]" value="0">
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
    <button type="button" id="add-routine-subject" class="button">Tambahkan Mata Pelajaran Rutin</button>
    <?php
}

// Save Meta Box Data
function save_kelas_meta_box_data($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['kelas_serial_subjects_meta_box_nonce']) || !isset($_POST['kelas_routine_subjects_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['kelas_serial_subjects_meta_box_nonce'], 'kelas_serial_subjects_meta_box') ||
        !wp_verify_nonce($_POST['kelas_routine_subjects_meta_box_nonce'], 'kelas_routine_subjects_meta_box')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'kelas' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Save serial subjects
    $serial_subjects = [];
    if (!empty($_POST['serial_subjects'])) {
        foreach ($_POST['serial_subjects'] as $subject) {
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
    update_post_meta($post_id, 'serial_subjects', $serial_subjects);

    // Save routine subjects
    $routine_subjects = [];
    if (!empty($_POST['routine_subjects'])) {
        foreach ($_POST['routine_subjects'] as $subject) {
            if (isset($subject['delete']) && $subject['delete'] === '1') {
                continue;
            }
            $days = isset($subject['days']) ? array_map('intval', $subject['days']) : [];
            $routine_subjects[] = [
                'name' => sanitize_text_field($subject['name']),
                'days' => $days,
            ];
        }
    }
    update_post_meta($post_id, 'routine_subjects', $routine_subjects);
}
add_action('save_post', 'save_kelas_meta_box_data');

?>