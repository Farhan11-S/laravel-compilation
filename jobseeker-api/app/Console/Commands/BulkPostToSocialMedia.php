<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Services\JobService;
use Illuminate\Console\Command;

class BulkPostToSocialMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bulk-post-to-social-media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post all jobs that flagged "should_post" to social media';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jobService = new JobService();

        $jobs = Job::where('should_post', true)->inRandomOrder()->get();

        $jobs->each(function (Job $job) {
            $job->should_post = false;
            $job->save();
        });

        $jobService->postToSocialMedia($jobs);
    }
}
