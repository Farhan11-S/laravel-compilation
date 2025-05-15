<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            ClusterSeeder::class,
        ]);

        if (User::where('email', 'simpul1@mail.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Simple User 1',
                'username' => 'simpul1',
                'email' => 'simpul1@mail.com',
                'role' => 'simpul',
                'password' => Hash::make('simpulHore1'),
            ]);
        }
        if (User::where('email', 'simpul2@mail.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Simple User 2',
                'username' => 'simpul2',
                'email' => 'simpul2@mail.com',
                'role' => 'simpul',
                'password' => Hash::make('simpulHore2'),
            ]);
        }
        if (User::where('email', 'verifikator@mail.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Verifikator User 1',
                'username' => 'verifikator1',
                'email' => 'verifikator@mail.com',
                'role' => 'verifikator',
                'password' => Hash::make('verifikatorHore1'),
            ]);
        }
        if (User::where('email', 'ketua@mail.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Ketua User 1',
                'username' => 'ketua1',
                'email' => 'ketua@mail.com',
                'role' => 'ketua',
                'password' => Hash::make('ketuaHore1'),
            ]);
        }
        if (User::where('email', 'simpul3@mail.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Simpul User 3',
                'username' => 'simpul3',
                'email' => 'simpul3@mail.com',
                'role' => 'simpul',
                'password' => Hash::make('simpulHore3'),
            ]);
        }
    }
}
