<?php

return [
    'sip_domain' => env('AUSOPHONE_SIP_DOMAIN', 'pbx.ausoworld.com'),
    'ws_url'     => env('AUSOPHONE_WS_URL', 'wss://pbx.ausoworld.com:8089/ws'),

    'credential_strategy' => env('AUSOPHONE_CREDENTIAL_STRATEGY', 'static'),
    'credential_ttl'      => (int) env('AUSOPHONE_CREDENTIAL_TTL', 3600),
    'register_expires'    => (int) env('AUSOPHONE_REGISTER_EXPIRES', 300),

    'realtime_connection' => env('AUSOPHONE_REALTIME_CONNECTION', 'asterisk'),

    'ami' => [
        'enabled'  => (bool) env('AUSOPHONE_AMI_ENABLED', false),
        'host'     => env('AUSOPHONE_AMI_HOST', '127.0.0.1'),
        'port'     => (int) env('AUSOPHONE_AMI_PORT', 5038),
        'username' => env('AUSOPHONE_AMI_USER'),
        'secret'   => env('AUSOPHONE_AMI_SECRET'),
    ],

    'ice_servers' => array_values(array_filter([
        env('AUSOPHONE_STUN_URL') ? ['urls' => env('AUSOPHONE_STUN_URL')] : null,
        env('AUSOPHONE_TURN_URL') ? [
            'urls'       => env('AUSOPHONE_TURN_URL'),
            'username'   => env('AUSOPHONE_TURN_USER'),
            'credential' => env('AUSOPHONE_TURN_PASS'),
        ] : null,
    ])),

    'branding' => [
        'logo'            => env('AUSOPHONE_LOGO', '/images/logo.png'),
        'company_name'    => env('AUSOPHONE_COMPANY', 'Auso World'),
        'primary_color'   => env('AUSOPHONE_PRIMARY_COLOR', '#0f766e'),
        'show_powered_by' => (bool) env('AUSOPHONE_POWERED_BY', true),
        'theme'           => env('AUSOPHONE_THEME', 'default'),
    ],

    'recording' => [
        'browser_enabled' => (bool) env('AUSOPHONE_BROWSER_RECORDING', false),
        'disk'            => env('AUSOPHONE_RECORDING_DISK', 'local'),
        'path'            => env('AUSOPHONE_RECORDING_PATH', 'call-recordings'),
    ],

    'cdr_token' => env('AUSOPHONE_CDR_TOKEN'),
];
