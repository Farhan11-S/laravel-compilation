<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Resume;
use Illuminate\Http\Request;

class CertificationController extends Controller
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

        Certification::updateOrCreate([
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
    public function show(Certification $certification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certification $certification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certification $certification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cert= Certification::findOrFail($id);
        $cert->delete();

        return [
            'message' => 'success remove certification',
            'success' => true
        ];
    }
}
