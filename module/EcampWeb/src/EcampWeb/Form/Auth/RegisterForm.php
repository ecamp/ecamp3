<?php

namespace EcampWeb\Form\Auth;

use EcampWeb\Form\AjaxBaseForm;

class RegisterForm extends AjaxBaseForm
{

    public function __construct()
    {
        parent::__construct('register');

    }

    public function init()
    {
        $this->add(array(
            'type' => 'EcampCore\Fieldset\User\UserCreateFieldset'
        ));

        $this->add(array(
            'type' => 'EcampCore\Fieldset\Login\LoginCreateFieldset'
        ));
    }

}
