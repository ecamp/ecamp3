<?php

namespace EcampCore\Fieldset\Login;

use EcampLib\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class SetPasswordFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('set-password');

        $this->add(array(
            'name' => 'password1',
            'options' => array(
                'label' => 'New Password:'
            ),
            'type'  => 'Password'
        ));

        $this->add(array(
            'name' => 'password2',
            'options' => array(
                'label' => 'Double check:'
            ),
            'type'  => 'Password'
        ));
    }

    public function getInputFilterSpecification()
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
