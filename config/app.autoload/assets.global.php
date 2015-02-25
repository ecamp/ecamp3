<?php

return array(
    'asset_manager' => array(
        'caching' => array(
            'default' => array(
                'cache'     => 'Filesystem',
                'options' => array(
                    'dir' => __DATA__ . "/assets",
                ),
            ),
        ),
        'resolver_configs' => array(
            'paths' => array(
                __VENDOR__ . "/components",
            ),

            'aliases' => array(
                'core-assets/' => __MODULE__ . "/EcampCore/assets/",
                'web-assets/' => __MODULE__ . "/EcampWeb/assets/",

                'plugin/material' => __PLUGINS__ . "/EcampMaterial/assets/",
            ),

            'collections' => array(
                'web-assets/entities.js' => array(
                    'web-assets/entity/camp.js',
                    'web-assets/entity/period.js',
                    'web-assets/entity/day.js',
                    'web-assets/entity/eventInstance.js',
                ),
                'web-assets/picasso/entities.js' => array(
                    'web-assets/picasso/entity/camp.js',
                    'web-assets/picasso/entity/period.js',
                    'web-assets/picasso/entity/date.js',
                    'web-assets/picasso/entity/day.js',
                    'web-assets/picasso/entity/eventInstance.js',
                )
            ),

            'map' => array(

            ),
        ),
    ),
);
