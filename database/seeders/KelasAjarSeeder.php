<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\KelasAjar;
use App\Models\TahunAjaran;
use App\Models\User;

class KelasAjarSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaranModels = TahunAjaran::all();
        $kelasList = Kelas::all();
        $waliKelas = User::role('Wali Kelas')->get();

        foreach ($tahunAjaranModels as $ta) {
            foreach ($kelasList as $idx => $kelas) {
                $wali = $waliKelas[$idx % count($waliKelas)];
                KelasAjar::firstOrCreate([
                    'kelas_id' => $kelas->kelas_id,
                    'tahun_ajaran_id' => $ta->tahun_ajaran_id,
                ], [
                    'wali_user_id' => $wali->id,
                ]);
            }
        }
    }
}
