<?php

namespace EcampCore\Fieldset\User;

use EcampLib\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class UserEmailFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('user-email');
    }

    public function init()
    {
        $this->add(array(
            'name' => 'email',
            'options' => array(
                'label' => 'E-Mail: '
            ),
            'type' => 'Text'
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'email' => array(
                'required' => true,
            )
        );
    }

}
