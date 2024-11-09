<?php

return [
    'api_url' => env('CALL_SERVER_API_URL', null),
    'mrk_api_url' => env('MRK_API_URL', null),
    'cdr_ref' => env('CDR_REF', 'singer'),
    'allow_skill_change' => env('ALLOW_SKILL_CHANGE_C', true),
    'auto_logout_queue' => env('AUTO_LOGOUT_QUEUE', false),
    'auto_logout_mins' => env('AUTO_LOGOUT_MINS', 1),

];
