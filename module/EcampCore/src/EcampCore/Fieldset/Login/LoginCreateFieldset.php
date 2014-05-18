<?php

namespace EcampCore\Fieldset\Login;

use EcampLib\Form\BaseFieldset;

class LoginCreateFieldset extends BaseFieldset
{
    public function __construct()
    {
        parent::__construct('login-create');
    }

    public function init()
    {
        $this->add(array(
            'name' => 'password1',
            'options' => array(
                'label' => 'Password',
                'column-size' => 'sm-9',
                'label_attributes' => array('class' => 'col-sm-3')
            ),
            'attributes' => array(),
            'type'  => 'Password'
        ));

        $this->add(array(
            'name' => 'password2',
            'options' => array(
                'label' => 'Wiederholen',
                'column-size' => 'sm-9',
                'label_attributes' => array('class' => 'col-sm-3')
            ),
            'attributes' => array(),
            'type'  => 'Password'
        ));
    }

    public static function createInputFilterSpecification()
    {
        return array(
            'password1' => array(
                'required' => true,
                'validators' => array(
                    'pwLength' => array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 6,
                            'max'      => 128,
                        )
                    )
                )
            ),

            'password2' => array(
                'validators' => array(
                    array(
                        'name' => 'identical',
                        'options' => array('token' => 'password1' )
                    ),
                )
            )
        );
    }
}
