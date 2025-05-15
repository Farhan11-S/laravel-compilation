<?php

namespace Database\Seeders;

use App\Enums\PremiumAdPlace;
use App\Models\SubscriptionLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'limit_create_job' => 10,
                'limit_interview_schedules' => 10,
                'unlimited_candidate_application' => true,
                'show_resume_search_menu' => false,
                'premium_ads' => json_encode([]),
            ],
            [
                'limit_create_job' => 20,
                'limit_interview_schedules' => 20,
                'unlimited_candidate_application' => true,
                'show_resume_search_menu' => true,
                'premium_ads' => json_encode([]),
            ],
            [
                'limit_create_job' => 30,
                'limit_interview_schedules' => 30,
                'unlimited_candidate_application' => true,
                'show_resume_search_menu' => true,
                'premium_ads' => json_encode([PremiumAdPlace::FIRST_JOB_HOMEPAGE]),
            ],
            [
                'limit_create_job' => 40,
                'limit_interview_schedules' => 40,
                'unlimited_candidate_application' => true,
                'show_resume_search_menu' => true,
                'premium_ads' => json_encode([
                    PremiumAdPlace::FIRST_JOB_HOMEPAGE,
                    PremiumAdPlace::BETWEEN_JOBS_HOMEPAGE,
                ]),
            ],
            [
                'limit_create_job' => 50,
                'limit_interview_schedules' => 50,
                'unlimited_candidate_application' => true,
                'show_resume_search_menu' => true,
                'premium_ads' => json_encode([
                    PremiumAdPlace::FIRST_JOB_HOMEPAGE,
                    PremiumAdPlace::BETWEEN_JOBS_HOMEPAGE,
                    PremiumAdPlace::JOB_DETAIL_PAGE,
                    PremiumAdPlace::BLOG_LIST_PAGE,
                    PremiumAdPlace::BLOG_DETAIL_PAGE,
                ]),
            ],
        ];
        for ($i = 1; $i <= 5; $i++) {
            if (SubscriptionLevel::where('id', $i)->exists()) {
                continue;
            }
            SubscriptionLevel::create($levels[$i - 1]);
        }
    }
}
