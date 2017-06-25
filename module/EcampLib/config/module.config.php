<?php
return array(
//     'ecamp' => array(
//         'doctrine' => array(
//             'repositories' => array(
//                 'ecamp_lib' => array(
//                     'entitymanager' => 'orm_default',
//                     'mappings' => array(
// //                        "/^EcampLib\\\\Repository\\\\(\\w+)$/" => "EcampLib\\\\Entity\\\\$1",
//                     )
//                 )
//             ),

//         )
//     ),

//     'zfctwig' => array(
//         'extensions' => array(
//             'ecampLibExtensions' => 'EcampLib\Twig\EcampLibExtensions'
//         ),
//     ),

//     'view_helpers' => array(
//         'invokables' => array(
//             'form' =>               'EcampLib\Form\View\Helper\Form',
//             'formRow' =>            'EcampLib\Form\View\Helper\FormRow',
//             'formCollection' =>     'EcampLib\Form\View\Helper\FormCollection',
//             'formElementErrors' =>  'EcampLib\Form\View\Helper\FormElementErrors',
//         ),
//         'factories' => array(
//             'formElement' =>    'EcampLib\Form\View\Helper\Factory\FormElementFactory'
//         )
//     ),

    'doctrine' => array(
        'driver' => array(
            'ecamp_lib_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/EcampLib/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'EcampLib\Entity' => 'ecamp_lib_entities'
                )
            )
        ),
    ),
);
