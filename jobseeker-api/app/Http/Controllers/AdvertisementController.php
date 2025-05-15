<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdvertismentRequest;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return [
            'data' => Advertisement::all()
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
    public function store(StoreAdvertismentRequest $request)
    {
        $validated = $request->validated();

        if (!$validated['is_code'] && $request->hasFile('img')) {
            $filename = time() . $validated['img']->getClientOriginalName();
            $validated['img']->storeAs('public', $filename);
            $validated['img'] = $filename;
        }

        Advertisement::upsert([$validated], ['type'], ['is_code', 'link', 'img', 'code']);

        return ['message' => 'success'];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
    public function destroy(string $type)
    {
        Advertisement::where('type', $type)->delete();

        return ['message' => 'success'];
    }
}
