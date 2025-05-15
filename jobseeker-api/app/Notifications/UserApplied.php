<?php

namespace App\Notifications;

use App\Models\Candidate;
use App\Models\EmailTemplate;
use App\Models\Job;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserApplied extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Job
     */
    protected $job;

    /**
     * @var User
     */
    protected $user_applied;

    /**
     * @var Candidate
     */
    protected $candidate;

    /**
     * @var mixed
     */
    protected $company;

    /**
     * Create a new notification instance.
     */
    public function __construct(Job $job, User $user_applied, Candidate $candidate, $company = [])
    {
        $this->job = $job;
        $this->user_applied = $user_applied;
        $this->company = $company;
        $this->candidate = $candidate;

        $this->afterCommit();
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
        $user_name = $this->user_applied->name ?? '';
        $company_name = $this->company->name ?? '';

        $emailTemplate = EmailTemplate::firstWhere('identifier', 'user-applied');

        $userDetail = $this->user_applied->resume?->user_detail;
        $subject = $this->job->job_title . '_' . "$userDetail?->first_name $userDetail?->last_name";

        if ($this->job->email_subject_format != null) {
            $subject = $this->extractVar($this->job->email_subject_format);
        }

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($emailTemplate->greeting)
            ->markdown('notifications::user-applied', [
                'job' => $this->job,
                'user_applied' => $this->user_applied
            ])
            ->salutation($emailTemplate->salutation);

        if (!empty($this->job->cc_emails)) {
            $mailMessage->cc($this->job->cc_emails);
        }

        foreach ($emailTemplate->lines as $line) {
            $line = str_replace('$user_name', $user_name, $line);
            $line = str_replace('$company_name', $company_name, $line);
            $mailMessage->line($line);
        }

        $mailMessage->action('View CV', env('FRONTEND_URL', url('/')) . '/dashboard/candidates/detail/' . $this->candidate->id,);
        return $mailMessage;
    }

    public function extractVar($string)
    {
        $userDetail = $this->user_applied->resume?->user_detail;
        $result = Str::replace('{$posisi}', $this->job->job_title, $string);
        $result = Str::replace('{$nama_depan_pelamar}', $userDetail?->first_name, $result);
        $result = Str::replace('{$nama_belakang_pelamar}', $userDetail?->last_name, $result);
        $result = Str::replace('{$nama_lengkap_pelamar}', "$userDetail?->first_name $userDetail?->last_name", $result);
        $result = Str::replace('{$domisili_pelamar}', $userDetail?->city, $result);

        return $result;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $name = $this->user_applied->name ?? '';
        return [
            'description' => 'User ' . $name . ' telah melamar di perusahaan anda!',
            'reference_type' => Candidate::class,
            'reference_id' => $this->candidate->id,
        ];
    }
}
