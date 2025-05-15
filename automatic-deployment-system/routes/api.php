<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::apiResource('/assignments', \App\Http\Controllers\Api\AssignmentController::class);
    Route::post('/change-password', [\App\Http\Controllers\Api\AuthController::class, 'changePassword']);
    Route::apiResource('/submissions', \App\Http\Controllers\Api\SubmissionController::class);
    Route::get('/user-submissions', function () {
        return \App\Models\Submission::where('submitted_by', auth()->id())
            ->latest()  // Mengurutkan berdasarkan created_at terbaru
            ->get()
            ->unique('assignment_id')  // Memfilter duplikat berdasarkan assignment_id
            ->values();
    });
    Route::apiResource('/submissions/{submission}/grades', \App\Http\Controllers\Api\GradeController::class);
});

Route::get('/list-user-submit/{assignment_id}', function ($assignment_id) {
    $doer = \App\Models\User::whereHas('submissions', function ($query) use ($assignment_id) {
        $query->where('assignment_id', $assignment_id);
    })
        ->with([
            'submissions' => fn($q) =>
            $q->where('assignment_id', $assignment_id)
                ->orderBy('created_at', 'desc'),
            'submissions.grade'
        ])
        ->get();

    $doer->map(function ($item) {
        $item->criteria_score = $item->submissions->first()->grade?->grade;
        $item->aestethic_score = $item->submissions->first()->grade?->aesthetic_score;
        unset($item->submissions);
        return $item;
    });

    return [
        'data' => $doer,
        'count' => $doer->count(),
        'message' => 'Data user yang belum submit tugas',
    ];
});
