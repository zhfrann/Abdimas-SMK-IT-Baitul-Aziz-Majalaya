<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaranList = [
            ['tahun' => '2023/2024', 'semester' => 'Ganjil'],
            ['tahun' => '2023/2024', 'semester' => 'Genap'],
            ['tahun' => '2024/2025', 'semester' => 'Ganjil'],
            ['tahun' => '2024/2025', 'semester' => 'Genap'],
            ['tahun' => '2025/2026', 'semester' => 'Ganjil'],
            ['tahun' => '2025/2026', 'semester' => 'Genap'],
        ];
        foreach ($tahunAjaranList as $ta) {
            TahunAjaran::firstOrCreate($ta);
        }
    }
}
