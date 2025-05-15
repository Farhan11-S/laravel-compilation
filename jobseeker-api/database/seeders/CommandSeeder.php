<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class CommandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commands = [
            'dynamic-command:jobs-newsletter' => '0 0 * * 1,3,5',
            'dynamic-command:resume-reminder' => '0 0 * * 1',
            'dynamic-command:candidate-review-reminder' => '0 0 * * 1',
            'dynamic-command:create-job-reminder' => '0 0 * * 2,4',
            'dynamic-command:jobseeker-daily-mail' => '0 0 * * 1,2,3,4,5,6,7',
        ];

        foreach ($commands as $command => $interval) {
            if (!Setting::firstWhere('name', $command)) {
                Setting::create([
                    'name' => $command,
                    'value' => $interval,
                    'is_image' => false,
                ])->save();
            }

            if (!Setting::firstWhere('name', $command . '-limit')) {
                Setting::create([
                    'name' => $command . '-limit',
                    'value' => 120,
                    'is_image' => false,
                ])->save();
            }
        }
    }
}
