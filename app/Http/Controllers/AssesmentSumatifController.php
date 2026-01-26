<?php

namespace App\Http\Controllers;

use App\Models\AsesmenSumatif;
use App\Models\Intrakurikuler;
use App\Models\LingkupMateri;
use App\Models\RiwayatKelas;
use App\Models\SkorAsesmenSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            ->sortBy(
                fn($rk) => trim($rk->siswa?->user?->name ?? $rk->siswa?->nama ?? ''),
                SORT_NATURAL | SORT_FLAG_CASE
            )
            ->values();


        $currentIndex = $all->search(fn($rk) => $rk->riwayat_kelas_id === $riwayatKelas->riwayat_kelas_id);
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
            ->sortBy(fn($a) => $a->asesmen_type === 'non_test' ? 1 : 2)
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
            ->map(fn($a) => isset($skor[$a->asesmen_sumatif_id]) ? $this->toIntOrNull($skor[$a->asesmen_sumatif_id]->nilai) : null)
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
            ->map(fn($x) => (int) $x)
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
                        'tahun_ajaran_id' => $tahunAjaranId,
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
        $vals = array_values(array_filter($vals, fn($v) => $v !== null));
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
        $vals = array_filter([$testVal, $nonTestVal], fn($v) => $v !== null);

        if (count($vals) === 2) {
            return (int) round(array_sum($vals) / 2);
        }
        if (count($vals) === 1) {
            return (int) array_values($vals)[0];
        }
        return null;
    }

    public function importExcelSumatif(Request $request, Intrakurikuler $intrakurikuler)
    {
        $request->validate([
            'excel' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $intrakurikuler->load(['kelasAjar.tahunAjaran']);

        $kelasAjarId   = $intrakurikuler->kelas_ajar_id;
        $tahunAjaranId = $intrakurikuler->kelasAjar->tahun_ajaran_id;

        // === Ambil semua riwayat kelas (untuk matching nama) ===
        $riwayatKelas = RiwayatKelas::query()
            ->where('kelas_ajar_id', $kelasAjarId)
            ->with(['siswa.user'])
            ->get();

        // Map nama (lowercase+trim) -> riwayat_kelas_id
        $namaToRiwayat = $riwayatKelas->mapWithKeys(function ($rk) {
            $nama = $rk->siswa?->user?->name ?? $rk->siswa?->nama ?? '';
            $key = Str::of($nama)->lower()->trim()->__toString();
            return [$key => $rk->riwayat_kelas_id];
        });

        // === Pastikan asesmen tersedia (lingkup + non_test + test) ===
        // (Kalau kamu udah selalu create di detail, ini tetap aman.)
        DB::transaction(function () use ($intrakurikuler, $tahunAjaranId) {
            $lingkupMateri = LingkupMateri::query()
                ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
                ->orderBy('lingkup_materi_id')
                ->get();

            $no = 1;
            foreach ($lingkupMateri as $lm) {
                AsesmenSumatif::firstOrCreate(
                    [
                        'intrakurikuler_id' => $intrakurikuler->intrakurikuler_id,
                        'tahun_ajaran_id'   => $tahunAjaranId,
                        'asesmen_type'      => 'sumatif_lingkup',
                        'lingkup_materi_id' => $lm->lingkup_materi_id,
                    ],
                    ['asesmen_no' => $no]
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
        });

        $asesmenLingkup = AsesmenSumatif::query()
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('asesmen_type', 'sumatif_lingkup')
            ->orderBy('asesmen_no')
            ->get();

        $nonTestAsesmen = AsesmenSumatif::query()
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('asesmen_type', 'non_test')
            ->first();

        $testAsesmen = AsesmenSumatif::query()
            ->where('intrakurikuler_id', $intrakurikuler->intrakurikuler_id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('asesmen_type', 'test')
            ->first();

        // === Baca Excel ===
        $filePath = $request->file('excel')->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheet(0); // sheet pertama

        // Template kamu: header 1-3, data mulai row 4
        $startRow = 4;

        // Kolom dinamis:
        // A: No, B: Nama, C..(C+sumatifCount-1): Sumatif 1..n
        // setelah itu: NA Lingkup, NonTes, Tes, NA AkhirSem, Nilai Rapor
        $sumatifCount = $asesmenLingkup->count(); // harus sama dengan jumlah kolom sumatif

        // kalau template ngikut LingkupMateri tapi asesmenLingkup kosong:
        if ($sumatifCount === 0) {
            return back()->with('error', 'Belum ada lingkup materi/asesmen sumatif lingkup untuk mapel ini.');
        }

        $firstSumatifColIndex = 3; // C
        $lastSumatifColIndex  = $firstSumatifColIndex + $sumatifCount - 1;

        $colNaLingkupIndex  = $lastSumatifColIndex + 1;
        $colNonTesIndex     = $colNaLingkupIndex + 1;
        $colTesIndex        = $colNaLingkupIndex + 2;

        // helper baca cell jadi int/null
        $toIntOrNull = function ($v) {
            if ($v === null) return null;
            if (is_string($v)) {
                $v = trim($v);
                if ($v === '' || $v === '-') return null;
            }
            if (!is_numeric($v)) return null;
            $n = (int) round((float) $v);
            if ($n < 0 || $n > 100) return null;
            return $n;
        };

        DB::transaction(function () use (
            $sheet,
            $startRow,
            $namaToRiwayat,
            $asesmenLingkup,
            $nonTestAsesmen,
            $testAsesmen,
            $firstSumatifColIndex,
            $sumatifCount,
            $colNonTesIndex,
            $colTesIndex,
            $tahunAjaranId,
            $toIntOrNull
        ) {
            $row = $startRow;

            while (true) {
                // Nama siswa di kolom B (index 2)
                $nama = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                $namaKey = Str::of((string)$nama)->lower()->trim()->__toString();

                // stop kalau nama kosong (anggap akhir data)
                if ($namaKey === '') break;

                // cari riwayat_kelas_id
                $riwayatId = $namaToRiwayat[$namaKey] ?? null;
                if (!$riwayatId) {
                    // kalau nama ga ketemu: skip row
                    $row++;
                    continue;
                }

                // 1) Sumatif lingkup 1..n (C..)
                for ($i = 0; $i < $sumatifCount; $i++) {
                    $asesmenId = $asesmenLingkup[$i]->asesmen_sumatif_id;
                    $colIndex  = $firstSumatifColIndex + $i;

                    $val = $sheet->getCellByColumnAndRow($colIndex, $row)->getCalculatedValue();
                    $nilai = $toIntOrNull($val);

                    if ($nilai === null) {
                        SkorAsesmenSiswa::query()
                            ->where('riwayat_kelas_id', $riwayatId)
                            ->where('asesmen_sumatif_id', $asesmenId)
                            ->delete();
                    } else {
                        SkorAsesmenSiswa::updateOrCreate(
                            [
                                'riwayat_kelas_id'   => $riwayatId,
                                'asesmen_sumatif_id' => $asesmenId,
                            ],
                            [
                                'nilai'          => $nilai,
                                'tahun_ajaran_id' => $tahunAjaranId,
                            ]
                        );
                    }
                }

                // 2) Non Tes
                if ($nonTestAsesmen) {
                    $val = $sheet->getCellByColumnAndRow($colNonTesIndex, $row)->getCalculatedValue();
                    $nilai = $toIntOrNull($val);

                    if ($nilai === null) {
                        SkorAsesmenSiswa::query()
                            ->where('riwayat_kelas_id', $riwayatId)
                            ->where('asesmen_sumatif_id', $nonTestAsesmen->asesmen_sumatif_id)
                            ->delete();
                    } else {
                        SkorAsesmenSiswa::updateOrCreate(
                            [
                                'riwayat_kelas_id'   => $riwayatId,
                                'asesmen_sumatif_id' => $nonTestAsesmen->asesmen_sumatif_id,
                            ],
                            [
                                'nilai'          => $nilai,
                                'tahun_ajaran_id' => $tahunAjaranId,
                            ]
                        );
                    }
                }

                // 3) Tes
                if ($testAsesmen) {
                    $val = $sheet->getCellByColumnAndRow($colTesIndex, $row)->getCalculatedValue();
                    $nilai = $toIntOrNull($val);

                    if ($nilai === null) {
                        SkorAsesmenSiswa::query()
                            ->where('riwayat_kelas_id', $riwayatId)
                            ->where('asesmen_sumatif_id', $testAsesmen->asesmen_sumatif_id)
                            ->delete();
                    } else {
                        SkorAsesmenSiswa::updateOrCreate(
                            [
                                'riwayat_kelas_id'   => $riwayatId,
                                'asesmen_sumatif_id' => $testAsesmen->asesmen_sumatif_id,
                            ],
                            [
                                'nilai'          => $nilai,
                                'tahun_ajaran_id' => $tahunAjaranId,
                            ]
                        );
                    }
                }

                $row++;
            }
        });

        return back()->with('success', 'Import Excel Sumatif berhasil. Nilai sudah tersimpan ke database.');
    }
}
