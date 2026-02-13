<?php

return [
    'project_id' => env('BIGQUERY_PROJECT_ID', 'partyhelp-bigquery'),
    'dataset' => env('BIGQUERY_DATASET', 'partyhelp_reporting'),
    'credentials_path' => env('BIGQUERY_CREDENTIALS_PATH')
        ? (str_starts_with(env('BIGQUERY_CREDENTIALS_PATH'), '/') ? env('BIGQUERY_CREDENTIALS_PATH') : base_path(env('BIGQUERY_CREDENTIALS_PATH')))
        : storage_path('app/bigquery-credentials.json'),
];
