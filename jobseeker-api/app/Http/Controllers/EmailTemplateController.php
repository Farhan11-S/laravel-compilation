<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmailTemplateRequest;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $search = $query['search'] ?? '';
        $limit = $query['limit'] ?? 0;

        $data = EmailTemplate::when(
            $search,
            function (Builder $query) use ($search) {
                $query->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search) . '%');
            }
        )->paginate($limit);

        return [
            'data' => $data
        ];
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return [
            'data' => $emailTemplate
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmailTemplateRequest $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validated();

        $emailTemplate->update($validated);

        return $emailTemplate;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        //
    }
}
