<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Grade\StoreGradeRequest;
use App\Http\Requests\Grade\UpdateGradeRequest;
use App\Models\GradeCriteria;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AssignmentCriteria;
use App\Models\Grade;
use App\Models\Submission;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Submission $submission, StoreGradeRequest $request)
    {
        // Get body
        $validated = $request->validated();

        // validate assignment
        if (Grade::where("submission_id", $submission->id)->first()) {
            return response()->json([
                "message" => "Tugas ini telah dinilai",
                "status" => "failed"
            ], 405);
        }

        // find criteria
        $criterias = AssignmentCriteria::where("assignment_id", $submission->assignment_id)->get();

        // Validate criteria IDs from the request body
        $criteriaIds = $criterias->pluck('id')->toArray();
        foreach ($validated['criterias'] as $criteria_id) {
            if (!in_array($criteria_id, $criteriaIds)) {
                return response()->json([
                    "message" => "Kriteria tidak ditemukan",
                    "status" => "failed"
                ], 404);
            }
        }

        // Calculate grade
        $grade_calculate = ceil(count($validated['criterias']) / count($criterias) * 100);
        
        // Insert criteria & create grade
        DB::transaction(function () use ($validated, $submission, $grade_calculate) {
            $aesthetic_score = $validated['aesthetic_score'] ?? 0;
            $grade = Grade::create([
                "created_by" => auth()->user()->id,
                "submission_id" => $submission->id,
                "grade" => $grade_calculate,
                "note" => $validated["note"],
                "aesthetic_score" => $aesthetic_score,
            ]);

            // Prepare grade criteria relations data 
            $gradeCriterias = [];
            foreach ($validated['criterias'] as $criteria_id) {
                $gradeCriterias[] = [
                    "grade_id" => $grade->id,
                    "assignment_criteria_id" => $criteria_id
                ];
            }
            
            GradeCriteria::insert($gradeCriterias);
        });

        return response()->json([
            "message" => "Penilaian telah berhasil",
            "data" => [
                "note" => $validated["note"],
                "created_by" => [
                    "name" => auth()->user()->name
                ],
                "grade" => $grade_calculate
            ],
            "status" => "success"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGradeRequest $request, Grade $grade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade)
    {
        //
    }
}
