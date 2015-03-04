<?php

namespace EcampCore\Fieldset\Login;

use EcampLib\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class CheckPasswordFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('check-password');

        $this->add(array(
            'name' => 'password',
            'options' => array(
                'label' => 'Password: ',
/*
                'column-size' => 'sm-9',
                'label_attributes' => array('class' => 'col-sm-3')
*/
            ),
//            'attributes' => array(),
            'type' => 'Password'
        ));
    }

    public function getInputFilterSpecification()
    {
        return array();
    }

}
