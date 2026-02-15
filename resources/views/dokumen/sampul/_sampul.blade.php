<style>
    .cover.page {
        margin: 18mm;
    }

    /* .cover {
        height: calc(297mm - 20mm - 18mm - 2mm);
        display: flex;
        flex-direction: column;
    } */

    .content {
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    .judul {
        text-align: center;
        font-weight: 700;
        line-height: 1.25;
        margin-top: 4mm;
        font-size: 14pt;
    }

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

    .content {
        margin-bottom: 42mm;
    }

    .footer {
        margin-top: auto;
        text-align: center;
        font-size: 11.5pt;
        line-height: 1.25;
        font-weight: 700;
        /* padding-bottom: 8mm; */
        padding-bottom: 0;
    }

    .footer .yayasan {
        font-size: 12pt;
        margin-bottom: 2mm;
    }
</style>

{{-- Halaman Sampul --}}
<div class="cover page">

    <div class="content">
        {{-- Judul --}}
        <div class="judul">
            <div>LAPORAN HASIL BELAJAR PESERTA DIDIK</div>
            <div>SEKOLAH MENENGAH KEJURUAN (SMK)</div>
            <div>ISLAM TERPADU BAITUL AZIZ</div>
        </div>

        {{-- Logo --}}
        <div class="logo-wrap">
            <img class="logo" src="{{ $logoPath }}" alt="Logo">
        </div>

        {{-- Bidang / Program / Konsentrasi --}}
        <table class="info-program">
            <tr>
                <td class="kiri">Bidang Keahlian</td>
                <td class="titik">:</td>
                <td class="kanan">Teknologi Informasi</td>
            </tr>
            <tr>
                <td class="kiri">Program Keahlian</td>
                <td class="titik">:</td>
                <td class="kanan">Pengembangan Perangkat Lunak dan Gim</td>
            </tr>
            <tr>
                <td class="kiri">Konsentrasi Keahlian</td>
                <td class="titik">:</td>
                <td class="kanan">Rekayasa Perangkat Lunak</td>
            </tr>
        </table>

        {{-- Nama --}}
        <div class="label-center label-nama">Nama Peserta Didik :</div>
        <div class="box-nama">
            {{ strtoupper($user->name ?? $siswa->nama) }}
        </div>
        <div class="sp-1"></div>

        {{-- NIS/NISN --}}
        <div class="label-center label-nis">NIS / NISN</div>
        <div class="box-nis">
            {{ $siswa->nis }} / {{ $siswa->nisn }}
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="yayasan">YAYASAN BAITUL AZIZ</div>
        <div>Jl. Pesantren Baitul Aziz - Kp. Sukahaji No.44 RT/RW 01/08</div>
        <div>Desa {{ $sekolah->kelurahan->nama }} Kec. {{ $sekolah->kelurahan->kecamatan->nama }} Kab.
            {{ Str::after($sekolah->kelurahan->kecamatan->kabupaten->nama, 'Kabupaten ') }}
            {{ $sekolah->kode_pos }}
        </div>
        <div>Telp.{{ $sekolah->telp }} - website: {{ $sekolah->website }} - email: {{ $sekolah->email }}</div>
    </div>

</div>
