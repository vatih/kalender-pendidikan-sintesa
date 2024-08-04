document.addEventListener('DOMContentLoaded', function () {
    // Log to ensure the script is loaded
    console.log('Admin script loaded');

    // Add serial subject row
    const addSerialButton = document.getElementById('add-serial-subject');
    if (addSerialButton) {
        addSerialButton.addEventListener('click', function () {
            const table = document.getElementById('serial-subjects').getElementsByTagName('tbody')[0];
            const rowCount = table.rows.length;
            const row = table.insertRow(rowCount);

            const cell1 = row.insertCell(0);
            const cell2 = row.insertCell(1);
            const cell3 = row.insertCell(2);
            const cell4 = row.insertCell(3);

            cell1.innerHTML = `<input type="text" name="serial_subjects[${rowCount}][name]" value="">`;
            cell2.innerHTML = `<input type="number" name="serial_subjects[${rowCount}][duration]" value="">`;
            cell3.innerHTML = `<input type="text" name="serial_subjects[${rowCount}][teacher]" value="">`;
            cell4.innerHTML = `<button type="button" class="remove-row button">Hapus</button>`;
            row.innerHTML += `<input type="hidden" name="serial_subjects[${rowCount}][delete]" value="0">`;
        });
    } else {
        console.log('Button "Tambahkan Mata Pelajaran Serial" tidak ditemukan');
    }

    // Add routine subject row
    const addRoutineButton = document.getElementById('add-routine-subject');
    if (addRoutineButton) {
        addRoutineButton.addEventListener('click', function () {
            const table = document.getElementById('routine-subjects').getElementsByTagName('tbody')[0];
            const rowCount = table.rows.length;
            const row = table.insertRow(rowCount);

            const cell1 = row.insertCell(0);
            const cell2 = row.insertCell(1);
            const cell3 = row.insertCell(2);

            cell1.innerHTML = `<input type="text" name="routine_subjects[${rowCount}][name]" value="">`;
            cell2.innerHTML = `
                <label><input type="checkbox" name="routine_subjects[${rowCount}][days][]" value="1"> Senin</label><br>
                <label><input type="checkbox" name="routine_subjects[${rowCount}][days][]" value="2"> Selasa</label><br>
                <label><input type="checkbox" name="routine_subjects[${rowCount}][days][]" value="3"> Rabu</label><br>
                <label><input type="checkbox" name="routine_subjects[${rowCount}][days][]" value="4"> Kamis</label><br>
                <label><input type="checkbox" name="routine_subjects[${rowCount}][days][]" value="5"> Jumat</label><br>
                <label><input type="checkbox" name="routine_subjects[${rowCount}][days][]" value="6"> Sabtu</label>
            `;
            cell3.innerHTML = `<button type="button" class="remove-row button">Hapus</button>`;
            row.innerHTML += `<input type="hidden" name="routine_subjects[${rowCount}][delete]" value="0">`;
        });
    } else {
        console.log('Button "Tambahkan Mata Pelajaran Rutin" tidak ditemukan');
    }

    // Add national holiday row
    const addHolidayButton = document.getElementById('add-national-holiday');
    if (addHolidayButton) {
        addHolidayButton.addEventListener('click', function () {
            console.log('Tambah Libur Nasional diklik'); // Tambahkan pesan log
            const table = document.getElementById('national-holidays');
            const rowCount = table.rows.length;
            const row = table.insertRow(rowCount);

            const cell1 = row.insertCell(0);
            const cell2 = row.insertCell(1);
            const cell3 = row.insertCell(2);

            cell1.innerHTML = `<input type="date" name="kalender_pendidikan_sintesa_hari_libur_nasional[${rowCount}][date]" value="">`;
            cell2.innerHTML = `<input type="text" name="kalender_pendidikan_sintesa_hari_libur_nasional[${rowCount}][description]" value="">`;
            cell3.innerHTML = `<button type="button" class="remove-row button">Hapus</button>`;
            row.innerHTML += `<input type="hidden" name="kalender_pendidikan_sintesa_hari_libur_nasional[${rowCount}][delete]" value="0">`;
        });
    } else {
        console.log('Button "Tambah Libur Nasional" tidak ditemukan');
    }

    // Add special holiday row
    const addSpecialHolidayButton = document.getElementById('add-special-holiday');
    if (addSpecialHolidayButton) {
        addSpecialHolidayButton.addEventListener('click', function () {
            console.log('Tambah Hari Khusus diklik'); // Tambahkan pesan log
            const table = document.getElementById('special-holidays');
            const rowCount = table.rows.length;
            const row = table.insertRow(rowCount);

            const cell1 = row.insertCell(0);
            const cell2 = row.insertCell(1);
            const cell3 = row.insertCell(2);

            cell1.innerHTML = `<input type="date" name="kalender_pendidikan_sintesa_hari_khusus[${rowCount}][date]" value="">`;
            cell2.innerHTML = `<input type="text" name="kalender_pendidikan_sintesa_hari_khusus[${rowCount}][description]" value="">`;
            cell3.innerHTML = `<button type="button" class="remove-row button">Hapus</button>`;
            row.innerHTML += `<input type="hidden" name="kalender_pendidikan_sintesa_hari_khusus[${rowCount}][delete]" value="0">`;
        });
    } else {
        console.log('Button "Tambah Hari Khusus" tidak ditemukan');
    }

    // Handle row removal
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-row')) {
            console.log('Hapus tombol diklik'); // Tambahkan pesan log
            const row = event.target.closest('tr');
            row.style.display = 'none';
            row.querySelector('input[type="hidden"][name$="[delete]"]').value = '1';
        }
    });
});
