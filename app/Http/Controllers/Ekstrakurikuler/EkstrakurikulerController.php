<?php

namespace App\Http\Controllers\Ekstrakurikuler;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EkstrakurikulerController extends Controller
{
    // Halaman Tabel Ekstrakurikuler
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $tahunAjaran = TahunAjaran::query()
            ->orderByDesc('tahun_ajaran_id')
            ->get();

        $guru = User::query()
            ->role('Guru Mapel')
            ->orderBy('name')
            ->get();

        $ekstraQuery = Ekstrakurikuler::query()
            ->with(['tahunAjaran', 'pembina'])
            ->withCount('peserta')
            ->orderByDesc('ekstrakurikuler_id');

        if ($user->hasRole('Guru Mapel')) {
            $ekstraQuery->where('user_id', $user->id);
        }
        $ekstrakurikuler = $ekstraQuery->get();

        return view('ekstrakurikuler.table_ekstrakurikuler', compact('tahunAjaran', 'guru', 'ekstrakurikuler'));
    }

    public function create() {}

    // Menambahkan ekstrakurikuler
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelajaran' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ekstrakurikuler', 'nama_pelajaran')
                    ->where(fn($q) => $q->where('tahun_ajaran_id', $request->tahun_ajaran_id)),
            ],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,tahun_ajaran_id'],
            'pengampu_user_id' => ['required', 'exists:users,id'],
        ], [
            'nama_pelajaran.unique' => 'Ekstrakurikuler ini sudah ada pada tahun ajaran yang dipilih.',
        ]);

        Ekstrakurikuler::create([
            'nama_pelajaran' => $request->nama_pelajaran,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'user_id' => $request->pengampu_user_id,
        ]);

        return redirect()->route('ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    public function show(string $id) {}

    // Mengedit Ekstrakurikuler
    public function edit($id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);
        return response()->json([
            'id' => $ekskul->ekstrakurikuler_id,
            'nama_pelajaran' => $ekskul->nama_pelajaran,
            'tahun_ajaran_id' => $ekskul->tahun_ajaran_id,
            'pengampu_user_id' => $ekskul->user_id,
        ]);
    }

    // Memperbarui Ekstrakurikuler
    public function update(Request $request, $id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);

        $request->validate([
            'nama_pelajaran' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ekstrakurikuler', 'nama_pelajaran')
                    ->where(fn($q) => $q->where('tahun_ajaran_id', $request->tahun_ajaran_id))
                    ->ignore($ekskul->ekstrakurikuler_id, 'ekstrakurikuler_id'),
            ],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,tahun_ajaran_id'],
            'pengampu_user_id' => ['required', 'exists:users,id'],
        ], [
            'nama_pelajaran.unique' => 'Ekstrakurikuler ini sudah ada pada tahun ajaran yang dipilih.',
        ]);

        $ekskul->update([
            'nama_pelajaran' => $request->nama_pelajaran,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'user_id' => $request->pengampu_user_id,
        ]);

        return redirect()->route('ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    // Menghapus Ekstrakurikuler
    public function destroy($id)
    {
        $ekskul = Ekstrakurikuler::findOrFail($id);

        // Cek relasi jika tidak boleh dihapus jika ada peserta/kehadiran
        if ($ekskul->peserta()->count() > 0 || $ekskul->kehadiran()->count() > 0) {
            return redirect()->route('ekstrakurikuler.index')->with('warning', 'Tidak bisa menghapus, masih ada data terkait.');
        }

        $ekskul->delete();

        return redirect()->route('ekstrakurikuler.index')->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}
