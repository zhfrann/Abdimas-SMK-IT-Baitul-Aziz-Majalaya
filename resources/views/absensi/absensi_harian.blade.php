@extends('layouts.master')

@section('title', 'Absen Harian')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
{{-- Flatpickr --}}
<link rel="stylesheet" href="/build/css/plugins/flatpickr.min.css" />
@endsection

@section('content')

<x-breadcrumb item="Absensi" active="Absen Harian" />

@php
// Tanggal dipilih (default: hari ini)
$selectedDate = request('date') ?: date('Y-m-d');

// ====== CONTOH DATA SISWA (hardcode) ======
$students = [
[
'id' => 1,
'name' => 'Airi Satou',
'avatar' => '/build/images/user/avatar-1.jpg',
'kelas' => 'XII IPA 1',
],
[
'id' => 2,
'name' => 'Bruno Nash',
'avatar' => '/build/images/user/avatar-2.jpg',
'kelas' => 'XII IPA 1',
],
[
'id' => 3,
'name' => 'Cedric Kelly',
'avatar' => '/build/images/user/avatar-3.jpg',
'kelas' => 'XII IPA 1',
],
];

// ====== CONTOH ABSENSI HARIAN (hardcode) ======
// Key: [tanggal][student_id] => data absensi
$attendanceByDate = [
$selectedDate => [
1 => ['status' => 'hadir', 'note' => '', 'updated_at' => '08:10'],
2 => ['status' => 'sakit', 'note' => 'Demam', 'updated_at' => '08:12'],
// 3 belum absen
],
];

// Rekap kecil (buat UI)
$hadir = 0; $alpha = 0; $sakit = 0; $izin = 0; $belum = 0;
foreach ($students as $s) {
$att = $attendanceByDate[$selectedDate][$s['id']] ?? null;
if (!$att) { $belum++; continue; }
if ($att['status'] === 'hadir') $hadir++;
if ($att['status'] === 'alpha') $alpha++;
if ($att['status'] === 'sakit') $sakit++;
if ($att['status'] === 'izin') $izin++;
}

$badgeClass = function($status) {
return match($status) {
'hadir' => 'bg-light-success',
'alpha' => 'bg-light-danger',
'sakit' => 'bg-light-warning',
'izin' => 'bg-light-info',
default => 'bg-light-secondary',
};
};

