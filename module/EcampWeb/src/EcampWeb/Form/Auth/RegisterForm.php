<?php

namespace EcampWeb\Form\Auth;

use EcampCore\Validation\Auth\RegisterFieldset;
use EcampWeb\Form\BaseForm;

class RegisterForm extends BaseForm
{

    public function __construct()
    {
        parent::__construct('register');

        $this->add(new RegisterFieldset());
    }

}
