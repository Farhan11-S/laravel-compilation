<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\SubscriberJob;
use App\Notifications\ResumeUploadReminder;
use Illuminate\Console\Command;

class ResumeReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Temporarily stop the command from running
    protected $signature = 'dynamic-command:resume-reminder-stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = Setting::firstWhere('name', 'dynamic-command:resume-reminder-limit');
        $baseQuery = SubscriberJob::where('status', 'active')
            ->whereHas('user', function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('resume', function ($query) {
                        $query->where('is_complete', 0);
                    })->orWhereDoesntHave('resume');
                });
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
                $user->load('resume');

                if ($user->hasRole(['superadmin', 'employer'])) {
                    return;
                }

                if (!empty($user->resume) && $user->resume?->is_complete) {
                    return;
                }

                $subscriber->update(['is_sent' => true]);
                $user->notify(new ResumeUploadReminder());
            });
    }
}
