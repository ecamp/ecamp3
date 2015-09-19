<?php

namespace EcampCore\Fieldset\Login;

use EcampLib\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class CheckPasswordFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('check-password');
    }

    public function init()
    {
        $this->add(array(
            'name' => 'password',
            'options' => array(
                'label' => 'Password: '
            ),
            'type' => 'Password'
        ));
    }

    public function getInputFilterSpecification()
    {
        return array();
    }

}
