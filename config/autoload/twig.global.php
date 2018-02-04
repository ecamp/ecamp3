<?php

return [

    'zend_twig' => [

        /**
         * In a ZF3 by default we have this structure:
         *  - ViewModel with template from 'layout/layout'
         *  - ViewModel as child with action template 'application/index/index'
         * In that case we should always force standalone state of child models
         */
        'force_standalone' => true,

        /**
         * Force Your application to use TwigRender for ViewModel.
         * If false, then TwigStrategy will be applied only for TwigModel
         *
         * @note: In release v.1.5 this parameter will be set to false
         */
        'force_twig_strategy' => true,

        'helpers' => [
            'configs' => [
                /**
                 * Here can be declared configuration classes for service manager:
                 *  \Zend\Form\View\HelperConfig::class,
                 *  \Zend\I18n\View\HelperConfig::class,
                 *  \Zend\Navigation\View\HelperConfig::class,
                 */
            ],
        ],
    ],
];