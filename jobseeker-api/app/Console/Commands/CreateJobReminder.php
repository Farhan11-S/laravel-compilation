<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\Setting;
use App\Models\SubscriberJob;
use App\Notifications\CreateJobReminder as NotificationsCreateJobReminder;
use Illuminate\Console\Command;

class CreateJobReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamic-command:create-job-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send create job reminder to employer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = Setting::firstWhere('name', 'dynamic-command:create-job-reminder-limit');
        $baseQuery = SubscriberJob::where('status', 'active')
            ->whereHas('user', function ($query) {
                $query->role('employer');
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

                if (!$user->hasRole(['employer'])) {
                    return;
                }

                $subscriber->update(['is_sent' => true]);
                $user->notify(new NotificationsCreateJobReminder());
            });
    }
}
