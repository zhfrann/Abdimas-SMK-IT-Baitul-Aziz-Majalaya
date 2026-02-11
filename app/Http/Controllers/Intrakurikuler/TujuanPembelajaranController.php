<?php

namespace App\Http\Controllers\Intrakurikuler;

use App\Http\Controllers\Controller;
use App\Models\Intrakurikuler;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TujuanPembelajaranController extends Controller
{
    // Halaman tabel tujuan pembelajaran sebuah intrakurikuler
    public function index($intrakurikuler_id)
    {
        $intrakurikuler = Intrakurikuler::with('kelasAjar.kelas')->findOrFail($intrakurikuler_id);
        $tujuanPembelajaran = TujuanPembelajaran::where('intrakurikuler_id', $intrakurikuler_id)->get();

        $user = Auth::user();
        $intrakurikuler = Intrakurikuler::query()->findOrFail($intrakurikuler_id);
        if (!$user->hasRole('Bagian Akademik') && $intrakurikuler->pengampu_user_id != $user->id) {
            $kelasAjar = $intrakurikuler->kelasAjar->kelas->nama_kelas;
            $tahunAjaran = $intrakurikuler->kelasAjar->tahunAjaran->tahun;
            $semester = $intrakurikuler->kelasAjar->tahunAjaran->semester;
            return back()->with('error', "Anda tidak punya akses untuk melihat Tujuan Pembelajaran di intrakurikuler $intrakurikuler->nama_pelajaran kelas $kelasAjar $tahunAjaran $semester");
        }

        return view('intrakurikuler.table_tujuan_pembelajaran', compact('intrakurikuler', 'tujuanPembelajaran'));
    }

    public function create() {}

    // Membuat 1 tujuan pembelajaran pada sebuah intrakurikuler
    public function store(Request $request, $intrakurikuler_id)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $intrakurikuler = Intrakurikuler::query()->findOrFail($intrakurikuler_id);
        if (!$user->hasRole('Bagian Akademik') && $intrakurikuler->pengampu_user_id != $user->id) {
            $kelasAjar = $intrakurikuler->kelasAjar->kelas->nama_kelas;
            $tahunAjaran = $intrakurikuler->kelasAjar->tahunAjaran->tahun;
            $semester = $intrakurikuler->kelasAjar->tahunAjaran->semester;
            return back()->with('error', "Anda tidak punya akses untuk menambahkan Tujuan Pembelajaran di intrakurikuler $intrakurikuler->nama_pelajaran kelas $kelasAjar $tahunAjaran $semester");
        }

        try {
            DB::beginTransaction();
            TujuanPembelajaran::create([
                'intrakurikuler_id' => $intrakurikuler_id,
                'deskripsi' => $request->deskripsi,
            ]);
            DB::commit();
            return back()->with('success', 'Tujuan pembelajaran berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambah tujuan pembelajaran.');
        }
    }

    public function show(string $id) {}

    public function edit(string $id) {}

    // Mengedit 1 tujuan pembelajaran pada sebuah intrakurikuler
    public function update(Request $request, $intrakurikuler_id, $id)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $intrakurikuler = Intrakurikuler::query()->findOrFail($intrakurikuler_id);
        if (!$user->hasRole('Bagian Akademik') && $intrakurikuler->pengampu_user_id != $user->id) {
            $kelasAjar = $intrakurikuler->kelasAjar->kelas->nama_kelas;
            $tahunAjaran = $intrakurikuler->kelasAjar->tahunAjaran->tahun;
            $semester = $intrakurikuler->kelasAjar->tahunAjaran->semester;
            return back()->with('error', "Anda tidak punya akses untuk mengedit Tujuan Pembelajaran di intrakurikuler $intrakurikuler->nama_pelajaran kelas $kelasAjar $tahunAjaran $semester");
        }

        try {
            DB::beginTransaction();
            $tujuan = TujuanPembelajaran::where('intrakurikuler_id', $intrakurikuler_id)->findOrFail($id);
            $tujuan->update(['deskripsi' => $request->deskripsi]);
            DB::commit();
            return back()->with('success', 'Tujuan pembelajaran berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate tujuan pembelajaran.');
        }
    }

    // Menghapus 1 tujuan pembelajaran pada sebuah intrakurikuler
    public function destroy($intrakurikuler_id, $id)
    {
        $user = Auth::user();
        $intrakurikuler = Intrakurikuler::query()->findOrFail($intrakurikuler_id);
        if (!$user->hasRole('Bagian Akademik') && $intrakurikuler->pengampu_user_id != $user->id) {
            $kelasAjar = $intrakurikuler->kelasAjar->kelas->nama_kelas;
            $tahunAjaran = $intrakurikuler->kelasAjar->tahunAjaran->tahun;
            $semester = $intrakurikuler->kelasAjar->tahunAjaran->semester;
            return back()->with('error', "Anda tidak punya akses untuk menghapus Tujuan Pembelajaran di intrakurikuler $intrakurikuler->nama_pelajaran kelas $kelasAjar $tahunAjaran $semester");
        }

        try {
            DB::beginTransaction();
            $tujuan = TujuanPembelajaran::where('intrakurikuler_id', $intrakurikuler_id)->findOrFail($id);
            $tujuan->delete();
            DB::commit();
            return back()->with('success', 'Tujuan pembelajaran berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus tujuan pembelajaran. Silakan coba lagi.');
        }
    }
}
