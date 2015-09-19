<?php

namespace EcampCore\Fieldset\User;

use Zend\InputFilter\InputFilterProviderInterface;

use EcampLib\Form\BaseFieldset;

class UserBaseFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    public function __construct($name = 'user-base')
    {
        parent::__construct($name);
    }

    public function init()
    {
        $this->add(array(
            'name' => 'firstname',
            'options' => array(
                'label' => 'Firstname',
                'column-size' => 'sm-9',
                'label_attributes' => array('class' => 'col-sm-3')
            ),
            'attributes' => array(),
            'type'  => 'Text'
        ));

        $this->add(array(
            'name' => 'surname',
            'options' => array(
                'label' => 'Surname',
                'column-size' => 'sm-9',
                'label_attributes' => array('class' => 'col-sm-3')
            ),
            'attributes' => array(),
            'type'  => 'Text'
        ));

        $this->add(array(
            'name' => 'scoutname',
            'options' => array(
                'label' => 'Scoutname',
                'column-size' => 'sm-9',
                'label_attributes' => array('class' => 'col-sm-3')
            ),
            'attributes' => array(),
            'type'  => 'Text'
        ));

        parent::init();
    }

    public function getInputFilterSpecification()
    {
        return array(
            'firstname' => array(
                'required' => true,
            ),

            'surname' => array(
                'required' => true,
            ),

            'scoutname' => array(
                'required' => false,
            ),
        );
    }
}
