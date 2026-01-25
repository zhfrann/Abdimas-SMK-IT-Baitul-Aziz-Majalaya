@extends('layouts.master')

@section('title', 'Dashboard Guru')

@section('content')
    <x-breadcrumb item="Dashboard" active="Guru" />

    <div class="row">
        {{-- =========================
            FILTER BAR (FULL WIDTH)
        ========================== --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard') }}" id="filter-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label mb-1">Mapel</label>
                                <select class="form-select" name="intrakurikuler_id" id="filter-mapel" required>
                                    @if($mapels->isEmpty())
                                        <option value="" selected>Tidak ada mapel</option>
                                    @else
                                        @foreach($mapels as $m)
                                            <option value="{{ $m->intrakurikuler_id }}"
                                                @selected($selected && (int)$selected->intrakurikuler_id === (int)$m->intrakurikuler_id)
                                            >
                                                {{ $m->nama_kelas }} - {{ $m->nama_pelajaran }}
                                                ({{ $m->tahun }} {{ $m->semester }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label mb-1">Periode</label>
                                <select class="form-select" name="periode" id="filter-periode">
                                    <option value="week" @selected($periode === 'week')>Minggu ini</option>
                                    <option value="month" @selected($periode === 'month')>Bulan ini</option>
                                    <option value="semester" @selected($periode === 'semester')>Semester ini</option>
                                </select>
                            </div>

                            {{-- (optional) advanced filter --}}
                            <input type="hidden" name="granularity" id="hidden-granularity" value="{{ $granularity }}">
                            <input type="hidden" name="bucket" id="hidden-bucket" value="{{ $bucket }}">
                            <input type="hidden" name="kkm" id="hidden-kkm" value="{{ $kkm }}">
                            <input type="hidden" name="rawan_threshold" id="hidden-rawan" value="{{ $thresholdRawan }}">

                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">
                                        <i class="ti ti-rotate-2"></i> Reset
                                    </a>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ti ti-filter"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($mapels->isEmpty())
                        <div class="alert alert-warning mt-3 mb-0">
                            Anda belum memiliki mapel yang diampu. Pastikan data <b>intrakurikuler</b> untuk guru ini sudah dibuat.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- =========================
            SECTION: ABSENSI
        ========================== --}}
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">Ringkasan Kehadiran</h5>
                <span class="text-muted">
                    @if($selected)
                        {{ $selected->nama_kelas }} • {{ $selected->nama_pelajaran }}
                    @else
                        Berdasarkan kelas & mapel terpilih
                    @endif
                </span>
            </div>
        </div>

        {{-- KPI ABSENSI (3 cards) --}}
        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-primary">
                                <i class="ti ti-checklist f-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Kehadiran Rata-rata</h6>
                            <small class="text-muted">Periode terpilih</small>
                        </div>
                    </div>
                    <div class="bg-body p-3 mt-3 rounded">
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <h4 class="mb-1">{{ number_format($kpiAbsensi['rate'] ?? 0, 1) }}%</h4>
                                @php $delta = (float)($kpiAbsensi['rate_delta'] ?? 0); @endphp
                                <p class="mb-0 {{ $delta >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="ti {{ $delta >= 0 ? 'ti-arrow-up-right' : 'ti-arrow-down-left' }}"></i>
                                    {{ $delta >= 0 ? '+' : '' }}{{ number_format($delta, 1) }}% dari periode lalu
                                </p>
                            </div>
                            <div style="width: 130px">
                                <div id="kpi-absensi-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-danger">
                                <i class="ti ti-alert-triangle f-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Alpha</h6>
                            <small class="text-muted">Akumulasi periode</small>
                        </div>
                    </div>
                    <div class="bg-body p-3 mt-3 rounded">
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <h4 class="mb-1">{{ (int)($kpiAbsensi['alpha'] ?? 0) }}</h4>
                                @php $deltaA = (int)($kpiAbsensi['alpha_delta'] ?? 0); @endphp
                                <p class="mb-0 {{ $deltaA <= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="ti {{ $deltaA <= 0 ? 'ti-arrow-down-left' : 'ti-arrow-up-right' }}"></i>
                                    {{ $deltaA >= 0 ? '+' : '' }}{{ $deltaA }} dari periode lalu
                                </p>
                            </div>
                            <div style="width: 130px">
                                <div id="kpi-absensi-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-warning">
                                <i class="ti ti-user-exclamation f-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Siswa Rawan Absensi</h6>
                            <small class="text-muted">Hadir &lt; {{ (int)($thresholdRawan * 100) }}%</small>
                        </div>
                    </div>
                    <div class="bg-body p-3 mt-3 rounded">
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <h4 class="mb-1">{{ (int)($kpiAbsensi['rawan_count'] ?? 0) }} siswa</h4>
                                <p class="text-warning mb-0">
                                    <i class="ti ti-arrows-left-right"></i> Perlu perhatian
                                </p>
                            </div>
                            <div style="width: 130px">
                                <div id="kpi-absensi-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ABSENSI: Chart 8 + List 4 --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0 pt-2">
                    <ul class="nav nav-tabs analytics-tab" id="absensiTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="absensi-tab-1" data-bs-toggle="tab"
                                data-bs-target="#absensi-tab-1-pane" type="button" role="tab"
                                aria-controls="absensi-tab-1-pane" aria-selected="true">
                                Trend
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="absensi-tab-2" data-bs-toggle="tab"
                                data-bs-target="#absensi-tab-2-pane" type="button" role="tab"
                                aria-controls="absensi-tab-2-pane" aria-selected="false">
                                Komposisi Status
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Grafik Kehadiran</h5>

                        {{-- ini akan submit ulang form filter ketika diganti --}}
                        <select class="form-select form-select-sm w-auto" id="absensi-granularity">
                            <option value="week" @selected($granularity === 'week')>Weekly</option>
                            <option value="day" @selected($granularity === 'day')>Daily</option>
                        </select>
                    </div>

                    <div class="tab-content" id="absensiTabContent">
                        <div class="tab-pane fade show active" id="absensi-tab-1-pane" role="tabpanel"
                            aria-labelledby="absensi-tab-1" tabindex="0">
                            <div id="chart-absensi-trend"></div>
                            <small class="text-muted d-block mt-2">
                                Series: Hadir, Izin, Sakit, Alpha
                            </small>
                        </div>
                        <div class="tab-pane fade" id="absensi-tab-2-pane" role="tabpanel"
                            aria-labelledby="absensi-tab-2" tabindex="0">
                            <div id="chart-absensi-stacked"></div>
                            <small class="text-muted d-block mt-2">
                                Stacked bar untuk komposisi status per periode.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- LIST RAWAN ABSENSI --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body border-bottom pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Siswa Rawan Absensi</h5>
                    </div>
                    <p class="text-muted mb-3">Hadir &lt; {{ (int)($thresholdRawan * 100) }}%</p>
                </div>

                <ul class="list-group list-group-flush">
                    @forelse($rawanAbsensiList as $s)
                        <li class="list-group-item">
                            <a href="#" class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s border bg-light-warning">
                                            <span>SR</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="row g-1">
                                            <div class="col-7">
                                                <h6 class="mb-0">{{ $s['nama'] }}</h6>
                                                <p class="text-muted mb-0">
                                                    <small>Hadir: {{ number_format($s['hadir_pct'], 1) }}%</small>
                                                </p>
                                            </div>
                                            <div class="col-5 text-end">
                                                <h6 class="mb-1">Alpha {{ (int)$s['alpha'] }}</h6>
                                                <p class="text-warning mb-0">
                                                    <small><i class="ti ti-alert-triangle"></i> rawan</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item">
                            <div class="text-muted">Tidak ada siswa rawan pada periode ini.</div>
                        </li>
                    @endforelse
                </ul>

                <div class="card-footer">
                    <div class="d-grid">
                        <button class="btn btn-outline-secondary" type="button" disabled>
                            Lihat Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================
            SECTION: NILAI
        ========================== --}}
        <div class="col-12 mt-2">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">Ringkasan Nilai</h5>
                <span class="text-muted">Sebaran & daftar tindakan</span>
            </div>
        </div>

        {{-- KPI NILAI (3 cards) --}}
        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-primary">
                                <i class="ti ti-chart-line f-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Rata-rata Nilai</h6>
                            <small class="text-muted">Periode terpilih</small>
                        </div>
                    </div>
                    <div class="bg-body p-3 mt-3 rounded">
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <h4 class="mb-1">{{ number_format($kpiNilai['avg'] ?? 0, 1) }}</h4>
                                @php $dN = (float)($kpiNilai['avg_delta'] ?? 0); @endphp
                                <p class="mb-0 {{ $dN >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="ti {{ $dN >= 0 ? 'ti-arrow-up-right' : 'ti-arrow-down-left' }}"></i>
                                    {{ $dN >= 0 ? '+' : '' }}{{ number_format($dN, 1) }} dari periode lalu
                                </p>
                            </div>
                            <div style="width: 130px">
                                <div id="kpi-nilai-1"></div>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">KKM default: {{ (int)$kkm }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-danger">
                                <i class="ti ti-mood-sad f-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Di Bawah KKM</h6>
                            <small class="text-muted">Nilai &lt; {{ (int)$kkm }}</small>
                        </div>
                    </div>
                    <div class="bg-body p-3 mt-3 rounded">
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <h4 class="mb-1">{{ (int)($kpiNilai['below_kkm'] ?? 0) }} siswa</h4>
                                @php
                                    $prevBelow = (int)($kpiNilai['below_kkm_prev'] ?? 0);
                                    $curBelow = (int)($kpiNilai['below_kkm'] ?? 0);
                                    $dBelow = $curBelow - $prevBelow;
                                @endphp
                                <p class="mb-0 {{ $dBelow <= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="ti {{ $dBelow <= 0 ? 'ti-arrow-down-left' : 'ti-arrow-up-right' }}"></i>
                                    {{ $dBelow >= 0 ? '+' : '' }}{{ $dBelow }} dari periode lalu
                                </p>
                            </div>
                            <div style="width: 130px">
                                <div id="kpi-nilai-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-success">
                                <i class="ti ti-award f-20"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Siswa Unggul</h6>
                            <small class="text-muted">Nilai ≥ 85</small>
                        </div>
                    </div>
                    <div class="bg-body p-3 mt-3 rounded">
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <h4 class="mb-1">{{ (int)($kpiNilai['unggul'] ?? 0) }} siswa</h4>
                                <p class="text-success mb-0"><i class="ti ti-bolt"></i> bagus</p>
                            </div>
                            <div style="width: 130px">
                                <div id="kpi-nilai-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SEBARAN NILAI (FULL WIDTH) --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 pt-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Sebaran Nilai (Histogram)</h5>
                        <select class="form-select form-select-sm w-auto" id="nilai-bucket">
                            <option value="10" @selected((int)$bucket === 10)>Bucket 10</option>
                            <option value="5" @selected((int)$bucket === 5)>Bucket 5</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-nilai-distribusi"></div>
                    <small class="text-muted d-block mt-2">
                        X: rentang nilai. Y: jumlah siswa per rentang.
                    </small>
                </div>
            </div>
        </div>

        {{-- LIST NILAI: ATENSI & UNGGUL --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body border-bottom pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Butuh Atensi</h5>
                        <span class="text-muted"><small>Urut nilai terendah</small></span>
                    </div>
                    <p class="text-muted mb-3">Saran: follow-up siswa dengan nilai rendah</p>
                </div>

                <ul class="list-group list-group-flush">
                    @forelse($atensiList as $s)
                        <li class="list-group-item">
                            <a href="#" class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s border bg-light-danger">
                                            <span>BA</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="row g-1">
                                            <div class="col-7">
                                                <h6 class="mb-0">{{ $s['nama'] }}</h6>
                                                <p class="text-muted mb-0"><small>Nilai: {{ number_format($s['nilai'], 1) }}</small></p>
                                            </div>
                                            <div class="col-5 text-end">
                                                @php $dd = (float)$s['delta']; @endphp
                                                <h6 class="mb-1">Δ {{ $dd >= 0 ? '+' : '' }}{{ number_format($dd, 1) }}</h6>
                                                <p class="mb-0 {{ $dd >= 0 ? 'text-success' : 'text-danger' }}">
                                                    <small>
                                                        <i class="ti {{ $dd >= 0 ? 'ti-arrow-up-right' : 'ti-arrow-down-left' }}"></i>
                                                        {{ $dd >= 0 ? 'naik' : 'turun' }}
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item">
                            <div class="text-muted">Belum ada data nilai pada periode ini.</div>
                        </li>
                    @endforelse
                </ul>

                <div class="card-footer">
                    <div class="d-grid">
                        <button class="btn btn-outline-secondary" type="button" disabled>Lihat Semua</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body border-bottom pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Unggul</h5>
                        <span class="text-muted"><small>Urut nilai tertinggi</small></span>
                    </div>
                    <p class="text-muted mb-3">Saran: beri apresiasi / challenge lanjutan</p>
                </div>

                <ul class="list-group list-group-flush">
                    @forelse($unggulList as $s)
                        <li class="list-group-item">
                            <a href="#" class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s border bg-light-success">
                                            <span>UG</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="row g-1">
                                            <div class="col-7">
                                                <h6 class="mb-0">{{ $s['nama'] }}</h6>
                                                <p class="text-muted mb-0"><small>Nilai: {{ number_format($s['nilai'], 1) }}</small></p>
                                            </div>
                                            <div class="col-5 text-end">
                                                @php $dd = (float)$s['delta']; @endphp
                                                <h6 class="mb-1">Δ {{ $dd >= 0 ? '+' : '' }}{{ number_format($dd, 1) }}</h6>
                                                <p class="mb-0 {{ $dd >= 0 ? 'text-success' : 'text-danger' }}">
                                                    <small>
                                                        <i class="ti {{ $dd >= 0 ? 'ti-arrow-up-right' : 'ti-arrow-down-left' }}"></i>
                                                        {{ $dd >= 0 ? 'naik' : 'turun' }}
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item">
                            <div class="text-muted">Belum ada data nilai pada periode ini.</div>
                        </li>
                    @endforelse
                </ul>

                <div class="card-footer">
                    <div class="d-grid">
                        <button class="btn btn-outline-secondary" type="button" disabled>Lihat Semua</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="/build/js/plugins/apexcharts.min.js"></script>

    <script>
        // data dari controller
        const chartAbsensi = @json($chartAbsensi);
        const chartNilaiDistribusi = @json($chartNilaiDistribusi);

        // ===== mini sparkline (dummy, bisa kamu ganti jadi data real)
        function renderMiniSpark(id, arr) {
            const options = {
                chart: { type: 'area', height: 60, sparkline: { enabled: true } },
                stroke: { curve: 'smooth', width: 2 },
                dataLabels: { enabled: false },
                series: [{ name: 'value', data: arr || [10, 12, 11, 14, 13, 16, 15] }],
                tooltip: { enabled: false }
            };
            const el = document.querySelector(id);
            if (el) new ApexCharts(el, options).render();
        }

        renderMiniSpark("#kpi-absensi-1");
        renderMiniSpark("#kpi-absensi-2");
        renderMiniSpark("#kpi-absensi-3");
        renderMiniSpark("#kpi-nilai-1");
        renderMiniSpark("#kpi-nilai-2");
        renderMiniSpark("#kpi-nilai-3");

        // ===== chart absensi trend (line)
        const trendEl = document.querySelector("#chart-absensi-trend");
        if (trendEl) {
            new ApexCharts(trendEl, {
                chart: { type: 'line', height: 320, toolbar: { show: false } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                series: chartAbsensi.series || [],
                xaxis: { categories: chartAbsensi.categories || [] }
            }).render();
        }

        // ===== chart absensi stacked (bar)
        const stackedEl = document.querySelector("#chart-absensi-stacked");
        if (stackedEl) {
            new ApexCharts(stackedEl, {
                chart: { type: 'bar', height: 320, stacked: true, toolbar: { show: false } },
                dataLabels: { enabled: false },
                series: chartAbsensi.series || [],
                xaxis: { categories: chartAbsensi.categories || [] }
            }).render();
        }

        // ===== chart nilai distribusi (bar)
        const nilaiEl = document.querySelector("#chart-nilai-distribusi");
        if (nilaiEl) {
            new ApexCharts(nilaiEl, {
                chart: { type: 'bar', height: 320, toolbar: { show: false } },
                dataLabels: { enabled: false },
                series: [{ name: "Jumlah Siswa", data: chartNilaiDistribusi.data || [] }],
                xaxis: { categories: chartNilaiDistribusi.categories || [] }
            }).render();
        }

        // ===== interaksi: granularity & bucket -> submit ulang form
        const form = document.querySelector('#filter-form');

        const granularitySelect = document.querySelector('#absensi-granularity');
        if (granularitySelect && form) {
            granularitySelect.addEventListener('change', function () {
                document.querySelector('#hidden-granularity').value = this.value;
                form.submit();
            });
        }

        const bucketSelect = document.querySelector('#nilai-bucket');
        if (bucketSelect && form) {
            bucketSelect.addEventListener('change', function () {
                document.querySelector('#hidden-bucket').value = this.value;
                form.submit();
            });
        }
    </script>
@endsection
