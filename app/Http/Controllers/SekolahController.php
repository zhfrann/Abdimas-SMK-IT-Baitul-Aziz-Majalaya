<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolah = DB::table('sekolah as sc')
            ->leftJoin('kelurahan as kel', 'kel.kelurahan_id', '=', 'sc.kelurahan_id')
            ->select([
                'sc.*',
                'kel.nama as kelurahan_nama',
            ])
            ->first();

        return view('akademik.sekolah.index', compact('sekolah'));
    }

    public function edit()
    {
        $sekolah = DB::table('sekolah')->first();

        $kelurahanLabel = null;
        if ($sekolah && $sekolah->kelurahan_id) {
            $kelurahanLabel = DB::table('kelurahan')
                ->where('kelurahan_id', $sekolah->kelurahan_id)
                ->value('nama');
        }

        return view('akademik.sekolah.edit', [
            'sekolah' => $sekolah,
            'kelurahanLabel' => $kelurahanLabel,
        ]);
    }


    public function update(Request $request)
    {
        $sekolah = DB::table('sekolah')->first();
        if (!$sekolah) {
            return redirect()->route('akademik.sekolah.index')
                ->with('error', 'Data sekolah belum tersedia.');
        }

        $data = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'nss' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'kelurahan_id' => ['nullable', 'string', Rule::exists('kelurahan', 'kelurahan_id')],
            'website' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telp' => ['nullable', 'string', 'max:50'],
            'nama_kepala_sekolah' => ['nullable', 'string', 'max:255'],
            'nuptk_kepala_sekolah' => ['nullable', 'string', 'max:255'],
        ]);

        DB::table('sekolah')
            ->where('npsn', $sekolah->npsn)
            ->update(array_merge($data, [
                'updated_at' => now(),
            ]));

        return redirect()->route('akademik.sekolah.index')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }
}
