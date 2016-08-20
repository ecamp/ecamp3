<?php

return array(
    'asset_manager' => array(
        'resolver_configs' => array(

            'aliases' => array(
                // jQuery
                'vendor-assets/jquery/js/'              => __ASSETS__ . '/jquery/dist/',

                // jQuery UI
                'vendor-assets/jquery-ui/js/'           => __ASSETS__ . '/jquery-ui/ui/',
                'vendor-assets/jquery-ui/css/'          => __ASSETS__ . '/jquery-ui/themes/smoothness/',

                // jQuery Autosize
                'vendor-assets/jquery-autosize/js/'     => __ASSETS__ . '/jquery-autosize/dist/',

                // Angular JS:
                'vendor-assets/angular/js/'             => __ASSETS__ . '/angular/',

                // Angular Bootstrap:
                'vendor-assets/angular-bootstrap/js/'   => __ASSETS__ . '/angular-bootstrap/',

                // Bootstrap:
                'vendor-assets/bootstrap/js/'           => __ASSETS__ . '/bootstrap/dist/js/',
                'vendor-assets/bootstrap/css/'          => __ASSETS__ . '/bootstrap/dist/css/',
                'vendor-assets/bootstrap/fonts/'        => __ASSETS__ . '/bootstrap/dist/fonts/',

                // Font Awesome:
                'vendor-assets/fontawesome/css/'        => __ASSETS__ . '/fontawesome/css/',
                'vendor-assets/fontawesome/fonts/'      => __ASSETS__ . '/fontawesome/fonts/'
            ),

            'map' => array(

                // Angular HAL:
                'vendor-assets/js/angular.hal.js'                           => __ASSETS__ . '/angular-hal/angular-hal.js',

                // Angular Resource:
                'vendor-assets/js/angular.resource.js'                      => __ASSETS__ . '/angular-resource/angular-resource.js',

                // Angular Sanitize:
                'vendor-assets/js/angular.sanitize.js'                      => __ASSETS__ . '/angular-sanitize/angular-sanitize.js',

                // Angular Translate:
                'vendor-assets/js/angular.translate.js'                     => __ASSETS__ . '/angular-translate/angular-translate.js',

                // ng-File-Upload:
                'vendor-assets/js/ng-file-upload.js'                        => __ASSETS__ . '/ng-file-upload/angular-file-upload.js',

                // Angular UI:
                'vendor-assets/js/angular.ui.js'                            => __ASSETS__ . '/jquery-ui/ui/minified/jquery-ui.min.js',

                // Angular UI Select:
                'vendor-assets/js/angular.ui.select.js'                     => __ASSETS__ . '/angular-ui-select/dist/select.js',

                // Angular UI Slider:
                'vendor-assets/js/angular.ui.slider.js'                     => __ASSETS__ . '/angular-ui-slider/src/slider.js',

                // Angular UI Sortable:
                'vendor-assets/js/angular.ui.sortable.js'                   => __ASSETS__ . '/angular-ui-sortable/sortable.js',

                // Bootstrap Select:
                'vendor-assets/js/bootstrap.select.js'                      => __ASSETS__ . '/bootstrap-select/dist/js/bootstrap-select.min.js',
                'vendor-assets/css/bootstrap.select.css'                    => __ASSETS__ . '/bootstrap-select/dist/css/bootstrap-select.min.css',

                // Typeahead.js:
                'vendor-assets/js/typeahead.bloodhound.js'                  => __ASSETS__ . '/typeahead.js/dist/bloodhound.js',
                'vendor-assets/js/typeahead.boundle.js'                     => __ASSETS__ . '/typeahead.js/dist/typeahead.bundle.js',
                'vendor-assets/js/typeahead.jquery.js'                      => __ASSETS__ . '/typeahead.js/dist/typeahead.jquery.js',

                // URI.js:
                'vendor-assets/js/uri.js'                                   => __ASSETS__ . '/uri.js/src/URI.js',
                'vendor-assets/js/uri.template.js'                          => __ASSETS__ . '/uri.js/src/URITemplate.js',

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
