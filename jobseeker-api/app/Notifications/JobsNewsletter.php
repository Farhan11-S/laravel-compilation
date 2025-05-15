<?php

namespace App\Notifications;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobsNewsletter extends Notification
{
    use Queueable;

    /**
     * @var mixed
     */
    protected $jobs;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($jobs, User $user)
    {
        $this->jobs = $jobs;
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
        $websiteName = Setting::firstWhere('name', 'website-name')->value;
        $websiteLogo = Setting::firstWhere('name', 'website-logo')->value;
        return (new MailMessage)
            ->markdown('notifications::jobs-newsletter', [
                'jobs' => $this->jobs,
                'user' => $this->user,
                'websiteName' => $websiteName,
                'websiteLogo' => $websiteLogo,
            ])
            ->action('Lihat lowongan lain', url(env('FRONTEND_URL')));
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
