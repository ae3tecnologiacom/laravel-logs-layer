<?php
/*
|--------------------------------------------------------------------------
| To publish the config files
|--------------------------------------------------------------------------
|
| Execute the command below do publish the config file
| php artisan vendor:publish --provider="Ae3\LaravelLogsLayer\app\Providers\LogsLayerServiceProvider" --tag="config"
*/

return [
    'queue' => [
        'enabled' => env('LOG_QUEUE_ENABLED', false),
        'retry_until_in_minutes' => env('LOG_QUEUE_RETRY_UNTIL_IN_MINUTES', 60),
        'backoff' => explode(',', env('LOG_QUEUE_BACKOFF', '15'))
    ],
    'sensitive_data' => env('LOGS_LAYER_SENSITIVE_DATA', 'password,password_confirmation,token,api_token,api_key,access_token,refresh_token,authorization_code,client_secret'),
    'server_ip' => env('SERVER_IP', '127.0.0.1')
];
