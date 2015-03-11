<?php

define("__BASE_URL__" , 'http://www.ecamp3.dev');

require_once 'define.php';

$typ = getenv('typ') ?: 'web';
$env = getenv('env') ?: 'prod';

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
            sprintf('config/autoload/{,*.}{common,%s}{,.%s}{,.local}.php', $typ, $env),
            // 'config/job.autoload/{,*.}{global,local}.php',
            // 'config/common.autoload/{,*.}{global,local}.php',
        )
    )
);
