<?php

namespace App\Services;

use App\Constants\JobTypes;
use App\Models\Candidate;
use App\Models\Certification;
use App\Models\Company;
use App\Models\Education;
use App\Models\Job;
use App\Models\LanguageSkill;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\WorkExperience;

class UserService
{
    public function formatPhoneNumber($phone)
    {
        if (empty($phone)) return $phone;

        if (!preg_match("/[^+0-9]/", trim($phone))) {
            // cek apakah no hp karakter ke 1 dan 2 adalah angka 62
            if (substr(trim($phone), 0, 2) == "62") {
                $phone = "+" . trim($phone);
            }
            // cek apakah no hp karakter ke 1 adalah angka 0
            else if (substr(trim($phone), 0, 1) == "0") {
                $phone = "+62" . substr(trim($phone), 1);
            }
        }
        return $phone;
    }

    public function userMock($prefix = 'user'): User
    {
        $user = new User([
            'name' => 'Username',
            'email' => $prefix . '-email@mail.com',
            'role_id' => 2,
            'phone' => $prefix . '-phone-number',
        ]);
        $company = new Company([
            'name' => $prefix . '-company-name',
            'industry' => $prefix . '-company-industry-category',
        ]);

        $resume = $this->resumeMock($prefix);

        $user->setRelation('resume', $resume);
        $user->setRelation('company', $company);
        return $user;
    }

    public function resumeMock($prefix = 'user'): Resume
    {
        $resume = new Resume();
        $user_detail = new UserDetail([
            'first_name' => $prefix . '-detail-first-name',
            'last_name' => $prefix . '-detail-last-name',
            'street_address' => $prefix . '-detail-street-address',
            'country' => $prefix . '-detail-country',
            'city' => $prefix . '-detail-city',
            'postal_code' => $prefix . '-detail-postal-code',
            'date_of_birth' => $prefix . '-detail-date_of-birth',
            'place_of_birth' => $prefix . '-detail-place-of_birth',
            'social_medias' => $prefix . '-detail-social-medias',
        ]);
        $certification = new Certification([
            'name' => $prefix . '-certification-name',
            'does_not_expire' => true,
            'from' => date('Y-m-d'),
            'description' => $prefix . '-certification-description',
        ]);
        $education = new Education([
            'level' => $prefix . '-education-level',
            'field_of_study' => $prefix . '-education',
            'school_name' => $prefix . '-education',
            'country' => $prefix . '-education',
            'city' => $prefix . '-education',
            'is_currently_enrolled' => true,
            'from' => date('Y-m-d'),
        ]);
        $skill = new Skill([
            'name' => $prefix . '-skill-name',
            'years_of_experience' => $prefix . '-skill-years-of-experience',
        ]);
        $work_experience = new WorkExperience([
            'job_title' => $prefix . '-work-experience-job-title',
            'company' => $prefix . '-work-experience-company',
            'country' => $prefix . '-work-experience-country',
            'city' => $prefix . '-work-experience-city',
            'description' => $prefix . '-work-experience-description',
            'is_currently_work_here' => true,
            'from' => date('Y-m-d'),
        ]);
        $language_skill = new LanguageSkill([
            'name' => $prefix . '-language-skill-name',
            'level' => $prefix . '-language-skill-level',
        ]);

        $resume->setRelation('user_detail', $user_detail);
        $resume->setRelation('education', $education);
        $resume->setRelation('language_skill', $language_skill);
        $resume->setRelation('work_experience', $work_experience);
        $resume->setRelation('skill', $skill);
        $resume->setRelation('certification', $certification);

        return $resume;
    }

    public function candidateMock($prefix = 'candidate'): Candidate
    {
        $candidate = new Candidate();
        $user = $this->userMock($prefix);
        $candidate->setRelation('user', $user);

        return $candidate;
    }

    public function jobMock(): Job
    {
        $job = new Job([
            'country' => 'Indonesia',
            'language' => 'Indonesia',
            'job_title' => 'Judul Sample',
            'location' => 'Yogyakarta',
            'int_hires_needed' => 3,
            'minimum_wage' => 2500000,
            'maximum_wage' => 15000000,
            'job_description' => "Deskripsi dari sample data",
            'communication_email' => "youremail@mail.com",
            'job_type' => join(',', JobTypes::JOB_TYPES)
        ]);
        return $job;
    }
}
