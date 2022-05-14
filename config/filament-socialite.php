<?php

// config for DutchCodingCompany/FilamentSocialite
return [
    // Allow login, and registration if enabled, for users with an email for one of the following domains.
    // All domains allowed by default
    'domain_allowlist' => [],

    // Allow registration through socials
    'registration' => false,

    // If you set this config to true, the `users.password` column must be nullable
    // or an error will be thrown when the user is created.
    // If you set this config to false, the user will be registered using a strong
    // random password.
    'keep_null_password' => true,

    // Specify the providers that should be visible on the login.
    // These should match the socialite providers you have setup in your services.php config.
    // Uses blade UI icons, for example: https://github.com/owenvoke/blade-fontawesome
    'providers' => [
//        'gitlab' => [
//            'label' => 'GitLab',
//            'icon' => 'fab-gitlab',
//        ],
//        'github' => [
//            'label' => 'GitHub',
//            'icon' => 'fab-github',
//        ],
    ],
];
