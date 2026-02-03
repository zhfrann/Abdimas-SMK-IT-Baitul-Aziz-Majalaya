{{-- Halaman 3: Identitas Peserta Didik --}}
<div class="page identitas-siswa">
    <div style="text-align:center; font-weight:bold; font-size:15pt; margin-top:18mm;">
        IDENTITAS PESERTA DIDIK
    </div>
    <table style="margin:30px auto 0 auto; font-size:13pt;">
        <tr>
            <td class="judul-table-identitas">Nama Peserta Didik</td>
            <td>:</td>
            <td>{{ $user->name ?? $siswa->nama }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">NIS / NISN</td>
            <td>:</td>
            <td>{{ $siswa->nis }} / {{ $siswa->nisn }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Tempat, Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $siswa->tempatLahirKabupaten->nama ?? '-' }},
                {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $siswa->jenis_kelamin === 'p' ? 'Perempuan' : 'Laki-laki' }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Agama</td>
            <td>:</td>
            <td>{{ $siswa->agama }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Pendidikan sebelumnya</td>
            <td>:</td>
            <td>{{ $siswa->pendidikan_sebelumnya ?? '-' }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Alamat Peserta Didik</td>
            <td>:</td>
            <td>
                {{ $siswa->alamat_lengkap ?? $siswa->alamat }}
            </td>
        </tr>

        <tr>
            <td class="sp-6" colspan="3"></td>
        </tr>

        <tr>
            <td class="judul-table-identitas">Nama Orang Tua</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Ayah</td>
            <td>:</td>
            <td>{{ $siswa->orangTua->nama_ayah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Ibu</td>
            <td>:</td>
            <td>{{ $siswa->orangTua->nama_ibu ?? '-' }}</td>
        </tr>

        <tr>
            <td class="sp-6" colspan="3"></td>
        </tr>

        <tr>
            <td class="judul-table-identitas">Pekerjaan Orang Tua</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Ayah</td>
            <td>:</td>
            <td>{{ $siswa->orangTua->pekerjaan_ayah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Ibu</td>
            <td>:</td>
            <td>{{ $siswa->orangTua->pekerjaan_ibu ?? '-' }}</td>
        </tr>

        <tr>
            <td class="sp-6" colspan="3"></td>
        </tr>

        <tr>
            <td class="judul-table-identitas">Alamat Orang Tua</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Jalan</td>
            <td>:</td>
            <td>{{ $siswa->orangTua->jalan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Kelurahan / Desa</td>
            <td>:</td>
            <td>{{ $siswa->orangTua->kelurahan->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Kecamatan</td>
            <td>:</td>
            <td>{{ $siswa->orangTua->kelurahan->kecamatan->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Kabupaten / Kota</td>
            <td>:</td>
            <td>{{ $siswa->orangTua->kelurahan->kecamatan->kabupaten->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Provinsi</td>
            <td>:</td>
            <td>{{ $siswa->orangTua->kelurahan->kecamatan->kabupaten->provinsi->nama ?? '-' }}</td>
        </tr>

        <tr>
            <td class="sp-6" colspan="3"></td>
        </tr>

        <tr>
            <td class="judul-table-identitas">Wali Peserta Didik</td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Nama</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Pekerjaan</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td class="judul-table-identitas">Alamat</td>
            <td>:</td>
            <td></td>
        </tr>
    </table>
    <div class="ttd-wrap">
        <div class="ttd-box">
            Bandung, {{ now()->translatedFormat('d F Y') }}<br>
            Kepala Sekolah,<br><br><br><br><br>
            <span class="ttd-nama">{{ $sekolah->nama_kepala_sekolah }}</span><br>
            NUPTK.
        </div>
    </div>

    {{-- <div class="foto-box"></div> --}}
</div>
