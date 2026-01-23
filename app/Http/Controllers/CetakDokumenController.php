<?php

namespace App\Http\Controllers;

use App\Models\KelasAjar;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class CetakDokumenController extends Controller
{
    // Tabel kelas
    public function kelas()
    {
        $kelasList = KelasAjar::query()
            ->join('tahun_ajaran', 'kelas_ajar.tahun_ajaran_id', '=', 'tahun_ajaran.tahun_ajaran_id')
            ->with('kelas', 'tahunAjaran', 'waliKelas')
            ->orderBy('tahun_ajaran.tahun', 'desc')
            ->select('kelas_ajar.*')
            ->get();

        return view('dokumen.kelas', compact('kelasList'));
    }

    // Pilih siswa dan jenis dokumen
    public function pilihCetak($kelas_ajar)
    {
        $kelasAjar = KelasAjar::with(['kelas', 'tahunAjaran', 'waliKelas', 'riwayatKelas.siswa.user'])->findOrFail($kelas_ajar);
        $siswaList = $kelasAjar->riwayatKelas->map(function ($rk) {
            $siswa = $rk->siswa;
            $user = $siswa->user ?? null;
            return [
                'riwayat_kelas_id' => $rk->riwayat_kelas_id,
                'nama' => $user->name ?? $siswa->nama,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'jenis_kelamin' => $siswa->jenis_kelamin,
                'alamat' => $siswa->alamat,
            ];
        });
        return view('dokumen.pilih_cetak', compact('kelasAjar', 'siswaList'));
    }

    // Generate PDF
    public function cetak(Request $request)
    {
        $request->validate([
            'kelas_ajar_id' => 'required|exists:kelas_ajar,kelas_ajar_id',
            'jenis' => 'required|in:sampul,rapor',
            'siswa' => 'array',
            'siswa.*' => 'exists:riwayat_kelas,riwayat_kelas_id',
        ]);

        $kelasAjar = KelasAjar::with(['kelas', 'tahunAjaran', 'waliKelas', 'riwayatKelas.siswa.user'])->findOrFail($request->kelas_ajar_id);

        $jenis = $request->jenis;
        $siswaIds = $request->siswa ?? $kelasAjar->riwayatKelas->pluck('riwayat_kelas_id')->toArray();

        $siswaList = $kelasAjar->riwayatKelas->whereIn('riwayat_kelas_id', $siswaIds);

        // Pilih blade sesuai jenis
        $view = $jenis === 'sampul' ? 'dokumen.pdf_sampul' : 'dokumen.pdf_rapor';

        $namaKelas = $kelasAjar->kelas->nama_kelas;
        $tahunAjaran = $kelasAjar->tahunAjaran->tahun;
        $semester = $kelasAjar->tahunAjaran->semester;

        $pdf = Pdf::view($view, [
            'kelasAjar' => $kelasAjar,
            'siswaList' => $siswaList,
        ])->format('A4')->download('dokumen-' . $jenis . '-' . $namaKelas . ' ' . $tahunAjaran . ' ' . $semester . '.pdf');

        return $pdf;
    }
}
