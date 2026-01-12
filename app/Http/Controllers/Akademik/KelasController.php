<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelasAjar;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function index()
    {
        $kelasAjar = KelasAjar::with(['kelas', 'tahunAjaran', 'waliKelas'])->get();
        return view('akademik.kelas.index', compact('kelasAjar'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $tahunAjaran = TahunAjaran::orderByDesc('tahun_ajaran_id')->get();
        $waliKelas = User::role('Wali Kelas')->get();
        return view('akademik.kelas.create', compact('kelas', 'tahunAjaran', 'waliKelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,tahun_ajaran_id',
            'wali_user_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // Cari atau buat kelas berdasarkan nama_kelas
            $kelas = Kelas::firstOrCreate([
                'nama_kelas' => $request->nama_kelas,
            ]);

            // Cek duplikat kelas ajar (kelas_id + tahun_ajaran_id)
            $exists = KelasAjar::where('kelas_id', $kelas->kelas_id)
                ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->exists();
            if ($exists) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['nama_kelas' => 'Kombinasi kelas dan tahun ajaran sudah ada!'])
                    ->withInput();
            }

            // Buat kelas ajar
            KelasAjar::create([
                'kelas_id' => $kelas->kelas_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'wali_user_id' => $request->wali_user_id,
            ]);

            DB::commit();
            return redirect()->route('akademik.kelas.index')->with('success', 'Kelas ajar berhasil ditambah');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['nama_kelas' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}
