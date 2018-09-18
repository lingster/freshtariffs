<?php

return [
    /*
    |----------------------------------------------------------------------------
    | Google application name
    |----------------------------------------------------------------------------
    */
    'application_name' => 'Fresh Tariffs',

    /*
    |----------------------------------------------------------------------------
    | Google OAuth 2.0 access
    |----------------------------------------------------------------------------
    |
    | Keys for OAuth 2.0 access, see the API console at
    | https://developers.google.com/console
    |
    */
    'client_id'       => '556038572184-665qbvof3e9tlktg86u20cgfjnqdqtb7.apps.googleusercontent.com',
    'client_secret'   => 'I0Dj6q2RYdsYDA2rbbiPbSt5',
    'redirect_uri'    => 'http://panel.freshtariffs.com/staff/settings/integrations/callback/' . IntegrationService::SERVICE_GOOGLE_DRIVE,
    'scopes'          => [Google_Service_Oauth2::USERINFO_EMAIL, Google_Service_Drive::DRIVE],
    'access_type'     => 'offline',
    'approval_prompt' => 'force',

    /*
    |----------------------------------------------------------------------------
    | Google developer key
    |----------------------------------------------------------------------------
    |
    | Simple API access key, also from the API console. Ensure you get
    | a Server key, and not a Browser key.
    |
    */
    'developer_key' => '',

    /*
    |----------------------------------------------------------------------------
    | Google service account
    |----------------------------------------------------------------------------
    |
    | Set the information below to use assert credentials
    | Leave blank to use app engine or compute engine.
    |
    */
    'service' => [
        /*
        | Example xxx@developer.gserviceaccount.com
        */
        'account' => '',

        /*
        | Example ['https://www.googleapis.com/auth/cloud-platform']
        */
        'scopes' => [],

        /*
        | Path to key file
        | Example storage_path().'/key/google.p12'
        */
        'key' => '',
    ],
];
