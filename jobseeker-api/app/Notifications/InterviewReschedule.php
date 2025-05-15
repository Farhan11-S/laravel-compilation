<?php

namespace App\Notifications;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewReschedule extends Notification
{
    use Queueable;

    /**
     * @var int
     */
    protected $candidate_id;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $candidate_id)
    {
        $this->candidate_id = $candidate_id;

        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'description' => 'Job Seeker telah merespon panggilan interview anda',
            'reference_type' => Candidate::class,
            'reference_id' => $this->candidate_id,
        ];
    }
}
