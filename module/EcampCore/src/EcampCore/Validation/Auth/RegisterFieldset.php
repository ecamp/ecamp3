<?php
namespace EcampCore\Validation\Auth;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class RegisterFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('register');

        $this->add(array(
            'name' => 'username',
            'options' => array(
                'label' => 'Username'
            ),
            'attributes' => array(
                'placeholder' => 'Username',
                'data-validation-required' => '',
                'data-v-remote-url' => "/web/login/checkUsername"
            ),
            'type'  => 'Text'
        ));

        $this->add(array(
            'name' => 'firstname',
            'options' => array(),
            'attributes' => array(
                'placeholder' => 'Firstname',
                'class' => 'validate',
                'data-validation-required' => ''
            ),
            'type'  => 'Text'
        ));

        $this->add(array(
            'name' => 'surname',
            'options' => array(),
            'attributes' => array(
                'placeholder' => 'Surname',
                'class' => 'validate',
                'data-validation-required' => ''
            ),
            'type'  => 'Text'
        ));

        $this->add(array(
            'name' => 'scoutname',
            'options' => array(),
            'attributes' => array(
                'placeholder' => 'Scoutname',
                'class' => 'validate',
                'data-validation-required' => ''
            ),
            'type'  => 'Text'
        ));

        $this->add(array(
            'name' => 'mail',
            'options' => array(),
            'attributes' => array(
                'placeholder' => 'Mail',
                'class' => "validate",
                'data-validation-email' => ''
            ),
            'type'  => 'Email'
        ));

        $this->add(array(
            'name' => 'password1',
            'options' => array(),
            'attributes' => array(
                'placeholder' => 'Password',
                'class' => 'validate',
                'data-validation-password' => ''
            ),
            'type'  => 'Password'
        ));

        $this->add(array(
            'name' => 'password2',
            'options' => array(),
            'attributes' => array(
                'placeholder' => 'Password',
                'class' => 'validate',
                'data-validation-password-repeat' => 'password1'
            ),
            'type'  => 'Password'
        ));

    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name' => 'username',
                'required' => true,
            ),
            array(
                'name' => 'firstname',
                'required' => true,
            ),
            array(
                'name' => 'surname',
                'required' => true,
            ),
            array(
                'name' => 'scoutname',
                'required' => true,
            ),
            array(
                'name' => 'mail',
                'required' => true,
            ),

        );
    }
}
