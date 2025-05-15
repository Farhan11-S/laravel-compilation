<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Assignment;
use App\Models\AssignmentCriteria;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = Assignment::with('criterias')->get();
        return response()->json(
            [   'status' => 'success',
                'message' => 'Data berhasil ditampilkan',
                'data' => $assignments
            ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest $request)
    {
        $validated = $request->validated();
        DB::transaction(function () use ($validated) {
            $assignment = Assignment::create([
                'title' => $validated['title'],
                'user_id' => auth()->user()->id,
            ]);

            foreach ($validated['criterias'] as $criteria) {
                AssignmentCriteria::create([
                    'assignment_id' => $assignment->id,
                    'criteria' => $criteria,
                ]);
            }
        });

        return response()->json(
            [   'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'data' => $validated
            ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        $assignment->load('criterias');
        return response()->json(
            [   'status' => 'success',
                'message' => 'Data berhasil ditampilkan',
                'data' => $assignment
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        $validated = $request->validated();
        DB::transaction(function () use ($validated, $assignment) {
            $assignment->update([
                'title' => $validated['title'],
            ]);

            $assignment->criterias()->delete();
            foreach ($validated['criterias'] as $criteria) {
                AssignmentCriteria::create([
                    'assignment_id' => $assignment->id,
                    'criteria' => $criteria,
                ]);
            }
        });

        return response()->json(
            [   'status' => 'success',
                'message' => 'Data berhasil diupdate',
                'data' => $assignment -> load('criterias')
            ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $assignment -> delete();
        return response()->json(
            [   'status' => 'success',
                'message' => 'Data berhasil dihapus',
                'data' => $assignment
            ], 200);
    }
}
