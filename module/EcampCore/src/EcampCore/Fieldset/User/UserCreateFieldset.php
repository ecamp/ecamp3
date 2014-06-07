<?php

namespace EcampCore\Fieldset\User;

class UserCreateFieldset extends UserBaseFieldset
{
    public function __construct()
    {
        parent::__construct('user-create');
    }

    public function init()
    {
        $this->add(array(
            'name' => 'username',
            'options' => array(
                'label' => 'Username',
                'column-size' => 'sm-9',
                'label_attributes' => array('class' => 'col-sm-3')
            ),
            'attributes' => array(),
            'type'  => 'Text'
        ));

        parent::init();

        $this->add(array(
            'name' => 'email',
            'options' => array(
                'label' => 'Mail',
                'column-size' => 'sm-9',
                'label_attributes' => array('class' => 'col-sm-3')
            ),
            'attributes' => array(),
            'type'  => 'Email'
        ));
    }

    public static function createInputFilterSpecification()
    {
        return array_merge_recursive(
            array(
                'username' => array(
                    'required' => true,
                    'validators' => array(
                        'uniqueUsername' => array(
                            'name' => 'UniqueUsername',
                        )
                    )
                ),
                'email' => array(
                    'required' => true,
                    'validators' => array(
                        'emailAddress' => array(
                            'name' => 'EmailAddress'
                        ),
                        'uniqueMailAddress' => array(
                            'name' => 'UniqueMailAddress'
                        )
                    )
                )
            ),
            UserBaseFieldset::createInputFilterSpecification()
        );
    }
}
