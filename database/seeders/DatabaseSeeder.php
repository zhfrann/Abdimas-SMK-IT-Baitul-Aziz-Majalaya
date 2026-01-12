<?php

namespace Database\Seeders;

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
            // WilayahSeeder::class,
        ]);


        $admin = User::query()->firstOrCreate(
            ['username' => 'admin'],
            ['name' => 'Super Admin', 'password' => Hash::make('admin12345')]
        );
        $admin->assignRole('Super Admin');
    }
}
