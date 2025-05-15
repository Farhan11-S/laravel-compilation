<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\WorkExperience;
use Illuminate\Http\Request;

class WorkExperienceController extends Controller
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
        $convertDateToTimeFrom = strtotime($request['from']);
        $request['from'] = date('Y-m-d', $convertDateToTimeFrom);

        if (!empty($request['to'])) {
            $convertDateToTimeTo = strtotime($request['to']);
            $request['to'] = date('Y-m-d', $convertDateToTimeTo);
        }

        WorkExperience::updateOrCreate([
            'id' => $id,
        ], $request);
        return [
            'message' => 'success creating work experiences',
            'success' => true
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkExperience $workExperience)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkExperience $workExperience)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkExperience $workExperience)
    {
        // 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workExperience = WorkExperience::findOrFail($id);
        $workExperience->delete();

        return [
            'message' => 'success remove work experience',
            'success' => true
        ];
    }
}
