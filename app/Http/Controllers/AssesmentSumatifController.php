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
        $tahunAjaranId = $intrakurikuler->kelasAjar?->tahun_ajaran_id;

        // daftar siswa di kelas ajar ini
        $riwayatKelas = RiwayatKelas::query()
            ->where('kelas_ajar_id', $kelasAjarId)
            ->with(['siswa.user']) // asumsi relasi ada
            ->get();

        // semua asesmen sumatif utk intrakurikuler ini
        $asesmen = AsesmenSumatif::query()
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->get();

        $asesmenIds = $asesmen->pluck('asesmen_sumatif_id')->all();
        $riwayatIds = $riwayatKelas->pluck('riwayat_kelas_id')->all();

        $skorGrouped = SkorAsesmenSiswa::query()
            ->when(count($riwayatIds) > 0, fn($q) => $q->whereIn('riwayat_kelas_id', $riwayatIds))
            ->when(count($asesmenIds) > 0, fn($q) => $q->whereIn('asesmen_sumatif_id', $asesmenIds))
            ->get()
            ->groupBy('riwayat_kelas_id');

        $asesmenById = $asesmen->keyBy('asesmen_sumatif_id');

        $avg = function (array $vals) {
            if (count($vals) === 0) return null;
            return (int) round(array_sum($vals) / count($vals));
        };

        $rows = $riwayatKelas->map(function ($rk) use ($skorGrouped, $asesmenById, $avg) {
            $rkSkor = $skorGrouped->get($rk->riwayat_kelas_id, collect());

            $lingkup = [];
            $sas = [];

            foreach ($rkSkor as $s) {
                $as = $asesmenById->get($s->asesmen_sumatif_id);
                if (!$as || $s->nilai === null) continue;

                if ($as->asesmen_type === 'sumatif_lingkup') $lingkup[] = (int) $s->nilai;
                if ($as->asesmen_type === 'sas') $sas[] = (int) $s->nilai;
            }

            $totalLingkup = $avg($lingkup);
            $totalSas = $avg($sas);

            $rapor = null;
            $parts = array_filter([$totalLingkup, $totalSas], fn($v) => $v !== null);
            if (count($parts) > 0) $rapor = (int) round(array_sum($parts) / count($parts));

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
