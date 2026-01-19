<?php

namespace App\Http\Controllers;

use App\Models\AsesmenSumatif;
use App\Models\Intrakurikuler;
use App\Models\LingkupMateri;
use App\Models\RiwayatKelas;
use App\Models\SkorAsesmenSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssesmentSumatifController extends Controller
{
    public function index(Intrakurikuler $intrakurikuler)
    {
        $intrakurikuler->load([
            'kelasAjar.kelas',
            'kelasAjar.tahunAjaran',
        ]);

        $kelasAjarId   = $intrakurikuler->kelas_ajar_id;
        $tahunAjaranId = $intrakurikuler->kelasAjar->tahun_ajaran_id;

        // 1) daftar siswa di kelas ajar ini
        $riwayatKelas = RiwayatKelas::query()
            ->where('kelas_ajar_id', $kelasAjarId)
            ->with(['siswa.user'])
            ->get();

        // 2) asesmen untuk intrakurikuler ini (filter tahun ajaran)
        $asesmen = AsesmenSumatif::query()
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->get();

        $riwayatIds = $riwayatKelas->pluck('riwayat_kelas_id');
        $asesmenIds = $asesmen->pluck('asesmen_sumatif_id');

        // skor grouped per siswa
        $skorGrouped = collect();
        if ($riwayatIds->isNotEmpty() && $asesmenIds->isNotEmpty()) {
            $skorGrouped = SkorAsesmenSiswa::query()
                ->whereIn('riwayat_kelas_id', $riwayatIds)
                ->whereIn('asesmen_sumatif_id', $asesmenIds)
                ->get()
                ->groupBy('riwayat_kelas_id');
        }

        $asesmenById = $asesmen->keyBy('asesmen_sumatif_id');

        $rows = $riwayatKelas->map(function ($rk) use ($skorGrouped, $asesmenById) {
            $rkSkor = $skorGrouped->get($rk->riwayat_kelas_id, collect());

            $lingkupVals = [];
            $testVal     = null;
            $nonTestVal  = null;

            foreach ($rkSkor as $s) {
                $as = $asesmenById->get($s->asesmen_sumatif_id);
                if (!$as) continue;

                $nilai = $this->toIntOrNull($s->nilai);
                if ($nilai === null) continue;

                if ($as->asesmen_type === 'sumatif_lingkup') $lingkupVals[] = $nilai;
                if ($as->asesmen_type === 'test') $testVal = $nilai;
                if ($as->asesmen_type === 'non_test') $nonTestVal = $nilai;
            }

            $totalLingkup  = $this->avg($lingkupVals);
            $totalSemester = $this->semesterTotalFromTestNonTest($testVal, $nonTestVal);
            $nilaiRapor    = $this->avg([$totalLingkup, $totalSemester]);

            return [
                'riwayat_kelas_id'       => $rk->riwayat_kelas_id,
                'nama'                   => $rk->siswa?->user?->name ?? $rk->siswa?->nama ?? '-',
                'total_lingkup_materi'   => $totalLingkup,
                'total_akhir_semester'   => $totalSemester,
                'nilai_rapor'            => $nilaiRapor,
            ];
        });

        return view('intrakurikuler.assesment_sumatif.index', compact('intrakurikuler', 'rows'));
    }

    public function detailAssesmentSumatif(Intrakurikuler $intrakurikuler, RiwayatKelas $riwayatKelas)
    {
        // Security: riwayat kelas harus dari kelas ajar yang sama
        abort_unless($riwayatKelas->kelas_ajar_id === $intrakurikuler->kelas_ajar_id, 404);

        $intrakurikuler->load(['kelasAjar.kelas', 'kelasAjar.tahunAjaran']);
        $riwayatKelas->load(['siswa.user']);

        // prev/next siswa (urut nama)
        $all = RiwayatKelas::query()
            ->where('kelas_ajar_id', $intrakurikuler->kelas_ajar_id)
            ->with(['siswa.user'])
            ->get()
            ->sortBy(fn ($rk) => strtolower($rk->siswa?->user?->name ?? $rk->siswa?->nama ?? ''))
            ->values();

        $currentIndex = $all->search(fn ($rk) => $rk->riwayat_kelas_id === $riwayatKelas->riwayat_kelas_id);
        $prevRiwayat  = ($currentIndex !== false && $currentIndex > 0) ? $all[$currentIndex - 1] : null;
        $nextRiwayat  = ($currentIndex !== false && $currentIndex < $all->count() - 1) ? $all[$currentIndex + 1] : null;

        $tahunAjaranId = $intrakurikuler->kelasAjar->tahun_ajaran_id;

        // Pastikan asesmen sumatif_lingkup dibuat berdasarkan LingkupMateri
        // serta pastikan komponen akhir semester (non_test & test) tersedia
        DB::transaction(function () use ($intrakurikuler, $tahunAjaranId) {
            $lingkupMateri = LingkupMateri::query()
                ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
                ->orderBy('lingkup_materi_id')
                ->get();

            $no = 1;
            foreach ($lingkupMateri as $lm) {
                AsesmenSumatif::firstOrCreate(
                    [
                        'intrakurikuler_id'   => $intrakurikuler->intrakurikuler_id,
                        'tahun_ajaran_id'     => $tahunAjaranId,
                        'asesmen_type'        => 'sumatif_lingkup',
                        'lingkup_materi_id'   => $lm->lingkup_materi_id,
                    ],
                    [
                        'asesmen_no' => $no,
                    ]
                );
                $no++;
            }

            AsesmenSumatif::firstOrCreate(
                [
                    'intrakurikuler_id' => $intrakurikuler->intrakurikuler_id,
                    'tahun_ajaran_id'   => $tahunAjaranId,
                    'asesmen_type'      => 'non_test',
                    'lingkup_materi_id' => null,
                ],
                ['asesmen_no' => 1]
            );

            AsesmenSumatif::firstOrCreate(
                [
                    'intrakurikuler_id' => $intrakurikuler->intrakurikuler_id,
                    'tahun_ajaran_id'   => $tahunAjaranId,
                    'asesmen_type'      => 'test',
                    'lingkup_materi_id' => null,
                ],
                ['asesmen_no' => 2]
            );

            // SAS sengaja dihapus (tidak dipakai)
            AsesmenSumatif::query()
                ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->where('asesmen_type', 'sas')
                ->delete();
        });

        // Ambil asesmen yang sudah dipastikan ada
        $asesmen = AsesmenSumatif::query()
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->with('lingkupMateri')
            ->get();

        $asesmenLingkup = $asesmen->where('asesmen_type', 'sumatif_lingkup')
            ->sortBy('asesmen_no')
            ->values();

        $asesmenSemester = $asesmen->whereIn('asesmen_type', ['non_test', 'test'])
            ->sortBy(fn ($a) => $a->asesmen_type === 'non_test' ? 1 : 2)
            ->values();

        // skor siswa
        $skor = SkorAsesmenSiswa::query()
            ->where('riwayat_kelas_id', $riwayatKelas->riwayat_kelas_id)
            ->whereIn('asesmen_sumatif_id', $asesmen->pluck('asesmen_sumatif_id'))
            ->get()
            ->keyBy('asesmen_sumatif_id');

        $hasExistingScores = $skor->isNotEmpty();

        // hitung total lingkup
        $lingkupVals = $asesmenLingkup
            ->map(fn ($a) => isset($skor[$a->asesmen_sumatif_id]) ? $this->toIntOrNull($skor[$a->asesmen_sumatif_id]->nilai) : null)
            ->all();

        $totalLingkup = $this->avg($lingkupVals);

        // hitung total semester (test + non_test; non_test opsional)
        $testAs    = $asesmenSemester->firstWhere('asesmen_type', 'test');
        $nonTestAs = $asesmenSemester->firstWhere('asesmen_type', 'non_test');

        $testVal = ($testAs && isset($skor[$testAs->asesmen_sumatif_id]))
            ? $this->toIntOrNull($skor[$testAs->asesmen_sumatif_id]->nilai)
            : null;

        $nonTestVal = ($nonTestAs && isset($skor[$nonTestAs->asesmen_sumatif_id]))
            ? $this->toIntOrNull($skor[$nonTestAs->asesmen_sumatif_id]->nilai)
            : null;

        $totalSemester = $this->semesterTotalFromTestNonTest($testVal, $nonTestVal);

        // nilai rapor (rata-rata dari totalLingkup & totalSemester jika keduanya ada)
        $nilaiRapor = $this->avg([$totalLingkup, $totalSemester]);

        return view('intrakurikuler.assesment_sumatif.detail_assesment_sumatif', compact(
            'intrakurikuler',
            'riwayatKelas',
            'asesmenLingkup',
            'asesmenSemester',
            'skor',
            'hasExistingScores',
            'totalLingkup',
            'totalSemester',
            'nilaiRapor',
            'prevRiwayat',
            'nextRiwayat'
        ));
    }

    public function storeDetailAssesmentSumatif(Request $request, Intrakurikuler $intrakurikuler, RiwayatKelas $riwayatKelas)
    {
        return $this->upsertDetailScores($request, $intrakurikuler, $riwayatKelas);
    }

    public function updateDetailAssesmentSumatif(Request $request, Intrakurikuler $intrakurikuler, RiwayatKelas $riwayatKelas)
    {
        return $this->upsertDetailScores($request, $intrakurikuler, $riwayatKelas);
    }

    private function upsertDetailScores(Request $request, Intrakurikuler $intrakurikuler, RiwayatKelas $riwayatKelas)
    {
        abort_unless($riwayatKelas->kelas_ajar_id === $intrakurikuler->kelas_ajar_id, 404);

        $intrakurikuler->load(['kelasAjar.tahunAjaran']);
        $tahunAjaranId = $intrakurikuler->kelasAjar->tahun_ajaran_id;

        $data = $request->validate([
            'scores'   => 'required|array',
            'scores.*' => 'nullable|integer|min:0|max:100',
        ]);

        // hanya allow asesmen IDs milik intrakurikuler + tahun ajaran ini
        $allowedAsesmenIds = AsesmenSumatif::query()
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->pluck('asesmen_sumatif_id')
            ->map(fn ($x) => (int) $x)
            ->all();

        $allowedSet = array_flip($allowedAsesmenIds);

        DB::transaction(function () use ($data, $riwayatKelas, $tahunAjaranId, $allowedSet) {
            foreach ($data['scores'] as $asesmenId => $nilai) {
                $asesmenId = (int) $asesmenId;
                if (!isset($allowedSet[$asesmenId])) continue;

                // IMPORTANT: kalau kosong (null / ""), hapus record agar tidak dianggap "ada"
                if ($nilai === null || $nilai === '') {
                    SkorAsesmenSiswa::query()
                        ->where('riwayat_kelas_id', $riwayatKelas->riwayat_kelas_id)
                        ->where('asesmen_sumatif_id', $asesmenId)
                        ->delete();
                    continue;
                }

                SkorAsesmenSiswa::updateOrCreate(
                    [
                        'riwayat_kelas_id'   => $riwayatKelas->riwayat_kelas_id,
                        'asesmen_sumatif_id' => $asesmenId,
                    ],
                    [
                        'nilai'          => (int) $nilai,
                        'tahun_ajaran_id'=> $tahunAjaranId,
                    ]
                );
            }
        });

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    /**
     * Rata-rata untuk array nilai yang boleh berisi null.
     * - kalau semua null => null
     * - kalau hanya 1 nilai => nilai itu
     * - kalau >1 => rata-rata
     */
    private function avg(array $vals): ?int
    {
        $vals = array_values(array_filter($vals, fn ($v) => $v !== null));
        if (count($vals) === 0) return null;
        return (int) round(array_sum($vals) / count($vals));
    }

    /**
     * Anggap valid jika:
     * - 0 itu valid
     * - "" / null itu dianggap null
     */
    private function toIntOrNull($val): ?int
    {
        if ($val === '' || $val === null) return null;
        return (int) $val;
    }

    /**
     * Total akhir semester:
     * - kalau test & non_test ada => rata-rata
     * - kalau hanya salah satu => pakai yang ada (TIDAK dibagi 2)
     * - kalau tidak ada => null
     */
    private function semesterTotalFromTestNonTest(?int $testVal, ?int $nonTestVal): ?int
    {
        $vals = array_filter([$testVal, $nonTestVal], fn ($v) => $v !== null);

        if (count($vals) === 2) {
            return (int) round(array_sum($vals) / 2);
        }
        if (count($vals) === 1) {
            return (int) array_values($vals)[0];
        }
        return null;
    }
}
