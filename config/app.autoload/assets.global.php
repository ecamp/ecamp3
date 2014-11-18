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

                /* jQuery Textarea autosize */
                'jquery-autosize/js/jquery.autosize.js' => 'https://raw.github.com/jackmoore/autosize/master/jquery.autosize.js',
                'jquery-autosize/js/jquery.autosize.min.js' => 'https://raw.github.com/jackmoore/autosize/master/jquery.autosize.min.js',

                /*Angular Strap */
// 				'assets/js/angular-strap.js' => 'https://raw.github.com/mgcrea/angular-strap/v0.7.5/dist/angular-strap.js',
// 				'assets/js/angular-strap.min.js' => 'https://raw.github.com/mgcrea/angular-strap/v0.7.5/dist/angular-strap.min.js',

                /* Angular UI */
                'angular-ui/js/ui-bootstrap.js' => __VENDOR__ . '/angular-ui/bootstrap/ui-bootstrap.js',
                'angular-ui/js/ui-bootstrap.min.js' => __VENDOR__ . '/angular-ui/bootstrap/ui-bootstrap.min.js',
                'angular-ui/js/ui-bootstrap-tpls.js' => __VENDOR__ . '/angular-ui/bootstrap/ui-bootstrap-tpls.js',
                'angular-ui/js/ui-bootstrap-tpls.min.js' => __VENDOR__ . '/angular-ui/bootstrap/ui-bootstrap-tpls.min.js',
                'angular-ui/js/ui-slider.js' => __VENDOR__ . '/angular-ui/ui-slider/src/slider.js',

                /* Bootstrap Select */
                'bootstrap-select/js/bootstrap-select.js' => __VENDOR__ . '/bootstrap-select/bootstrap-select/dist/js/bootstrap-select.js',
                'bootstrap-select/js/bootstrap-select.min.js' => __VENDOR__ . '/bootstrap-select/bootstrap-select/dist/js/bootstrap-select.min.js',
                'bootstrap-select/css/bootstrap-select.css' => __VENDOR__ . '/bootstrap-select/bootstrap-select/dist/css/bootstrap-select.css',
                'bootstrap-select/css/bootstrap-select.min.css' => __VENDOR__ . '/bootstrap-select/bootstrap-select/dist/css/bootstrap-select.min.css',

                /* Bootstrap Datetime picker */
                'bootstrap-datetimepicker/js/bootstrap-datetimepicker.js' => 'https://raw.github.com/smalot/bootstrap-datetimepicker/master/js/bootstrap-datetimepicker.js',
                'bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js' => 'https://raw.github.com/smalot/bootstrap-datetimepicker/master/js/bootstrap-datetimepicker.min.js',
                'bootstrap-datetimepicker/css/bootstrap-datetimepicker.css' => 'https://raw.github.com/smalot/bootstrap-datetimepicker/master/css/bootstrap-datetimepicker.css',
                'bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css' => 'https://raw.github.com/smalot/bootstrap-datetimepicker/master/css/bootstrap-datetimepicker.min.css',

                /* Angular-Hal */
                'angular-hal/angular-hal.js' => __VENDOR__ . '/LuvDaSun/angular-hal/angular-hal.js',
            ),
        ),
    ),
);
