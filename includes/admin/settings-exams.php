<?php
function kalender_pendidikan_sintesa_settings_exams_page() {
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kalender_pendidikan_sintesa_exams_settings_nonce'])) {
        if (!wp_verify_nonce($_POST['kalender_pendidikan_sintesa_exams_settings_nonce'], 'kalender_pendidikan_sintesa_exams_settings')) {
            echo 'Nonce verification failed!';
            return;
        }

        // Update options for exams
        if (isset($_POST['kalender_pendidikan_sintesa_ujian'])) {
            $exams = [];
            foreach ($_POST['kalender_pendidikan_sintesa_ujian'] as $exam) {
                if (isset($exam['start']) && isset($exam['duration'])) {
                    $exam_start_date = sanitize_text_field($exam['start']);
                    $exam_duration = intval($exam['duration']);
                    $exams[] = [
                        'start' => $exam_start_date,
                        'duration' => $exam_duration
                    ];
                }
            }
            update_option('kalender_pendidikan_sintesa_ujian', $exams);
        }

        echo '<div class="updated"><p>Settings saved</p></div>';
    }

    // Get current values
    $exams = get_option('kalender_pendidikan_sintesa_ujian', [
        ['start' => '', 'duration' => ''],
        ['start' => '', 'duration' => '']
    ]);

    ?>

    <div class="wrap">
        <h1>Pengaturan Ujian</h1>
        <form method="post" action="">
            <?php wp_nonce_field('kalender_pendidikan_sintesa_exams_settings', 'kalender_pendidikan_sintesa_exams_settings_nonce'); ?>

            <h2>Ujian</h2>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Nama Semester</th>
                        <th>Mulai</th>
                        <th>Durasi (hari)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Semester 1</td>
                        <td><input type="date" name="kalender_pendidikan_sintesa_ujian[0][start]" value="<?php echo esc_attr($exams[0]['start']); ?>"></td>
                        <td><input type="number" name="kalender_pendidikan_sintesa_ujian[0][duration]" value="<?php echo esc_attr($exams[0]['duration']); ?>"></td>
                    </tr>
                    <tr>
                        <td>Semester 2</td>
                        <td><input type="date" name="kalender_pendidikan_sintesa_ujian[1][start]" value="<?php echo esc_attr($exams[1]['start']); ?>"></td>
                        <td><input type="number" name="kalender_pendidikan_sintesa_ujian[1][duration]" value="<?php echo esc_attr($exams[1]['duration']); ?>"></td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
?>
