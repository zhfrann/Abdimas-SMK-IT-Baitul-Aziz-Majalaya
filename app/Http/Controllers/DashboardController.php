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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
