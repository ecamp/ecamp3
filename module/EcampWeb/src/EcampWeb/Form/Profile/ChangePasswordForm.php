<?php

namespace EcampWeb\Form\Profile;

use EcampCore\Fieldset\Login\CheckPasswordFieldset;
use EcampCore\Fieldset\Login\SetPasswordFieldset;
use EcampWeb\Form\BaseForm;

class ChangePasswordForm
    extends BaseForm
{
    public function __construct()
    {
        parent::__construct('change-password-form');

        $checkPasswordFieldset = new CheckPasswordFieldset();
        $this->add($checkPasswordFieldset);

        $setPasswordFieldset = new SetPasswordFieldset();
        $this->add($setPasswordFieldset);
    }
}
