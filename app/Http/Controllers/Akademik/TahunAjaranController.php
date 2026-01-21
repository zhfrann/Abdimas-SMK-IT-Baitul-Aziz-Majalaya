<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::orderByDesc('tahun_ajaran_id')->get();
        return view('akademik.tahun_ajaran.index', compact('tahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => [
                'bail',
                'required',
                'string',
                'max:20',
                Rule::unique('tahun_ajaran')->where(function ($query) use ($request) {
                    return $query->where('semester', $request->semester);
                }),
                'regex:/^\d{4}\/\d{4}$/',  //format YYYY/YYYY
                // validasi logika tahun +1
                function ($attribute, $value, $fail) {
                    [$start, $end] = explode('/', $value);
                    if ((int)$end !== ((int)$start + 1)) {
                        $fail('Tahun ajaran harus berurutan, contoh: 2025/2026');
                    }
                },
            ],
            'semester' => 'required|in:Ganjil,Genap',
        ], [
            // optional: custom message
            'tahun.regex' => 'Format tahun ajaran harus seperti 2025/2026.',
            'tahun.unique' => 'Kombinasi tahun dan semester tersebut sudah ada.',
        ]);


        try {
            TahunAjaran::create($request->only('tahun', 'semester'));
            return redirect()->route('akademik.tahun_ajaran.index')->with('success', 'Tahun ajaran berhasil ditambah');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['tahun' => 'Terjadi kesalahan. Gagal menambah tahun ajaran']);
        }
    }

    public function edit($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        return response()->json($tahunAjaran);
    }

    public function update(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $request->validate([
            'tahun' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tahun_ajaran')->where(function ($query) use ($request, $id) {
                    return $query->where('semester', $request->semester)
                        ->where('tahun_ajaran_id', '!=', $id);
                }),
            ],
            'semester' => 'required|in:Ganjil,Genap',
        ], [
            'tahun.unique' => 'Kombinasi tahun dan semester tersebut sudah ada.',
        ]);
        try {
            $tahunAjaran->update($request->only('tahun', 'semester'));
            return redirect()->route('akademik.tahun_ajaran.index')->with('success', 'Tahun ajaran berhasil diupdate');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['tahun' => 'Terjadi kesalahan. Gagal update tahun ajaran.']);
        }
    }

    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        try {
            $tahunAjaran->delete();
            return redirect()->route('akademik.tahun_ajaran.index')->with('success', 'Tahun ajaran berhasil dihapus');
        } catch (\Throwable $e) {
            return back()->with('error', 'Tidak dapat menghapus tahun ajaran. Pastikan tidak ada data terkait dengan data yang ingin dihapus.');
        }
    }
}
