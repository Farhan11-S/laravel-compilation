<?php

use Illuminate\Support\Facades\Facade;

return [
    'url' => env('XENDIT_URL', 'https://api.xendit.co/'),
    'api_key' => env('XENDIT_API_KEY', ''),
    'recurring_action' => 'PAYMENT',
    'currency' => 'IDR',
    'failed_cycle_action' => 'STOP',
    'immediate_action_type' => 'FULL_AMOUNT',
    'description' => 'Rheinjob Subscription',
];
