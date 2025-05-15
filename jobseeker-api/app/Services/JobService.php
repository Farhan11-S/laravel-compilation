<?php

namespace App\Services;

use App\Models\Job;
use App\Services\AutoPost\SendTo;
use App\Services\AutoPost\Whatsapp\Api;
use Hashids\Hashids;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class JobService
{
    public function createJob(array $data, $shouldSave = true, $shouldPostDirectly = true): Job
    {
        $job = new Job([
            'country' => $data['country'],
            'language' => $data['language'],
            'is_hiring_manager' => true,
            'job_title' => $data['job_title'],
            'job_type' => $data['job_type'],
            'location' => $data['location'],
            'int_hires_needed' => $data['int_hires_needed'],
            'expected_hire_date' => 3,
            'minimum_wage' => $data['minimum_wage'],
            'maximum_wage' => $data['maximum_wage'],
            'rate' => 'per month',
            'job_description' => $data['job_description'],
            'email_subject_format' => $data['email_subject_format'],
            'resume_required' => $data['resume_required'],
            'application_deadline' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'communication_email' => $data['communication_email'],
            'cc_emails' => $data['cc_emails'],
            'user_id' => $data['user_id'],
            'company_id' => $data['company_id'],
            'external_apply_link' => $data['external_apply_link'],
            'is_walk_in_interview' => $data['is_walk_in_interview'] === 1 ? 1 : 0,
            'currency_code' => $data['currency_code'],
            'published_at' => $data['published_at'] ?? null,
            'deleted_at' => null,
            'should_post' => !$shouldPostDirectly,
        ]);

        if ($shouldPostDirectly) {
            $this->postToSocialMedia(collect([$job]));
        }

        if ($shouldSave) {
            $job->save();
        }

        return $job;
    }

    public function postToSocialMedia(Collection $jobs): void
    {
        if ($jobs->isEmpty()) {
            return;
        }

        foreach ($jobs as $job) {
            $jobDescriptionEllipsis = $this->jobDescriptionEllipsis($job);

            $postContent = '';
            $postContent .= 'Posisi : ' . $job->job_title . "\n";
            $postContent .= 'Perusahaan : ' . $job->company->name . "\n";
            $postContentHTML = $postContent . "\nDeskripsi Pekerjaan \n" . $jobDescriptionEllipsis['html'] . "\n\n";
            $postContentText = $postContent . "\nDeskripsi Pekerjaan \n" . $jobDescriptionEllipsis['text'] . "\n\n";

            SendTo::Telegram($postContentHTML);
            SendTo::Whatsapp($postContentText);
        }
    }

    public function jobDescriptionEllipsis(Job $job)
    {
        $hashids = new Hashids(env('HASHIDS_SALT', ''), 10);
        $jobDescriptionLines = explode("\n", $job->job_description);
        if (count($jobDescriptionLines) > 4) {
            $jobDescriptionEllipsis = implode("\n", array_slice($jobDescriptionLines, 0, 4)) . '...';
        } else {
            $jobDescriptionEllipsis = $job->job_description;
        }

        $jobDetailURL = env('FRONTEND_URL', 'https://rheinjobs.com/') . 'jobs/' . Str::slug($job->job_title) . '-di-' . Str::slug($job->company->name) . '-' . $hashids->encode($job->id);
        $jobDescriptionEllipsisHTML = $jobDescriptionEllipsis . "\n<a href='" . $jobDetailURL . "'>Baca Selengkapnya...</a>";
        $jobDescriptionEllipsisText = $jobDescriptionEllipsis . "\nBaca Selengkapnya di " . $jobDetailURL;
        return [
            'text' => $jobDescriptionEllipsisText,
            'html' => $jobDescriptionEllipsisHTML,
        ];
    }

    public function updateJob(Job $job, array $data): Job
    {
        $job->update($data);
        return $job;
    }

    public function deleteJob(Job $job): void
    {
        $job->delete();
    }

    public function getJobById(int $id): ?Job
    {
        return Job::find($id);
    }

    public function getAllJobs(): Collection
    {
        return Job::all();
    }
}
