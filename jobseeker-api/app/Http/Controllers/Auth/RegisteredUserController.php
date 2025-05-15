<?php

namespace App\Http\Controllers\Auth;

use App\Constants\Roles;
use App\Enums\CouponType;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Role;
use App\Models\SubscriberJob;
use App\Models\User;
use App\Notifications\PostRegisterCongratulary;
use App\Services\UserService;
use DateTime;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['nullable', 'string', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'coupon_code' => ['nullable', 'string'],
            'role_id' => ['required', 'integer', 'exists:' . Role::class . ',id'],
            'company_name' => ['required_if:role_id,' . Roles::EMPLOYER, 'string'],
            'company_industry' => ['required_if:role_id,' . Roles::EMPLOYER, 'string'],
            'company_logo' => ['nullable'],
            'coupon_code' => ['nullable', 'string']
        ]);

        $request->phone = (new UserService())->formatPhoneNumber($request->phone);
        $logoURL = null;
        if ($request->hasFile('company_logo')) {
            $filename = time() . $request['company_logo']->getClientOriginalName();
            $request['company_logo']->storeAs('public', $filename);
            $logoURL = $filename;
        }

        DB::transaction(function () use ($request, $logoURL) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role_id' => $request->role_id,
            ]);

            $role = Role::findById($request->role_id, 'web');
            $user->assignRole($role->name);

            if ($request->role_id == Roles::EMPLOYER) {
                $company = Company::firstOrCreate([
                    'name' => $request->company_name,
                ], [
                    'industry' => $request->company_industry,
                    'logo' => $logoURL,
                ]);

                $user['company_id'] = $company->id;
                $user->save();
            }

            $today = (new DateTime(now()))->format('Y-m-d');
            $coupon = empty($request->coupon_code) ? $request->coupon_code : null;
            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('type', CouponType::REGISTRATION)
                ->whereNotNull('value')
                ->whereDate('expired_at', '>=', $today)
                ->first();
            // $referral = User::where('referral_code', $request->coupon_code)->first('id');

            // if($coupon->expired_at >= now()->day()) {
            //     throw ValidationException::withMessages(['field_name' => 'This value is incorrect']);
            // }

            if ($coupon) {
                $endedAt = now()->addDays(7);
                if ($coupon) {
                    $endedAt = now()->addDays($coupon->duration ?? 7);
                }

                CouponUsage::create([
                    'coupon_id' => $coupon?->id,
                    'referral_id' => null,
                    'type' => $coupon ? 'coupon' : 'referral',
                    'user_id' => $user->id,
                    'ended_at' => $endedAt,
                ]);
                $user->package_type = 1;
                $user->save();
            }

            // SubscriberJob::create([
            //     'email' => $user->email,
            //     'token' => null,
            //     'status' => 'active',
            //     'user_id' => $user->id,
            //     'created_by' => null,
            //     'deleted_by' => null,
            // ]);

            event(new Registered($user));

            $user->notify(new PostRegisterCongratulary());

            Auth::login($user);
        });

        return response()->noContent();
    }
}
