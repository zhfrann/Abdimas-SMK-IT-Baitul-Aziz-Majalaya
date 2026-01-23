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

    /* ====== A4 ====== */
    @page {
        size: A4;
        margin: 18mm 18mm 18mm 18mm;
    }

    .page {
        page-break-after: always;
        position: relative;
    }

    .foto-box {
        position: absolute;
        /* atur posisi kotak */
        right: 80mm;
        /* geser kanan/kiri */
        bottom: -25mm;
        /* geser naik/turun */
        /* ukuran pas foto (contoh 3x4 cm) */
        width: 30mm;
        height: 40mm;

        border: 1px solid #000;
    }

    /* opsional: kalau mau ada tulisan "Pas Foto" di tengah */
    .foto-box::after {
        content: "Pas Foto";
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10pt;
    }

    /* .page:last-child {
        page-break-after: auto;
    } */

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

    .judul-table-identitas {
        width: 33%;
    }

    .cover {
        height: calc(297mm - 20mm - 18mm - 2mm);
        display: flex;
        flex-direction: column;
    }

    .content {
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

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

    /* ====== Judul (bold, center) ====== */
    .judul {
        text-align: center;
        font-weight: 700;
        line-height: 1.25;
        margin-top: 4mm;
        font-size: 14pt;
    }

    /* ====== Logo ====== */
    .logo-wrap {
        text-align: center;
        margin-top: 10mm;
        margin-bottom: 8mm;
    }

    .logo {
        width: 52mm;
        /* mendekati ukuran pada contoh */
        height: auto;
        display: inline-block;
    }

    /* ====== Info program (kiri agak bold) ====== */
    .info-program {
        width: 85%;
        margin: 0 0 0 15%;
        border-collapse: collapse;
        font-size: 12pt;
        margin-top: 2mm;
    }

    .info-program td {
        padding: 2px 0;
    }

    .info-program .kiri {
        width: 30%;
        font-weight: 700;
    }

    .info-program .titik {
        width: 4%;
        text-align: center;
        font-weight: 700;
    }

    .info-program .kanan {
        width: 70%;
        font-weight: 700;
    }

    /* ====== Label center ====== */
    .label-center {
        text-align: center;
        font-weight: 700;
        margin: 0 0 3mm 0;
    }

    .label-nama {
        margin-top: 32mm;
    }

    .label-nis {
        margin-top: -10mm;
    }

    /* ====== Box nama & nis ====== */
    .box-nama,
    .box-nis {
        width: 86%;
        margin: 0 auto;
        border: 2px solid #000;
        text-align: center;
        font-weight: 700;
        letter-spacing: 0.4px;
        padding: 6px 10px;
    }

    .box-nama {
        font-size: 13pt;
        margin-bottom: 5mm;
    }

    .box-nis {
        font-size: 13pt;
        margin-top: 3mm;
    }

    /* ====== Footer (di bawah, center, bold di yayasan) ====== */
    .footer {
        margin-top: auto;
        text-align: center;
        font-size: 10.5pt;
        line-height: 1.25;
        font-weight: 700;
        /* padding-bottom: 8mm; */
        padding-bottom: 0;
    }

    .footer .yayasan {
        font-size: 12pt;
        margin-bottom: 2mm;
    }

    /* ===== Halaman Data Sekolah ===== */
    .data-sekolah>div:first-child {
        margin-top: 0mm !important;
    }

    /* ===== Halaman Identitas Siswa ===== */
    .identitas-siswa>div:first-child {
        margin-top: 0mm !important;
    }

    .ttd-wrap {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
    }

    .ttd-box {
        width: 70mm;
        text-align: left;
        font-size: 13pt;
    }

    .ttd-nama {
        font-weight: 700;
        text-decoration: underline;
    }
</style>
