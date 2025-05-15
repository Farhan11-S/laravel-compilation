<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return [
            'data' => Setting::all()->pluck('value', 'name'),
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
    public function store(StoreSettingRequest $request)
    {
        $validated = $request->validated();

        if ($validated['is_image']) {
            $filename = time() . $validated['value']->getClientOriginalName();
            $validated['value']->storeAs('public', $filename);
            $validated['value'] = $filename;
        }

        Setting::upsert([$validated], ['name'], ['value', 'is_image']);

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
    public function destroy(string $name)
    {
        Setting::where('name', $name)->delete();

        return ['message' => 'success'];
    }
}
