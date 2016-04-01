<?php

require_once 'define.php';

$typ = getenv('typ') ?: 'web';
$env = getenv('env') ?: 'prod';

return array(
    'modules' => array(
        'DoctrineModule',
        'DoctrineORMModule',
    	'BsbDoctrineTranslationLoader',

        'ZfcTwig',

        'EcampLib',
        'EcampCore',
        'EcampWeb',
        'EcampApi',
        'EcampDB',

        'EcampStoryboard',
        'EcampMaterial',
    ),

    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
            './plugins'
        ),
        'config_glob_paths' => array(
            sprintf('config/autoload/{,*.}{common,%s}{,.%s}{,.local}.php', $typ, $env),
            // 'config/job.autoload/{,*.}{global,local}.php',
            // 'config/common.autoload/{,*.}{global,local}.php',
        )
    )
);
