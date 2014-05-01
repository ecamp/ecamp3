<?php
return array(

    'invokables' => array(
        // Serious Controllers:
        'EcampCore\Controller\Avatar'           => 'EcampCore\Controller\AvatarController',
        'EcampCore\Controller\Plugin'           => 'EcampCore\Controller\PluginController',
        'EcampCore\Controller\FormValidation'   => 'EcampCore\Controller\FormValidationController',

        // Experimental Controllers:
        'EcampCore\Controller\Test'  => 'EcampCore\Controller\TestController',
        'EcampCore\Controller\Demo'  => 'EcampCore\Controller\DemoController',
        'EcampCore\Controller\Index' => 'EcampCore\Controller\IndexController',
        'EcampCore\Controller\Login' => 'EcampCore\Controller\LoginController',
        'EcampCore\Controller\Event' => 'EcampCore\Controller\EventController',
    )
);
