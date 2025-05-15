<?php

namespace App\Exports;

use App\Constants\JobTypes;
use App\Models\Job;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JobsExport implements FromCollection, WithHeadings
{
    /**
     * @var bool
     */
    protected $isAdmin = false;

    /**
     * Create a new notification instance.
     */
    public function __construct(bool $isAdmin = false)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = [
            'country' => 'Indonesia',
            'language' => 'Indonesia',
            'job_title' => 'Judul Sample',
            'location' => 'Yogyakarta',
            'int_hires_needed' => 3,
            'minimum_wage' => 2500000,
            'maximum_wage' => 15000000,
            'job_description' => "Deskripsi dari sample data",
            'email_subject_format' => '{$posisi}_{$domisili_pelamar}_{$nama_lengkap_pelamar}',
            'communication_email' => "youremail@mail.com",
            'job_type' => join(',', JobTypes::JOB_TYPES)
        ];

        if ($this->isAdmin) {
            $data['user_email'] = 'existing-user-email@mail.com';
            $data['cc_emails'] = 'youremail@mail.com';
            $data['company_name'] = 'PT. Surya Nusantara Sentosa';
            $data['external_apply_link'] = null;
            $data['is_walk_in_interview'] = null;
        }

        $data['currency_code'] = null;
        $data['published_at'] = Carbon::parse(now())->isoFormat('Y-MM-DD');

        return collect([$data]);
    }

    public function headings(): array
    {
        $heading = [
            'Country',
            'Language',
            'Job Title',
            'Location',
            'Total Hires Needed',
            'Minimum Wage',
            'Maximum Wage',
            'Job Description',
            'Email Subject Format',
            'Communication Email',
            'Job Type',
        ];

        if ($this->isAdmin) {
            $heading[] = 'User Email';
            $heading[] = 'CC Emails';
            $heading[] = 'Company Name';
            $heading[] = 'External Apply Link';
            $heading[] = 'Is Walk In Interview';
        }

        $heading[] = 'Currency Code';
        $heading[] = 'Published At';

        return $heading;
    }
}
