<?php

namespace App\Http\Controllers;

use App\Models\Intrakurikuler;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TujuanPembelajaranController extends Controller
{
    public function index($intrakurikuler_id)
    {
        $intrakurikuler = Intrakurikuler::with('kelasAjar.kelas')->findOrFail($intrakurikuler_id);
        $tujuanPembelajaran = TujuanPembelajaran::where('intrakurikuler_id', $intrakurikuler_id)->get();

        return view('intrakurikuler.table_tujuan_pembelajaran', compact('intrakurikuler', 'tujuanPembelajaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request, $intrakurikuler_id)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
        ]);

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

    public function update(Request $request, $intrakurikuler_id, $id)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:255',
        ]);

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

    public function destroy($intrakurikuler_id, $id)
    {
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
