<?php

namespace App\Notifications;

use App\Enums\CandidateAttachmentType;
use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidateAttachment extends Notification
{
    use Queueable;

    /**
     * @var Candidate
     */
    protected $candidate;

    /**
     * @var string
     */
    protected $link, $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($type, $link, Candidate $candidate)
    {
        $this->link = $link;
        $this->type = $type;
        $this->candidate = $candidate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $line = 'Employer meminta dokumen tambahan untuk melengkapi data lamaran anda. Silakan unggah dokumen yang diminta di aplikasi kami.';

        if ($this->type == CandidateAttachmentType::ASSESMENT_TEST->value) {
            $line = 'Employer meminta anda untuk menyelesaikan tes penilaian. Silakan jawab pertanyaan yang diberikan di aplikasi kami.';
        }

        return (new MailMessage)
            ->line('Employer telah merespon lamaran anda pada lowongan ' . $this->candidate->job?->job_title)
            ->line($line)
            ->action('Pergi ke Aplikasi Kami', env('FRONTEND_URL', url('/')))
            ->line('Pergi ke Profile -> Pekerjaan Saya -> Sudah Melamar -> Cari pekerjaan yang anda lamar -> Unggah dokumen atau jawab pertanyaan yang diberikan.')
            ->line('Terima kasih.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $line = 'Employer meminta dokumen tambahan untuk melengkapi data lamaran anda. Silakan unggah dokumen yang diminta.';

        if ($this->type == CandidateAttachmentType::ASSESMENT_TEST->value) {
            $line = 'Employer meminta anda untuk menyelesaikan tes penilaian. Silakan jawab pertanyaan yang diberikan.';
        }
        return [
            'description' => $line,
            'reference_type' => Candidate::class,
            'reference_id' => $this->candidate->id,
        ];
    }
}
