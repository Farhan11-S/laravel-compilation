<?php

namespace App\Notifications;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResumeUploadReminder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct() {}

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
            ->subject($notifiable->name . ', Lengkapi Profil Anda Sekarang')
            ->markdown('notifications::resume-upload-reminder', [
                'websiteName' => $websiteName,
                'websiteLogo' => $websiteLogo,
            ])
            ->action('Tambahkan Resume', url('/'));
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