<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\Setting;
use App\Models\SubscriberJob;
use App\Notifications\JobsNewsletter as NotificationsJobsNewsletter;
use App\Notifications\ResumeUploadReminder;
use Illuminate\Console\Command;

class JobseekerDailyMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamic-command:jobseeker-daily-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends to all user a daily newsletter with the latest jobs/job that related to their applied job type and candidate resume reminder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = Setting::firstWhere('name', 'dynamic-command:jobseeker-daily-mail-limit');
        $baseQuery = SubscriberJob::where('status', 'active')
            ->whereHas('user', function ($query) {
                $query->role('job seeker');
            });

        if ($baseQuery->clone()->where('is_sent', false)->count() === 0) {
            $baseQuery->clone()->update(['is_sent' => false]);
        }

        $baseQuery
            ->where('is_sent', false)
            ->limit($limit?->value ?? 120)
            ->get()
            ->each(function ($subscriber) {
                $user = $subscriber->user;
                if ($user->hasRole(['superadmin', 'employer'])) {
                    return;
                }

                $jobs = Job::when($user->candidates()->exists(), function ($query) use ($user) {
                    return $query->where(function ($query) use ($user) {
                        $candidate = $user->candidates()->whereHas('job')->first();

                        if (!$candidate) {
                            return;
                        }
                        $relatedJob = $candidate->job;
                        $explodedJob = explode(' ', $relatedJob->job_title);
                        $query->where('location', $relatedJob->location);

                        foreach ($explodedJob as $job) {
                            $query->orWhere('job_title', 'like', '%' . $job . '%');
                        }
                    });
                })->with('company')->latest()->limit(10)->get();

                $subscriber->update(['is_sent' => true]);
                $user->notify(new NotificationsJobsNewsletter($jobs, $user));

                if (!empty($user->resume) && $user->resume?->is_complete) {
                    return;
                }

                $user->notify(new ResumeUploadReminder());
            });
    }
}
