<?php
function display_calendar_header($year, $month) {
    echo '<table class="kalender-pendidikan">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="judul_bulan" colspan="7">' . date('F Y', strtotime("$year-$month-01")) . '</th>';
    echo '</tr>';
    echo '<tr>';
    echo '<th>Senin</th><th>Selasa</th><th>Rabu</th><th>Kamis</th><th>Jumat</th><th>Sabtu</th><th>Minggu</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
}

function check_holidays($current_day, $national_holidays, $lebaran_start_date, $lebaran_end_date, $semester_start_date, $semester_end_date, $exam_periods) {
    $date = $current_day->format('Y-m-d');
    $td_classes = ['sel_tanggal'];
    $holiday_description = [];

    // Check for national holidays
    if (isset($national_holidays[$date])) {
        $td_classes[] = 'libur nasional';
        $holiday_description[] = esc_html($national_holidays[$date]);
    }

    // Check for lebaran holiday and the 10 days before and after
    if ($current_day >= $lebaran_start_date && $current_day <= $lebaran_end_date) {
        $td_classes[] = 'libur lebaran';
        $holiday_description[] = 'Libur Lebaran';
    }

    // Check for semester holiday
    if ($current_day >= $semester_start_date && $current_day <= $semester_end_date) {
        $td_classes[] = 'libur semester';
        $holiday_description[] = 'Libur Semester';
    }

    // Check for exam periods
    foreach ($exam_periods as $exam) {
        $exam_start_date = new DateTime($exam['start']);
        $exam_end_date = (clone $exam_start_date)->modify('+' . ($exam['duration'] - 1) . ' days');

        if ($current_day >= $exam_start_date && $current_day <= $exam_end_date) {
            $td_classes[] = 'ujian';
            $holiday_description[] = esc_html($exam['semester']);
        }
    }

    return [$td_classes, $holiday_description];
}

function display_calendar_row($year, $month, $days_in_month, $first_day_of_month, $start_date, $end_date, $serial_subjects, $plus_subjects, $national_holidays, $lebaran_start_date, $lebaran_end_date, $semester_start_date, $semester_end_date, &$serial_index, &$serial_day_count, $serial_subject_days, $exam_periods) {
    $day = 1;
    $day_of_week = $first_day_of_month;

    // Output the calendar rows
    while ($day <= $days_in_month) {
        echo '<tr>';

        // Output empty cells until the first day of the month
        if ($day == 1) {
            for ($i = 0; $i < $first_day_of_month; $i++) {
                echo '<td class="empty"></td>';
            }
        }

        // Output the days of the month
        while ($day <= $days_in_month && $day_of_week < 7) {
            $current_day = new DateTime("$year-$month-$day");

            if ($current_day >= $start_date && $current_day <= $end_date) {
                $date = $current_day->format('Y-m-d');
                $is_holiday = false;
                $is_plus_subject = false;

                list($td_classes, $holiday_description) = check_holidays($current_day, $national_holidays, $lebaran_start_date, $lebaran_end_date, $semester_start_date, $semester_end_date, $exam_periods);

                // Check for Sunday and add class 'minggu'
                if ($day_of_week == 6) {
                    $td_classes[] = 'minggu';
                }

                // Check for Plus subjects (including Saturdays)
                foreach ($plus_subjects as $subject) {
                    $adjusted_days = array_map(function($day) {
                        return $day - 1; // Convert 1 (Monday) to 0
                    }, $subject['days']);

                    if (in_array($day_of_week, $adjusted_days)) {
                        $td_classes[] = 'mapelkhusus';
                        break;
                    }
                }

                echo '<td class="' . implode(' ', $td_classes) . '">';
                echo '<span class="angka_tanggal">' . $day . '</span>';

                // Output subjects if not a holiday
                if ($is_holiday) {
                    foreach ($holiday_description as $description) {
                        echo '<span class="mapel takada">' . $description . '</span>';
                    }
                } else {
                    // For Plus subjects (including Saturdays)
                    foreach ($plus_subjects as $subject) {
                        $adjusted_days = array_map(function($day) {
                            return $day - 1; // Convert 1 (Monday) to 0
                        }, $subject['days']);

                        if (in_array($day_of_week, $adjusted_days)) {
                            echo '<span class="mapel">' . esc_html($subject['name']) . '</span>';
                            $is_plus_subject = true;
                            break;
                        }
                    }

                    // For Serial subjects (Monday to Friday)
                    if (!$is_plus_subject && $day_of_week >= 0 && $day_of_week <= 4) { // Monday to Friday
                        if ($serial_day_count < $serial_subject_days) {
                            $current_serial_subject = $serial_subjects[$serial_index];
                            echo '<span class="mapel">' . esc_html($current_serial_subject['name']) . '</span>';
                            $serial_day_count++;
                            if ($serial_day_count % $current_serial_subject['duration'] == 0) {
                                $serial_index = ($serial_index + 1) % count($serial_subjects);
                            }
                        }
                    }
                }

                echo '</td>';
            } else {
                echo '<td class="empty"></td>';
            }

            $day++;
            $day_of_week++;
        }

        // Reset the day of the week counter
        if ($day_of_week == 7) {
            $day_of_week = 0;
        }

        echo '</tr>';
    }
}

?>
