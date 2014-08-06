<?php

require_once 'define.php';

$modules = array(
    'AtPhpSettings',
    'AssetManager',

    'DoctrineModule',
    'DoctrineORMModule',

    'ZfcTwig',
    'TwbBundle',

    'PhlyRestfully',

    'EcampLib',
    'EcampCore',
    'EcampWeb',
    'EcampApi',

    'EcampStoryboard',
    'EcampMaterial',
);

if (__ENV__ == __ENV_DEV__) {
    $config_glob_paths = array(
        'config/app.autoload/{,*.}{,dev.}{global,local}.php',
        'config/common.autoload/{,*.}{,dev.}{global,local}.php',
    );

//    $modules[] = 'ZFTool';
//    $modules[] = 'ZendDeveloperTools';
    $modules[] = 'EcampDB';

} elseif (__ENV__ == __ENV_TEST__) {
    $config_glob_paths = array(
        'config/app.autoload/{,*.}{,test.}{global,local}.php',
        'config/common.autoload/{,*.}{,test.}{global,local}.php',
    );

} else {
    $config_glob_paths = array(
        'config/app.autoload/{,*.}{,prod.}{global,local}.php',
        'config/common.autoload/{,*.}{,prod.}{global,local}.php',
    );
}

return array(
    'modules' => $modules,

    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
            './plugins'
        ),
        'config_glob_paths' => $config_glob_paths
    )
);
