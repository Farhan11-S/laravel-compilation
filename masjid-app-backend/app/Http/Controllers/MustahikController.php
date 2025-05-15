<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mustahik\StoreMustahikRequest;
use App\Models\Jemaah;
use App\Models\Mustahik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MustahikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $jemaah = Jemaah::query()
            ->has('mustahik')
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
    public function store(StoreMustahikRequest $request)
    {
        $validated = $request->validated();
        $mustahik = Mustahik::create([
            'uuid' => Str::uuid(),
            'jemaah_id' => $validated['jemaah_id'],
            'verified_by' => auth()->user()->id,
            'is_disabled' => false,
        ]);

        return [
            'message' => 'Mustahik created successfully',
            'data' => $mustahik,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $mustahik)
    {
        $jemaah = Jemaah::findOrFail($mustahik);
        $jemaah->load('mustahik');
        return [
            'message' => 'Mustahik retrieved successfully',
            'data' => $jemaah,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mustahik $mustahik)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mustahik $mustahik)
    {
        $mustahik->is_disabled = !$mustahik->is_disabled;
        $mustahik->save();

        return [
            'message' => 'Mustahik disabled/enabled successfully',
            'data' => $mustahik,
        ];
    }
}
