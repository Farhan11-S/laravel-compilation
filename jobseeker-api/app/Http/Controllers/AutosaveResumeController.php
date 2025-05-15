<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resumes\StoreCertificationRequest;
use App\Http\Requests\Resumes\StoreEducationRequest;
use App\Http\Requests\Resumes\StoreLanguageSkillRequest;
use App\Http\Requests\Resumes\StoreSkillRequest;
use App\Http\Requests\Resumes\StoreUserDetailRequest;
use App\Http\Requests\Resumes\StoreWorkExperienceRequest;
use App\Models\Resume;
use Illuminate\Http\Request;

class AutosaveResumeController extends Controller
{
    public function autosaveUserDetail(StoreUserDetailRequest $request)
    {
        $validated = $request->validated();
        $user_detail = $validated['user_detail'];
        if (!empty($user_detail['date_of_birth'])) {
            $convertDateToTimeTo = strtotime($user_detail['date_of_birth']);
            $user_detail['date_of_birth'] = date('Y-m-d', $convertDateToTimeTo);
        }

        $resume = Resume::where('user_id', auth()->user()->id)->firstOrFail();

        $resume->user_detail()->firstOrCreate([
            'resume_id' => $resume->id
        ], $user_detail);

        $resume->user_detail->update($user_detail);

        return [
            'status' => 'success',
            'data' => $resume->user_detail
        ];
    }

    public function autosaveEducation(StoreEducationRequest $request)
    {
        $validated = $request->validated();
        $resume = Resume::where('user_id', auth()->user()->id)->firstOrFail();

        $convertDateToTimeFrom = strtotime($validated['from']);
        $validated['from'] = date('Y-m-d', $convertDateToTimeFrom);

        if (!empty($validated['to'])) {
            $convertDateToTimeTo = strtotime($validated['to']);
            $validated['to'] = date('Y-m-d', $convertDateToTimeTo);
        }

        $resume->educations()->create($validated);

        return [
            'status' => 'success',
            'data' => $resume->educations
        ];
    }

    public function autosaveWorkExperience(StoreWorkExperienceRequest $request)
    {
        $validated = $request->validated();
        $resume = Resume::where('user_id', auth()->user()->id)->firstOrFail();

        $convertDateToTimeFrom = strtotime($validated['from']);
        $validated['from'] = date('Y-m-d', $convertDateToTimeFrom);

        if (!empty($validated['to'])) {
            $convertDateToTimeTo = strtotime($validated['to']);
            $validated['to'] = date('Y-m-d', $convertDateToTimeTo);
        }

        $resume->work_experiences()->create($validated);

        return [
            'status' => 'success',
            'data' => $resume->work_experiences
        ];
    }

    public function autosaveCertification(StoreCertificationRequest $request)
    {
        $validated = $request->validated();
        $resume = Resume::where('user_id', auth()->user()->id)->firstOrFail();

        $convertDateToTimeFrom = strtotime($validated['from']);
        $validated['from'] = date('Y-m-d', $convertDateToTimeFrom);

        if (!empty($validated['to'])) {
            $convertDateToTimeTo = strtotime($validated['to']);
            $validated['to'] = date('Y-m-d', $convertDateToTimeTo);
        }

        $resume->certifications()->create($validated);

        return [
            'status' => 'success',
            'data' => $resume->certifications
        ];
    }

    public function autosaveSkill(StoreSkillRequest $request)
    {
        $validated = $request->validated();
        $resume = Resume::where('user_id', auth()->user()->id)->firstOrFail();

        $resume->skills()->create($validated);

        return [
            'status' => 'success',
            'data' => $resume->skills
        ];
    }

    public function autosaveLanguageSkill(StoreLanguageSkillRequest $request)
    {
        $validated = $request->validated();
        $resume = Resume::where('user_id', auth()->user()->id)->firstOrFail();

        $resume->language_skills()->create($validated);

        return [
            'status' => 'success',
            'data' => $resume->language_skills
        ];
    }
}
