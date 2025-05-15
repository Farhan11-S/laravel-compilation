<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\Setting;
use App\Models\SubscriberJob;
use App\Notifications\CandidateReviewReminder as NotificationsCandidateReviewReminder;
use Illuminate\Console\Command;

class CandidateReviewReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamic-command:candidate-review-reminder';

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
        $limit = Setting::firstWhere('name', 'dynamic-command:candidate-review-reminder-limit');
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
                $user->load('jobs');

                $candidates = Candidate::whereIn('job_id', $user->jobs->pluck('id'))->exists();
                if (empty($user->jobs) || !$candidates) {
                    return;
                }

                if (!$user->hasRole(['employer'])) {
                    return;
                }

                $subscriber->update(['is_sent' => true]);
                $user->notify(new NotificationsCandidateReviewReminder());
            });
    }
}
