<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\Intrakurikuler;
use App\Models\KehadiranIntrakurikuler;
use App\Models\KelasAjar;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\SiswaEkstrakurikuler;
use App\Models\SkorAsesmenSiswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Bagian Akademik')) {
            return redirect()->route('dashboard.akademik');
        }

        if ($user->hasRole('Kepala Sekolah')) {
            return redirect()->route('dashboard.kepalaSekolah');
        }

        if($user->hasRole('Guru Mapel')){
            return redirect()->route('dashboard.guruMapel');
        }

        //role lain...
    }

    public function dashboardAkademik(Request $request)
    {
        // Filter tahun ajaran & semester
        $tahunAjaranId = $request->get('tahun_ajaran_id');
        $semester = $request->get('semester');

        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->get();

        // Default: tahun & semester terakhir
        $tahunAjaranAktif = $tahunAjaranId
            ? TahunAjaran::find($tahunAjaranId)
            : TahunAjaran::orderBy('tahun_ajaran_id', 'desc')->first();

        $semesterAktif = $semester ?: ($tahunAjaranAktif ? $tahunAjaranAktif->semester : null);

        // --- 1. Rerata nilai semua mapel per tahun/semester ---
        $kelasAjarIds = KelasAjar::where('tahun_ajaran_id', $tahunAjaranAktif?->tahun_ajaran_id)->pluck('kelas_ajar_id');
        $mapelList = Intrakurikuler::whereIn('kelas_ajar_id', $kelasAjarIds)->get();

        $rerataNilai = [];
        foreach ($mapelList as $mapel) {
            // Join skor_asesmen_siswa ke asesmen_sumatif untuk dapatkan nilai per mapel
            $nilai = SkorAsesmenSiswa::whereHas('asesmenSumatif', function ($q) use ($mapel) {
                $q->where('intrakurikuler_id', $mapel->intrakurikuler_id);
            })
                ->avg('nilai');
            $rerataNilai[] = [
                'nama_pelajaran' => $mapel->nama_pelajaran,
                'rerata' => round($nilai, 2),
            ];
        }

        // --- 2. Minat ekstrakurikuler/mata pelajaran tiap semester ---
        $ekskulList = Ekstrakurikuler::where('tahun_ajaran_id', $tahunAjaranAktif?->tahun_ajaran_id)->get();
        $minatEkskul = [];
        foreach ($ekskulList as $ekskul) {
            $jumlah = SiswaEkstrakurikuler::where('ekstrakurikuler_id', $ekskul->ekstrakurikuler_id)->count();
            $minatEkskul[] = [
                'nama_ekskul' => $ekskul->nama_pelajaran,
                'jumlah' => $jumlah,
            ];
        }

        // --- 3. Minat intrakurikuler (jumlah siswa per mapel) ---
        $minatIntrakurikuler = [];
        foreach ($mapelList as $mapel) {
            // Hitung jumlah siswa unik yang punya skor di mapel ini
            $jumlah = SkorAsesmenSiswa::whereHas('asesmenSumatif', function ($q) use ($mapel) {
                $q->where('intrakurikuler_id', $mapel->intrakurikuler_id);
            })->distinct('riwayat_kelas_id')->count('riwayat_kelas_id');
            $minatIntrakurikuler[] = [
                'nama_pelajaran' => $mapel->nama_pelajaran,
                'jumlah' => $jumlah,
            ];
        }

        // dd([
        //     'tahun ajaran list' => $tahunAjaranList,
        //     'tahunAjaranAktif' => $tahunAjaranAktif,
        //     'semesterAktif' => $semesterAktif,
        //     'rerataNilai' => $rerataNilai,
        //     'minatEkskul' => $minatEkskul,
        //     'ekskulList' => $ekskulList,
        //     'minatIntrakurikuler' => $minatIntrakurikuler,
        // ]);

        return view('dashboard.akademik', compact(
            'tahunAjaranList',
            'tahunAjaranAktif',
            'semesterAktif',
            'rerataNilai',
            'minatEkskul',
            'minatIntrakurikuler'
        ));
    }

    public function dashboardKepalaSekolah(Request $request)
    {
        // Filter tahun ajaran & semester
        $tahunAjaranId = $request->get('tahun_ajaran_id');
        $semester = $request->get('semester');

        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->get();

        // Default: tahun & semester terakhir
        $tahunAjaranAktif = $tahunAjaranId
            ? TahunAjaran::find($tahunAjaranId)
            : TahunAjaran::orderBy('tahun_ajaran_id', 'desc')->first();

        $semesterAktif = $semester ?: ($tahunAjaranAktif ? $tahunAjaranAktif->semester : null);

        // --- 1. Kehadiran Siswa ---
        // Total siswa aktif pada tahun ajaran & semester ini
        $kelasAjarIds = KelasAjar::where('tahun_ajaran_id', $tahunAjaranAktif?->tahun_ajaran_id)->pluck('kelas_ajar_id');
        $riwayatKelasIds = RiwayatKelas::whereIn('kelas_ajar_id', $kelasAjarIds)->pluck('riwayat_kelas_id');
        $totalSiswa = RiwayatKelas::whereIn('kelas_ajar_id', $kelasAjarIds)->count();

        // Total kehadiran (hadir) siswa pada semester ini
        $totalKehadiranSiswa = KehadiranIntrakurikuler::whereIn('riwayat_kelas_id', $riwayatKelasIds)
            ->where('status', 'hadir')
            ->count();

        // Total pertemuan (untuk persentase kehadiran)
        $totalPertemuan = KehadiranIntrakurikuler::whereIn('riwayat_kelas_id', $riwayatKelasIds)->count();

        $persenKehadiranSiswa = $totalPertemuan > 0 ? round(($totalKehadiranSiswa / $totalPertemuan) * 100, 2) : 0;

        // --- 2. Kehadiran Guru (kosongkan) ---
        $persenKehadiranGuru = null;

        // --- 3. Rerata nilai semua mapel (sama seperti akademik) ---
        $mapelList = Intrakurikuler::whereIn('kelas_ajar_id', $kelasAjarIds)->get();
        $rerataNilai = [];
        foreach ($mapelList as $mapel) {
            $nilai = SkorAsesmenSiswa::whereHas('asesmenSumatif', function ($q) use ($mapel) {
                $q->where('intrakurikuler_id', $mapel->intrakurikuler_id);
            })->avg('nilai');
            $rerataNilai[] = [
                'nama_mapel' => $mapel->nama_pelajaran,
                'rerata' => $nilai ? round($nilai, 2) : 0,
            ];
        }

        // --- 4. Rerata nilai tiap kelas paralel ---
        $kelasParalel = KelasAjar::where('tahun_ajaran_id', $tahunAjaranAktif?->tahun_ajaran_id)->get();
        $rerataKelas = [];
        foreach ($kelasParalel as $kelas) {
            $riwayatKelasIds = $kelas->riwayatKelas->pluck('riwayat_kelas_id');
            $nilai = SkorAsesmenSiswa::whereIn('riwayat_kelas_id', $riwayatKelasIds)->avg('nilai');
            $rerataKelas[] = [
                'nama_kelas' => $kelas->kelas->nama_kelas ?? 'Kelas',
                'rerata' => $nilai ? round($nilai, 2) : 0,
            ];
        }

        // --- 5. Rerata nilai tiap siswa (untuk siswa berprestasi) ---
        $siswaList = Siswa::whereHas('riwayatKelas', function ($q) use ($kelasAjarIds) {
            $q->whereIn('kelas_ajar_id', $kelasAjarIds);
        })->get();

        $rerataSiswa = [];
        foreach ($siswaList as $siswa) {
            $riwayatKelasIds = $siswa->riwayatKelas->whereIn('kelas_ajar_id', $kelasAjarIds)->pluck('riwayat_kelas_id');
            $nilai = SkorAsesmenSiswa::whereIn('riwayat_kelas_id', $riwayatKelasIds)->avg('nilai');
            $rerataSiswa[] = [
                'nama_siswa' => $siswa->nama,
                'rerata' => $nilai ? round($nilai, 2) : 0,
                'kelas' => $siswa->riwayatKelasTerakhir->kelasAjar->kelas->nama_kelas
            ];
        }
        // Urutkan siswa berprestasi (nilai tertinggi)
        usort($rerataSiswa, fn($a, $b) => $b['rerata'] <=> $a['rerata']);


        // --- 6. Rerata Nilai Tiap Kelas Paralel (Cashflow style, group per tahun ajaran, bar berdampingan) ---
        $tahunAjaranAll = TahunAjaran::orderBy('tahun_ajaran_id', 'desc')->get()->groupBy('tahun');
        $categories = [];
        $dataGenap = [];
        $dataGanjil = [];

        foreach ($tahunAjaranAll as $tahun => $listTahun) {
            $categories[] = $tahun;
            // Cari semester ganjil & genap pada tahun ini
            $taGanjil = $listTahun->firstWhere('semester', 'Ganjil');
            $taGenap  = $listTahun->firstWhere('semester', 'Genap');

            // Ganjil
            if ($taGanjil) {
                $kelasAjarIds = KelasAjar::where('tahun_ajaran_id', $taGanjil->tahun_ajaran_id)->pluck('kelas_ajar_id');
                $riwayatKelasIds = RiwayatKelas::whereIn('kelas_ajar_id', $kelasAjarIds)->pluck('riwayat_kelas_id');
                $nilaiGanjil = SkorAsesmenSiswa::whereIn('riwayat_kelas_id', $riwayatKelasIds)->avg('nilai');
                $dataGanjil[] = $nilaiGanjil ? round($nilaiGanjil, 2) : 0;
            } else {
                $dataGanjil[] = 0;
            }

            // Genap
            if ($taGenap) {
                $kelasAjarIds = KelasAjar::where('tahun_ajaran_id', $taGenap->tahun_ajaran_id)->pluck('kelas_ajar_id');
                $riwayatKelasIds = RiwayatKelas::whereIn('kelas_ajar_id', $kelasAjarIds)->pluck('riwayat_kelas_id');
                $nilaiGenap = SkorAsesmenSiswa::whereIn('riwayat_kelas_id', $riwayatKelasIds)->avg('nilai');
                $dataGenap[] = $nilaiGenap ? round($nilaiGenap, 2) : 0;
            } else {
                $dataGenap[] = 0;
            }
        }

        return view('dashboard.kepalaSekolah', compact(
            'tahunAjaranList',
            'tahunAjaranAktif',
            'semesterAktif',
            'persenKehadiranSiswa',
            'persenKehadiranGuru',
            'rerataNilai',
            'rerataKelas',
            'rerataSiswa',
            'categories',
            'dataGenap',
            'dataGanjil',
        ));
    }


    public function guruMapel(Request $request)
    {
        $userId = Auth::id();

        // ====== Mapel yang diampu guru ini ======
        $mapels = DB::table('intrakurikuler as i')
            ->join('kelas_ajar as ka', 'ka.kelas_ajar_id', '=', 'i.kelas_ajar_id')
            ->join('kelas as k', 'k.kelas_id', '=', 'ka.kelas_id')
            ->join('tahun_ajaran as ta', 'ta.tahun_ajaran_id', '=', 'ka.tahun_ajaran_id')
            ->where('i.pengampu_user_id', $userId)
            ->orderBy('ta.tahun_ajaran_id', 'desc')
            ->orderBy('k.nama_kelas')
            ->orderBy('i.nama_pelajaran')
            ->get([
                'i.intrakurikuler_id',
                'i.nama_pelajaran',
                'i.kelas_ajar_id',
                'k.nama_kelas',
                'ta.tahun_ajaran_id',
                'ta.tahun',
                'ta.semester',
            ]);

        // default filter UI
        $periode       = $request->get('periode', 'month');        // week|month|semester
        $granularity   = $request->get('granularity', 'week');     // week|day
        $bucket        = (int) $request->get('bucket', 10);        // 10|5
        $kkm           = (int) $request->get('kkm', 75);
        $thresholdRawan = (float) $request->get('rawan_threshold', 0.80); // 0.80
        $atensiThreshold = (int) $request->get('atensi_threshold', 60);  // FIX: default < 60

        // kalau belum punya mapel
        if ($mapels->isEmpty()) {
            return view('dashboard.guru_mapel', [
                'mapels' => $mapels,
                'selected' => null,
                'periode' => $periode,
                'granularity' => $granularity,
                'bucket' => $bucket,
                'kkm' => $kkm,
                'thresholdRawan' => $thresholdRawan,
                'atensiThreshold' => $atensiThreshold,

                'kpiAbsensi' => $this->emptyKpiAbsensi(),
                'chartAbsensi' => $this->emptyChartAbsensi(),
                'rawanAbsensiList' => collect(),

                'kpiNilai' => $this->emptyKpiNilai(),
                'chartNilaiDistribusi' => $this->emptyChartNilaiDistribusi(),
                'atensiList' => collect(),
                'unggulList' => collect(),
            ]);
        }

        // ====== Filter pilihan mapel ======
        $selectedId = (int) $request->get('intrakurikuler_id', $mapels->first()->intrakurikuler_id);
        $selected = $mapels->firstWhere('intrakurikuler_id', $selectedId) ?? $mapels->first();
        $selectedId = (int) $selected->intrakurikuler_id;

        // ====== Range tanggal current & prev ======
        [$start, $end] = $this->resolveDateRange($periode, $selected);
        [$prevStart, $prevEnd] = $this->previousRange($start, $end);

        // ====== ABSENSI ======
        $kpiAbsensi = $this->buildKpiAbsensi($selectedId, $start, $end, $prevStart, $prevEnd, $thresholdRawan);
        $chartAbsensi = $this->buildChartAbsensi($selectedId, $start, $end, $granularity);
        $rawanAbsensiList = $this->buildRawanAbsensiList($selectedId, $start, $end, $thresholdRawan, 6);

        // ====== NILAI ======
        $kpiNilai = $this->buildKpiNilai($selectedId, $start, $end, $prevStart, $prevEnd, $kkm);
        $chartNilaiDistribusi = $this->buildNilaiDistribusi($selectedId, $start, $end, $bucket);

        // FIX: atensi berdasarkan threshold (<60)
        [$atensiList, $unggulList] = $this->buildAtensiUnggulList(
            $selectedId,
            $start,
            $end,
            $prevStart,
            $prevEnd,
            6,
            $atensiThreshold
        );

        return view('dashboard.guru_mapel', compact(
            'mapels',
            'selected',
            'periode',
            'granularity',
            'bucket',
            'kkm',
            'thresholdRawan',
            'atensiThreshold',
            'kpiAbsensi',
            'chartAbsensi',
            'rawanAbsensiList',
            'kpiNilai',
            'chartNilaiDistribusi',
            'atensiList',
            'unggulList'
        ));
    }

    // =========================
    // ABSENSI - KPI
    // =========================
    private function buildKpiAbsensi(
        int $intrakurikulerId,
        Carbon $start,
        Carbon $end,
        Carbon $prevStart,
        Carbon $prevEnd,
        float $thresholdRawan
    ): array {
        $current = $this->absensiAgg($intrakurikulerId, $start, $end);
        $prev = $this->absensiAgg($intrakurikulerId, $prevStart, $prevEnd);

        $rateCurrent = $current['total'] > 0 ? round(($current['hadir'] / $current['total']) * 100, 1) : 0.0;
        $ratePrev    = $prev['total'] > 0 ? round(($prev['hadir'] / $prev['total']) * 100, 1) : 0.0;
        $deltaRate   = round($rateCurrent - $ratePrev, 1);

        $alphaCurrent = $current['alpha'];
        $alphaPrev    = $prev['alpha'];
        $deltaAlpha   = $alphaCurrent - $alphaPrev;

        $rawanCount = DB::table('kehadiran_intrakurikuler as ki')
            ->where('ki.intrakurikuler_id', $intrakurikulerId)
            ->whereBetween('ki.tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('ki.riwayat_kelas_id,
                SUM(CASE WHEN ki.status = "hadir" THEN 1 ELSE 0 END) as hadir_count,
                COUNT(*) as total_count
            ')
            ->groupBy('ki.riwayat_kelas_id')
            ->havingRaw('(SUM(CASE WHEN ki.status = "hadir" THEN 1 ELSE 0 END) / COUNT(*)) < ?', [$thresholdRawan])
            ->get()
            ->count();

        return [
            'rate' => $rateCurrent,
            'rate_prev' => $ratePrev,
            'rate_delta' => $deltaRate,

            'alpha' => $alphaCurrent,
            'alpha_prev' => $alphaPrev,
            'alpha_delta' => $deltaAlpha,

            'rawan_count' => $rawanCount,
        ];
    }

    private function absensiAgg(int $intrakurikulerId, Carbon $start, Carbon $end): array
    {
        $row = DB::table('kehadiran_intrakurikuler')
            ->where('intrakurikuler_id', $intrakurikulerId)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha,
                SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin
            ')
            ->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'hadir' => (int) ($row->hadir ?? 0),
            'alpha' => (int) ($row->alpha ?? 0),
            'sakit' => (int) ($row->sakit ?? 0),
            'izin'  => (int) ($row->izin ?? 0),
        ];
    }

    // =========================
    // ABSENSI - CHART
    // =========================
    private function buildChartAbsensi(int $intrakurikulerId, Carbon $start, Carbon $end, string $granularity): array
    {
        $granularity = in_array($granularity, ['day', 'week']) ? $granularity : 'week';

        $rows = DB::table('kehadiran_intrakurikuler')
            ->where('intrakurikuler_id', $intrakurikulerId)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('tanggal, status, COUNT(*) as c')
            ->groupBy('tanggal', 'status')
            ->orderBy('tanggal')
            ->get();

        $bucketKey = function (string $date) use ($granularity) {
            $d = Carbon::parse($date);
            return $granularity === 'day'
                ? $d->toDateString()
                : $d->startOfWeek(Carbon::MONDAY)->toDateString();
        };

        $labels = [];
        $data = [
            'hadir' => [],
            'alpha' => [],
            'sakit' => [],
            'izin'  => [],
        ];

        foreach ($rows as $r) {
            $key = $bucketKey($r->tanggal);

            if (!isset($labels[$key])) {
                $labels[$key] = $key;
                $data['hadir'][$key] = 0;
                $data['alpha'][$key] = 0;
                $data['sakit'][$key] = 0;
                $data['izin'][$key]  = 0;
            }

            $status = $r->status;
            if (isset($data[$status])) {
                $data[$status][$key] += (int) $r->c;
            }
        }

        // pastikan urut
        ksort($labels);
        foreach ($data as $status => $arr) {
            ksort($arr);
            $data[$status] = $arr;
        }

        $categories = array_values($labels);

        $prettyCategories = array_map(function ($d) use ($granularity) {
            $c = Carbon::parse($d);
            return $granularity === 'day'
                ? $c->format('d M')
                : 'Minggu ' . $c->format('d M');
        }, $categories);

        return [
            'categories' => $prettyCategories,
            'series' => [
                ['name' => 'Hadir', 'data' => array_values($data['hadir'])],
                ['name' => 'Izin',  'data' => array_values($data['izin'])],
                ['name' => 'Sakit', 'data' => array_values($data['sakit'])],
                ['name' => 'Alpha', 'data' => array_values($data['alpha'])],
            ],
        ];
    }

    private function buildRawanAbsensiList(int $intrakurikulerId, Carbon $start, Carbon $end, float $thresholdRawan, int $limit)
    {
        $rows = DB::table('kehadiran_intrakurikuler as ki')
            ->join('riwayat_kelas as rk', 'rk.riwayat_kelas_id', '=', 'ki.riwayat_kelas_id')
            ->join('siswa as s', 's.siswa_id', '=', 'rk.siswa_id')
            ->where('ki.intrakurikuler_id', $intrakurikulerId)
            ->whereBetween('ki.tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('
                ki.riwayat_kelas_id,
                s.siswa_id,
                s.nama as nama_siswa,
                SUM(CASE WHEN ki.status = "hadir" THEN 1 ELSE 0 END) as hadir_count,
                SUM(CASE WHEN ki.status = "alpha" THEN 1 ELSE 0 END) as alpha_count,
                COUNT(*) as total_count
            ')
            ->groupBy('ki.riwayat_kelas_id', 's.siswa_id', 's.nama')
            ->havingRaw('(SUM(CASE WHEN ki.status = "hadir" THEN 1 ELSE 0 END) / COUNT(*)) < ?', [$thresholdRawan])
            ->orderByRaw('(SUM(CASE WHEN ki.status = "hadir" THEN 1 ELSE 0 END) / COUNT(*)) asc')
            ->limit($limit)
            ->get();

        return $rows->map(function ($r) {
            $pct = $r->total_count > 0 ? round(($r->hadir_count / $r->total_count) * 100, 1) : 0;
            return [
                'siswa_id' => (int) $r->siswa_id,
                'nama' => $r->nama_siswa,
                'hadir_pct' => $pct,
                'alpha' => (int) $r->alpha_count,
            ];
        });
    }

    // =========================
    // NILAI - KPI
    // =========================
    private function buildKpiNilai(int $intrakurikulerId, Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd, int $kkm): array
    {
        $current = $this->nilaiAgg($intrakurikulerId, $start, $end, $kkm);
        $prev    = $this->nilaiAgg($intrakurikulerId, $prevStart, $prevEnd, $kkm);

        $avgDelta = round(($current['avg'] ?? 0) - ($prev['avg'] ?? 0), 1);

        return [
            'avg' => $current['avg'],
            'avg_prev' => $prev['avg'],
            'avg_delta' => $avgDelta,

            'below_kkm' => $current['below_kkm'],
            'below_kkm_prev' => $prev['below_kkm'],

            'unggul' => $current['unggul'],
            'unggul_prev' => $prev['unggul'],
        ];
    }

    private function nilaiAgg(int $intrakurikulerId, Carbon $start, Carbon $end, int $kkm): array
    {
        // NOTE: periode nilai pakai created_at (karena schema belum punya tanggal asesmen)
        $row = DB::table('skor_asesmen_siswa as sas')
            ->join('asesmen_sumatif as a', 'a.asesmen_sumatif_id', '=', 'sas.asesmen_sumatif_id')
            ->where('a.intrakurikuler_id', $intrakurikulerId)
            ->whereNotNull('sas.nilai')
            ->whereBetween(DB::raw('DATE(sas.created_at)'), [$start->toDateString(), $end->toDateString()])
            ->selectRaw('
                AVG(sas.nilai) as avg_nilai,
                SUM(CASE WHEN sas.nilai < ? THEN 1 ELSE 0 END) as below_kkm,
                SUM(CASE WHEN sas.nilai >= 85 THEN 1 ELSE 0 END) as unggul
            ', [$kkm])
            ->first();

        return [
            'avg' => $row?->avg_nilai ? round((float) $row->avg_nilai, 1) : 0.0,
            'below_kkm' => (int) ($row->below_kkm ?? 0),
            'unggul' => (int) ($row->unggul ?? 0),
        ];
    }

    private function buildNilaiDistribusi(int $intrakurikulerId, Carbon $start, Carbon $end, int $bucket): array
    {
        $bucket = in_array($bucket, [5, 10]) ? $bucket : 10;

        $nilai = DB::table('skor_asesmen_siswa as sas')
            ->join('asesmen_sumatif as a', 'a.asesmen_sumatif_id', '=', 'sas.asesmen_sumatif_id')
            ->where('a.intrakurikuler_id', $intrakurikulerId)
            ->whereNotNull('sas.nilai')
            ->whereBetween(DB::raw('DATE(sas.created_at)'), [$start->toDateString(), $end->toDateString()])
            ->pluck('sas.nilai')
            ->map(fn($n) => (int) $n)
            ->all();

        $ranges = [];
        for ($from = 0; $from <= 100; $from += $bucket) {
            $to = min($from + $bucket - 1, 100);
            $key = "{$from}-{$to}";
            $ranges[$key] = 0;
        }

        foreach ($nilai as $n) {
            $n = max(0, min(100, $n));
            $from = (int) (floor($n / $bucket) * $bucket);
            $to = min($from + $bucket - 1, 100);
            $key = "{$from}-{$to}";
            if (isset($ranges[$key])) $ranges[$key]++;
        }

        return [
            'categories' => array_keys($ranges),
            'data' => array_values($ranges),
        ];
    }

    /**
     * FIX: Atensi berdasarkan nilai < $atensiThreshold (default 60)
     * Unggul: top nilai (>= 85) atau top N (yang kamu minta)
     */
    private function buildAtensiUnggulList(
        int $intrakurikulerId,
        Carbon $start,
        Carbon $end,
        Carbon $prevStart,
        Carbon $prevEnd,
        int $limit,
        int $atensiThreshold = 60
    ): array {
        // Current AVG per siswa (riwayat_kelas)
        $current = DB::table('skor_asesmen_siswa as sas')
            ->join('asesmen_sumatif as a', 'a.asesmen_sumatif_id', '=', 'sas.asesmen_sumatif_id')
            ->where('a.intrakurikuler_id', $intrakurikulerId)
            ->whereNotNull('sas.nilai')
            ->whereBetween(DB::raw('DATE(sas.created_at)'), [$start->toDateString(), $end->toDateString()])
            ->groupBy('sas.riwayat_kelas_id')
            ->selectRaw('sas.riwayat_kelas_id, AVG(sas.nilai) as avg_nilai')
            ->pluck('avg_nilai', 'riwayat_kelas_id');

        // Prev AVG per siswa
        $prev = DB::table('skor_asesmen_siswa as sas')
            ->join('asesmen_sumatif as a', 'a.asesmen_sumatif_id', '=', 'sas.asesmen_sumatif_id')
            ->where('a.intrakurikuler_id', $intrakurikulerId)
            ->whereNotNull('sas.nilai')
            ->whereBetween(DB::raw('DATE(sas.created_at)'), [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->groupBy('sas.riwayat_kelas_id')
            ->selectRaw('sas.riwayat_kelas_id, AVG(sas.nilai) as avg_nilai')
            ->pluck('avg_nilai', 'riwayat_kelas_id');

        $riwayatIds = $current->keys()->all();
        if (empty($riwayatIds)) return [collect(), collect()];

        // Map nama siswa
        $siswaMap = DB::table('riwayat_kelas as rk')
            ->join('siswa as s', 's.siswa_id', '=', 'rk.siswa_id')
            ->whereIn('rk.riwayat_kelas_id', $riwayatIds)
            ->pluck('s.nama', 'rk.riwayat_kelas_id');

        $rows = collect($riwayatIds)->map(function ($rkId) use ($current, $prev, $siswaMap) {
            $cur = (float) $current[$rkId];
            $prv = isset($prev[$rkId]) ? (float) $prev[$rkId] : 0.0;

            return [
                'riwayat_kelas_id' => (int) $rkId,
                'nama' => $siswaMap[$rkId] ?? 'Tanpa Nama',
                'nilai' => round($cur, 1),
                'delta' => round($cur - $prv, 1),
            ];
        });

        // ===== ATENSI (FIX) =====
        // hanya yang nilai < threshold (default 60)
        $atensi = $rows
            ->filter(fn($x) => (float)$x['nilai'] < $atensiThreshold)
            ->sortBy('nilai')
            ->take($limit)
            ->values();

        // ===== UNGGUL =====
        // ambil top N yang nilai tertinggi (atau kamu bisa filter >= 85 dulu)
        $unggul = $rows
            ->sortByDesc('nilai')
            ->take($limit)
            ->values();

        return [$atensi, $unggul];
    }

    // =========================
    // RANGE HELPER
    // =========================
    private function resolveDateRange(string $periode, $selected): array
    {
        $today = Carbon::today();

        if ($periode === 'week')  return [$today->copy()->startOfWeek(Carbon::MONDAY), $today->copy()->endOfDay()];
        if ($periode === 'month') return [$today->copy()->startOfMonth(), $today->copy()->endOfDay()];

        // semester (berdasarkan tahun_ajaran terpilih)
        $tahun = (string) $selected->tahun; // contoh "2025/2026"
        $parts = preg_split('/\D+/', $tahun);
        $y1 = isset($parts[0]) ? (int) $parts[0] : (int) $today->year;
        $y2 = isset($parts[1]) ? (int) $parts[1] : $y1 + 1;

        if ($selected->semester === 'Ganjil') {
            $start = Carbon::create($y1, 7, 1)->startOfDay();
            $end   = Carbon::create($y1, 12, 31)->endOfDay();
        } else {
            $start = Carbon::create($y2, 1, 1)->startOfDay();
            $end   = Carbon::create($y2, 6, 30)->endOfDay();
        }

        if ($end->greaterThan($today)) $end = $today->copy()->endOfDay();

        return [$start, $end];
    }

    private function previousRange(Carbon $start, Carbon $end): array
    {
        $days = $start->diffInDays($end) + 1;
        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1)->startOfDay();
        return [$prevStart, $prevEnd];
    }

    // =========================
    // EMPTY HELPERS
    // =========================
    private function emptyKpiAbsensi(): array
    {
        return [
            'rate' => 0,
            'rate_prev' => 0,
            'rate_delta' => 0,
            'alpha' => 0,
            'alpha_prev' => 0,
            'alpha_delta' => 0,
            'rawan_count' => 0,
        ];
    }

    private function emptyChartAbsensi(): array
    {
        return [
            'categories' => [],
            'series' => [
                ['name' => 'Hadir', 'data' => []],
                ['name' => 'Izin',  'data' => []],
                ['name' => 'Sakit', 'data' => []],
                ['name' => 'Alpha', 'data' => []],
            ],
        ];
    }

    private function emptyKpiNilai(): array
    {
        return [
            'avg' => 0,
            'avg_prev' => 0,
            'avg_delta' => 0,
            'below_kkm' => 0,
            'below_kkm_prev' => 0,
            'unggul' => 0,
            'unggul_prev' => 0,
        ];
    }

    private function emptyChartNilaiDistribusi(): array
    {
        return ['categories' => [], 'data' => []];
    }
}
