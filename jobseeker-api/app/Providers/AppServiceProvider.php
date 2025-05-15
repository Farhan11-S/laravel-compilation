<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $larasaps = Setting::where('name', 'LIKE', '%larasap.%')->pluck('value', 'name')->toArray();
        $wassengers = Setting::where('name', 'LIKE', '%wassenger.authorisation.%')->pluck('value', 'name')->toArray();

        Config::set($larasaps);
        Config::set($wassengers);
    }
}
