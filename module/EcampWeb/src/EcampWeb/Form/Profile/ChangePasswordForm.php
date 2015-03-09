<?php

namespace EcampWeb\Form\Profile;

use EcampWeb\Form\BaseForm;

class ChangePasswordForm
    extends BaseForm
{
    public function __construct()
    {
        parent::__construct('change-password-form');
    }

    public function init()
    {
        $this->add(array(
            'type' => 'EcampCore\Fieldset\Login\CheckPasswordFieldset'
        ));

        $this->add(array(
            'type' => 'EcampCore\Fieldset\Login\SetPasswordFieldset'
        ));
    }
}
