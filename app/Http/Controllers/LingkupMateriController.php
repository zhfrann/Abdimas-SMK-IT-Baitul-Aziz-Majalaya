<?php

namespace App\Http\Controllers;

use App\Models\Intrakurikuler;
use App\Models\LingkupMateri;
use Illuminate\Http\Request;

class LingkupMateriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Intrakurikuler $intrakurikuler)
    {
        $lingkupMateri = LingkupMateri::where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->orderByDesc('lingkup_materi_id')
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

        // optional: cegah duplikat nama materi di intrakurikuler yang sama
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
        // pastikan data lingkup materi memang milik intrakurikuler yg sedang dibuka
        if ((int) $lingkupMateri->intrakurikuler_id !== (int) $intrakurikuler->intrakurikuler_id) {
            abort(404);
        }

        $validated = $request->validate([
            'nama_materi' => ['required', 'string', 'max:255'],
        ]);

        // optional: cegah duplikat nama materi di intrakurikuler yang sama (kecuali record ini sendiri)
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
    public function destroy(string $id)
    {
        //
    }
}
