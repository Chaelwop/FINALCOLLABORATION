<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Two Factor Authentication
    |--------------------------------------------------------------------------
    |
    | Policy for the SMS based two factor authentication. Codes are short
    | lived, single use and can only be attempted a limited number of times
    | before the user has to request a new one.
    |
    */

    'two_factor' => [
        'enabled' => env('TWO_FACTOR_ENABLED', true),
        'code_length' => (int) env('TWO_FACTOR_CODE_LENGTH', 6),
        'code_ttl' => (int) env('TWO_FACTOR_CODE_TTL', 300),
        'max_attempts' => (int) env('TWO_FACTOR_MAX_ATTEMPTS', 5),
        'resend_interval' => (int) env('TWO_FACTOR_RESEND_INTERVAL', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Delivery
    |--------------------------------------------------------------------------
    |
    | The driver used to deliver verification codes. The "twilio" driver
    | sends real messages through the Twilio API, while the "log" driver
    | writes the message to the application log instead.
    |
    */

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
    ],

];
