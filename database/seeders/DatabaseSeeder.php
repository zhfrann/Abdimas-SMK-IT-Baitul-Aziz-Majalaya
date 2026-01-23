<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Admin',
        //     'username' => 'admin',
        //     'email' => 'admin@phoenixcoded.com',
        //     'password' => bcrypt('12345678'),
        // ]);

        $this->call([
            RoleSeeder::class,

            WilayahSeeder::class,

            // DemoAkademikSeeder::class,
            UserStaffSeeder::class,
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            KelasAjarSeeder::class,
            SiswaSeeder::class,
        ]);


        $admin = User::query()->firstOrCreate(
            ['username' => 'admin'],
            ['name' => 'Super Admin', 'password' => Hash::make('admin12345')]
        );
        $admin->assignRole('Super Admin');

        Sekolah::query()->firstOrCreate(
            ['nama_sekolah' => 'SMK IT BAITUL AZIZ'],
            [
                'nama_sekolah' => 'SMK IT BAITUL AZIZ',
                'npsn' => '69908714',
                'alamat' => 'Jl.Pesantren Baitul Aziz - Kp. Sukahaji No.44',
                'kode_pos' => '40382',
                'kelurahan_id' => '32.04.33.2010',
                'website' => 'www.smkitbaitulaziz.sch.id',
                'nama_kepala_sekolah' => 'Feny Irfany Muhammad,ST.,M.AP.',
                'email' => 'smkitbaitulaziz@gmail.com'
            ]
        );
    }
}
