<?php

namespace EcampWeb\Form\Auth;

use EcampWeb\Form\AjaxBaseForm;

class ResetPasswordForm extends AjaxBaseForm
{
    public function __construct()
    {
        parent::__construct('reset-password');
    }

    public function init()
    {
        $this->add(array(
            'type' => 'EcampCore\Fieldset\Login\SetPasswordFieldset'
        ));
    }

}
