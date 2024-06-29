<?php
// Sanitize functions
function kalender_pendidikan_sintesa_sanitize_serial_subjects($input) {
    $output = [];
    if (is_array($input)) {
        foreach ($input as $subject) {
            $output[] = [
                'name' => sanitize_text_field($subject['name']),
                'duration' => absint($subject['duration']),
                'teacher' => sanitize_text_field($subject['teacher']),
            ];
        }
    }
    return $output;
}

function kalender_pendidikan_sintesa_sanitize_plus_subjects($input) {
    $output = [];
    if (is_array($input)) {
        foreach ($input as $subject) {
            $output[] = [
                'name' => sanitize_text_field($subject['name']),
                'days' => array_map('absint', $subject['days']),
            ];
        }
    }
    return $output;
}
?>
