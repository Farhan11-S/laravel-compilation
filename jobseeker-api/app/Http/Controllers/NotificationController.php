<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Models\InterviewSchedule;
use App\Models\User;
use App\Notifications\SubjectMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification as Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications->toArray();

        $canBeRead = auth()->user()
            ->unreadNotifications
            ->filter(fn ($value) => @$value->data['reference_type'] != InterviewSchedule::class);

        foreach ($canBeRead as $notification) {
            $notification->markAsRead();
        }

        return [
            'success' => true,
            'data' => $notifications,
            'message' => 'Berhasil mengambil notifikasi!'
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificationRequest $request)
    {
        $validated = $request->validated();

        if ($validated['type'] == 'subject-message-notification') {
            $user = User::firstWhere('id', $validated['notifiable_id']);

            $user->notify(new SubjectMessageNotification($validated['data']['subject'], $validated['data']['message']));

            return [
                'success' => true,
                'message' => 'Notifikasi berhasil dikirim!',
            ];
        } else {
            abort(404, 'Tipe notifikasi tidak ditemukan!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
