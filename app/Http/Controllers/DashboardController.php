<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\Intrakurikuler;
use App\Models\KelasAjar;
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
}
