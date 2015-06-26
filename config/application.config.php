<?php

require_once 'define.php';

$typ = getenv('typ') ?: 'web';  // web, job
$env = getenv('env') ?: 'prod'; // dev, test, prod

return array(
    'modules' => array(
        'ZFTool',
        'ZendDeveloperTools',

        'AssetManager',

        'DoctrineTools',
        'DoctrineModule',
        'DoctrineORMModule',
        'DoctrineDataFixtureModule',
        'BsbDoctrineTranslationLoader',

// 		'DiWrapper',
//		'OcraServiceManager',
//    	'OcraDiCompiler',

        'ZfcTwig',
        'TwbBundle',    // Bootstrap3 integration

        'AtPhpSettings',

        'PhlyRestfully',

        'EcampLib',
        'EcampCore',
        'EcampWeb',
        'EcampApi',
        'EcampDB',
//     	'EcampDev',

        'EcampStoryboard',
        'EcampMaterial',

//      'Application'
    ),

    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
            './plugins'
        ),
        'config_glob_paths' => array(
            sprintf('config/autoload/{,*.}{common,%s}{,.%s}{,.local}.php', $typ, $env),
            // 'config/app.autoload/{,*.}{global,local}.php',
            // 'config/common.autoload/{,*.}{global,local}.php',
        )
    )
);
