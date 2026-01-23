<?php

namespace App\Http\Controllers;

use App\Models\Intrakurikuler;
use App\Models\KelasAjar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IntrakurikulerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $intrakurikuler = Intrakurikuler::query()
    //         ->with([
    //             'kelasAjar' => function ($q) {
    //                 $q->with(['kelas', 'tahunAjaran'])
    //                   ->withCount('riwayatKelas');
    //             },
    //             'pengampu.staff',
    //         ])
    //         ->orderByDesc('intrakurikuler_id')
    //         ->get();

    //     return view('intrakurikuler.index', compact('intrakurikuler'));
    // }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $id = $user->id;
        $role = $user?->getRoleNames()->first();

        if ($role === 'Guru Mapel') {
            $intrakurikuler = Intrakurikuler::query()
                ->with([
                    'kelasAjar' => function ($q) {
                        $q->with(['kelas', 'tahunAjaran'])
                            ->withCount('riwayatKelas');
                    },
                    'pengampu.staff',
                ])
                ->where('pengampu_user_id', $id)
                ->orderByDesc('intrakurikuler_id')
                ->get();

            // buat modal create
            $kelasAjar = KelasAjar::with(['kelas', 'tahunAjaran'])
                ->orderByDesc('tahun_ajaran_id')
                ->orderByDesc('kelas_ajar_id')
                ->get();

            $guru = User::role('Guru Mapel')
                ->with('staff')
                ->orderBy('name')
                ->get();
        }
        if ($role == 'Bagian Akademik') {
            $intrakurikuler = Intrakurikuler::query()
                ->with([
                    'kelasAjar' => function ($q) {
                        $q->with(['kelas', 'tahunAjaran'])
                            ->withCount('riwayatKelas');
                    },
                    'pengampu.staff',
                ])
                ->orderByDesc('intrakurikuler_id')
                ->get();

            // buat modal create
            $kelasAjar = KelasAjar::with(['kelas', 'tahunAjaran'])
                ->orderByDesc('tahun_ajaran_id')
                ->orderByDesc('kelas_ajar_id')
                ->get();

            $guru = User::role('Guru Mapel')
                ->with('staff')
                ->orderBy('name')
                ->get();
        }

        return view('intrakurikuler.index', compact('intrakurikuler', 'kelasAjar', 'guru'));
    }


    public function create()
    {
        // $kelasAjar = KelasAjar::with(['kelas', 'tahunAjaran'])
        //     ->orderByDesc('tahun_ajaran_id')
        //     ->orderByDesc('kelas_ajar_id')
        //     ->get();

        // $guru = User::role('Guru Mapel')
        //     ->with('staff')
        //     ->orderBy('name')
        //     ->get();

        // return view('intrakurikuler.create', compact('kelasAjar', 'guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelajaran' => [
                'required',
                'string',
                'max:255',
                // biar pesan error enak sebelum kena unique index DB
                Rule::unique('intrakurikuler', 'nama_pelajaran')
                    ->where(fn($q) => $q->where('kelas_ajar_id', $request->kelas_ajar_id)),
            ],
            'kelas_ajar_id' => ['required', 'exists:kelas_ajar,kelas_ajar_id'],
            'pengampu_user_id' => ['required', 'exists:users,id'],
        ], [
            'nama_pelajaran.unique' => 'Mapel ini sudah ada pada kelas ajar yang dipilih.',
        ]);

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
        $intrakurikuler = Intrakurikuler::findOrFail($id);

        $validated = $request->validate([
            'nama_pelajaran' => [
                'required',
                'string',
                'max:255',
                // unique: (kelas_ajar_id, nama_pelajaran) kecuali record ini
                Rule::unique('intrakurikuler', 'nama_pelajaran')
                    ->where(fn($q) => $q->where('kelas_ajar_id', $request->kelas_ajar_id))
                    ->ignore($intrakurikuler->intrakurikuler_id, 'intrakurikuler_id'),
            ],
            'kelas_ajar_id' => ['required', 'exists:kelas_ajar,kelas_ajar_id'],
            'pengampu_user_id' => ['required', 'exists:users,id'],
        ], [
            'nama_pelajaran.unique' => 'Mapel ini sudah ada pada kelas ajar yang dipilih.',
        ]);

        try {
            DB::transaction(function () use ($intrakurikuler, $validated) {
                $intrakurikuler->update([
                    'nama_pelajaran'   => $validated['nama_pelajaran'],
                    'kelas_ajar_id'    => $validated['kelas_ajar_id'],
                    'pengampu_user_id' => $validated['pengampu_user_id'],
                ]);
            });

            return redirect()
                ->route('intrakurikuler.index')
                ->with('success', 'Intrakurikuler berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('warning', 'Gagal memperbarui intrakurikuler. Coba lagi atau pastikan data tidak konflik.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $intrakurikuler = Intrakurikuler::query()
                    ->withCount([
                        'lingkupMateri',
                        'tujuanPembelajaran',
                        'asesmenFormatif',
                        'asesmenSumatif',
                        'kehadiran',
                    ])
                    ->findOrFail($id);

                $hasChild =
                    ($intrakurikuler->lingkup_materi_count ?? 0) > 0 ||
                    ($intrakurikuler->tujuan_pembelajaran_count ?? 0) > 0 ||
                    ($intrakurikuler->asesmen_formatif_count ?? 0) > 0 ||
                    ($intrakurikuler->asesmen_sumatif_count ?? 0) > 0 ||
                    ($intrakurikuler->kehadiran_count ?? 0) > 0;

                if ($hasChild) {
                    return redirect()
                        ->route('intrakurikuler.index')
                        ->with('warning', 'Tidak bisa menghapus intrakurikuler karena sudah memiliki data turunan (lingkup materi/tujuan/asesmen/kehadiran). Hapus data turunannya dulu.');
                }

                $intrakurikuler->delete();

                return redirect()
                    ->route('intrakurikuler.index')
                    ->with('success', 'Intrakurikuler berhasil dihapus.');
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('intrakurikuler.index')
                ->with('warning', 'Gagal menghapus intrakurikuler. Pastikan tidak ada data yang masih terikat atau coba lagi.');
        }
    }
}
