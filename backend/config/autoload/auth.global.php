<?php

return [
    'hybridauth' => [
        'providers' => [
            'google' => [
                'enabled' => true,
                'keys' => [
                    'id' => '889440431087-ueuhpadf2g7h5ucdke92mvfaf4l779m4.apps.googleusercontent.com',
                    'secret' => 'HNaD1FNO-a1qliacIrIfcGqO',
                ]
            ],
            'facebook' => [
                'enabled' => true,
                'id' => 'YOUR FACEBOOK CLIENT ID',
                'secret' => 'YOUR FACEBOOK CLIENT SECRET',
                'scope' => 'email, public_profile',
            ],
        ],
    ],
];
