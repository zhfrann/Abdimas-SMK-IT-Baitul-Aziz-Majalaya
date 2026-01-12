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

    public function create()
    {
        return view('akademik.tahun_ajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tahun_ajaran')->where(function ($query) use ($request) {
                    return $query->where('semester', $request->semester);
                }),
            ],
            'semester' => 'required|in:Ganjil,Genap',
        ], [
            // optional: custom message
            'tahun.unique' => 'Kombinasi tahun dan semester tersebut sudah ada.',
        ]);


        TahunAjaran::create($request->only('tahun', 'semester'));
        return redirect()->route('akademik.tahun_ajaran.index')->with('success', 'Tahun ajaran berhasil ditambah');
    }
}
