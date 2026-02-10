<?php

namespace App\Http\Controllers\Ekstrakurikuler;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\PenilaianEkstrakurikuler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenilaianEkstrakurikulerController extends Controller
{
    // Halaman untuk penilaian siswa ekstrakurikuler
    public function index($ekstrakurikuler_id)
    {
        $ekskul = Ekstrakurikuler::query()->with(['peserta.siswa', 'peserta.penilaians', 'tahunAjaran'])->findOrFail($ekstrakurikuler_id);
        $user = Auth::user();
        if (!$user->hasRole('Bagian Akademik') && $ekskul->user_id != $user->id) {
            $namaEkstrakurikuler = $ekskul->nama_pelajaran;
            $tahunAjaran = $ekskul->tahunAjaran->tahun;
            $semester = $ekskul->tahunAjaran->semester;
            return back()->with('error', "Anda tidak punya akses untuk melihat Penilaian di ekstrakurikuler $namaEkstrakurikuler $tahunAjaran $semester");
        }


        // Ambil semua peserta ekskul beserta penilaian (jika ada)
        $peserta = $ekskul->peserta->map(function ($peserta) {
            $penilaian = $peserta->penilaians->first();
            return [
                'siswa_ekstrakurikuler_id' => $peserta->siswa_ekstrakurikuler_id,
                'nama' => $peserta->siswa->user->name ?? $peserta->siswa->nama,
                'deskripsi' => $penilaian ? $penilaian->deskripsi : '',
            ];
        });

        return view('ekstrakurikuler.penilaian_ekstrakurikuler', [
            'ekskul' => $ekskul,
            'peserta' => $peserta,
        ]);
    }

    public function create() {}

    // Membuat penilaian untuk 1 siswa ekstrakurikuler
    public function store(Request $request, $ekstrakurikuler_id)
    {
        $validated = $request->validate([
            'siswa_ekstrakurikuler_id' => 'required|exists:siswa_ekstrakurikuler,siswa_ekstrakurikuler_id',
            'deskripsi' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $penilaian = PenilaianEkstrakurikuler::firstOrNew([
                'siswa_ekstrakurikuler_id' => $validated['siswa_ekstrakurikuler_id'],
            ]);
            $penilaian->deskripsi = $validated['deskripsi'];
            $penilaian->save();

            DB::commit();
            return redirect()->back()->with('success', 'Penilaian berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan penilaian.');
        }
    }

    public function show(string $id) {}

    public function edit(string $id) {}

    // Memperbarui penilaian untuk 1 siswa ekstrakurikuler
    public function update(Request $request, $ekstrakurikuler_id, $siswa_ekstrakurikuler_id)
    {
        $validated = $request->validate([
            'deskripsi' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $penilaian = PenilaianEkstrakurikuler::where('siswa_ekstrakurikuler_id', $siswa_ekstrakurikuler_id)->firstOrFail();
            $penilaian->deskripsi = $validated['deskripsi'];
            $penilaian->save();

            DB::commit();
            return redirect()->back()->with('success', 'Penilaian berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate penilaian.');
        }
    }

    // Menghapus penilaian untuk 1 siswa ekstrakurikuler
    public function destroy($ekstrakurikuler_id, $siswa_ekstrakurikuler_id)
    {
        DB::beginTransaction();
        try {
            $penilaian = PenilaianEkstrakurikuler::where('siswa_ekstrakurikuler_id', $siswa_ekstrakurikuler_id)->first();
            if ($penilaian) {
                $penilaian->delete();
            }
            DB::commit();
            return redirect()->back()->with('success', 'Penilaian berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus penilaian.');
        }
    }
}
