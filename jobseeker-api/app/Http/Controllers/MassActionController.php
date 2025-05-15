<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SubjectMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MassActionController extends Controller
{
    public $nonAdminDeletable = ['jobs', 'blogs'];
    public function massDelete(Request $request, string $table)
    {
        $request->validate([
            'ids' => ['required', 'array'],
        ]);

        if (!auth()->user()->isSuperadmin() && !in_array($table, $this->nonAdminDeletable)) {
            abort(403);
        }

        DB::table($table)->whereIn('id', $request['ids'])
            ->update([
                'deleted_at' => now(),
                'deleted_by' => auth()->user()->id
            ]);
    }

    public function massRestore(Request $request, string $table)
    {
        $request->validate([
            'ids' => ['required', 'array'],
        ]);

        if (!auth()->user()->isSuperadmin() && !in_array($table, $this->nonAdminDeletable)) {
            abort(403);
        }

        DB::table($table)->whereIn('id', $request['ids'])
            ->whereNotNull('deleted_at')
            ->update([
                'deleted_at' => null,
                'deleted_by' => null
            ]);
    }

    public function massSendEmail(Request $request)
    {
        $request->validate([
            'user_ids' => ['required', 'array'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'img' => ['nullable', 'image', 'max:2048'],
        ]);

        $imageName = null;
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public', $imageName);
        }

        $users = User::whereIn('id', $request['user_ids'])->get();

        $users->each(function (User $user) use ($request, $imageName) {
            $user->notify(new SubjectMessageNotification($request['subject'], $request['message'], $imageName));
        });
    }
}
