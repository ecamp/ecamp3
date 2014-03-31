<?php

namespace EcampWeb\Form\Auth;

use EcampWeb\Form\BaseForm;

class LoginForm extends BaseForm
{

    public function __construct()
    {
        parent::__construct('login');

        $this->add(array(
            'name' => 'login',
            'options' => array(
                'label' => 'Login',
            ),
            'type'  => 'Text'
        ));

        $this->add(array(
            'name' => 'password',
            'options' => array(
                'label' => 'Password',
            ),
            'type'  => 'Password'
        ));

        $this->add(array(
            'name' => 'rememberme',
            'options' => array(
                'label' => 'Remember me',
            ),
            'type'  => 'Checkbox'
        ));

        $this->add(array(
            'name' => 'submit',
            'options' => array(
                'label' => 'Login',
            ),
            'attributes' => array(
                'value' => 'Login'
            ),
            'type' => 'Submit'
        ));

        $this->add(array(
            'name' => 'redirect',
            'options' => array(
            ),
            'attributes' => array(
            ),
            'type' => 'Hidden'
        ));
    }

    public function setRedirect($url)
    {
        $this->get('redirect')->setValue($url);
    }

    public function getRedirect()
    {
        return $this->get('redirect')->getValue();
    }

    public function hasRedirect()
    {
        $url = $this->getRedirect();

        return !empty($url);
    }

}
