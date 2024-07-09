<?php

function kalender_pendidikan_sintesa_enqueue_styles() {
    wp_enqueue_style('kalender_pendidikan_sintesa_frontend_css', KALENDER_PENDIDIKAN_SINTESA_URL . 'includes/assets/css/style.css');
}
add_action('wp_enqueue_scripts', 'kalender_pendidikan_sintesa_enqueue_styles');

function kalender_pendidikan_sintesa_display_schedule($atts) {
    ob_start();

    // Ambil pengaturan tanggal mulai dan tanggal selesai
    $tahun_ajaran_mulai = get_option('kalender_pendidikan_sintesa_tahun_ajaran_mulai', '2024-06-07');
    $tahun_ajaran_selesai = get_option('kalender_pendidikan_sintesa_tahun_ajaran_selesai', '2025-06-24');

    $start_date = new DateTime($tahun_ajaran_mulai);
    $end_date = new DateTime($tahun_ajaran_selesai);

    // Pastikan tanggal selesai adalah akhir hari
    $end_date->setTime(23, 59, 59);

    $current_date = clone $start_date;
    $serial_subjects = get_option('kalender_pendidikan_sintesa_mata_pelajaran_serial', []);
    $plus_subjects = get_option('kalender_pendidikan_sintesa_mata_pelajaran_plus', []);
    $national_holidays = get_option('kalender_pendidikan_sintesa_hari_libur_nasional', []);
    $special_holidays = get_option('kalender_pendidikan_sintesa_hari_khusus', []); // Tambahkan ini untuk hari khusus
    $lebaran_holiday = get_option('kalender_pendidikan_sintesa_hari_libur_lebaran', '');
    $semester_holiday = get_option('kalender_pendidikan_sintesa_hari_libur_semester', ['start' => '', 'end' => '']);
    $classmeeting = get_option('kalender_pendidikan_sintesa_hari_classmeeting', ['start' => '', 'end' => '']);
    $exam_periods = get_option('kalender_pendidikan_sintesa_ujian', []);

    // Calculate the 10 days before and after Lebaran holiday
    $lebaran_start_date = new DateTime($lebaran_holiday);
    $lebaran_start_date->modify('-10 days');
    $lebaran_end_date = new DateTime($lebaran_holiday);
    $lebaran_end_date->modify('+10 days');

    // Calculate semester holiday end date from start date and duration
    $semester_start_date = new DateTime($semester_holiday['start']);
    $semester_duration = $semester_start_date->diff(new DateTime($semester_holiday['end']))->days + 1;
    $semester_end_date = new DateTime($semester_holiday['start']);
    $semester_end_date->modify('+' . ($semester_duration - 1) . ' days');

    // Calculate class meeting end date from start date and duration
    $classmeeting_start_date = new DateTime($classmeeting['start']);
    $classmeeting_duration = $classmeeting_start_date->diff(new DateTime($classmeeting['end']))->days + 1;
    $classmeeting_end_date = new DateTime($classmeeting['start']);
    $classmeeting_end_date->modify('+' . ($classmeeting_duration - 1) . ' days');

    // Calculate the number of serial subject days
    $serial_subject_days = array_reduce($serial_subjects, function($carry, $subject) {
        return $carry + $subject['duration'];
    }, 0);

    $serial_index = 0;
    $serial_day_count = 0;
    $remaining_serial_days = $serial_subjects[$serial_index]['duration'];

    echo '<div class="kalender-pendidikan">';
    echo '<div class="table-wrapper">';

    while ($current_date <= $end_date) {
        $year = $current_date->format('Y');
        $month = $current_date->format('m');

        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $first_day_of_month = (new DateTime("$year-$month-01"))->format('w');

        // Adjust first day of month to start from Monday (0 for Monday, 6 for Sunday)
        $first_day_of_month = ($first_day_of_month == 0) ? 6 : $first_day_of_month - 1;

        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="judul_bulan" colspan="7">' . date('F Y', strtotime("$year-$month-01")) . '</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<th>Senin</th><th>Selasa</th><th>Rabu</th><th>Kamis</th><th>Jumat</th><th>Sabtu</th><th>Minggu</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

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
                    $is_exam = false;
                    $is_classmeeting = false;
                    $td_classes = ['sel_tanggal'];
                    $holiday_description = [];

                    // Check for exams
                    foreach ($exam_periods as $exam) {
                        $exam_start_date = new DateTime($exam['start']);
                        $exam_end_date = (clone $exam_start_date)->modify('+' . ($exam['duration'] - 1) . ' days');

                        if ($current_day >= $exam_start_date && $current_day <= $exam_end_date && $day_of_week < 6) { // Exclude Sundays
                            $td_classes = ['sel_tanggal', 'ujian'];
                            $holiday_description[] = esc_html(isset($exam['semester']) ? $exam['semester'] : 'Ujian');
                            $is_holiday = true;
                            $is_exam = true;
                            break;
                        }
                    }

                    // Check for national holidays
                    if (!$is_exam && isset($national_holidays[$date])) {
                        $td_classes[] = 'libur nasional';
                        $holiday_description[] = esc_html($national_holidays[$date]);
                        $is_holiday = true;
                    }

                    // Check for special holidays
                    if (!$is_exam && !$is_holiday && isset($special_holidays[$date])) {
                        $td_classes[] = 'libur khusus';
                        $holiday_description[] = esc_html($special_holidays[$date]);
                        $is_holiday = true;
                    }

                    // Check for lebaran holiday and the 10 days before and after
                    if (!$is_exam && $current_day >= $lebaran_start_date && $current_day <= $lebaran_end_date) {
                        $td_classes[] = 'libur lebaran';
                        $holiday_description[] = 'Libur Lebaran';
                        $is_holiday = true;
                    }

                    // Add Warming Up day after Lebaran holiday
                    if (!$is_exam && !$is_holiday && $current_day == (clone $lebaran_end_date)->modify('+1 day')) {
                        $td_classes = ['sel_tanggal', 'warmingup'];
                        $holiday_description[] = 'Warming Up';
                        $is_holiday = true;
                    }

                    // Check for semester holiday
                    if (!$is_exam && $current_day >= $semester_start_date && $current_day <= $semester_end_date) {
                        $td_classes[] = 'libur semester';
                        $holiday_description[] = 'Libur Semester';
                        $is_holiday = true;
                    }

                    // Add Warming Up day after semester holiday
                    if (!$is_exam && !$is_holiday && $current_day == (clone $semester_end_date)->modify('+1 day')) {
                        $td_classes = ['sel_tanggal', 'warmingup'];
                        $holiday_description[] = 'Warming Up';
                        $is_holiday = true;
                    }

                    // Check for class meeting
                    if (!$is_exam && !$is_holiday && $current_day >= $classmeeting_start_date && $current_day <= $classmeeting_end_date) {
                        $td_classes[] = 'classmeeting';
                        $holiday_description[] = 'Class Meeting';
                        $is_holiday = true;
                        $is_classmeeting = true;
                    }

                    // Check for Sunday and add class 'minggu'
                    if ($day_of_week == 6) {
                        $td_classes[] = 'minggu';
                    }

                    // Check for Plus subjects (including Saturdays)
                    if (!$is_exam && !$is_holiday) {
                        foreach ($plus_subjects as $subject) {
                            // Adjust for PHP's week day index (0 = Monday, 6 = Sunday)
                            $adjusted_days = array_map(function($day) {
                                return $day - 1; // Convert 1 (Monday) to 0
                            }, $subject['days']);

                            if (in_array($day_of_week, $adjusted_days)) {
                                $td_classes[] = 'mapelkhusus';
                                break;
                            }
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
                            // Adjust for PHP's week day index (0 = Monday, 6 = Sunday)
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
                                $remaining_serial_days--;

                                if ($remaining_serial_days == 0) {
                                    $serial_index = ($serial_index + 1) % count($serial_subjects);
                                    $remaining_serial_days = $serial_subjects[$serial_index]['duration'];
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

        echo '</tbody>';
        echo '</table>';

        // Move to the next month
        $current_date->modify('first day of next month');
    }

    echo '</div>';
    echo '</div>';

    return ob_get_clean();
}
?>

