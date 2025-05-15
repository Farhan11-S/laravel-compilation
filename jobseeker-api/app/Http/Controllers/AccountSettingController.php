<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountSettings\StorePasswordRequest;
use App\Http\Requests\AccountSettings\UpdateEmailRequest;
use App\Http\Requests\AccountSettings\UpdateEmployerPositionRequest;
use App\Http\Requests\AccountSettings\UpdatePasswordRequest;
use App\Http\Requests\AccountSettings\UpdatePhoneRequest;
use App\Http\Requests\AccountSettings\UpdateRoleRequest;
use App\Notifications\PasswordResetNotification;
use App\Services\UserService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;

class AccountSettingController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function updateAccountRole(UpdateRoleRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $user->role_id = $validated['role_id'];
        $user->save();

        return [
            'success' => true,
            'message' => 'Tipe akun telah berhasil diubah!'
        ];
    }

    public function updateAccountEmail(UpdateEmailRequest $request)
    {
        $user = auth()->user();

        $validated = $request->validated();

        $user->email = $validated['new_email'];
        $user->save();

        return [
            'success' => true,
            'message' => 'Email telah berhasil diubah!'
        ];
    }

    public function updateAccountPassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();

        $validated = $request->validated();

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        event(new PasswordReset($user));

        return [
            'success' => true,
            'message' => 'Password telah berhasil diubah!'
        ];
    }

    public function updateAccountPhone(UpdatePhoneRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $user->phone = $this->userService->formatPhoneNumber($validated['phone']);
        $user->save();

        return [
            'success' => true,
            'message' => 'Nomor telepon telah berhasil diubah!'
        ];
    }

    public function storeAccountPassword(StorePasswordRequest $request)
    {
        $user = auth()->user();

        $validated = $request->validated();

        $user->password = Hash::make($validated['password']);
        $user->save();

        event(new PasswordReset($user));

        return [
            'success' => true,
            'message' => 'Password telah berhasil diubah!'
        ];
    }

    public function updateEmployerPosition(UpdateEmployerPositionRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $user->employerDetail()->updateOrCreate(
            ['user_id' => $user->id],
            ['position' => $validated['position']]
        );

        return [
            'success' => true,
            'message' => 'Jabatan telah berhasil diubah!'
        ];
    }
}
