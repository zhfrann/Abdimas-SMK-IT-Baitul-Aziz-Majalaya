@foreach ($siswaList as $rk)
    @php
        $siswa = $rk->siswa;
        $user = $siswa->user ?? null;

        $logoPath = public_path('build/images/logo.png');
    @endphp

    @include('dokumen.sampul._sampul', ['siswa' => $siswa, 'user' => $user, 'logoPath' => $logoPath])
    @include('dokumen.sampul._data_sekolah', ['siswa' => $siswa, 'user' => $user, 'sekolah' => $sekolah])
    @include('dokumen.sampul._identitas_siswa', ['siswa' => $siswa, 'user' => $user])
@endforeach

<style>
    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
    }

    .page {
        page-break-after: always;
        position: relative;
    }

    /* ====== Utilities ====== */
    .sp-1 {
        height: 18mm;
    }

    .sp-2 {
        height: 10mm;
    }

    .sp-3 {
        height: 8mm;
    }

    .sp-4 {
        height: 6mm;
    }

    .sp-5 {
        height: 4mm;
    }

    .sp-6 {
        height: 2mm;
    }

    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 12pt;
        color: #000;
    }

    table {
        width: 95%;
        /* margin-left: 20%; */
    }

    td {
        padding: 2px 6px;
        vertical-align: top;
    }
</style>
