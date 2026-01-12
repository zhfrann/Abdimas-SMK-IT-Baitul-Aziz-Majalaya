<?php

namespace App\Http\Controllers;

use App\Models\Intrakurikuler;
use App\Models\KelasAjar;
use App\Models\User;
use Illuminate\Http\Request;

class IntrakurikulerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $intrakurikuler = Intrakurikuler::query()
            ->with([
                'kelasAjar' => function ($q) {
                    $q->with(['kelas', 'tahunAjaran'])
                        ->withCount('riwayatKelas'); // <-- count di sini (milik KelasAjar)
                },
                'pengampu.staff',
            ])
            ->get();

        return view('intrakurikuler.index', compact('intrakurikuler'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Kelas ajar untuk dipilih (tahun ajaran akan ikut kebaca)
        $kelasAjar = KelasAjar::with(['kelas', 'tahunAjaran'])
            ->orderByDesc('tahun_ajaran_id')
            ->orderByDesc('kelas_ajar_id')
            ->get();

        // Semua guru mapel
        $guru = User::role('Guru Mapel')
            ->with('staff') // opsional untuk tampil nama staff/nip
            ->orderBy('name')
            ->get();

        /**
         * Mapping guru yang SUDAH dipakai per tahun_ajaran_id:
         * tahun_ajaran_id => [pengampu_user_id, ...]
         * Dibangun via join intrakurikuler -> kelas_ajar (karena intrakurikuler tidak punya tahun_ajaran_id).
         */
        $usedGuruByTahunAjaran = Intrakurikuler::query()
            ->join('kelas_ajar', 'kelas_ajar.kelas_ajar_id', '=', 'intrakurikuler.kelas_ajar_id')
            ->select('kelas_ajar.tahun_ajaran_id', 'intrakurikuler.pengampu_user_id')
            ->get()
            ->groupBy('tahun_ajaran_id')
            ->map(fn($rows) => $rows->pluck('pengampu_user_id')->unique()->values())
            ->toArray();

        return view('intrakurikuler.create', compact('kelasAjar', 'guru', 'usedGuruByTahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelajaran' => ['required', 'string', 'max:255'],
            'kelas_ajar_id' => ['required', 'exists:kelas_ajar,kelas_ajar_id'],
            'pengampu_user_id' => ['required', 'exists:users,id'],
        ]);

        $kelasAjar = KelasAjar::findOrFail($request->kelas_ajar_id);
        $tahunAjaranId = $kelasAjar->tahun_ajaran_id;

        // Validasi aturan bisnis: 1 guru hanya boleh dipakai 1x per tahun ajaran (intrakurikuler manapun)
        $exists = Intrakurikuler::query()
            ->where('pengampu_user_id', $request->pengampu_user_id)
            ->whereHas('kelasAjar', function ($q) use ($tahunAjaranId) {
                $q->where('tahun_ajaran_id', $tahunAjaranId);
            })
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'pengampu_user_id' => 'Guru ini sudah menjadi pengampu intrakurikuler pada tahun ajaran yang sama. Pilih guru lain atau gunakan tahun ajaran berbeda.',
                ])
                ->withInput();
        }

        Intrakurikuler::create([
            'nama_pelajaran' => $request->nama_pelajaran,
            'kelas_ajar_id' => $request->kelas_ajar_id,
            'pengampu_user_id' => $request->pengampu_user_id,
        ]);

        return redirect()->route('intrakurikuler.index')->with('success', 'Intrakurikuler berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
