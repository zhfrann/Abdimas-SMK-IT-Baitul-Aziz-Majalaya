<?php

namespace Database\Seeders;

use App\Models\KelasAjar;
use App\Models\Intrakurikuler;
use App\Models\TujuanPembelajaran;
use App\Models\LingkupMateri;
use Illuminate\Database\Seeder;

class DummyIntrakurikulerSeeder extends Seeder
{
    public function run(): void
    {
        // Load data dummy dari file eksternal
        $dummyPath = database_path('seeders/DummyData/IntrakurikulerDummy.php');
        require $dummyPath; // variabel di file ini akan terisi

        // Daftar mapel yang ingin diinput
        $mapelList = [
            'Pendidikan Agama Islam' => $pendidikan_agama_islam,
            'Seni Rupa' => $seni_rupa,
            'Projek IPA 1' => $projek_ipa_1,
            'Pendidikan Pancasila' => $pendidikan_pancasila,
            'Bahasa Sunda' => $bahasa_sunda,
            'Matematika' => $matematika,
            'Bahasa Indonesia' => $bahasa_indonesia,
        ];

        // Cari kelas ajar yang dimaksud
        $kelasAjar = KelasAjar::query()
            ->whereHas('kelas', function ($q) {
                $q->where('nama_kelas', 'X RPL 1');
            })
            ->whereHas('tahunAjaran', function ($q) {
                $q->where('tahun', '2025/2026')->where('semester', 'Genap');
            })
            ->first();

        if (!$kelasAjar) {
            $this->command->error('Kelas ajar X RPL 1 2025/2026 Genap tidak ditemukan!');
            return;
        }

        foreach ($mapelList as $namaMapel => $dataMapel) {

            // Buat intrakurikuler
            $intrakurikuler = Intrakurikuler::firstOrCreate([
                'kelas_ajar_id' => $kelasAjar->kelas_ajar_id,
                'nama_pelajaran' => $namaMapel,
            ], [
                'pengampu_user_id' => $dataMapel['pengampu_user_id'] ?? 1,
            ]);

            // Tujuan Pembelajaran
            foreach ($dataMapel['tujuan_pembelajaran'] as $tp) {
                TujuanPembelajaran::firstOrCreate([
                    'intrakurikuler_id' => $intrakurikuler->intrakurikuler_id,
                    'deskripsi' => $tp,
                ]);
            }

            // Lingkup Materi
            foreach ($dataMapel['lingkup_materi'] as $lm) {
                LingkupMateri::firstOrCreate([
                    'intrakurikuler_id' => $intrakurikuler->intrakurikuler_id,
                    'nama_materi' => $lm,
                ]);
            }
        }

        $this->command->info('Dummy intrakurikuler, tujuan pembelajaran, dan lingkup materi berhasil diinput.');
    }
}
