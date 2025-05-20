<?php

return [

    'paths' => ['api/*', 'starships/*', 'sanctum/csrf-cookie', 'login', 'register', '*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:3000'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
