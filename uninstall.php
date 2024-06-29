<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete options
delete_option('kalender_pendidikan_sintesa_tahun_ajaran_mulai');
delete_option('kalender_pendidikan_sintesa_tahun_ajaran_selesai');
delete_option('kalender_pendidikan_sintesa_nama_sekolah');
delete_option('kalender_pendidikan_sintesa_mata_pelajaran_serial');
delete_option('kalender_pendidikan_sintesa_mata_pelajaran_plus');
delete_option('kalender_pendidikan_sintesa_hari_libur_nasional');
delete_option('kalender_pendidikan_sintesa_hari_libur_lebaran');
delete_option('kalender_pendidikan_sintesa_hari_libur_semester');
?>
