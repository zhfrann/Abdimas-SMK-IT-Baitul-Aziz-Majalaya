<?php

namespace App\Http\Controllers;

use App\Models\AsesmenFormatif;
use App\Models\AsesmenFormatifDetail;
use App\Models\Intrakurikuler;
use App\Models\RiwayatKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AssesmentFormatifController extends Controller
{
    public function index($intrakurikuler_id)
    {
        $user = Auth::user();
        $intrakurikuler = Intrakurikuler::query()->findOrFail($intrakurikuler_id);
        if (!$user->hasRole('Bagian Akademik') && $intrakurikuler->pengampu_user_id != $user->id) {
            $kelasAjar = $intrakurikuler->kelasAjar->kelas->nama_kelas;
            $tahunAjaran = $intrakurikuler->kelasAjar->tahunAjaran->tahun;
            $semester = $intrakurikuler->kelasAjar->tahunAjaran->semester;
            return back()->with('error', "Anda tidak punya akses untuk melihat Asesmen Formatif di intrakurikuler $intrakurikuler->nama_pelajaran kelas $kelasAjar $tahunAjaran $semester");
        }

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
        $user = Auth::user();
        $intrakurikuler = Intrakurikuler::query()->findOrFail($intrakurikuler_id);
        if (!$user->hasRole('Bagian Akademik') && $intrakurikuler->pengampu_user_id != $user->id) {
            $kelasAjar = $intrakurikuler->kelasAjar->kelas->nama_kelas;
            $tahunAjaran = $intrakurikuler->kelasAjar->tahunAjaran->tahun;
            $semester = $intrakurikuler->kelasAjar->tahunAjaran->semester;
            return back()->with('error', "Anda tidak punya akses untuk melihat Detail Asesmen Formatif di intrakurikuler $intrakurikuler->nama_pelajaran kelas $kelasAjar $tahunAjaran $semester");
        }

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

        DB::beginTransaction();
        try {
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

            DB::commit();
            return redirect()->route('assesment-formatif.index', $intrakurikuler_id)
                ->with('success', 'Data asesmen formatif berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan asesmen formatif.');
        }
    }

    public function importExcel(Request $request, $intrakurikuler_id)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $intrakurikuler = Intrakurikuler::with('tujuanPembelajaran', 'kelasAjar.riwayatKelas.siswa.user')->findOrFail($intrakurikuler_id);
        $tpList = $intrakurikuler->tujuanPembelajaran;
        $tpCount = $tpList->count();

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();

        $filename = $file->getClientOriginalName();
        $expectedFilename = [
            strtolower($intrakurikuler->nama_pelajaran),
            strtolower($intrakurikuler->kelasAjar->kelas->nama_kelas),
            str_replace('/', '-', strtolower($intrakurikuler->kelasAjar->tahunAjaran->tahun)),
            strtolower($intrakurikuler->kelasAjar->tahunAjaran->semester),
        ];

        $filenameLower = strtolower($filename);
        foreach ($expectedFilename as $part) {
            if (strpos($filenameLower, $part) === false) {
                $namaKelas = $intrakurikuler->kelasAjar->kelas->nama_kelas;
                $tahunAjaran = str_replace('/', '-', $intrakurikuler->kelasAjar->tahunAjaran->tahun);
                $semester = $intrakurikuler->kelasAjar->tahunAjaran->semester;
                $namaIntrakurikuler = $intrakurikuler->nama_pelajaran;
                $correctFilename = 'Template Asesmen Formatif ' . "$namaIntrakurikuler $namaKelas $tahunAjaran $semester" . '.xlsx';
                return redirect()->back()->with('error', "Nama file Excel tidak sesuai dengan intrakurikuler yang dipilih. Pastikan Anda mengisi dan mengupload template yang benar ($correctFilename)");
            }
        }

        // Map nama siswa ke riwayat_kelas_id
        $namaToRiwayatKelas = [];
        foreach ($intrakurikuler->kelasAjar->riwayatKelas as $rk) {
            $nama = $rk->siswa->user->name ?? $rk->siswa->nama;
            $namaToRiwayatKelas[trim(strtolower($nama))] = $rk->riwayat_kelas_id;
        }

        DB::beginTransaction();
        try {
            $row = 4;
            while (true) {
                $no = $sheet->getCell("A{$row}")->getValue();
                $namaSiswa = trim((string) $sheet->getCell("B{$row}")->getValue());
                if (!$namaSiswa) break; // stop jika baris kosong

                $riwayat_kelas_id = $namaToRiwayatKelas[strtolower($namaSiswa)] ?? null;
                if (!$riwayat_kelas_id) {
                    $row++;
                    continue; // skip jika tidak ditemukan
                }

                $colTertinggiIndex = 3 + ($tpCount * 2); // C = 3
                $colTerendahIndex  = $colTertinggiIndex + 1;
                $colTertinggi = Coordinate::stringFromColumnIndex($colTertinggiIndex);
                $colTerendah  = Coordinate::stringFromColumnIndex($colTerendahIndex);

                $tertinggi = (string) $sheet->getCell($colTertinggi . $row)->getCalculatedValue();
                $terendah  = (string) $sheet->getCell($colTerendah . $row)->getCalculatedValue();

                $formatif = AsesmenFormatif::query()->updateOrCreate(
                    [
                        'intrakurikuler_id' => $intrakurikuler_id,
                        'riwayat_kelas_id'  => $riwayat_kelas_id,
                    ],
                    [
                        'deskripsi_catatan_tertinggi' => $tertinggi,
                        'deskripsi_catatan_terendah'  => $terendah,
                    ]
                );

                // Simpan detail TP
                $startTPColIndex = 3;
                for ($i = 0; $i < $tpCount; $i++) {
                    $tp = $tpList[$i];

                    // $colKKTP = chr(67 + ($i * 2));
                    // $colTampil = chr(67 + ($i * 2) + 1);
                    $colKKTP  = Coordinate::stringFromColumnIndex($startTPColIndex + ($i * 2));
                    $colTampil = Coordinate::stringFromColumnIndex($startTPColIndex + ($i * 2) + 1);

                    $kktp = $sheet->getCell($colKKTP . $row)->getValue();
                    $tampil = $sheet->getCell($colTampil . $row)->getValue();

                    AsesmenFormatifDetail::updateOrCreate(
                        [
                            'asesmen_formatif_id' => $formatif->asesmen_formatif_id,
                            'tujuan_pembelajaran_id' => $tp->tujuan_pembelajaran_id,
                        ],
                        [
                            'kktp' => $kktp == 1,
                            'tampil' => $tampil == 1,
                        ]
                    );
                }

                $row++;
            }
            DB::commit();
            return redirect()->back()->with('success', 'Data asesmen formatif berhasil diimport dari Excel.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $th->getMessage());
        }
    }
}
