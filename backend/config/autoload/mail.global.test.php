<?php

return [
    'laminas_mail' => [
        'transport' => [
            'type' => 'InMemory',
            'options' => [
                'path' => __DIR__.'/../../data/mail',
            ],
        ],
    ],
];
