<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
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

        Skill::updateOrCreate([
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
    public function show(Skill $skill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Skill $skill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $skill = Skill::findOrFail($id);
        $skill->delete();

        return [
            'message' => 'success remove skill',
            'success' => true
        ];
    }
}
