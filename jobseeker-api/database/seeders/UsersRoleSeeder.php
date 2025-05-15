<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::doesntHave('roles')->get();
        foreach ($users as $user) {
            switch ($user->role_id) {
                case 1:
                    $user->assignRole('superadmin');
                    break;
                case 2:
                    $user->assignRole('employer');
                    break;
                case 3:
                    $user->assignRole('job seeker');
                    break;
                default:
                    $user->assignRole('job seeker');
                    break;
            }
        }
    }
}
