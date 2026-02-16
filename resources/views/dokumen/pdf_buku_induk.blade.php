@foreach ($siswaList as $rk)
    @php
        $siswa = $rk->siswa;
        $user = $siswa->user ?? null;
        $riwayatKelas = $rk;
        $kelasAjar = $kelasAjar;
        $tahunAjaran = $kelasAjar->tahunAjaran;
        $sekolah = $sekolah ?? null;
        $intrakurikulerList = $kelasAjar->intrakurikuler ?? [];
        $ekskulList = $siswa->siswaEkstrakurikuler
            ->where('ekstrakurikuler.tahun_ajaran_id', $tahunAjaran->tahun_ajaran_id)
            ->values();
    @endphp

    <div class="page">
        <div style="text-align:center; font-weight:bold; font-size:16pt; margin-bottom:10px;">
            BUKU INDUK
        </div>
        <table style="width:100%; font-size:12pt; margin-bottom:5mm;">
            <tr>
                <td style="width:25%;">Nama Peserta Didik</td>
                <td style="width:2%;">:</td>
                <td style="width:36%;">{{ $user->name ?? $siswa->nama }}</td>
                <td style="width:20%;">Kelas</td>
                <td style="width:2%;">:</td>
                <td>{{ $kelasAjar->kelas->nama_kelas }}</td>
            </tr>
            <tr>
                <td>NISN</td>
                <td>:</td>
                <td>{{ $siswa->nisn }}</td>
                <td>Fase</td>
                <td>:</td>
                <td>{{ $kelasAjar->kelas->nama_kelas[0] === 'X' || $kelasAjar->kelas->nama_kelas[0] === '10' ? 'E' : 'F' }}
                </td>
            </tr>
            <tr>
                <td>Sekolah</td>
                <td>:</td>
                <td>{{ $sekolah->nama_sekolah ?? 'SMK IT BAITUL AZIZ' }}</td>
                <td>Semester</td>
                <td>:</td>
                <td>{{ $tahunAjaran->semester }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $sekolah->alamat ?? '-' }}</td>
                <td>Tahun Pelajaran</td>
                <td>:</td>
                <td>{{ $tahunAjaran->tahun }}</td>
            </tr>
        </table>

        {{-- Tabel Nilai --}}
        <table border="1" cellpadding="4" cellspacing="0" class="table-nilai"
            style="width:100%; font-size:11pt; border-collapse:collapse; margin-bottom: 8mm;">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:60%;">Muatan Pelajaran</th>
                    <th style="width:15%;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @php $no=1; @endphp
                @forelse ($intrakurikulerList as $mapel)
                    @php
                        $skor = $siswa->skorAsesmenSumatif
                            ->where('riwayat_kelas_id', $riwayatKelas->riwayat_kelas_id)
                            ->where('asesmenSumatif.intrakurikuler_id', $mapel->intrakurikuler_id)
                            ->sortByDesc('skor_asesmen_siswa_id')
                            ->first();
                        $nilaiAkhir = $skor->nilai ?? '-';
                    @endphp
                    <tr>
                        <td style="text-align:center;">{{ $no++ }}</td>
                        <td>{{ $mapel->nama_pelajaran }}</td>
                        <td style="text-align:center;">{{ $nilaiAkhir }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center;">-</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabel Ekstrakurikuler --}}
        <table border="1" cellpadding="4" cellspacing="0" class="table-ekstrakurikuler"
            style="width:100%; font-size:11pt; border-collapse:collapse; margin-bottom: 8mm;">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:40%;">EKSTRAKURIKULER</th>
                    <th>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @php $noEks=1; @endphp
                @forelse ($ekskulList as $ekskul)
                    @php
                        $penilaian = $ekskul->penilaians->first();
                        $deskripsi = $penilaian->deskripsi ?? '-';
                    @endphp
                    <tr>
                        <td style="text-align:center;">{{ $noEks++ }}</td>
                        <td>{{ $ekskul->ekstrakurikuler->nama_pelajaran ?? '-' }}</td>
                        <td>{{ $deskripsi }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">-</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabel Ketidakhadiran --}}
        <table border="1" cellpadding="4" cellspacing="0" class="table-ketidakhadiran"
            style="width:45%; font-size:11pt; border-collapse:collapse; margin-bottom:18px;">
            <thead>
                <tr>
                    <th colspan="3" style="text-align:center;">KETIDAKHADIRAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 8%; text-align: center;">1</td>
                    <td>Sakit</td>
                    <td style="width: 24%;">{{ $rk->rekap_kehadiran['sakit'] }} hari</td>
                </tr>
                <tr>
                    <td style="width: 8%; text-align: center;">2</td>
                    <td>Izin</td>
                    <td style="width: 24%;">{{ $rk->rekap_kehadiran['izin'] }} hari</td>
                </tr>
                <tr>
                    <td style="width: 8%; text-align: center;">3</td>
                    <td>Tanpa Keterangan</td>
                    <td style="width: 24%;">{{ $rk->rekap_kehadiran['alpha'] }} hari</td>
                </tr>
            </tbody>
        </table>
    </div>
@endforeach

<style>
    .page {
        page-break-after: always;
    }

    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 12pt;
        color: #000;
    }

    .table-nilai tr,
    .table-nilai td,
    .table-nilai th {
        border: 1px solid black;
    }

    .table-ekstrakurikuler tr,
    .table-ekstrakurikuler td,
    .table-ekstrakurikuler th {
        border: 1px solid black;
    }

    .table-ketidakhadiran tr,
    .table-ketidakhadiran td,
    .table-ketidakhadiran th {
        border: 1px solid black;
    }
</style>
