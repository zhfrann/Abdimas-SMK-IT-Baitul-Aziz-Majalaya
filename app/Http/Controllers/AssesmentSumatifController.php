<?php

namespace App\Http\Controllers;

use App\Models\AsesmenSumatif;
use App\Models\Intrakurikuler;
use App\Models\RiwayatKelas;
use App\Models\SkorAsesmenSiswa;
use Illuminate\Http\Request;

class AssesmentSumatifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Intrakurikuler $intrakurikuler)
    {
        $intrakurikuler->load([
            'kelasAjar.kelas',
            'kelasAjar.tahunAjaran',
        ]);

        $kelasAjarId = $intrakurikuler->kelas_ajar_id;

        // 1) daftar siswa di kelas ajar ini
        $riwayatKelas = RiwayatKelas::query()
            ->where('kelas_ajar_id', $kelasAjarId)
            ->with(['siswa.user'])
            ->get();

        // 2) asesmen untuk intrakurikuler ini
        $asesmen = AsesmenSumatif::query()
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->get();

        $riwayatIds = $riwayatKelas->pluck('riwayat_kelas_id');
        $asesmenIds = $asesmen->pluck('asesmen_sumatif_id');

        // kalau tidak ada siswa atau tidak ada asesmen, jangan query skor besar-besaran
        $skorGrouped = collect();
        if ($riwayatIds->isNotEmpty() && $asesmenIds->isNotEmpty()) {
            $skorGrouped = SkorAsesmenSiswa::query()
                ->whereIn('riwayat_kelas_id', $riwayatIds)
                ->whereIn('asesmen_sumatif_id', $asesmenIds)
                ->get()
                ->groupBy('riwayat_kelas_id');
        }

        $asesmenById = $asesmen->keyBy('asesmen_sumatif_id');

        $avg = function ($vals) {
            $vals = array_values(array_filter($vals, fn($v) => $v !== null));
            if (count($vals) === 0) return null;
            return (int) round(array_sum($vals) / count($vals));
        };

        $rows = $riwayatKelas->map(function ($rk) use ($skorGrouped, $asesmenById, $avg) {
            $rkSkor = $skorGrouped->get($rk->riwayat_kelas_id, collect());

            $lingkup = [];
            $sas = [];

            foreach ($rkSkor as $s) {
                $as = $asesmenById->get($s->asesmen_sumatif_id);
                if (!$as) continue;

                $nilai = is_null($s->nilai) ? null : (int) $s->nilai;
                if ($nilai === null) continue;

                if ($as->asesmen_type === 'sumatif_lingkup') $lingkup[] = $nilai;
                if ($as->asesmen_type === 'sas') $sas[] = $nilai;
            }

            $totalLingkup = $avg($lingkup);
            $totalSas = $avg($sas);

            $rapor = $avg(array_filter([$totalLingkup, $totalSas], fn($v) => $v !== null));

            return [
                'riwayat_kelas_id' => $rk->riwayat_kelas_id,
                'nama' => $rk->siswa?->user?->name ?? $rk->siswa?->nama ?? '-',
                'total_lingkup_materi' => $totalLingkup,
                'total_akhir_semester' => $totalSas,
                'nilai_rapor' => $rapor,
            ];
        });

        return view('intrakurikuler.assesment_sumatif.index', compact('intrakurikuler', 'rows'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

    public function detailAssesmentSumatif()
    {
        return view('intrakurikuler.detail_assesment_sumatif');
    }
}
