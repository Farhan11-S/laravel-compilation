<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GreetingFromAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        $emailTemplate = EmailTemplate::firstWhere('identifier', 'greeting-from-admin');

        return (new MailMessage)
            ->markdown('notifications::greeting-from-admin', [
                'user' => $this->user
            ])
            ->greeting($emailTemplate->greeting)
            ->line($emailTemplate->lines[0])
            ->action('Pergi ke Aplikasi Kami', env('FRONTEND_URL', url('/')))
            ->line($emailTemplate->lines[1]);
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
