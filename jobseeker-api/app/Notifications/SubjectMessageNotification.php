<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubjectMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    protected $subject, $message;

    /**
     * @var mixed
     */
    protected $img;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $subject, string $message, $img)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->img = $img;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // return ['mail', 'database'];
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $websiteName = Setting::firstWhere('name', 'website-name')->value;
        $websiteLogo = Setting::firstWhere('name', 'website-logo')->value;
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->markdown('notifications::subject-message', [
                'message' => $this->message,
                'img' => $this->img,
                'websiteName' => $websiteName,
                'websiteLogo' => $websiteLogo,
            ]);

        // if ($this->img) {
        // $mail->attach(Attachment::fromStorageDisk('public', $this->img));
        // }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         //
    //     ];
    // }
}