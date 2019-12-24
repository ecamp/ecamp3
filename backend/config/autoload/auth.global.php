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
              'id' => '2a955efdaaac73f665b29ec182cd9a114db01675ced710a464d33d10f58be600',
              'secret' => '00a23e48bcb776d453b255428ffe810643db7155a9f3d743d7edf52eac400580',
            ],
            'endpoints' => [
                'authorize' => 'https://pbs.puzzle.ch/oauth/authorize',
                'token' => 'https://pbs.puzzle.ch/oauth/token',
                'profile' => 'https://pbs.puzzle.ch/de/oauth/profile',
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
