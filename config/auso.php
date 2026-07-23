<?php

return [
    'api_url' => env('CALL_SERVER_API_URL', null),
    'mrk_api_url' => env('MRK_API_URL', null),
    'cdr_ref' => env('CDR_REF', 'singer'),
    'allow_skill_change' => env('ALLOW_SKILL_CHANGE_C', true),
    'auto_logout_queue' => env('AUTO_LOGOUT_QUEUE', false),
    'auto_logout_mins' => env('AUTO_LOGOUT_MINS', 1),
    'ticket_sla_enabled' => env('TICKET_SLA_ENABLED', true),
    'external_extension_url' => env('EXTERNAL_EXTENSION_URL',false ),
    'phone_type' => env('PHONE_TYPE', 'microsip'),
    'phone_auto_register' => env('PHONE_AUTO_REGISTER', false),
    'softphone_server' => env('SOFTPHONE_SERVER', '123.231.74.22'),
    'softphone_password' => env('SOFTPHONE_PASSWORD', '@u5051p'),
    'softphone_autoanswerdelay' => env('SOFTPHONE_AUTOANSWERDELAY', '3'),

];
