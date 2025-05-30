<?php

/*
    |--------------------------------------------------------------------------
    | Configuration file for Laravel Application
    |--------------------------------------------------------------------------
    |
    | Publish this config into Laravel Config Directory
    | Check Documentation
    |
    */

return [

    /*
    |--------------------------------------------------------------------------
    | Authorisation
    |--------------------------------------------------------------------------
    |
    | User API key token needs to be used on every API request that requires
    | authentication. Get or create your API key from the Web Console. 
    | Note: chat agents and supervisors have no access to the API.
    |
    */

    'authorisation' => [
        'api_key' => env('WASSENGER_API_KEY', ''),
        'api_host'   => env('WASSENGER_API_URL', 'https://api.wassenger.com'),
        'api_version' => 1,
        'default_device' => env('DEFAULT_DEVICE', ''),
        'channel_id' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP CLIENT CONFIGURATION
    |--------------------------------------------------------------------------
    |
    |
    */

    'http_client' => [
        'return_json_errors' => false,
    ],
];
