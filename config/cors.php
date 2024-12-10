<?php

return [
    'paths' => ['api/*',  'sanctum/csrf-cookie', 'images/*', '/check-approval/*', 'edit-page', 'uat-data', '/uats/trashed', 'uat_pages', 'dashboard-data', 'sanctum/csrf-cookie', 'users/profile', 'users', 'users/*', 'uats', 'uats/*', 'csrf-token', 'logout', 'perusahaan', 'perusahaan/*', 'clients', 'upload-logo', 'clients/*', '/users/update-profile', 'register', 'login', 'uat', 'add-user', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'supports_credentials' => true,
    'allowed_origins' => ['http://localhost:5173', ],
    'allowed_origins_patterns' => [
        '/^https:\/\/.*\.example\.com$/',
    ],
    'allowed_headers' => ['*', 'X-CSRF-TOKEN'],
    'exposed_headers' => [],
    'max_age' => 0,
];



