<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $jsonFiles = [
            'database/data/users.json',
            'database/data/users-1.json'
        ];
        
        foreach ($jsonFiles as $file) {
            if (File::exists($file)) {
                $json = File::get($file);
                $data = json_decode($json);
        
                foreach ($data->data as $obj) {
                    if (User::where('nim', $obj->nim)->exists()) {
                        continue;
                    }
                    User::create([
                        'name' => $obj->name,
                        'nim' => $obj->nim,
                        'department' => $obj->department,
                        'position' => $obj->position,
                        'password' => bcrypt($obj->password),
                    ]);
                }
            } else {
                $this->command->warn("File {$file} tidak ditemukan.");
            }
        }
        
    }
}
