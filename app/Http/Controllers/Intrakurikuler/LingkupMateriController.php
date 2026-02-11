<?php

namespace App\Http\Controllers\Intrakurikuler;

use App\Http\Controllers\Controller;
use App\Models\Intrakurikuler;
use App\Models\LingkupMateri;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LingkupMateriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Intrakurikuler $intrakurikuler)
    {
        $lingkupMateri = LingkupMateri::where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->get();

        return view('intrakurikuler.lingkup_materi.index', compact('intrakurikuler', 'lingkupMateri'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request, Intrakurikuler $intrakurikuler)
    {
        $validated = $request->validate([
            'nama_materi' => ['required', 'string', 'max:255'],
        ]);


        $exists = LingkupMateri::where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->where('nama_materi', $validated['nama_materi'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['nama_materi' => 'Nama materi ini sudah ada pada intrakurikuler ini.'])
                ->withInput();
        }

        LingkupMateri::create([
            'intrakurikuler_id' => $intrakurikuler->intrakurikuler_id,
            'nama_materi' => $validated['nama_materi'],
        ]);

        return redirect()
            ->route('lingkup-materi.index', $intrakurikuler->intrakurikuler_id)
            ->with('success', 'Lingkup materi berhasil ditambahkan.');
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
    public function update(Request $request, Intrakurikuler $intrakurikuler, LingkupMateri $lingkupMateri)
    {
        $user = Auth::user();
        // pastikan data lingkup materi memang milik intrakurikuler yg sedang dibuka
        if ((int) $lingkupMateri->intrakurikuler_id !== (int) $intrakurikuler->intrakurikuler_id) {
            abort(404);
        }

        if (!$user->hasRole('Bagian Akademik') && !((int) $intrakurikuler->pengampu_user_id === (int) $user->id)) {
            abort(403); // atau 404 kalau mau “nggak bocorin”
            // abort(404);
        }

        $validated = $request->validate([
            'nama_materi' => ['required', 'string', 'max:255'],
        ]);

        $exists = LingkupMateri::where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->where('nama_materi', $validated['nama_materi'])
            ->where('lingkup_materi_id', '!=', $lingkupMateri->lingkup_materi_id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['nama_materi' => 'Nama materi ini sudah ada pada intrakurikuler ini.'])
                ->withInput();
        }

        $lingkupMateri->update([
            'nama_materi' => $validated['nama_materi'],
        ]);

        return redirect()
            ->route('lingkup-materi.index', $intrakurikuler->intrakurikuler_id)
            ->with('success', 'Lingkup materi berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $intrakurikuler, string $lingkup_materi)
    {
        $user = Auth::user();

        try {
            return DB::transaction(function () use ($user, $intrakurikuler, $lingkup_materi) {

                $lm = LingkupMateri::query()
                    ->where('lingkup_materi_id', $lingkup_materi)
                    ->where('intrakurikuler_id', $intrakurikuler)
                    ->firstOrFail();

                $intra = Intrakurikuler::query()
                    ->where('intrakurikuler_id', $intrakurikuler)
                    ->firstOrFail();


                if (! $user->hasRole('Bagian Akademik') && ! ((int) $intra->pengampu_user_id === (int) $user->id)) {
                    abort(403);
                }

                $lm->delete();

                return back()->with('success', 'Lingkup Materi berhasil dihapus.');
            });
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;
            $mysqlCode = $e->errorInfo[1] ?? null;

            if ($sqlState === '23000' && (int)$mysqlCode === 1451) {
                return back()->with(
                    'warning',
                    'Tidak bisa menghapus Lingkup Materi karena masih terhubung dengan data lain.'
                );
            }

            return back()->with('warning', 'Gagal menghapus Lingkup Materi. Coba lagi.');
        } catch (\Throwable $e) {
            return back()->with('warning', 'Gagal menghapus Lingkup Materi. Coba lagi.');
        }
    }
}
