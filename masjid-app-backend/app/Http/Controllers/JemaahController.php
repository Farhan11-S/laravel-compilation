<?php

namespace App\Http\Controllers;

use App\Http\Requests\Jemaah\StoreJemaahRequest;
use App\Http\Requests\Jemaah\UpdateJemaahRequest;
use App\Models\Jemaah;
use Illuminate\Http\Request;

class JemaahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $jemaah = Jemaah::query()
            ->whereDoesntHave('mustahik')
            ->where(function ($query) use ($search) {
                $query->where('nik', 'LIKE', "%$search%")
                    ->orWhere('nama', 'LIKE', "%$search%");
            })
            ->paginate(10);

        return $jemaah;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJemaahRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $validated['pic'] = $user->name;
        $validated['submitted_by'] = $user->id;
        $vadated['keterangan'] = $validated['keterangan'] ?? '';
        $validated['clusters'] = auth()->user()->cluster->name ?? '';

        $jemaah = Jemaah::create($validated);

        return [
            'message' => 'Jemaah created successfully',
            'data' => $jemaah,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Jemaah $jemaah)
    {
        return [
            'message' => 'Jemaah retrieved successfully',
            'data' => $jemaah,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJemaahRequest $request, Jemaah $jemaah)
    {
        $validated = $request->validated();

        $jemaah->update($validated);

        return $jemaah;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jemaah $jemaah)
    {
        return $jemaah;
    }
}
