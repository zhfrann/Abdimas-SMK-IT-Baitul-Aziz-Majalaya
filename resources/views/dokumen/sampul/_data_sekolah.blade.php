<style>
    .page.data-sekolah {
        margin: 18mm 14mm 18mm;
    }

    .data-sekolah>div:first-child {
        margin-top: 0mm !important;
    }
</style>

{{-- Halaman 2: Data Sekolah --}}
<div class="page data-sekolah">
    <div style="text-align:center; font-weight:bold; font-size:16pt; margin-top:20mm;">
        R A P O R <br>
        PESERTA DIDIK <br>
        SEKOLAH MENENGAH KEJURUAN ( SMK )
    </div>
    <table style="margin:40px auto 0 auto; font-size:13pt;">
        <tr>
            <td>Nama Sekolah</td>
            <td>:</td>
            <td>{{ $sekolah->nama_sekolah }}</td>
        </tr>
        <tr>
            <td>NPSN</td>
            <td>:</td>
            <td>{{ $sekolah->npsn }}</td>
        </tr>
        <tr>
            <td>Alamat Sekolah</td>
            <td>:</td>
            <td>{{ $sekolah->alamat }}</td>
        </tr>
        <tr>
            <td>Kode Pos</td>
            <td>:</td>
            <td>{{ $sekolah->kode_pos }}</td>
        </tr>
        <tr>
            <td>Desa / Kelurahan</td>
            <td>:</td>
            <td>{{ $sekolah->kelurahan->nama }}</td>
        </tr>
        <tr>
            <td>Kecamatan</td>
            <td>:</td>
            <td>{{ $sekolah->kelurahan->kecamatan->nama }}</td>
        </tr>
        <tr>
            <td>Kabupaten / Kota</td>
            <td>:</td>
            <td>{{ $sekolah->kelurahan->kecamatan->kabupaten->nama }}</td>
        </tr>
        <tr>
            <td>Provinsi</td>
            <td>:</td>
            <td>{{ $sekolah->kelurahan->kecamatan->kabupaten->provinsi->nama }}</td>
        </tr>
        <tr>
            <td>Website</td>
            <td>:</td>
            <td>{{ $sekolah->website }}</td>
        </tr>
        <tr>
            <td>E-mail</td>
            <td>:</td>
            <td>{{ $sekolah->email }}</td>
        </tr>
    </table>
</div>
