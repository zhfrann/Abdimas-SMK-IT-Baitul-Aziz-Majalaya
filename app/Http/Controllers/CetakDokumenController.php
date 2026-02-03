<?php

namespace App\Http\Controllers;

use App\Models\KelasAjar;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelPdf\Facades\Pdf;

class CetakDokumenController extends Controller
{
    // Tabel kelas
    public function kelas()
    {
        $user = Auth::user();
        $userId = Auth::id();

        if ($user->hasRole('Wali Kelas')) {
            $kelasList = KelasAjar::query()
                ->where('wali_user_id', $userId)
                ->join('tahun_ajaran', 'kelas_ajar.tahun_ajaran_id', '=', 'tahun_ajaran.tahun_ajaran_id')
                ->select('kelas_ajar.*')
                ->with('kelas', 'tahunAjaran', 'waliKelas')
                ->withCount('riwayatKelas')
                ->orderBy('tahun_ajaran.tahun', 'desc')
                ->get();
        }
        if ($user->hasRole('Bagian Akademik')) {
            $kelasList = KelasAjar::query()
                ->join('tahun_ajaran', 'kelas_ajar.tahun_ajaran_id', '=', 'tahun_ajaran.tahun_ajaran_id')
                ->select('kelas_ajar.*')
                ->with('kelas', 'tahunAjaran', 'waliKelas')
                ->withCount('riwayatKelas')
                ->orderBy('tahun_ajaran.tahun', 'desc')
                ->get();
        }

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
                'alamat' => $siswa->alamat . ' Kel. ' . $siswa->kelurahan->nama . ' Kec. ' . $siswa->kelurahan->kecamatan->nama . $siswa->kelurahan->kecamatan->kabupaten->nama,
            ];
        });

        return view('dokumen.pilih_cetak', compact('kelasAjar', 'siswaList'));
    }

    // Generate PDF
    public function cetakSampul(Request $request)
    {
        $request->validate([
            'kelas_ajar_id' => 'required|exists:kelas_ajar,kelas_ajar_id',
            'jenis' => 'required|in:sampul,rapor',
            'siswa' => 'array',
            'siswa.*' => 'exists:riwayat_kelas,riwayat_kelas_id',
        ]);

        $sekolah = Sekolah::firstOrFail();
        $kelasAjar = KelasAjar::with(['kelas', 'tahunAjaran', 'waliKelas', 'riwayatKelas.siswa.user', 'riwayatKelas.siswa.kelurahan.kecamatan.kabupaten'])->findOrFail($request->kelas_ajar_id);

        $siswaIds = $request->siswa ?? $kelasAjar->riwayatKelas->pluck('riwayat_kelas_id')->toArray();
        $siswaList = $kelasAjar->riwayatKelas->whereIn('riwayat_kelas_id', $siswaIds);

        $namaKelas = $kelasAjar->kelas->nama_kelas;
        $tahunAjaran = $kelasAjar->tahunAjaran->tahun;
        $semester = $kelasAjar->tahunAjaran->semester;

        $pdf = Pdf::view('dokumen.pdf_sampul', [
            'kelasAjar' => $kelasAjar,
            'siswaList' => $siswaList,
            'sekolah' => $sekolah,
            // ])->format('A4')->download('Sampul Rapor ' . $namaKelas . ' ' . $tahunAjaran . ' ' . $semester . '.pdf');
        ])->format('A4');

        return $pdf;
    }


    // Cetak Rapor
    public function cetakRapor(Request $request)
    {
        $request->validate([
            'kelas_ajar_id' => 'required|exists:kelas_ajar,kelas_ajar_id',
            'siswa' => 'array',
            'siswa.*' => 'exists:riwayat_kelas,riwayat_kelas_id',
        ]);

        $sekolah = Sekolah::with('kelurahan.kecamatan.kabupaten')->first();
        $kelasAjar = KelasAjar::with([
            'kelas',
            'tahunAjaran',
            'waliKelas',
            'intrakurikuler.pengampu',
            'intrakurikuler.tujuanPembelajaran',
            'intrakurikuler.asesmenFormatif.details.tujuanPembelajaran',
            'intrakurikuler.asesmenSumatif.skorSiswa',
            'riwayatKelas.siswa.user',
            'riwayatKelas.siswa.siswaEkstrakurikuler.ekstrakurikuler.pembina',
            'riwayatKelas.siswa.siswaEkstrakurikuler.penilaians',
            'riwayatKelas.kehadiranIntrakurikuler',
            'riwayatKelas.skorAsesmen.asesmenSumatif',
            'riwayatKelas.siswa',
        ])->findOrFail($request->kelas_ajar_id);

        $siswaIds = $request->siswa ?? $kelasAjar->riwayatKelas->pluck('riwayat_kelas_id')->toArray();
        $siswaList = $kelasAjar->riwayatKelas->whereIn('riwayat_kelas_id', $siswaIds);

        // Hitung kehadiran per siswa
        foreach ($siswaList as $rk) {
            $kehadiran = [
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
            ];
            foreach ($rk->kehadiranIntrakurikuler as $absen) {
                if ($absen->status === 'sakit') $kehadiran['sakit']++;
                if ($absen->status === 'izin') $kehadiran['izin']++;
                if ($absen->status === 'alpha') $kehadiran['alpha']++;
            }
            $rk->rekap_kehadiran = $kehadiran;
        }

        $pdf = Pdf::view('dokumen.pdf_rapor', [
            'kelasAjar' => $kelasAjar,
            'siswaList' => $siswaList,
            'sekolah' => $sekolah,
        ])->format('A4');

        return $pdf;
    }

    // Cetak Buku Induk
    public function cetakBukuInduk(Request $request)
    {
        $request->validate([
            'kelas_ajar_id' => 'required|exists:kelas_ajar,kelas_ajar_id',
            'siswa' => 'array',
            'siswa.*' => 'exists:riwayat_kelas,riwayat_kelas_id',
        ]);

        $sekolah = Sekolah::with('kelurahan.kecamatan.kabupaten')->first();
        $kelasAjar = KelasAjar::with([
            'kelas',
            'tahunAjaran',
            'waliKelas',
            'intrakurikuler.pengampu',
            'intrakurikuler.tujuanPembelajaran',
            'intrakurikuler.asesmenFormatif.details.tujuanPembelajaran',
            'intrakurikuler.asesmenSumatif.skorSiswa',
            'riwayatKelas.siswa.user',
            'riwayatKelas.siswa.siswaEkstrakurikuler.ekstrakurikuler.pembina',
            'riwayatKelas.siswa.siswaEkstrakurikuler.penilaians',
            'riwayatKelas.kehadiranIntrakurikuler',
            'riwayatKelas.skorAsesmen.asesmenSumatif',
            'riwayatKelas.siswa',
        ])->findOrFail($request->kelas_ajar_id);

        $siswaIds = $request->siswa ?? $kelasAjar->riwayatKelas->pluck('riwayat_kelas_id')->toArray();
        $siswaList = $kelasAjar->riwayatKelas->whereIn('riwayat_kelas_id', $siswaIds);

        // Hitung kehadiran per siswa
        foreach ($siswaList as $rk) {
            $kehadiran = [
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
            ];
            foreach ($rk->kehadiranIntrakurikuler as $absen) {
                if ($absen->status === 'sakit') $kehadiran['sakit']++;
                if ($absen->status === 'izin') $kehadiran['izin']++;
                if ($absen->status === 'alpha') $kehadiran['alpha']++;
            }
            $rk->rekap_kehadiran = $kehadiran;
        }

        $pdf = Pdf::view('dokumen.pdf_buku_induk', [
            'kelasAjar' => $kelasAjar,
            'siswaList' => $siswaList,
            'sekolah' => $sekolah,
        ])->format('A4');

        return $pdf;
    }

    public function mutasi()
    {
        return view('dokumen.mutasi');
    }

    public function cetakMutasi(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'jumlah' => 'required|integer|min:1|max:50',
        ]);

        $jenis = $request->jenis;
        $jumlah = (int) $request->jumlah;

        $view = $jenis === 'keluar'
            ? 'dokumen.pdf_mutasi_keluar'
            : 'dokumen.pdf_mutasi_masuk';

        $pdf = \Spatie\LaravelPdf\Facades\Pdf::view($view, [
            'jumlah' => $jumlah,
        ])->format('A4');

        return $pdf;
    }
}
