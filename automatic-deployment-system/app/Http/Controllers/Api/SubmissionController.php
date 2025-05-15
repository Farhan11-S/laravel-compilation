<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\StoreSubmissionRequest;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get request query 
        $queryUser = $request->query('user-search');
        $queryAssignment = $request->query('assignment-search');

        $data = Submission::with([
            'submitted_by',
            'assignment',
            'grade',
        ])
            ->select('id', 'folder_path', 'created_at', 'updated_at', 'assignment_id', 'submitted_by')
            ->when($queryUser, function ($q) use ($queryUser) {
                // Search user name from query value 
                $q->whereHas('user', function ($subQuery) use ($queryUser) {
                    $subQuery->whereAny([
                        'name',
                        'nim',
                        'department'
                    ], 'like', '%' . $queryUser . '%');
                });
            })
            ->when($queryAssignment, function ($q) use ($queryAssignment) {
                // Search assignment name from query value 
                $q->whereHas('assignment', function ($subQuery) use ($queryAssignment) {
                    $subQuery->where('title', 'like', '%' . $queryAssignment . '%');
                });
                $q->distinct('submitted_by');
            })
            ->latest()
            ->get();

        $data = $data->unique(function ($item) {
            return $item['submitted_by'] . '-' . $item['assignment_id'];
        })->flatten();

        $paginate = PaginationHelper::paginate($data, $request->limit ?? 10);

        return response()->json([
            'data' => $paginate,
            'message' => 'Berhasil mengambil data',
            'status' => 'success'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubmissionRequest $request)
    {
        // 1. Data validasi sudah otomatis divalidasi oleh StoreSubmissionRequest
        $validated = $request->validated();

        // 2. Proses upload file ke dalam storage submissions/id_peserta/assignment/file
        if ($request->hasFile('files')) {
            $file_list = [];
            $userID = auth()->user()->id;
            $destinationPath = "submissions/{$userID}/{$validated['assignment_id']}";

            // Remove all files in the destination path
            Storage::disk('public')->deleteDirectory($destinationPath);

            foreach ($request->file('files') as $file) {
                $filename = $file->getClientOriginalName();
                $uploadedFilePath = $file->storeAs($destinationPath, $filename, 'public');
                array_push($file_list, $uploadedFilePath);
            }
        }

        // 3. Simpan data ke database
        $submission = Submission::create([
            'assignment_id' => $validated['assignment_id'], // id assignment atau tugas
            'submitted_by' => $userID, // id peserta
            'folder_path' => $destinationPath, // lokasi file yang disimpan
            'file_list' => $file_list, // daftar file yang diupload
        ]);
        // 4. Berikan respons berhasil
        return response()->json([
            'message' => 'Submit berhasil',
            'data' => $submission
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Submission $submission)
    {
        /**
         * Ensure that the data is correctly in array form
         */
        $grade_criteria = json_decode($submission->grade->criterias ?? null, true);
        $assignment_criteria = json_decode($submission->assignment->criterias, true);

        // Mapping object to ID value
        $grade_criteria_id = isset($grade_criteria) ? array_column($grade_criteria, 'assignment_criteria_id') : [];

        /**
         * Verify the criteria contained within by comparing the grade criteria ID and assignment criteria.
         */
        $criterias = array_map(function ($criteria) use ($grade_criteria_id) {
            $criteria['status'] = in_array($criteria['id'], $grade_criteria_id);
            return $criteria;
        }, $assignment_criteria);


        // Read all file paths uploaded
        $all_file_path = [];

        // Validate if path is exist
        try {
            $all_file_path = scandir(storage_path('/app/private' . $submission->folder_path));
        } catch (\Exception $e) {
        }

        $submission->files = $all_file_path;
        $submission->criterias = $criterias;

        return response()->json([
            'data' => $submission,
            'message' => 'Berhasil mengambil informasi submisi',
            'status' => 'success',
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
