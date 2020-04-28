<?php

return [
    'zend_mail' => [
        'transport' => [
            'type' => 'file',
            'options' => [
                'path' => __DIR__.'/../../data/mail',
            ],
        ],
    ],
];
