<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Feature flags
    |--------------------------------------------------------------------------
    |
    | Posts are temporarily disabled for regular users. The code and data are
    | kept intact so the section can be re-enabled with an environment flag.
    |
    */

    'posts_enabled' => (bool) env('FEATURE_POSTS_ENABLED', false),
];
