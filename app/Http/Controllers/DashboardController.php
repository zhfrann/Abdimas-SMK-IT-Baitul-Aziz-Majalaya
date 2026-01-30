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

        if ($user->hasRole('Guru Mapel')) {
            return redirect()->route('dashboard.guruMapel');
        }

        if ($user->hasRole('Siswa')) {
            return redirect()->route('dashboard.siswa');
        }
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

    public function dashboardSiswa(Request $request)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        // List tahun ajaran (urut terbaru)
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran_id', 'desc')->get();

        // Tahun ajaran aktif (default: terbaru)
        $tahunAjaranId = $request->get('tahun_ajaran_id');
        $tahunAjaranAktif = $tahunAjaranId
            ? TahunAjaran::find($tahunAjaranId)
            : TahunAjaran::orderBy('tahun_ajaran_id', 'desc')->first();

        // Ambil riwayat kelas siswa pada tahun ajaran aktif
        $riwayatKelasAktif = $siswa->riwayatKelas()->where('kelas_ajar_id', function ($q) use ($tahunAjaranAktif) {
            $q->select('kelas_ajar_id')
                ->from('kelas_ajar')
                ->where('tahun_ajaran_id', $tahunAjaranAktif?->tahun_ajaran_id)
                ->limit(1);
        })->first();

        // --- 1. Distribusi nilai per semester (per mapel) ---
        $nilaiDistribusi = [];
        if ($riwayatKelasAktif) {
            $skorList = SkorAsesmenSiswa::where('riwayat_kelas_id', $riwayatKelasAktif->riwayat_kelas_id)
                ->with('asesmenSumatif.intrakurikuler')
                ->get();

            // Group by mapel
            $mapelNilai = [];
            foreach ($skorList as $skor) {
                $mapel = $skor->asesmenSumatif->intrakurikuler->nama_pelajaran ?? 'Mapel';
                $mapelNilai[$mapel][] = $skor->nilai;
            }
            foreach ($mapelNilai as $mapel => $nilaiArr) {
                $nilaiDistribusi[] = [
                    'mapel' => $mapel,
                    'rerata' => round(array_sum($nilaiArr) / count($nilaiArr), 2),
                    'nilai' => $nilaiArr,
                ];
            }
        }

        // --- 2. Perkembangan nilai keseluruhan siswa (dari semester ke semester) ---
        $riwayatKelasAll = $siswa->riwayatKelas()->with('kelasAjar.tahunAjaran')->get();
        $perkembanganNilai = [];
        foreach ($riwayatKelasAll as $rk) {
            $tahunAjaran = $rk->kelasAjar->tahunAjaran->tahun ?? '';
            $semester = $rk->kelasAjar->tahunAjaran->semester ?? '';
            $label = $tahunAjaran . ' S' . $semester;
            $rerata = SkorAsesmenSiswa::where('riwayat_kelas_id', $rk->riwayat_kelas_id)->avg('nilai');
            $perkembanganNilai[] = [
                'label' => $label,
                'rerata' => $rerata ? round($rerata, 2) : 0,
            ];
        }

        return view('dashboard.siswa', compact(
            'tahunAjaranList',
            'tahunAjaranAktif',
            'nilaiDistribusi',
            'perkembanganNilai'
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
        $periode         = 'semester'; // week|month|semester
        $granularity     = 'week';     // week|day
        $bucket          = 10;         // 10|5
        $kkm             = (int) $request->get('kkm', 75);
        $thresholdRawan  = (float) $request->get('rawan_threshold', 0.80);   // 0.80
        $atensiThreshold = (int) $request->get('atensi_threshold', 60);      // default 60
        $showAll         = $request->boolean('show_all');                    // ?show_all=1
        $limitList       = $showAll ? 10000 : 6;

        // kalau belum punya mapel
        if ($mapels->isEmpty()) {
            return view('dashboard.guru_mapel', [
                'mapels'          => $mapels,
                'selected'        => null,
                'periode'         => $periode,
                'granularity'     => $granularity,
                'bucket'          => $bucket,
                'kkm'             => $kkm,
                'thresholdRawan'  => $thresholdRawan,
                'atensiThreshold' => $atensiThreshold,

                'kpiAbsensi'      => $this->emptyKpiAbsensi(),
                'chartAbsensi'    => $this->emptyChartAbsensi(),
                'rawanAbsensiList' => collect(),

                'kpiNilai'             => $this->emptyKpiNilai(),
                'chartNilaiDistribusi' => $this->emptyChartNilaiDistribusi(),
                'atensiList'           => collect(),
                'unggulList'           => collect(),
            ]);
        }

        // ====== Filter pilihan mapel ======
        $selectedId = (int) $request->get('intrakurikuler_id', $mapels->first()->intrakurikuler_id);
        $selected   = $mapels->firstWhere('intrakurikuler_id', $selectedId) ?? $mapels->first();
        $selectedId = (int) $selected->intrakurikuler_id;

        // ====== Range tanggal current & prev ======
        [$start, $end] = $this->resolveDateRange($periode, $selected);
        [$prevStart, $prevEnd] = $this->previousRange($start, $end);

        // ====== ABSENSI ======
        $kpiAbsensi       = $this->buildKpiAbsensi($selectedId, $start, $end, $prevStart, $prevEnd, $thresholdRawan);
        $chartAbsensi     = $this->buildChartAbsensi($selectedId, $start, $end, $granularity);
        $rawanAbsensiList = $this->buildRawanAbsensiList($selectedId, $start, $end, $thresholdRawan, 6);

        // ====== NILAI (PAKAI NILAI AKHIR: SL + SAS) ======
        $kpiNilai             = $this->buildKpiNilai($selectedId, $start, $end, $prevStart, $prevEnd, $kkm);
        $chartNilaiDistribusi = $this->buildNilaiDistribusi($selectedId, $start, $end, $bucket);

        [$atensiList, $unggulList] = $this->buildAtensiUnggulList(
            $selectedId,
            $start,
            $end,
            $prevStart,
            $prevEnd,
            $limitList,
            $atensiThreshold,   // threshold atensi (bisa diset = KKM kalau mau)
            85                  // threshold unggul
        );

        return view('dashboard.guru_mapel', compact(
            'mapels',
            'selected',
            'showAll',
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
        $prev    = $this->absensiAgg($intrakurikulerId, $prevStart, $prevEnd);

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
            'rate'        => $rateCurrent,
            'rate_prev'   => $ratePrev,
            'rate_delta'  => $deltaRate,

            'alpha'       => $alphaCurrent,
            'alpha_prev'  => $alphaPrev,
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

        ksort($labels);
        foreach ($data as $status => $arr) {
            ksort($arr);
            $data[$status] = $arr;
        }

        $categories = array_values($labels);

        $prettyCategories = array_map(function ($d) use ($granularity) {
            $c = Carbon::parse($d);
            if ($granularity === 'day') {
                return $c->format('d M');
            }

            $startW = $c->copy(); // ini sudah Monday
            $endW   = $c->copy()->endOfWeek(Carbon::SUNDAY);
            return $startW->format('d M') . ' - ' . $endW->format('d M');
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
                'siswa_id'  => (int) $r->siswa_id,
                'nama'      => $r->nama_siswa,
                'hadir_pct' => $pct,
                'alpha'     => (int) $r->alpha_count,
            ];
        });
    }

    // =========================
    // NILAI - HELPER UTAMA (SL + TEST + NON_TEST -> SAS -> FINAL)
    // =========================
    private function finalScorePerSiswa(
        int $intrakurikulerId,
        Carbon $start,
        Carbon $end
    ) {
        $rows = DB::table('skor_asesmen_siswa as sas')
            ->join('asesmen_sumatif as a', 'a.asesmen_sumatif_id', '=', 'sas.asesmen_sumatif_id')
            ->where('a.intrakurikuler_id', $intrakurikulerId)
            ->whereNotNull('sas.nilai')
            ->whereBetween(DB::raw('DATE(sas.created_at)'), [$start->toDateString(), $end->toDateString()])
            ->groupBy('sas.riwayat_kelas_id')
            ->selectRaw('
                sas.riwayat_kelas_id,
                AVG(CASE WHEN a.asesmen_type = "sumatif_lingkup" THEN sas.nilai END) as sumatif_lingkup,
                AVG(CASE WHEN a.asesmen_type = "test"            THEN sas.nilai END) as test_nilai,
                AVG(CASE WHEN a.asesmen_type = "non_test"        THEN sas.nilai END) as non_test_nilai
            ')
            ->get();

        return $rows
            ->map(function ($r) {
                $sl      = $r->sumatif_lingkup !== null ? (float) $r->sumatif_lingkup : null;
                $test    = $r->test_nilai       !== null ? (float) $r->test_nilai       : null;
                $nonTest = $r->non_test_nilai   !== null ? (float) $r->non_test_nilai   : null;

                // SAS dari test & non_test
                $sas = null;
                $components = [];
                if ($test !== null)    $components[] = $test;
                if ($nonTest !== null) $components[] = $nonTest;

                if (count($components) === 1) {
                    $sas = $components[0]; // hanya satu → pakai apa adanya
                } elseif (count($components) === 2) {
                    $sas = array_sum($components) / 2; // dua-duanya → rata-rata
                }

                // Final: (SL + SAS) / 2 kalau dua-duanya ada
                if ($sl !== null && $sas !== null) {
                    $final = ($sl + $sas) / 2;
                } elseif ($sl !== null) {
                    $final = $sl;
                } else {
                    $final = $sas; // bisa null kalau nggak ada apa-apa
                }

                return [
                    'riwayat_kelas_id' => (int) $r->riwayat_kelas_id,
                    'sl'    => $sl,
                    'sas'   => $sas,
                    'final' => $final !== null ? round($final, 1) : null,
                ];
            })
            ->filter(fn($x) => $x['final'] !== null)
            ->values();
    }

    // =========================
    // NILAI - KPI
    // =========================
    private function buildKpiNilai(
        int $intrakurikulerId,
        Carbon $start,
        Carbon $end,
        Carbon $prevStart,
        Carbon $prevEnd,
        int $kkm
    ): array {
        $current = $this->nilaiAgg($intrakurikulerId, $start, $end, $kkm);
        $prev    = $this->nilaiAgg($intrakurikulerId, $prevStart, $prevEnd, $kkm);

        $avgDelta = round(($current['avg'] ?? 0) - ($prev['avg'] ?? 0), 1);

        return [
            'avg'            => $current['avg'],
            'avg_prev'       => $prev['avg'],
            'avg_delta'      => $avgDelta,

            'below_kkm'      => $current['below_kkm'],
            'below_kkm_prev' => $prev['below_kkm'],

            'unggul'         => $current['unggul'],
            'unggul_prev'    => $prev['unggul'],
        ];
    }

    private function nilaiAgg(
        int $intrakurikulerId,
        Carbon $start,
        Carbon $end,
        int $kkm
    ): array {
        $finals = $this->finalScorePerSiswa($intrakurikulerId, $start, $end);

        if ($finals->isEmpty()) {
            return [
                'avg'       => 0.0,
                'below_kkm' => 0,
                'unggul'    => 0,
            ];
        }

        $avg       = round($finals->avg('final'), 1);
        $belowKkm  = $finals->filter(fn($x) => $x['final'] < $kkm)->count();
        $unggul    = $finals->filter(fn($x) => $x['final'] >= 85)->count();

        return [
            'avg'       => $avg,
            'below_kkm' => $belowKkm,
            'unggul'    => $unggul,
        ];
    }

    private function buildNilaiDistribusi(
        int $intrakurikulerId,
        Carbon $start,
        Carbon $end,
        int $bucket
    ): array {
        $bucket = in_array($bucket, [5, 10]) ? $bucket : 10;

        // Ambil nilai akhir per siswa (final score)
        $finals = $this->finalScorePerSiswa($intrakurikulerId, $start, $end);

        $nilaiPerSiswa = $finals->pluck('final')
            ->map(fn($n) => (int) round($n))
            ->all();

        // ===== Bikin bucket =====
        // Contoh:
        //  bucket 10 -> 0-9,10-19,...,80-89,90-100
        //  bucket 5  -> 0-4,5-9,...,90-94,95-100
        $ranges = [];
        for ($from = 0; $from < 100; $from += $bucket) {
            if ($from + $bucket >= 100) {
                // Paksa bucket terakhir selalu "100 - bucket" s/d 100
                $from = 100 - $bucket;
                $to   = 100;
                $key  = "{$from}-{$to}";
                $ranges[$key] = 0;
                break;
            } else {
                $to  = $from + $bucket - 1;
                $key = "{$from}-{$to}";
                $ranges[$key] = 0;
            }
        }

        // ===== Isi histogram =====
        foreach ($nilaiPerSiswa as $n) {
            $n = max(0, min(100, (int) $n));

            // Tentukan awal bucket
            if ($n === 100) {
                $from = 100 - $bucket;
            } else {
                $from = (int) (floor($n / $bucket) * $bucket);

                // Kalau somehow nyelonong ke atas, paksa ke bucket terakhir
                if ($from + $bucket > 100) {
                    $from = 100 - $bucket;
                }
            }

            $to = ($from === 100 - $bucket)
                ? 100
                : $from + $bucket - 1;

            $key = "{$from}-{$to}";

            if (isset($ranges[$key])) {
                $ranges[$key]++;
            }
            // kalau nggak ada, berarti ada mismatch definisi range (tapi
            // dengan logika di atas seharusnya nggak kejadian lagi)
        }

        return [
            'categories'  => array_keys($ranges),
            'data'        => array_values($ranges),
            'total_siswa' => count($nilaiPerSiswa),
        ];
    }


    private function buildAtensiUnggulList(
        int $intrakurikulerId,
        Carbon $start,
        Carbon $end,
        Carbon $prevStart,
        Carbon $prevEnd,
        int $limit,
        int $atensiThreshold = 60,
        int $unggulThreshold = 85
    ): array {
        $current = $this->finalScorePerSiswa($intrakurikulerId, $start, $end)
            ->keyBy('riwayat_kelas_id');

        $prev = $this->finalScorePerSiswa($intrakurikulerId, $prevStart, $prevEnd)
            ->keyBy('riwayat_kelas_id');

        if ($current->isEmpty()) {
            return [collect(), collect()];
        }

        $riwayatIds = $current->keys()->all();

        $siswaMap = DB::table('riwayat_kelas as rk')
            ->join('siswa as s', 's.siswa_id', '=', 'rk.siswa_id')
            ->whereIn('rk.riwayat_kelas_id', $riwayatIds)
            ->pluck('s.nama', 'rk.riwayat_kelas_id');

        $rows = $current->map(function ($row, $rkId) use ($prev, $siswaMap) {
            $cur = (float) $row['final'];
            $prv = $prev[$rkId]['final'] ?? null;
            $delta = $prv !== null ? round($cur - (float) $prv, 1) : 0.0;

            return [
                'riwayat_kelas_id' => (int) $rkId,
                'nama'  => $siswaMap[$rkId] ?? 'Tanpa Nama',
                'nilai' => round($cur, 1),
                'delta' => $delta,
            ];
        })->values();

        $atensi = $rows
            ->filter(fn($x) => $x['nilai'] < $atensiThreshold)
            ->sortBy('nilai')
            ->when($limit > 0, fn($q) => $q->take($limit))
            ->values();

        $unggul = $rows
            ->filter(fn($x) => $x['nilai'] >= $unggulThreshold)
            ->sortByDesc('nilai')
            ->when($limit > 0, fn($q) => $q->take($limit))
            ->values();

        return [$atensi, $unggul];
    }

    // =========================
    // RANGE HELPER
    // =========================
    private function resolveDateRange(string $periode, $selected): array
    {
        $today = Carbon::today();

        if ($periode === 'week') {
            return [$today->copy()->startOfWeek(Carbon::MONDAY), $today->copy()->endOfDay()];
        }
        if ($periode === 'month') {
            return [$today->copy()->startOfMonth(), $today->copy()->endOfDay()];
        }

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

        if ($end->greaterThan($today)) {
            $end = $today->copy()->endOfDay();
        }

        return [$start, $end];
    }

    private function previousRange(Carbon $start, Carbon $end): array
    {
        $days     = $start->diffInDays($end) + 1;
        $prevEnd  = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1)->startOfDay();
        return [$prevStart, $prevEnd];
    }

    // =========================
    // EMPTY HELPERS
    // =========================
    private function emptyKpiAbsensi(): array
    {
        return [
            'rate'        => 0,
            'rate_prev'   => 0,
            'rate_delta'  => 0,
            'alpha'       => 0,
            'alpha_prev'  => 0,
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
            'avg'            => 0,
            'avg_prev'       => 0,
            'avg_delta'      => 0,
            'below_kkm'      => 0,
            'below_kkm_prev' => 0,
            'unggul'         => 0,
            'unggul_prev'    => 0,
        ];
    }

    private function emptyChartNilaiDistribusi(): array
    {
        return [
            'categories'  => [],
            'data'        => [],
            'total_siswa' => 0,
        ];
    }



    public function waliKelas(Request $request)
    {
        $userId = Auth::id();

        // ===== default =====
        $topN = 10;
        $atensiThreshold = 60; // avg semua mapel < 60 => atensi
        $bucket = 10;          // histogram bucket

        // ===== kelas_ajar wali =====
        $kelasAjars = DB::table('kelas_ajar as ka')
            ->join('kelas as k', 'k.kelas_id', '=', 'ka.kelas_id')
            ->join('tahun_ajaran as ta', 'ta.tahun_ajaran_id', '=', 'ka.tahun_ajaran_id')
            ->where('ka.wali_user_id', $userId)
            ->orderBy('ta.tahun_ajaran_id', 'desc')
            ->orderBy('k.nama_kelas')
            ->get([
                'ka.kelas_ajar_id',
                'ka.tahun_ajaran_id',
                'k.nama_kelas',
                'ta.tahun',
                'ta.semester',
            ]);

        if ($kelasAjars->isEmpty()) {
            return view('dashboard.wali_kelas', [
                'kelasAjars' => $kelasAjars,
                'selectedKelasAjar' => null,

                'mapels' => collect(),
                'selectedMapel' => null,
                'topMapel' => null,
                'mapelDipakai' => null,

                'kpi' => $this->emptyKpiWali(),
                'chartDistribusi' => $this->emptyChartDistribusi(),
                'topMapelList' => collect(),
                'attendanceList' => collect(),
            ]);
        }

        // ===== kelas_ajar terpilih =====
        $selectedKelasAjarId = (int) $request->get('kelas_ajar_id', $kelasAjars->first()->kelas_ajar_id);
        $selectedKelasAjar = $kelasAjars->firstWhere('kelas_ajar_id', $selectedKelasAjarId) ?? $kelasAjars->first();
        $selectedKelasAjarId = (int) $selectedKelasAjar->kelas_ajar_id;

        // ===== range semester =====
        [$start, $end] = $this->resolveSemesterRangeFromKelasAjar($selectedKelasAjar);

        // ===== mapel di kelas_ajar =====
        $mapels = DB::table('intrakurikuler as i')
            ->where('i.kelas_ajar_id', $selectedKelasAjarId)
            ->orderBy('i.nama_pelajaran')
            ->get(['i.intrakurikuler_id', 'i.nama_pelajaran']);

        // ===== mapel fokus default (mapel dengan avg kelas terendah) =====
        $topMapel = null;
        if ($mapels->isNotEmpty()) {
            $mapelAvgRows = DB::table('skor_asesmen_siswa as sas')
                ->join('asesmen_sumatif as a', 'a.asesmen_sumatif_id', '=', 'sas.asesmen_sumatif_id')
                ->join('intrakurikuler as i', 'i.intrakurikuler_id', '=', 'a.intrakurikuler_id')
                ->where('i.kelas_ajar_id', $selectedKelasAjarId)
                ->whereNotNull('sas.nilai')
                ->whereBetween(DB::raw('DATE(sas.created_at)'), [$start->toDateString(), $end->toDateString()])
                ->groupBy('a.intrakurikuler_id')
                ->selectRaw('a.intrakurikuler_id, AVG(sas.nilai) as avg_kelas')
                ->orderByRaw('AVG(sas.nilai) asc')
                ->get();

            $focusIdDefault = (int) ($mapelAvgRows->first()->intrakurikuler_id ?? $mapels->first()->intrakurikuler_id);
            $topMapel = $mapels->firstWhere('intrakurikuler_id', $focusIdDefault) ?? $mapels->first();
        }

        // ===== mapel pilihan user =====
        $selectedMapelId = (int) $request->get('intrakurikuler_id', 0);
        $selectedMapel = $selectedMapelId
            ? ($mapels->firstWhere('intrakurikuler_id', $selectedMapelId) ?? null)
            : null;

        $mapelDipakai = $selectedMapel ?? $topMapel;

        // ===== KPI =====
        $kpi = $this->buildKpiWaliKelas($selectedKelasAjarId, $start, $end, $atensiThreshold);

        // ===== Distribusi =====
        $chartDistribusi = $this->buildDistribusiAvgSiswaOverall($selectedKelasAjarId, $start, $end, $bucket);

        // ===== Top N siswa mapel dipakai =====
        $topMapelList = $mapelDipakai
            ? $this->buildTopMapelList($selectedKelasAjarId, (int) $mapelDipakai->intrakurikuler_id, $start, $end, $topN)
            : collect();

        // ===== Kehadiran lebih informatif =====
        // urutan: hadir% paling rendah, kalau sama alpha terbanyak
        $attendanceList = $this->buildAttendanceListOverall($selectedKelasAjarId, $start, $end, $topN);

        return view('dashboard.wali_kelas', [
            'kelasAjars' => $kelasAjars,
            'selectedKelasAjar' => $selectedKelasAjar,

            'mapels' => $mapels,
            'selectedMapel' => $selectedMapel,
            'topMapel' => $topMapel,
            'mapelDipakai' => $mapelDipakai,

            'kpi' => $kpi,
            'chartDistribusi' => $chartDistribusi,
            'topMapelList' => $topMapelList,
            'attendanceList' => $attendanceList,
        ]);
    }

    // =========================
    // KPI WALI KELAS (nilai + kehadiran)
    // =========================
    private function buildKpiWaliKelas(int $kelasAjarId, Carbon $start, Carbon $end, int $atensiThreshold): array
    {
        $nilaiRow = DB::table('skor_asesmen_siswa as sas')
            ->join('asesmen_sumatif as a', 'a.asesmen_sumatif_id', '=', 'sas.asesmen_sumatif_id')
            ->join('intrakurikuler as i', 'i.intrakurikuler_id', '=', 'a.intrakurikuler_id')
            ->where('i.kelas_ajar_id', $kelasAjarId)
            ->whereNotNull('sas.nilai')
            ->whereBetween(DB::raw('DATE(sas.created_at)'), [$start->toDateString(), $end->toDateString()])
            ->selectRaw('AVG(sas.nilai) as avg_kelas')
            ->first();

        $avgKelas = $nilaiRow?->avg_kelas ? round((float) $nilaiRow->avg_kelas, 1) : 0.0;

        $perSiswa = $this->queryAvgOverallPerSiswa($kelasAjarId, $start, $end);
        $unggulCount = $perSiswa->filter(fn($r) => (float) $r->avg_nilai >= 85)->count();
        $atensiCount = $perSiswa->filter(fn($r) => (float) $r->avg_nilai < $atensiThreshold)->count();

        $absRow = DB::table('kehadiran_intrakurikuler as ki')
            ->join('intrakurikuler as i', 'i.intrakurikuler_id', '=', 'ki.intrakurikuler_id')
            ->where('i.kelas_ajar_id', $kelasAjarId)
            ->whereBetween('ki.tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN ki.status = "hadir" THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN ki.status = "alpha" THEN 1 ELSE 0 END) as alpha
            ')
            ->first();

        $totalAbs = (int) ($absRow->total ?? 0);
        $hadirAbs = (int) ($absRow->hadir ?? 0);
        $alphaTotal = (int) ($absRow->alpha ?? 0);
        $hadirRate = $totalAbs > 0 ? round(($hadirAbs / $totalAbs) * 100, 1) : 0.0;

        return [
            'avg_nilai_kelas' => $avgKelas,
            'unggul_count' => $unggulCount,
            'atensi_count' => $atensiCount,
            'hadir_rate' => $hadirRate,
            'alpha_total' => $alphaTotal,
        ];
    }

    // =========================
    // Distribusi AVG Nilai per Siswa (overall semua mapel)
    // =========================
    private function buildDistribusiAvgSiswaOverall(int $kelasAjarId, Carbon $start, Carbon $end, int $bucket): array
    {
        $perSiswa = $this->queryAvgOverallPerSiswa($kelasAjarId, $start, $end);
        $nilai = $perSiswa->pluck('avg_nilai')->map(fn($x) => (int) round((float) $x, 0))->all();

        $bucket = max(1, (int) $bucket);
        $maxFrom = 100 - $bucket;

        $ranges = [];
        for ($from = 0; $from <= $maxFrom; $from += $bucket) {
            $to = ($from === $maxFrom) ? 100 : ($from + $bucket - 1);
            $ranges["{$from}-{$to}"] = 0;
        }

        foreach ($nilai as $n) {
            $n = max(0, min(100, (int) $n));

            $from = (int) (floor($n / $bucket) * $bucket);
            if ($from > $maxFrom) $from = $maxFrom;

            $to = ($from === $maxFrom) ? 100 : ($from + $bucket - 1);
            $key = "{$from}-{$to}";

            if (isset($ranges[$key])) $ranges[$key]++;
        }

        return [
            'categories' => array_keys($ranges),
            'data' => array_values($ranges),
        ];
    }

    // =========================
    // Top N siswa (mapel tertentu)
    // =========================
    private function buildTopMapelList(int $kelasAjarId, int $intrakurikulerId, Carbon $start, Carbon $end, int $limit)
    {
        $rows = DB::table('skor_asesmen_siswa as sas')
            ->join('asesmen_sumatif as a', 'a.asesmen_sumatif_id', '=', 'sas.asesmen_sumatif_id')
            ->join('intrakurikuler as i', 'i.intrakurikuler_id', '=', 'a.intrakurikuler_id')
            ->join('riwayat_kelas as rk', 'rk.riwayat_kelas_id', '=', 'sas.riwayat_kelas_id')
            ->join('siswa as s', 's.siswa_id', '=', 'rk.siswa_id')
            ->where('i.kelas_ajar_id', $kelasAjarId)
            ->where('a.intrakurikuler_id', $intrakurikulerId)
            ->whereNotNull('sas.nilai')
            ->whereBetween(DB::raw('DATE(sas.created_at)'), [$start->toDateString(), $end->toDateString()])
            ->groupBy('sas.riwayat_kelas_id', 's.nama')
            ->selectRaw('s.nama as nama, AVG(sas.nilai) as avg_nilai')
            ->orderByRaw('AVG(sas.nilai) desc')
            ->limit($limit)
            ->get();

        return $rows->map(fn($r) => [
            'nama' => $r->nama,
            'avg_nilai' => round((float) $r->avg_nilai, 1),
        ]);
    }

    // =========================
    // Kehadiran siswa (overall semua mapel) - lebih informatif
    // - hadir_pct: persen hadir
    // - alpha: jumlah alpha
    // - total: total pertemuan tercatat
    // sort: hadir_pct ASC, alpha DESC
    // =========================
    private function buildAttendanceListOverall(int $kelasAjarId, Carbon $start, Carbon $end, int $limit)
    {
        $rows = DB::table('kehadiran_intrakurikuler as ki')
            ->join('intrakurikuler as i', 'i.intrakurikuler_id', '=', 'ki.intrakurikuler_id')
            ->join('riwayat_kelas as rk', 'rk.riwayat_kelas_id', '=', 'ki.riwayat_kelas_id')
            ->join('siswa as s', 's.siswa_id', '=', 'rk.siswa_id')
            ->where('i.kelas_ajar_id', $kelasAjarId)
            ->whereBetween('ki.tanggal', [$start->toDateString(), $end->toDateString()])
            ->groupBy('ki.riwayat_kelas_id', 's.nama')
            ->selectRaw('
                s.nama as nama,
                SUM(CASE WHEN ki.status = "hadir" THEN 1 ELSE 0 END) as hadir_count,
                SUM(CASE WHEN ki.status = "alpha" THEN 1 ELSE 0 END) as alpha_count,
                COUNT(*) as total_count
            ')
            ->orderByRaw('(SUM(CASE WHEN ki.status="hadir" THEN 1 ELSE 0 END) / NULLIF(COUNT(*),0)) asc')
            ->orderByRaw('SUM(CASE WHEN ki.status="alpha" THEN 1 ELSE 0 END) desc')
            ->limit($limit)
            ->get();

        return $rows->map(function ($r) {
            $total = (int) ($r->total_count ?? 0);
            $hadir = (int) ($r->hadir_count ?? 0);
            $alpha = (int) ($r->alpha_count ?? 0);

            $pct = $total > 0 ? round(($hadir / $total) * 100, 1) : 0.0;

            // label sederhana biar informatif
            $label = 'Aman';
            if ($pct < 80 || $alpha >= 3) $label = 'Waspada';
            if ($pct < 70 || $alpha >= 5) $label = 'Prioritas';

            return [
                'nama' => $r->nama,
                'hadir_pct' => $pct,
                'alpha' => $alpha,
                'total' => $total,
                'label' => $label,
            ];
        });
    }

    // =========================
    // Query helper: avg overall per siswa (semua mapel)
    // =========================
    private function queryAvgOverallPerSiswa(int $kelasAjarId, Carbon $start, Carbon $end)
    {
        return DB::table('skor_asesmen_siswa as sas')
            ->join('asesmen_sumatif as a', 'a.asesmen_sumatif_id', '=', 'sas.asesmen_sumatif_id')
            ->join('intrakurikuler as i', 'i.intrakurikuler_id', '=', 'a.intrakurikuler_id')
            ->join('riwayat_kelas as rk', 'rk.riwayat_kelas_id', '=', 'sas.riwayat_kelas_id')
            ->join('siswa as s', 's.siswa_id', '=', 'rk.siswa_id')
            ->where('i.kelas_ajar_id', $kelasAjarId)
            ->whereNotNull('sas.nilai')
            ->whereBetween(DB::raw('DATE(sas.created_at)'), [$start->toDateString(), $end->toDateString()])
            ->groupBy('sas.riwayat_kelas_id', 's.nama')
            ->selectRaw('s.nama as nama, AVG(sas.nilai) as avg_nilai')
            ->get();
    }

    // =========================
    // Semester range helper
    // =========================
    private function resolveSemesterRangeFromKelasAjar($kelasAjarRow): array
    {
        $today = Carbon::today();

        $tahun = (string) ($kelasAjarRow->tahun ?? $today->year);
        $parts = preg_split('/\D+/', $tahun);
        $y1 = isset($parts[0]) ? (int) $parts[0] : (int) $today->year;
        $y2 = isset($parts[1]) ? (int) $parts[1] : $y1 + 1;

        if (($kelasAjarRow->semester ?? 'Ganjil') === 'Ganjil') {
            $start = Carbon::create($y1, 7, 1)->startOfDay();
            $end = Carbon::create($y1, 12, 31)->endOfDay();
        } else {
            $start = Carbon::create($y2, 1, 1)->startOfDay();
            $end = Carbon::create($y2, 6, 30)->endOfDay();
        }

        if ($end->greaterThan($today)) $end = $today->copy()->endOfDay();
        return [$start, $end];
    }

    private function emptyKpiWali(): array
    {
        return [
            'avg_nilai_kelas' => 0,
            'unggul_count' => 0,
            'atensi_count' => 0,
            'hadir_rate' => 0,
            'alpha_total' => 0,
        ];
    }

    private function emptyChartDistribusi(): array
    {
        return ['categories' => [], 'data' => []];
    }
}
