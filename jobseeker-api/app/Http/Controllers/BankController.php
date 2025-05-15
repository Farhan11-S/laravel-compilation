<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Bank\StoreBankRequest;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return [
            'data' => Bank::all(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $filename = time() . $validated['logo']->getClientOriginalName();
            $validated['logo']->storeAs('public', $filename);
            $validated['logo'] = $filename;
        }

        $bank = Bank::create($validated);

        return [
            'data' => $bank,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        return [
            'data' => $bank,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $filename = time() . $validated['logo']->getClientOriginalName();
            $validated['logo']->storeAs('public', $filename);
            $validated['logo'] = $filename;
        }

        $bank->update($validated);

        return [
            'data' => $bank,
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();

        return [
            'data' => $bank,
        ];
    }
}
