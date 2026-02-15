@foreach ($siswaList as $rk)
    @php
        $siswa = $rk->siswa;
        $user = $siswa->user ?? null;
        $riwayatKelas = $rk;
        $kelasAjar = $kelasAjar;
        $tahunAjaran = $kelasAjar->tahunAjaran;
        $waliKelas = $kelasAjar->waliKelas;
        $sekolah = $sekolah ?? null;
        $intrakurikulerList = $kelasAjar->intrakurikuler ?? [];
        $ekskulList = $siswa->siswaEkstrakurikuler
            ->where('ekstrakurikuler.tahun_ajaran_id', $tahunAjaran->tahun_ajaran_id)
            ->values();

        // Koleksi formatif & sumatif siswa
        $asesmenFormatifList = $siswa->asesmenFormatif ?? collect();
        $skorSumatifList = $siswa->skorAsesmenSumatif ?? collect();
    @endphp

    <div class="page">
        {{-- Judul --}}
        <div style="text-align:center; font-weight:bold; font-size:16pt; margin-bottom:10px;">
            LAPORAN HASIL BELAJAR<br>(RAPOR)
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

        {{-- Tabel Intrakurikuler --}}
        <table border="1" cellpadding="4" cellspacing="0" class="table-intrakurikuler"
            style="width:100%; font-size:11pt; border-collapse:collapse; margin-bottom: 8mm;">
            <thead>
                <tr>
                    <th style="width:3%;">No</th>
                    <th style="width:30%;">Muatan Pelajaran</th>
                    <th style="width:8%;">Nilai Akhir</th>
                    <th>Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                @php $no=1; @endphp
                @forelse ($intrakurikulerList as $mapel)
                    @php
                        // Skor sumatif siswa pada mapel & riwayat kelas ini
                        $skor = $skorSumatifList
                            ->where('riwayat_kelas_id', $riwayatKelas->riwayat_kelas_id)
                            ->where('asesmenSumatif.intrakurikuler_id', $mapel->intrakurikuler_id)
                            ->sortByDesc('skor_asesmen_siswa_id')
                            ->first();
                        $nilaiAkhir = $skor->nilai ?? '-';

                        // Formatif siswa pada mapel & riwayat kelas ini
                        $formatif = $asesmenFormatifList
                            ->where('riwayat_kelas_id', $riwayatKelas->riwayat_kelas_id)
                            ->where('intrakurikuler_id', $mapel->intrakurikuler_id)
                            ->first();
                        $capaianTertinggi = $formatif->deskripsi_catatan_tertinggi ?? '-';
                        $capaianTerendah = $formatif->deskripsi_catatan_terendah ?? '-';
                        $rowspan = $capaianTerendah && $capaianTerendah !== '-' ? 2 : 1;
                    @endphp
                    <tr>
                        <td style="text-align:center; width: 3%;" rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $mapel->nama_pelajaran }}</td>
                        <td style="text-align:center;" rowspan="{{ $rowspan }}">{{ $nilaiAkhir }}</td>
                        <td>{{ $capaianTertinggi }}</td>
                    </tr>
                    @if ($capaianTerendah && $capaianTerendah !== '-')
                        <tr>
                            <td>{{ $capaianTerendah }}</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;">-</td>
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
                    <th style="width:40%;">Ekstrakurikuler</th>
                    <th>Keterangan</th>
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
                        <td colspan="3" style="text-align:center;">-</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabel Ketidakhadiran --}}
        <table border="1" cellpadding="4" cellspacing="0" class="table-ketidakhadiran"
            style="width:45%; font-size:11pt; border-collapse:collapse; margin-left: 8mm; margin-bottom:18px;">
            <thead>
                <tr>
                    <th colspan="2" style="text-align:center;">Ketidakhadiran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sakit</td>
                    <td>{{ $rk->rekap_kehadiran['sakit'] }} hari</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td>{{ $rk->rekap_kehadiran['izin'] }} hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan</td>
                    <td>{{ $rk->rekap_kehadiran['alpha'] }} hari</td>
                </tr>
            </tbody>
        </table>

        {{-- Tanda Tangan --}}
        <div style="page-break-inside: avoid;">
            <div style="text-align:right; margin:0 5mm;">
                Bandung, {{ now()->translatedFormat('d F Y') }}
            </div>

            <!-- Dua tanda tangan (kiri & kanan) -->
            <table style="width:100%; margin-top:2mm; border-collapse:collapse;">
                <tr>
                    <td style="width:30%; text-align:center; vertical-align:top;">
                        Orang Tua,<br><br><br><br><br>
                        .................................................
                    </td>

                    <!-- Spacer -->
                    <td style="width:40%;"></td>

                    <td style="width:30%; text-align:center; vertical-align:top;">
                        Wali Kelas,<br><br><br><br><br>
                        {{ $waliKelas->name ?? 'Wali Kelas' }}
                    </td>
                </tr>
            </table>

            <table style="width:100%; margin-top:8mm; border-collapse:collapse;">
                <tr>
                    <td style="width:25%;"></td>
                    <td style="width:50%; text-align:center; vertical-align:top;">
                        Mengetahui,<br>
                        Kepala Sekolah<br><br><br><br><br>
                        <span style="font-weight:bold;">
                            {{ $sekolah->nama_kepala_sekolah ?? '' }}
                        </span><br>
                    </td>
                    <td style="width:25%;"></td>
                </tr>
            </table>
        </div>

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

    .table-intrakurikuler tr,
    .table-intrakurikuler td,
    .table-intrakurikuler th {
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
