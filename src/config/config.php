<?php
/*
|--------------------------------------------------------------------------
| To publish the config files
|--------------------------------------------------------------------------
|
| Execute the command below do publish the config file
| php artisan vendor:publish --provider="Ae3\LogsLayer\app\Providers\LogsLayerServiceProvider" --tag="config"
*/

return [
    'logstash' => [
        'environments' => env('LOGSTASH_ENVIRONMENTS', 'production')
    ]
];
