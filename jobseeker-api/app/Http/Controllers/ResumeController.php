<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resumes\StoreResumeRequest;
use App\Http\Requests\Resumes\UpdateResumeRequest;
use App\Models\Resume;
use App\Services\AvatarService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;

class ResumeController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $avatarService = new AvatarService();

        $resume = Resume::firstOrCreate(['user_id' => auth()->user()->id]);

        $resume->certifications;
        $resume->educations;
        $resume->skills;
        $resume->language_skills;
        $resume->user_detail;
        $resume->work_experiences;
        $resume->user;

        if (!empty($resume->user)) $resume->user = $avatarService->getUserWithAvatar($resume->user);

        $resume->user?->makeVisible(['phone']);

        if (!empty($resume->user_detail) && $resume->user_detail != null) {
            $resume->user_detail?->makeVisible(['street_address']);
        }

        return $resume;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResumeRequest $request)
    {
        $CController = new CertificationController();
        $EController = new EducationController();
        $SController = new SkillController();
        $LSController = new LanguageSkillController();
        $WEController = new WorkExperienceController();
        $validated = $request->validated();
        DB::transaction(function () use ($validated, $WEController, $CController, $EController, $SController, $LSController) {
            $resume = Resume::firstOrCreate(['user_id' => auth()->user()->id]);
            $certificationsID = $resume->certifications->pluck('id')->all();
            $educationsID = $resume->educations->pluck('id')->all();
            $skillsID = $resume->skills->pluck('id')->all();
            $langSkillsID = $resume->language_skills()->pluck('id')->all();
            $work_experiencesID = $resume->work_experiences->pluck('id')->all();
            foreach ($validated['certifications'] as $certification) {
                $singularID = $certification['id'] ?? 0;
                $CController->store($certification, $resume->id);

                if (($key = array_search($singularID, $certificationsID)) !== false) {
                    unset($certificationsID[$key]);
                }
            }

            foreach ($validated['educations'] as $education) {
                $singularID = $education['id'] ?? 0;
                $EController->store($education, $resume->id);

                if (($key = array_search($singularID, $educationsID)) !== false) {
                    unset($educationsID[$key]);
                }
            }

            foreach ($validated['skills'] as $skill) {
                $singularID = $skill['id'] ?? 0;
                $SController->store($skill, $resume->id);

                if (($key = array_search($singularID, $skillsID)) !== false) {
                    unset($skillsID[$key]);
                }
            }

            foreach ($validated['language_skills'] as $language_skill) {
                $singularID = $language_skill['id'] ?? 0;
                $LSController->store($language_skill, $resume->id);

                if (($key = array_search($singularID, $langSkillsID)) !== false) {
                    unset($langSkillsID[$key]);
                }
            }

            foreach ($validated['work_experiences'] as $work_experience) {
                $singularID = $work_experience['id'] ?? 0;
                $WEController->store($work_experience, $resume->id);

                if (($key = array_search($singularID, $work_experiencesID)) !== false) {
                    unset($work_experiencesID[$key]);
                }
            }

            foreach ($certificationsID as $id) {
                $CController->destroy($id);
            }

            foreach ($educationsID as $id) {
                $EController->destroy($id);
            }

            foreach ($skillsID as $id) {
                $SController->destroy($id);
            }

            foreach ($langSkillsID as $id) {
                $LSController->destroy($id);
            }

            foreach ($work_experiencesID as $id) {
                $WEController->destroy($id);
            }

            $user_detail = $validated['user_detail'];
            if (!empty($user_detail['date_of_birth'])) {
                $convertDateToTimeTo = strtotime($user_detail['date_of_birth']);
                $user_detail['date_of_birth'] = date('Y-m-d', $convertDateToTimeTo);
            }

            $resume->user_detail()->create($user_detail);
            $resume->is_complete = 1;

            $resume->save();
        });

        return [
            'success' => true,
            'message' => 'Berhasil menyimpan resume!'
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Resume $resume)
    {
        $avatarService = new AvatarService();

        $resume->certifications;
        $resume->educations;
        $resume->skills;
        $resume->language_skills;
        $resume->user_detail;
        $resume->work_experiences;
        $resume->user;

        $resume->user?->makeVisible(['phone']);
        if (!empty($resume->user_detail) && $resume->user_detail != null) {
            $resume->user_detail?->makeVisible(['street_address']);
        }

        if (!empty($resume->user)) $resume->user = $avatarService->getUserWithAvatar($resume->user);

        return [
            'data' => $resume
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resume $resume)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResumeRequest $request, Resume $resume)
    {
        $CController = new CertificationController();
        $EController = new EducationController();
        $SController = new SkillController();
        $LSController = new LanguageSkillController();
        $WEController = new WorkExperienceController();
        $validated = $request->validated();
        $phone = $this->userService->formatPhoneNumber($validated['user']['phone']);
        $resume->user->phone = $phone;
        DB::transaction(function () use ($resume, $validated, $WEController, $CController, $EController, $SController, $LSController) {
            $certificationsID = $resume->certifications->pluck('id')->all();
            $educationsID = $resume->educations->pluck('id')->all();
            $skillsID = $resume->skills->pluck('id')->all();
            $langSkillsID = $resume->language_skills()->pluck('id')->all();
            $work_experiencesID = $resume->work_experiences->pluck('id')->all();

            foreach ($validated['certifications'] as $certification) {
                $singularID = $certification['id'] ?? 0;
                $CController->store($certification, $resume->id);

                if (($key = array_search($singularID, $certificationsID)) !== false) {
                    unset($certificationsID[$key]);
                }
            }

            foreach ($validated['educations'] as $education) {
                $singularID = $education['id'] ?? 0;
                $EController->store($education, $resume->id);

                if (($key = array_search($singularID, $educationsID)) !== false) {
                    unset($educationsID[$key]);
                }
            }

            foreach ($validated['skills'] as $skill) {
                $singularID = $skill['id'] ?? 0;
                $SController->store($skill, $resume->id);

                if (($key = array_search($singularID, $skillsID)) !== false) {
                    unset($skillsID[$key]);
                }
            }

            foreach ($validated['language_skills'] as $language_skill) {
                $singularID = $language_skill['id'] ?? 0;
                $LSController->store($language_skill, $resume->id);

                if (($key = array_search($singularID, $langSkillsID)) !== false) {
                    unset($langSkillsID[$key]);
                }
            }

            foreach ($validated['work_experiences'] as $work_experience) {
                $singularID = $work_experience['id'] ?? 0;
                $WEController->store($work_experience, $resume->id);

                if (($key = array_search($singularID, $work_experiencesID)) !== false) {
                    unset($work_experiencesID[$key]);
                }
            }

            foreach ($certificationsID as $id) {
                $CController->destroy($id);
            }

            foreach ($educationsID as $id) {
                $EController->destroy($id);
            }

            foreach ($skillsID as $id) {
                $SController->destroy($id);
            }

            foreach ($langSkillsID as $id) {
                $LSController->destroy($id);
            }

            foreach ($work_experiencesID as $id) {
                $WEController->destroy($id);
            }

            $resume->is_shared = $validated['is_shared'];
            $resume->cv_template = $validated['cv_template'];

            $user_detail = $validated['user_detail'];
            if (!empty($user_detail['date_of_birth'])) {
                $convertDateToTimeTo = strtotime($user_detail['date_of_birth']);
                $user_detail['date_of_birth'] = date('Y-m-d', $convertDateToTimeTo);
            }

            $resume->user_detail->update($user_detail);
            $resume->save();
            $resume->user->save();
        });
        $resume->save();
        return [
            'success' => true,
            'message' => 'Berhasil diubah!'
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $CController = new CertificationController();
        $EController = new EducationController();
        $SController = new SkillController();
        $LSController = new LanguageSkillController();
        $WEController = new WorkExperienceController();

        $explodedID = explode("-", $id);

        $response = [
            'success' => false,
            'message' => 'Data belum terubah'
        ];
        if (empty($explodedID[1])) abort(404);

        switch ($explodedID[0]) {
            case 'work_experience':
                $response = $WEController->destroy($explodedID[1]);
                break;

            case 'certification':
                $response = $CController->destroy($explodedID[1]);
                break;

            case 'skill':
                $response = $SController->destroy($explodedID[1]);
                break;

            case 'language_skill':
                $response = $LSController->destroy($explodedID[1]);
                break;

            case 'education':
                $response = $EController->destroy($explodedID[1]);
                break;

            default:
                abort(404);
                break;
        }

        return $response;
    }
}
