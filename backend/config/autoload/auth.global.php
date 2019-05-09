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
          'hitobito' => [
            'enabled' => true,
            'adapter' => '\eCamp\Core\Auth\Provider\Hitobito',
            'keys' => [
              'id' => 'd3ebb60ba888580cd81d2df57d45dece279a36d4f55fe741502df7ca9974ff5a',
              'secret' => '28084b340d05ec6aa43ff2f737ce15e92e86828fc92c51335f710880edcc0807',
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
