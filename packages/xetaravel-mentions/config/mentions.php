<?php

return [
    'pools' => [
        'users' => [
            // Model that will be mentioned.
            'model' => App\Models\User::class,

            // The column that will be used to search the model by the parser.
            'column' => 'username',

            // The route used to generate the user link.
            'route' => '/users/profile/@',

            // Notification class to use when this model is mentioned.
            'notification' => App\Notifications\MentionNotification::class,
        ]
    ]
];
