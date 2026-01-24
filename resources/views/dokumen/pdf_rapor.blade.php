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
        // Ambil semua ekskul yang diambil siswa pada tahun ajaran & semester ini
        $ekskulList = $siswa->siswaEkstrakurikuler
            ->where('ekstrakurikuler.tahun_ajaran_id', $tahunAjaran->tahun_ajaran_id)
            ->values();
        // Index skor sumatif siswa (riwayat_kelas_id + intrakurikuler_id)
        $skorSumatifMap = collect($rk->skorAsesmen)->keyBy(function ($skor) {
            return $skor->asesmenSumatif->intrakurikuler_id ?? null;
        });
        // Index asesmen formatif siswa (intrakurikuler_id)
        $formatifMap = collect($rk->siswa->asesmenFormatif ?? [])->keyBy('intrakurikuler_id');
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
        <table border="1" cellpadding="4" cellspacing="0"
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
                @foreach ($intrakurikulerList as $mapel)
                    @php
                        // Ambil skor sumatif siswa pada mapel ini
                        $skor = $skorSumatifMap[$mapel->intrakurikuler_id] ?? null;
                        $nilaiAkhir = $skor->nilai ?? '-';

                        // Ambil capaian kompetensi dari asesmen formatif
                        $formatif = $formatifMap[$mapel->intrakurikuler_id] ?? null;
                        $capaianTertinggi = $formatif->deskripsi_catatan_tertinggi ?? '-';
                        $capaianTerendah = $formatif->deskripsi_catatan_terendah ?? '-';
                    @endphp
                    <tr>
                        <td style="text-align:center;">{{ $no++ }}</td>
                        <td>{{ $mapel->nama_pelajaran }}</td>
                        <td style="text-align:center;">{{ $nilaiAkhir }}</td>
                        <td>
                            <div>
                                {{ $capaianTertinggi }}
                            </div>
                            @if ($capaianTerendah && $capaianTerendah !== '-')
                                <div style="margin-top:2px;">
                                    <span style="font-style:italic;">{{ $capaianTerendah }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Tabel Ekstrakurikuler --}}
        <table border="1" cellpadding="4" cellspacing="0"
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
        <table border="1" cellpadding="4" cellspacing="0"
            style="width:45%; font-size:11pt; border-collapse:collapse; margin-left: 8mm; margin-bottom:18px;">
            <thead>
                <tr>
                    <th colspan="2" style="text-align:center;">Ketidakhadiran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sakit</td>
                    <td>0 hari</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td>0 hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan</td>
                    <td>0 hari</td>
                </tr>
            </tbody>
        </table>

        {{-- Tanda Tangan --}}
        <div style="text-align: right; margin: 0 5mm;">Bandung, {{ now()->translatedFormat('d F Y') }}</div>
        <div style="width:100%; display:flex; justify-content:space-between; margin-top: 2mm;">
            {{-- Orang Tua --}}
            <div style="width: 30%; text-align: center;">
                Orang Tua,<br><br><br><br>
                .................................................
            </div>

            {{-- Wali Kelas --}}
            <div style="width: 30%; text-align: center;">
                Wali Kelas,<br><br><br><br>
                {{ $waliKelas->name ?? 'Wali Kelas' }}
            </div>
        </div>
        <div style="width:50%; margin: 5mm auto 0; text-align:center;">
            Mengetahui,<br>
            Kepala Sekolah<br><br><br><br>
            <span style="font-weight:bold;">{{ $sekolah->nama_kepala_sekolah ?? '' }}</span><br>
            NIP.
        </div>
    </div>
@endforeach

<style>
    @page {
        size: A4;
        margin: 18mm 12mm 12mm 12mm;
    }

    .page {
        page-break-after: always;
    }

    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 12pt;
        color: #000;
    }
</style>
