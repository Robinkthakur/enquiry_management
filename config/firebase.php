<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    */
    'project_id' => env('FIREBASE_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Service Account Credentials Path
    |--------------------------------------------------------------------------
    |
    | Path to the Firebase service account credentials JSON file relative
    | to the application root.
    |
    */
    'credentials_path' => env('FIREBASE_CREDENTIALS_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Service Account Credentials JSON String
    |--------------------------------------------------------------------------
    |
    | Direct JSON string containing service account credentials. Useful for
    | deployments where file writing is restricted.
    |
    */
    'credentials_json' => env('FIREBASE_CREDENTIALS_JSON'),
];
