<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OAuthProviderEnum;
use App\Http\Controllers\Controller;
use App\Models\SubscriberJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthProviderController extends Controller
{
    public function index(OAuthProviderEnum $provider)
    {
        if ($provider->value) {
            return Socialite::driver($provider->value)->redirect();
        }
        return Socialite::driver($provider->value)->redirect();
    }

    public function store(OAuthProviderEnum $provider)
    {
        try {
            $socialite = Socialite::driver($provider->value)->user();
            $avatar = $socialite->getAvatar();
            $link = match ($provider) {
                OAuthProviderEnum::FACEBOOK => $socialite->user['link'],
                OAuthProviderEnum::GITHUB => $socialite->user['html_url'],
                default => null,
            };


            $user = User::firstOrCreate([
                'email' => $socialite->getEmail(),
            ], [
                'name' => $socialite->getName(),
            ]);

            $user->assignRole('job seeker');

            $user->providers()->updateOrCreate([
                'provider' => $provider,
                'provider_id' => $socialite->getId(),
            ], [
                'avatar' => $avatar,
                'profile_url' => $link,
            ]);

            if($user->subscriberJob === null) {
                SubscriberJob::create([
                    'email' => $user->email,
                    'token' => null,
                    'status' => 'active',
                    'user_id' => $user->id,
                    'created_by' => null,
                    'deleted_by' => null,
                ]);
            }

            Auth::login($user);

            return redirect(config('app.frontend_url') . '/');
        } catch (\Throwable $th) {
            return redirect(config('app.frontend_url') . '/auth/job-seeker');
        }
    }
}
