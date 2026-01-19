<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = ['X RPL 1', 'X RPL 2', 'X TKJ 1', 'X TKJ 2'];
        foreach ($kelasList as $namaKelas) {
            Kelas::firstOrCreate(['nama_kelas' => $namaKelas]);
        }
    }
}
