<?php

namespace App\Http\Controllers;

use App\Models\AsesmenFormatif;
use App\Models\AsesmenFormatifDetail;
use App\Models\Intrakurikuler;
use App\Models\RiwayatKelas;
use Illuminate\Http\Request;

class AssesmentFormatifController extends Controller
{
    public function index($intrakurikuler_id)
    {
        $intrakurikuler = Intrakurikuler::with([
            'kelasAjar.kelas',
            'kelasAjar.tahunAjaran',
            'kelasAjar.riwayatKelas.siswa.user',
            'tujuanPembelajaran',
        ])->findOrFail($intrakurikuler_id);

        $tpList = $intrakurikuler->tujuanPembelajaran;
        $tpCount = $tpList->count();

        // Ambil semua asesmen formatif untuk intrakurikuler ini, index by riwayat_kelas_id
        $formatifList = AsesmenFormatif::where('intrakurikuler_id', $intrakurikuler_id)
            ->get()
            ->keyBy('riwayat_kelas_id');

        $rows = [];
        foreach ($intrakurikuler->kelasAjar->riwayatKelas as $rk) {
            $siswa = $rk->siswa;
            $user = $siswa->user ?? null;

            // Ambil formatif per siswa
            $formatif = $formatifList->get($rk->riwayat_kelas_id);

            // Ambil details per siswa
            $details = $formatif
                ? $formatif->details()->whereHas('tujuanPembelajaran', function ($q) use ($intrakurikuler_id) {
                    $q->where('intrakurikuler_id', $intrakurikuler_id);
                })->get()
                : collect();

            $tp_tercapai = $details->where('kktp', true)->count();
            $tp_tidak_tercapai = $tpCount - $tp_tercapai;

            $capaian_tertinggi = $formatif->deskripsi_catatan_tertinggi ?? '-';
            $capaian_terendah = $formatif->deskripsi_catatan_terendah ?? '-';

            $rows[] = [
                'riwayat_kelas_id' => $rk->riwayat_kelas_id,
                'siswa_id' => $siswa->siswa_id,
                'nama' => $user->name ?? $siswa->nama,
                'tp_tercapai' => $tp_tercapai,
                'tp_tidak_tercapai' => $tp_tidak_tercapai,
                'tp_total' => $tpCount,
                'capaian_tertinggi' => $capaian_tertinggi,
                'capaian_terendah' => $capaian_terendah,
            ];
        }

        return view('intrakurikuler.assesment_formatif', compact('intrakurikuler', 'rows', 'tpList'));
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

    public function detailAssesmentFormatif($intrakurikuler_id, $riwayat_kelas_id)
    {
        $riwayatKelas = RiwayatKelas::with('siswa.user', 'kelasAjar.kelas', 'kelasAjar.tahunAjaran')->findOrFail($riwayat_kelas_id);
        $intrakurikuler = Intrakurikuler::with('tujuanPembelajaran')->findOrFail($intrakurikuler_id);

        $formatif = AsesmenFormatif::where('riwayat_kelas_id', $riwayat_kelas_id)
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->first();

        $details = $formatif
            ? $formatif->details()->get()
            : collect();

        // Ambil semua riwayat_kelas di kelas ajar ini, urutkan by riwayat_kelas_id || nama
        $riwayatKelasList = $intrakurikuler->kelasAjar->riwayatKelas->sortBy(function ($rk) {
            return $rk->riwayat_kelas_id ?? $rk->siswa->user->name ?? $rk->siswa->nama;
        })->values();

        // Cari index current
        $currentIndex = $riwayatKelasList->search(function ($rk) use ($riwayat_kelas_id) {
            return $rk->riwayat_kelas_id == $riwayat_kelas_id;
        });

        $prev = $currentIndex > 0 ? $riwayatKelasList[$currentIndex - 1] : null;
        $next = $currentIndex < $riwayatKelasList->count() - 1 ? $riwayatKelasList[$currentIndex + 1] : null;

        return view('intrakurikuler.detail_assesment_formatif', [
            'intrakurikuler' => $intrakurikuler,
            'riwayatKelas' => $riwayatKelas,
            'formatif' => $formatif,
            'details' => $details,
            'prevSiswa' => $prev,
            'nextSiswa' => $next,
        ]);
    }

    public function saveDetail(Request $request, $intrakurikuler_id, $riwayat_kelas_id)
    {
        $formatif = AsesmenFormatif::query()->firstOrCreate([
            'intrakurikuler_id' => $intrakurikuler_id,
            'riwayat_kelas_id' => $riwayat_kelas_id,
        ], [
            'deskripsi_catatan_tertinggi' => $request->capaian_tertinggi ?? '',
            'deskripsi_catatan_terendah' => $request->capaian_terendah ?? '',
        ]);

        $formatif->deskripsi_catatan_tertinggi = $request->capaian_tertinggi;
        $formatif->deskripsi_catatan_terendah = $request->capaian_terendah;
        $formatif->save();

        $tpData = $request->input('tp', []);
        $intrakurikuler = Intrakurikuler::with('tujuanPembelajaran')->findOrFail($intrakurikuler_id);

        foreach ($intrakurikuler->tujuanPembelajaran as $tp) {
            $tp_id = $tp->tujuan_pembelajaran_id;
            $data = $tpData[$tp_id] ?? [];
            $kktp = isset($data['tercapai']) && $data['tercapai'] == 1;
            $tampil = isset($data['tampil_rapor']) && $data['tampil_rapor'] == 1;

            AsesmenFormatifDetail::updateOrCreate(
                [
                    'asesmen_formatif_id' => $formatif->asesmen_formatif_id,
                    'tujuan_pembelajaran_id' => $tp_id,
                ],
                [
                    'kktp' => $kktp,
                    'tampil' => $tampil,
                ]
            );
        }

        return redirect()->route('assesment-formatif.index', $intrakurikuler_id)
            ->with('success', 'Data asesmen formatif berhasil disimpan.');
    }
}
