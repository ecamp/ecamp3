<?php

namespace EcampWeb\Form\Profile;

use EcampWeb\Form\BaseForm;

class ChangeEmailForm
    extends BaseForm
{
    public function __construct()
    {
        parent::__construct('change-email-form');
    }

    public function init()
    {
        $this->add(array(
            'type' => 'EcampCore\Fieldset\User\UserEmailFieldset'
        ));
    }
}
