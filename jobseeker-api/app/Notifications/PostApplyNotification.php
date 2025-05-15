<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\Job;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostApplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Job
     */
    protected $job;

    /**
     * Create a new notification instance.
     */
    public function __construct(Job $job)
    {
        $this->job = $job;

        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $emailTemplate = EmailTemplate::firstWhere('identifier', 'post-apply-notification');

        $mailMessage = (new MailMessage)
            ->greeting($emailTemplate->greeting)
            ->markdown('notifications::post-apply-notification', [
                'job' => $this->job,
            ]);

        foreach ($emailTemplate->lines as $line) {
            $mailMessage->line($line);
        }

        $mailMessage->action('Lihat Status Pekerjaan', env('FRONTEND_URL', url('/')) . '/my-jobs');
        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
