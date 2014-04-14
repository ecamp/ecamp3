<?php

require_once 'define.php';

return array(
    'modules' => array(
        'DoctrineModule',
        'DoctrineORMModule',

        'EcampLib',
        'EcampCore',
        'EcampWeb',
        'EcampApi',
        'EcampDB',
    ),

    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
        'config_glob_paths' => array(
            'config/job.autoload/{,*.}{global,local}.php',
            'config/common.autoload/{,*.}{global,local}.php',
        )
    )
);
