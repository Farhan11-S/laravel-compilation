<?php

namespace App\Providers;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class DatabaseConfigProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    public function public_storage_path($path): string
    {
        return public_path('storage/' . $path);
    }

    /**
     * Bootstrap services.
     */
    public function boot(Repository $appConfig): void
    {
        $mapper = [
            [
                'config_name' => 'laravel-google-analytics.service_account_credentials_json',
                'db_name' => 'env-google-service-account-credentials',
                'callback' => 'public_storage_path'
            ],
            [
                'config_name' => 'laravel-google-analytics.property_id',
                'db_name' => 'env-google-analytics-property-id'
            ],
        ];

        $result = [];
        if (!App::runningInConsole()) {
            $result = \DB::select('select name, value from settings');
        }

        foreach ($mapper as $map) {

            foreach ($result as $row) {
                if ($map['db_name'] == $row->name) {
                    $value = isset($map['callback'])
                        ? (
                            function_exists($map['callback'])
                            ? $map['callback']($row->value)
                            : $this->{$map['callback']}($row->value)
                        )
                        : $row->value;
                    $appConfig->set($map['config_name'], $value);
                }
            }
        }
    }
}
