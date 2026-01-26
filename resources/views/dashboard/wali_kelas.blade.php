@extends('layouts.master')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<x-breadcrumb item="Dashboard" active="Wali Kelas" />

<div class="row">
    {{-- FILTER BAR --}}
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    {{-- KELAS: auto submit --}}
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('dashboard.waliKelas') }}" id="form-kelas">
                            <label class="form-label mb-1">Kelas (Wali)</label>
                            <select class="form-select" name="kelas_ajar_id" id="kelas-ajar" required>
                                @forelse($kelasAjars as $ka)
                                <option value="{{ $ka->kelas_ajar_id }}"
                                    @selected(optional($selectedKelasAjar)->kelas_ajar_id == $ka->kelas_ajar_id)>
                                    {{ $ka->nama_kelas }} • {{ $ka->tahun }} ({{ $ka->semester }})
                                </option>
                                @empty
                                <option selected>Belum ada kelas</option>
                                @endforelse
                            </select>
                        </form>
                        <small class="text-muted d-block mt-2">
                            Periode otomatis: <b>semester</b> sesuai tahun ajaran kelas.
                        </small>
                    </div>

                    {{-- MAPEL: butuh tombol Terapkan --}}
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('dashboard.waliKelas') }}" id="form-mapel">
                            <input type="hidden" name="kelas_ajar_id" value="{{ optional($selectedKelasAjar)->kelas_ajar_id }}">
                            <label class="form-label mb-1">Mapel (untuk Top Siswa)</label>

                            <div class="d-flex gap-2">
                                <select class="form-select" name="intrakurikuler_id" required>
                                    @forelse($mapels as $m)
                                    <option value="{{ $m->intrakurikuler_id }}"
                                        @selected(optional($selectedMapel)->intrakurikuler_id == $m->intrakurikuler_id)>
                                        {{ $m->nama_pelajaran }}
                                    </option>
                                    @empty
                                    <option selected>Tidak ada mapel</option>
                                    @endforelse
                                </select>

                                <button class="btn btn-primary" type="submit" @disabled($mapels->isEmpty() || !$selectedKelasAjar)>
                                    Terapkan
                                </button>
                            </div>

                            <small class="text-muted d-block mt-2">
                                Default (otomatis) mapel fokus:
                                <b>{{ $topMapel?->nama_pelajaran ?? '-' }}</b>
                            </small>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$selectedKelasAjar)
    <div class="col-12">
        <div class="alert alert-warning mb-0">Anda belum menjadi wali kelas.</div>
    </div>
    @else
    {{-- HEADER --}}
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Dashboard Wali: {{ $selectedKelasAjar->nama_kelas }}</h5>
            <span class="text-muted">{{ $selectedKelasAjar->tahun }} ({{ $selectedKelasAjar->semester }})</span>
        </div>
    </div>

    {{-- KPI --}}
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-s bg-light-primary">
                        <i class="ti ti-chart-line f-20"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">Rerata Nilai Kelas</h6>
                        <small class="text-muted">Semua mapel</small>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="mb-0">{{ $kpi['avg_nilai_kelas'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-s bg-light-success">
                        <i class="ti ti-award f-20"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">Unggul</h6>
                        <small class="text-muted">Avg siswa ≥ 85</small>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="mb-0">{{ $kpi['unggul_count'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-s bg-light-danger">
                        <i class="ti ti-user-exclamation f-20"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">Butuh Atensi</h6>
                        <small class="text-muted">Avg semua mapel &lt; 60</small>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="mb-0">{{ $kpi['atensi_count'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-s bg-light-warning">
                        <i class="ti ti-checklist f-20"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">Kehadiran</h6>
                        <small class="text-muted">Semua mapel</small>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="mb-0">{{ $kpi['hadir_rate'] }}%</h3>
                    <small class="text-muted">Alpha total: {{ $kpi['alpha_total'] }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- DISTRIBUSI --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Distribusi Rata-rata Nilai Siswa</h5>
                <small class="text-muted">Histogram avg nilai per siswa (semua mapel) • bucket 10</small>
            </div>
            <div class="card-body">
                <div id="chart-distribusi"></div>
            </div>
        </div>
    </div>

    {{-- TOP MAPEL & ATENSI --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top 10 Siswa ({{ $mapelDipakai?->nama_pelajaran ?? '-' }})</h5>
                <small class="text-muted">Avg nilai per siswa pada mapel terpilih</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="text-end">Avg</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topMapelList as $t)
                            <tr>
                                <td>{{ $t['nama'] }}</td>
                                <td class="text-end fw-semibold">{{ $t['avg_nilai'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-muted text-center py-3">Belum ada data nilai mapel pada semester ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Siswa Perlu Atensi (Overall)</h5>
                <small class="text-muted">Avg semua mapel &lt; 60 (Top 10 terendah)</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="text-end">Avg</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($atensiList as $a)
                            <tr>
                                <td>{{ $a['nama'] }}</td>
                                <td class="text-end fw-semibold text-danger">{{ $a['avg_nilai'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-muted text-center py-3">Tidak ada siswa di bawah 60 pada semester ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- KEHADIRAN --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Kehadiran Siswa (Terendah dulu)</h5>
                <small class="text-muted">Agregat semua mapel • hadir% & alpha (Top 10)</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="text-end">Hadir %</th>
                                <th class="text-end">Alpha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceList as $x)
                            <tr>
                                <td>{{ $x['nama'] }}</td>
                                <td class="text-end">{{ $x['hadir_pct'] }}%</td>
                                <td class="text-end fw-semibold text-danger">{{ $x['alpha'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-muted text-center py-3">Belum ada data absensi pada semester ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MAPEL RENDAH --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Jumlah Mapel Rendah per Siswa</h5>
                <small class="text-muted">Rendah = avg mapel &lt; 75 (Top 10 terbanyak)</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="text-end">Mapel Rendah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowSubjectCountList as $r)
                            <tr>
                                <td>{{ $r['nama'] }}</td>
                                <td class="text-end fw-semibold">{{ $r['low_mapel_count'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-muted text-center py-3">Belum ada data nilai lintas mapel pada semester ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="/build/js/plugins/apexcharts.min.js"></script>

<script>
    // auto submit kelas (tanpa tombol)
    const kelasSel = document.querySelector('#kelas-ajar');
    const formKelas = document.querySelector('#form-kelas');
    if (kelasSel && formKelas) {
        kelasSel.addEventListener('change', () => formKelas.submit());
    }

    // chart distribusi
    const distribusi = @json($chartDistribusi);
    const chartEl = document.querySelector("#chart-distribusi");
    if (chartEl) {
        new ApexCharts(chartEl, {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: "Jumlah Siswa",
                data: distribusi.data || []
            }],
            xaxis: {
                categories: distribusi.categories || []
            }
        }).render();
    }
</script>
@endsection