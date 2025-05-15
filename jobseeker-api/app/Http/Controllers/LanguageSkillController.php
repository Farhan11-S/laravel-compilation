<?php

namespace App\Http\Controllers;

use App\Models\LanguageSkill;
use App\Models\Resume;
use Illuminate\Http\Request;

class LanguageSkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store($request, string $resume_id)
    {
        if (empty($resume_id) || !Resume::find($resume_id)) {
            return [
                'message' => 'relation not found',
                'success' => false
            ];
        }
        $id = $request['id'] ?? 0;
        $request['resume_id'] = $resume_id;

        LanguageSkill::updateOrCreate([
            'id' => $id,
        ], $request);
        return [
            'message' => 'success creating certifications',
            'success' => true
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(LanguageSkill $skill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LanguageSkill $skill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LanguageSkill $skill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lang = LanguageSkill::findOrFail($id);
        $lang->delete();

        return [
            'message' => 'success remove language skill',
            'success' => true
        ];
    }
}
