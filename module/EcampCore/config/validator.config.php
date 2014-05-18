<?php
return array(

    'aliases' => array(
        'UniqueUsername' => 'EcampCore\Validator\User\UniqueUsername',
        'UniqueMailAddress' => 'EcampCore\Validator\User\UniqueMailAddress'
    ),

    'factories' => array(
        'EcampCore\Validator\User\UniqueUsername' => 'EcampCore\Validator\User\UniqueUsernameFactory',
        'EcampCore\Validator\User\UniqueMailAddress' => 'EcampCore\Validator\User\UniqueMailAddressFactory'
    ),

);
