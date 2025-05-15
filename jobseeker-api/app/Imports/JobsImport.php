<?php

namespace App\Imports;

use App\Constants\Currencies;
use App\Constants\JobTypes;
use App\Constants\Roles;
use App\Models\Company;
use App\Models\Job;
use App\Models\SubscriberJob;
use App\Models\User;
use App\Services\JobService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class JobsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    /**
     * @var Job
     */
    protected $isAdmin = false;

    /**
     * Create a new notification instance.
     */
    public function __construct(bool $isAdmin = false, private JobService $jobService)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $job_types = explode(',', $row['job_type']);
        foreach ($job_types as $i => $v) {
            if (!JobTypes::isProperJobType(trim($v))) {
                unset($job_types[$i]);
            }
        }
        // $job_descriptions = explode("\n", $row['job_description']);
        // $job_description = '<p>' . join('</p><p>', $job_descriptions)  . '</p>';

        $userID = auth()->user()->id;
        $companyID = null;
        $ccEmails = null;
        $communicationEmails = explode("\n", $row['communication_email']);

        if ($this->isAdmin) {
            $trx = DB::transaction(function () use ($row) {
                $userEmail = $row['user_email'];
                $newUsername = explode('@', $userEmail)[0];

                $company = Company::firstOrCreate([
                    'name' => trim($row['company_name'])
                ]);

                $user = User::firstWhere([
                    'email' => $userEmail,
                ]);

                if (!$user) {
                    $user = User::create([
                        'name' => $newUsername,
                        'email' => $userEmail,
                        'role_id' => Roles::EMPLOYER,
                        'password' => Hash::make('Rheinjobs2024')
                    ]);

                    $user->providers()->create([
                        'provider' => 'admin',
                        'provider_id' => $user->id,
                    ]);

                    $user->assignRole('employer');
                    event(new Registered($user));
                }

                if ($user->subscriberJob === null) {
                    SubscriberJob::create([
                        'email' => $user->email,
                        'token' => null,
                        'status' => 'active',
                        'user_id' => $user->id,
                        'created_by' => null,
                        'deleted_by' => null,
                    ]);
                }

                $user->company_id = $company->id;
                $user->save();

                return compact('user');
            });
            $userID = $trx['user']?->id;
            $companyID = $trx['user']?->company_id;

            $communicationEmails = explode("\n", $row['communication_email']);

            if (!empty($row['cc_emails'])) {
                $ccEmails = explode("\n", $row['cc_emails']);
            }
            // $user->notify(new JobCreatedNotification());
        }

        return $this->jobService->createJob([
            'country' => $row['country'],
            'language' => $row['language'],
            'is_hiring_manager' => true,
            'job_title' => $row['job_title'],
            'job_type' => $job_types,
            'location' => $row['location'],
            'int_hires_needed' => $row['total_hires_needed'],
            'expected_hire_date' => 3,
            'minimum_wage' => $row['minimum_wage'],
            'maximum_wage' => $row['maximum_wage'],
            'rate' => 'per month',
            'job_description' => $row['job_description'],
            'email_subject_format' => $row['email_subject_format'],
            'resume_required' => 'yes',
            'application_deadline' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'communication_email' => $communicationEmails,
            'cc_emails' => $ccEmails,
            'user_id' => $userID,
            'company_id' => $companyID,
            'external_apply_link' => $row['external_apply_link'],
            'is_walk_in_interview' => $row['is_walk_in_interview'] === 1 ? 1 : 0,
            'is_walk_in_interview' => $row['is_walk_in_interview'] === 1 ? 1 : 0,
            'currency_code' => $row['currency_code'],
            'published_at' => empty($row['published_at']) ? null : Carbon::createFromFormat('d/m/Y', $row['published_at'])->toDateTimeString(),
        ], false, false);
    }

    public function rules(): array
    {
        $rules = [
            'country' => ['required', 'string'],
            'language' => ['required', 'string'],
            'job_title' => ['required', 'string'],
            'location' => ['required', 'string'],
            'total_hires_needed' => ['required', 'integer'],
            'minimum_wage' => ['nullable', 'integer'],
            'maximum_wage' => ['nullable', 'integer'],
            'rate' => ['nullable', 'string'],
            'job_description' => ['required', 'string'],
            'email_subject_format' => ['nullable', 'string'],
            'communication_email' => ['required', 'string'],
            'job_type' => ['nullable', 'string'],
        ];

        if ($this->isAdmin) {
            $rules['user_email'] = ['required', 'string'];
            $rules['cc_emails'] = ['nullable', 'string'];
            $rules['company_name'] = ['required', 'string'];
            $rules['external_apply_link'] = ['nullable', 'string', 'url:http,https'];
            $rules['is_walk_in_interview'] = ['nullable', 'boolean'];
        }

        $rules['currency_code'] = ['nullable', Rule::in(Currencies::CURRENCIES)];
        $rules['published_at'] = ['nullable', 'date_format:d/m/Y'];

        return $rules;
    }
}
