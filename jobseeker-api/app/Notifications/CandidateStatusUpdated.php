<?php

namespace App\Notifications;

use App\Models\Job;
use App\Models\Candidate;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidateStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Candidate
     */
    protected $candidate;

    /**
     * @var Job
     */
    protected $job;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var mixed
     */
    protected $company;

    /**
     * Create a new notification instance.
     */
    public function __construct(Candidate $candidate, Job $job, User $user, $company = [])
    {
        $this->candidate = $candidate;
        $this->job = $job;
        $this->user = $user;
        $this->company = $company;
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
        $user_name = $this->user->name ?? '';
        $company_name = $this->company->name ?? '';

        $emailTemplate = EmailTemplate::firstWhere('identifier', 'candidate-status-updated');
        $mailMessage = (new MailMessage)
            ->greeting($emailTemplate->greeting)
            ->markdown('notifications::candidate-status', [
                'candidate' => $this->candidate,
                'job' => $this->job,
                'user' => $this->user,
                'company' => $this->company
            ])
            ->salutation($emailTemplate->salutation);

        foreach ($emailTemplate->lines as $line) {
            $line = str_replace('$user_name', $user_name, $line);
            $line = str_replace('$company_name', $company_name, $line);
            $mailMessage->line($line);
        }

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
