<?php

return array(
    'asset_manager' => array(
        'caching' => array(
            'default' => array(
//     			'cache'     => 'FileSystem',
                'cache'     => 'FilePath',
                'options' => array(
                    'dir' => __PUBLIC__,
                ),
            ),
        ),
        'resolver_configs' => array(
            'paths' => array(
                __COMPONENTS__
            ),

            'map' => array(

                /* jQuery Textarea autosize */
                'jquery-autosize/js/jquery.autosize.js' => 'https://raw.github.com/jackmoore/autosize/master/jquery.autosize.js',
                'jquery-autosize/js/jquery.autosize.min.js' => 'https://raw.github.com/jackmoore/autosize/master/jquery.autosize.min.js',

                /*Angular Strap */
// 				'assets/js/angular-strap.js' => 'https://raw.github.com/mgcrea/angular-strap/v0.7.5/dist/angular-strap.js',
// 				'assets/js/angular-strap.min.js' => 'https://raw.github.com/mgcrea/angular-strap/v0.7.5/dist/angular-strap.min.js',

                /* Bootstrap Select */
                'bootstrap-select/js/bootstrap-select.js' => __VENDOR__ . '/bootstrap-select/bootstrap-select/bootstrap-select.js',
                'bootstrap-select/js/bootstrap-select.min.js' => __VENDOR__ . '/bootstrap-select/bootstrap-select/bootstrap-select.min.js',
                'bootstrap-select/css/bootstrap-select.css' => __VENDOR__ . '/bootstrap-select/bootstrap-select/bootstrap-select.css',
                'bootstrap-select/css/bootstrap-select.min.css' => __VENDOR__ . '/bootstrap-select/bootstrap-select/bootstrap-select.min.css',

                /* Bootstrap Datetime picker */
                'bootstrap-datetimepicker/js/bootstrap-datetimepicker.js' => 'https://raw.github.com/smalot/bootstrap-datetimepicker/master/js/bootstrap-datetimepicker.js',
                'bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js' => 'https://raw.github.com/smalot/bootstrap-datetimepicker/master/js/bootstrap-datetimepicker.min.js',
                'bootstrap-datetimepicker/css/bootstrap-datetimepicker.css' => 'https://raw.github.com/smalot/bootstrap-datetimepicker/master/css/bootstrap-datetimepicker.css',
                'bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css' => 'https://raw.github.com/smalot/bootstrap-datetimepicker/master/css/bootstrap-datetimepicker.min.css',

            ),
        ),
    ),
);
