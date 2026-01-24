<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sekolah::query()->firstOrCreate(
            ['nama_sekolah' => 'SMK IT BAITUL AZIZ'],
            [
                'nama_sekolah' => 'SMK IT BAITUL AZIZ',
                'npsn' => '69908714',
                'alamat' => 'Jl.Pesantren Baitul Aziz - Kp. Sukahaji No.44',
                'kode_pos' => '40382',
                'kelurahan_id' => '32.04.33.2010',
                'website' => 'www.smkitbaitulaziz.sch.id',
                'email' => 'smkitbaitulaziz@gmail.com',
                'nama_kepala_sekolah' => 'Feny Irfany Muhammad,ST.,M.AP.',
            ]
        );
    }
}
