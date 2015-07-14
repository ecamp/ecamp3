<?php
return array(
    'router' => array(
        'routes' => array(
            'web' => include __DIR__ . '/routes/web.php'
        ),
    ),

    'asset_manager' => include __DIR__ . '/assets/assets.config.php',

    'translator' => array(
        'remote_translation' => array(
            /* add a remote translation loader for each text domain */
            array('type' => 'BsbDoctrineTranslationLoader', 'text_domain' => 'EcampWeb'),
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'layout'                   => 'layout/layout.default',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.twig',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'zfctwig' => array(
        'environment_options' => array(
            //'cache' => 'data/cache',
        )
    ),
);