$statusLabel = function($status) {
return match($status) {
'hadir' => 'Hadir',
'alpha' => 'Alpha',
'sakit' => 'Sakit',
'izin' => 'Izin',
default => '-',
};
};
@endphp

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-1">Absen Harian</h5>
                        <small class="text-muted">Pilih tanggal untuk mengisi absensi. Jika sudah terisi, kamu bisa edit.</small>
                    </div>

                    {{-- Menu atas --}}
                    <div class="d-flex gap-2">
                        <a href="{{ route('absensi.harian') }}" class="btn btn-primary">
                            Absen Harian
                        </a>
                        <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary">
                            Rekap Absensi
                        </a>
                    </div>
                </div>

                {{-- Filter tanggal + ringkasan --}}
                <div class="row g-2 align-items-end mt-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label mb-1">Tanggal Absensi</label>
                        <input
                            type="text"
                            id="pickDate"
                            class="form-control"
                            placeholder="Pilih tanggal"
                            autocomplete="off" />
                        <input type="hidden" id="dateSelected" name="date" value="{{ $selectedDate }}">
                    </div>
                </div>
            </div>

            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Update</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $s)
                            @php
                            $att = $attendanceByDate[$selectedDate][$s['id']] ?? null;
                            @endphp

                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $s['avatar'] }}" alt="user image" class="img-radius wid-40" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">{{ $s['name'] }}</h6>
                                            <small class="text-muted">{{ $s['kelas'] }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($att)
                                    <span class="badge {{ $badgeClass($att['status']) }}">
                                        {{ $statusLabel($att['status']) }}
                                    </span>
                                    @else
                                    <span class="badge bg-light-secondary">Belum</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ ($att && !empty($att['note'])) ? $att['note'] : '-' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $att['updated_at'] ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    @if($att)
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm btnOpenModal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#absenModal"
                                        data-mode="edit"
                                        data-date="{{ $selectedDate }}"
                                        data-student-id="{{ $s['id'] }}"
                                        data-student-name="{{ $s['name'] }}"
                                        data-status="{{ $att['status'] }}"
                                        data-note="{{ $att['note'] }}">
                                        Edit
                                    </button>
                                    @else
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm btnOpenModal"
                                        data-bs-toggle="modal"
                                        data-bs-target="#absenModal"
                                        data-mode="create"
                                        data-date="{{ $selectedDate }}"
                                        data-student-id="{{ $s['id'] }}"
                                        data-student-name="{{ $s['name'] }}"
                                        data-status=""
                                        data-note="">
                                        Absensi
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

{{-- MODAL CREATE/EDIT --}}
<div class="modal fade" id="absenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="#">
                @csrf
                {{-- nanti: kalau edit bisa pakai @method('PUT') sesuai route --}}

                <div class="modal-header">
                    <h5 class="modal-title" id="absenModalTitle">Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="date" id="mDate">
                    <input type="hidden" name="student_id" id="mStudentId">
                    <input type="hidden" name="mode" id="mMode">

                    <div class="mb-2">
                        <label class="form-label mb-1">Siswa</label>
                        <input type="text" class="form-control" id="mStudentName" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1">Status</label>
                        <select class="form-select" name="status" id="mStatus" required>
                            <option value="">Pilih status</option>
                            <option value="hadir">Hadir</option>
                            <option value="alpha">Alpha</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                        </select>
                    </div>

                    {{-- KETERANGAN: hanya muncul untuk sakit/izin --}}
                    <div class="mb-0 d-none" id="noteWrapper">
                        <label class="form-label mb-1">Keterangan</label>
                        <textarea
                            class="form-control"
                            name="note"
                            id="mNote"
                            rows="2"
                            placeholder="Contoh: demam / izin keluarga / dsb"></textarea>
                        <small class="text-muted" id="noteHint">Wajib diisi untuk status Izin / Sakit.</small>
                    </div>

                    <div class="alert alert-warning mt-3 mb-0">
                        <small>
                            Ini masih UI demo. Submit belum tersambung ke backend.
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="mSubmitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
{{-- DataTable --}}
<script type="module">
    import {
        DataTable
    } from '/build/js/plugins/module.js';
    window.dt = new DataTable('#pc-dt-simple');
</script>

{{-- Flatpickr --}}
<script src="/build/js/plugins/flatpickr.min.js"></script>

<script>
    // ====== FILTER TANGGAL ======
    const pickDate = document.getElementById('pickDate');
    const dateSelected = document.getElementById('dateSelected');
    const presetDate = document.getElementById('presetDate');
    const btnApply = document.getElementById('btnApply');

    const fp = flatpickr(pickDate, {
        mode: 'single',
        dateFormat: 'Y-m-d',
        defaultDate: dateSelected.value || null,

        maxDate: 'today',

        onChange: function(selectedDates) {
            if (selectedDates.length) {
                dateSelected.value = fp.formatDate(selectedDates[0], 'Y-m-d');
            }
        }
    });

    function setPreset(val) {
        const now = new Date();
        const d = new Date(now.getFullYear(), now.getMonth(), now.getDate());

        if (val === 'yesterday') d.setDate(d.getDate() - 1);

        fp.setDate(d, true);
        dateSelected.value = fp.formatDate(d, 'Y-m-d');
    }

    presetDate.addEventListener('change', (e) => {
        if (!e.target.value) return;
        setPreset(e.target.value);
    });

    btnApply.addEventListener('click', () => {
        const params = new URLSearchParams(window.location.search);
        if (dateSelected.value) params.set('date', dateSelected.value);
        else params.delete('date');

        const baseUrl = window.location.pathname;
        window.location.href = `${baseUrl}?${params.toString()}`;
    });

    // ====== MODAL CREATE/EDIT ======
    const modalTitle = document.getElementById('absenModalTitle');
    const mDate = document.getElementById('mDate');
    const mStudentId = document.getElementById('mStudentId');
    const mStudentName = document.getElementById('mStudentName');
    const mMode = document.getElementById('mMode');
    const mStatus = document.getElementById('mStatus');
    const mNote = document.getElementById('mNote');
    const mSubmitBtn = document.getElementById('mSubmitBtn');

    const noteWrapper = document.getElementById('noteWrapper');

    function toggleNoteByStatus(status) {
        const needNote = (status === 'izin' || status === 'sakit');

        if (needNote) {
            noteWrapper.classList.remove('d-none');
            mNote.required = true;
        } else {
            noteWrapper.classList.add('d-none');
            mNote.required = false;
            mNote.value = ''; // kosongkan biar tidak nyangkut
        }
    }

    // Saat user ganti status di dropdown
    mStatus.addEventListener('change', () => {
        toggleNoteByStatus(mStatus.value);
    });

    document.querySelectorAll('.btnOpenModal').forEach(btn => {
        btn.addEventListener('click', () => {
            const mode = btn.dataset.mode;
            const date = btn.dataset.date;
            const studentId = btn.dataset.studentId;
            const studentName = btn.dataset.studentName;
            const status = btn.dataset.status || '';
            const note = btn.dataset.note || '';

            mMode.value = mode;
            mDate.value = date;
            mStudentId.value = studentId;
            mStudentName.value = studentName;

            // set dropdown dulu
            mStatus.value = status;

            // set note (kalau status butuh)
            mNote.value = note;

            // tampil/sembunyi note sesuai status
            toggleNoteByStatus(status);

            if (mode === 'edit') {
                modalTitle.textContent = 'Edit Absensi';
                mSubmitBtn.textContent = 'Update';
            } else {
                modalTitle.textContent = 'Input Absensi';
                mSubmitBtn.textContent = 'Simpan';

                // kalau create dan status masih kosong, pastikan note hidden
                if (!status) toggleNoteByStatus('');
            }
        });
    });
</script>
@endsection