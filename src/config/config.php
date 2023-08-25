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
    'sensitive_data' => env('LOGS_LAYER_SENSITIVE_DATA', 'password,password_confirmation,token,api_token,api_key,access_token'),
];
