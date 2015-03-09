<?php

namespace EcampWeb\Form\Auth;

use EcampWeb\Form\AjaxBaseForm;

class ForgotPasswordForm extends AjaxBaseForm
{
    public function __construct()
    {
        parent::__construct('forgot-password');
    }

    public function init()
    {
        $this->add(array(
            'type' => 'EcampCore\Fieldset\User\UserEmailFieldset'
        ));
    }

}
