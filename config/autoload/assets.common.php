<?php

return array(
    'asset_manager' => array(
        'resolver_configs' => array(

            'map' => array(

                // jQuery:
                'vendor-assets/js/jquery.js'                    => __ASSETS__ . '/jquery/dist/jquery.js',

                // jQuery UI:
                'vendor-assets/js/jquery.ui.js'                 => __ASSETS__ . '/jquery-ui/ui/minified/jquery-ui.min.js',
                'vendor-assets/css/jquery-ui.css'               => __ASSETS__ . '/jquery-ui/themes/base/minified/jquery-ui.min.css',

                // jQuery Autosize:
                'vendor-assets/js/jquery.autosize.js'           => __ASSETS__ . '/jquery-autosize/dist/autosize.min.js',

                // Angular JS:
                'vendor-assets/js/angular.js'                   => __ASSETS__ . '/angular/angular.js',

                // Angular Bootstrap:
                'vendor-assets/js/angular.bootstrap.js'         => __ASSETS__ . '/angular-bootstrap/ui-bootstrap-tpls.min.js',

                // Angular HAL:
                'vendor-assets/js/angular.hal.js'               => __ASSETS__ . '/angular-hal/angular-hal.js',

                // Angular Resource:
                'vendor-assets/js/angular.resource.js'          => __ASSETS__ . '/angular-resource/angular-resource.js',

                // Angular Sanitize:
                'vendor-assets/js/angular.sanitize.js'          => __ASSETS__ . '/angular-sanitize/angular-sanitize.js',

                // Angular Translate:
                'vendor-assets/js/angular.translate.js'         => __ASSETS__ . '/angular-translate/angular-translate.js',

                // Angular UI:
                'vendor-assets/js/angular.ui.js'                => __ASSETS__ . '/jquery-ui/ui/minified/jquery-ui.min.js',

                // Angular UI Select:
                'vendor-assets/js/angular.ui.select.js'         => __ASSETS__ . '/angular-ui-select/dist/select.js',

                // Angular UI Slider:
                'vendor-assets/js/angular.ui.slider.js'         => __ASSETS__ . '/angular-ui-slider/src/slider.js',

                // Angular UI Sortable:
                'vendor-assets/js/angular.ui.sortable.js'       => __ASSETS__ . '/angular-ui-sortable/sortable.js',

                // Bootstrap:
                'vendor-assets/js/bootstrap.js'                 => __ASSETS__ . '/bootstrap/dist/js/bootstrap.js',
                'vendor-assets/css/bootstrap.css'               => __ASSETS__ . '/bootstrap/dist/css/bootstrap.css',

                // Bootstrap Select:
                'vendor-assets/js/bootstrap.select.js'          => __ASSETS__ . '/bootstrap-select/dist/js/bootstrap-select.min.js',
                'vendor-assets/css/bootstrap.select.css'        => __ASSETS__ . '/bootstrap-select/dist/css/bootstrap-select.min.css',

                // Font Awesome:
                'vendor-assets/css/fontawesome.css'             => __ASSETS__ . '/fontawesome/css/font-awesome.min.css',
                'vendor-assets/fonts/FontAwesome.otf'           => __ASSETS__ . '/fontwaesome/font/FontAwesome.ltf',
                'vendor-assets/fonts/fontawesome-webfont.eot'   => __ASSETS__ . '/fontawesome/fonts/fontawesome-webfont.eot',
                'vendor-assets/fonts/fontawesome-webfont.svg'   => __ASSETS__ . '/fontawesome/fonts/fontawesome-webfont.svg',
                'vendor-assets/fonts/fontawesome-webfont.ttf'   => __ASSETS__ . '/fontawesome/fonts/fontawesome-webfont.ttf',
                'vendor-assets/fonts/fontawesome-webfont.woff'  => __ASSETS__ . '/fontawesome/fonts/fontawesome-webfont.woff',
                'vendor-assets/fonts/fontawesome-webfont.woff2' => __ASSETS__ . '/fontawesome/fonts/fontawesome-webfont.woff2',

                // Typeahead.js:
                'vendor-assets/js/typeahead.bloodhound.js'      => __ASSETS__ . '/typeahead.js/dist/bloodhound.js',
                'vendor-assets/js/typeahead.boundle.js'         => __ASSETS__ . '/typeahead.js/dist/typeahead.bundle.js',
                'vendor-assets/js/typeahead.jquery.js'          => __ASSETS__ . '/typeahead.js/dist/typeahead.jquery.js',

                // URI.js:
                'vendor-assets/js/uri.js'                       => __ASSETS__ . '/uri.js/src/URI.js',
                'vendor-assets/js/uri.template.js'              => __ASSETS__ . '/uri.js/src/URITemplate.js',

            ),
        ),

        'caching' => array(
            'default' => array(
                'cache'     => 'Filesystem',
                'options' => array(
                    'dir' => __DATA__ . "/assets",
                ),
            ),
        ),
    ),
);
