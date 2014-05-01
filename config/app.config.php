<?php

require_once 'define.php';

return array(
    'modules' => array(
//        'ZFTool',
//        'ZendDeveloperTools',

        'AssetManager',

        'DoctrineTools',
        'DoctrineModule',
        'DoctrineORMModule',

// 		'DiWrapper',
//		'OcraServiceManager',
//    	'OcraDiCompiler',

        'ZfcTwig',
        'TwbBundle',    // Bootstrap3 integration

        'PhlyRestfully',

        'EcampLib',
        'EcampCore',
        'EcampWeb',
        'EcampApi',
        'EcampDB',
//     	'EcampDev',

        'EcampStoryboard',
        'EcampMaterial',

        'Application'
    ),

    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
            './plugins'
        ),
        'config_glob_paths' => array(
            'config/app.autoload/{,*.}{global,local}.php',
            'config/common.autoload/{,*.}{global,local}.php',
        )
    )
);
