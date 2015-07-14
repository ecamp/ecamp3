<?php
return array(
    'resolver_configs' => array(
        'collections' => array(
/*
 *           Composed Asset:
 *          'example/file.js' => array(
 *              'js/a.js',
 *              'js/b.js',
 *              'js/c.js',
 *          )
 */

        ),

        'paths' => array(
/*
 *          Include Paths:
 *          __DIR__ . '/example/directory',
 */

        ),

        'aliases' => array(
/*
 *          Alias Folder:
 *          'vender/' => __DIR__ . '/../../vendor',
 */
        ),

        'map' => array(
            'web-assets/js/ns.js'                                   =>  __MODULE__ . '/EcampWeb/assets/js/ns.js',
            'web-assets/js/ajax-form-element.js'                    =>  __MODULE__ . '/EcampWeb/assets/js/ajax-form-element.js',

            'web-assets/js/remote-data.js'                          =>  __MODULE__ . '/EcampWeb/assets/js/remote-data.js',
            'web-assets/js/modal-form.js'                           =>  __MODULE__ . '/EcampWeb/assets/js/modal-form.js',
            'web-assets/js/ng-paginator.js'                         =>  __MODULE__ . '/EcampWeb/assets/js/ng-paginator.js',
            'web-assets/js/ng-contextmenu.js'                       =>  __MODULE__ . '/EcampWeb/assets/js/ng-contextmenu.js',
            'web-assets/js/sorted-dictionary.js'                    =>  __MODULE__ . '/EcampWeb/assets/js/sorted-dictionary.js',

            'web-assets/js/camp/picasso/remote-data.js'             =>  __MODULE__ . '/EcampWeb/assets/js/camp/picasso/remote-data.js',
            'web-assets/js/camp/picasso/picasso-data.js'            =>  __MODULE__ . '/EcampWeb/assets/js/camp/picasso/picasso-data.js',
            'web-assets/js/camp/picasso/picasso-event-create.js'    =>  __MODULE__ . '/EcampWeb/assets/js/camp/picasso/picasso-event-create.js',
            'web-assets/js/camp/picasso/picasso-event-instance.js'  =>  __MODULE__ . '/EcampWeb/assets/js/camp/picasso/picasso-event-instance.js',
            'web-assets/js/camp/picasso/picasso-timeline.js'        =>  __MODULE__ . '/EcampWeb/assets/js/camp/picasso/picasso-timeline.js',
            'web-assets/js/camp/picasso/picasso-app.js'             =>  __MODULE__ . '/EcampWeb/assets/js/camp/picasso/picasso-app.js',
            'web-assets/js/camp/picasso/picasso.de.js'              =>  __MODULE__ . '/EcampWeb/assets/js/camp/picasso/picasso.de.js',
            'web-assets/css/camp/picasso/picasso.css'               =>  __MODULE__ . '/EcampWeb/assets/css/camp/picasso/picasso.css',

            'web-assets/js/camp/print/print-controller.js'          =>  __MODULE__ . '/EcampWeb/assets/js/camp/print/print-controller.js',

            'web-assets/js/camp/event/event-controller.js'          =>  __MODULE__ . '/EcampWeb/assets/js/camp/event/event-controller.js',

            'web-assets/js/camp/collaboration.js'                   =>  __MODULE__ . '/EcampWeb/assets/js/camp/collaboration.js',
            'web-assets/js/group/membership.js'                     =>  __MODULE__ . '/EcampWeb/assets/js/group/membership.js',

            'web-assets/css/bootstrap.css'                          =>  __MODULE__ . '/EcampWeb/assets/css/bootstrap.css',
            'web-assets/css/default.css'                            =>  __MODULE__ . '/EcampWeb/assets/css/default.css',
            'web-assets/css/ng-contextmenu.css'                     =>  __MODULE__ . '/EcampWeb/assets/css/ng-contextmenu.css',

            'web-assets/tpl/async-modal-window.html'                =>  __MODULE__ . '/EcampWeb/assets/tpl/async-modal-window.html',

            'web-assets/tpl/camp/collaboration/kick.html'           =>  __MODULE__ . '/EcampWeb/assets/tpl/camp/collaboration/kick.html',
            'web-assets/tpl/camp/collaboration/leave.html'          =>  __MODULE__ . '/EcampWeb/assets/tpl/camp/collaboration/leave.html',
            'web-assets/tpl/camp/collaboration/operation.html'      =>  __MODULE__ . '/EcampWeb/assets/tpl/camp/collaboration/operation.html',

            'web-assets/tpl/group/membership/kick.html'             =>  __MODULE__ . '/EcampWeb/assets/tpl/group/membership/kick.html',
            'web-assets/tpl/group/membership/leave.html'            =>  __MODULE__ . '/EcampWeb/assets/tpl/group/membership/leave.html',
            'web-assets/tpl/group/membership/operation.html'        =>  __MODULE__ . '/EcampWeb/assets/tpl/group/membership/operation.html',

            'web-assets/tpl/camp/picasso/picasso.html'              =>  __MODULE__ . '/EcampWeb/assets/tpl/camp/picasso/picasso.html',
            'web-assets/tpl/camp/picasso/event-instance.html'       =>  __MODULE__ . '/EcampWeb/assets/tpl/camp/picasso/event-instance.html'
        )
    ),

    'filters' => array(
    ),

    'caching' => array(
    ),
);
