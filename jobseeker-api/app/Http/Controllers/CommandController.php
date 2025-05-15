<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCommandRequest;
use App\Models\Setting;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commands = Setting::where('name', 'like', 'dynamic-command:%')
            ->where('name', 'not like', '%-limit')
            ->get();

        $commands = $commands->map(function ($command) {
            $limit = Setting::where('name', $command->name . '-limit')->first() ?? 120;
            return $command->setAttribute('limit', $limit->value);
        });
        return [
            'message' => 'Successfully fetched all commands',
            'data' => $commands
        ];
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
    public function show(string $id)
    {
        $command = Setting::where('id', $id)->firstOrFail();
        $limit = Setting::where('name', $command->name . '-limit')->first();
        $command->setAttribute('limit', $limit?->value ?? 120);
        return [
            'message' => 'Successfully fetched command',
            'data' => $command,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommandRequest $request, string $id)
    {
        $validated = $request->validated();
        $command = Setting::where('id', $id)->first();
        $limit = Setting::where('name', $command->name . '-limit')->update(['value' => $validated['limit']]);

        $command->update(['value' => $validated['value']]);

        return [
            'message' => 'Successfully updated command',
            'data' => [
                'command' => $command,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
