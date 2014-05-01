<?php
return array(

    'aliases' => array(

    ),

    'factories' => array(

        'ecampcore.register.user' => function($fem){
            return new \EcampCore\Validation\Auth\RegisterFieldset();
        },

        'ecampweb.register.user' => function($fem){
            $form = new \Zend\Form\Form('ecampweb.register.user');
            $form->add($fem->get('ecampcore.register.user'));

            return $form;
        }
    ),

    'invokables' => array(

    ),

    'initializers' => array(
        function($element, $fem){

        }
    ),
);
