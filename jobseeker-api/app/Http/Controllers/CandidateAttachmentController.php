<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateAttachment\UpdateCandidateAttachmentRequest;
use App\Http\Requests\CandidateAttachment\StoreCandidateAttachmentRequest;
use App\Models\Candidate;
use App\Models\CandidateAttachment;
use App\Models\User;
use App\Notifications\CandidateAttachment as NotificationsCandidateAttachment;

class CandidateAttachmentController extends Controller
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
    public function store(StoreCandidateAttachmentRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->user()->id;

        $jobSeeker = User::firstWhere('id', $validated['notifiable_id']);
        $candidate = Candidate::firstWhere('id', $validated['candidate_id']);
        $jobSeeker->notify(new NotificationsCandidateAttachment($validated['content_type'], $validated['content_link'], $candidate));

        unset($validated['notifiable_id']);
        $candidateAttachment = CandidateAttachment::create($validated);

        return response()->json([
            'message' => 'Attachment created successfully',
            'data' => $candidateAttachment
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CandidateAttachment $candidateAttachment)
    {
        return response()->json([
            'data' => $candidateAttachment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCandidateAttachmentRequest $request, CandidateAttachment $candidateAttachment)
    {
        $validated = $request->validated();
        foreach ($validated['user_attachments'] as $index => $attachment) {
            if ($request->hasFile('user_attachments.' . $index)) {
                $filename = time() . $attachment->getClientOriginalName();
                $attachment->storeAs('public', $filename);
                $validated['user_attachments'][$index] = $filename;
            }
        }
        $candidateAttachment->update($validated);

        return response()->json([
            'message' => 'Attachment updated successfully',
            'data' => $candidateAttachment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CandidateAttachment $candidateAttachment)
    {
        //
    }
}
