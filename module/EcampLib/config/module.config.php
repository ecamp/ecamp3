<?php
return array(
    'controllers' => array(
        'abstract_factories' => array(
//			'EcampLib\Controller\CommonControllerAbstractFactory'
        ),
    ),

    'zfctwig' => array(
        'extensions' => array(
            'ecampLibExtensions' => 'EcampLib\Twig\EcampLibExtensions'
        ),
    ),

    'view_helpers' => array(
        'invokables' => array(
            'form' =>               'EcampLib\Form\View\Helper\Form',
            'formRow' =>            'EcampLib\Form\View\Helper\FormRow',
            'formCollection' =>     'EcampLib\Form\View\Helper\FormCollection',
            'formElement' =>        'EcampLib\Form\View\Helper\FormElement',
            'formElementErrors' =>  'EcampLib\Form\View\Helper\FormElementErrors',
        )
    ),

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
