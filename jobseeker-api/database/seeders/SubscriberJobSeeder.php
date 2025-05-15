<?php

namespace Database\Seeders;

use App\Models\SubscriberJob;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriberJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::doesntHave('subscriberJob')->get();
        foreach ($users as $user) {
            SubscriberJob::create([
                'email' => $user->email,
                'token' => null,
                'status' => 'active',
                'user_id' => $user->id,
                'created_by' => null,
                'deleted_by' => null,
            ]);
        }
    }
}
