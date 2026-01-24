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
        <div>Desa Neglasari Kec. Majalaya Kab. Bandung 40382</div>
        <div>Telp.022-5950175 - website: www.smkbaitulaziz.sch.id - email: smkbaitulaziz@gmail.com</div>
    </div>

</div>
