<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstWhere('email', 'admin@mail.com');
        if (!Category::firstWhere('name', 'For Jobseeker') && $user) {
            Category::create([
                'name' => 'For Jobseeker',
                'type' => 'blog',
                'created_by' => $user->id
            ]);
        }

        if (!Category::firstWhere('name', 'For Employer') && $user) {
            Category::create([
                'name' => 'For Employer',
                'type' => 'blog',
                'created_by' => $user->id
            ]);
        }
    }
}
